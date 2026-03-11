<?php

/** @var array $config */

require_once '../lib/boot.php';

use Photobooth\FileUploader;
use Photobooth\Image;
use Photobooth\Enum\FolderEnum;
use Photobooth\Service\DatabaseManagerService;
use Photobooth\Service\LoggerService;
use Photobooth\Utility\PathUtility;
use Photobooth\Rembg;

header('Content-Type: application/json');

$logger = LoggerService::getInstance()->getLogger('main');
$logger->debug(basename($_SERVER['PHP_SELF']));

$imageHandler = new Image();

$database = DatabaseManagerService::getInstance();

if (isset($_FILES['images'])) {
    $folderName = 'data/tmp';
    $uploadedFiles = $_FILES['images'];

    $uploader = new FileUploader($folderName, $uploadedFiles, $logger);
    $response = $uploader->uploadFiles();
    list($success, $message, $errors, $uploadedFiles, $failedFiles) = [
        $response['success'],
        $response['message'],
        $response['errors'],
        $response['uploadedFiles'],
        $response['failedFiles']
    ];

    try {
        if (count($errors) > 0) {
            throw new \Exception('Failed to upload selfie.');
        }

        $previews = [];

        foreach ($uploadedFiles as $imageName) {
            $tmp = FolderEnum::TEMP->absolute() . DIRECTORY_SEPARATOR . $imageName;
            $imageNewName = Image::createNewFilename($config['picture']['naming']);
            $filename_photo = FolderEnum::IMAGES->absolute() . DIRECTORY_SEPARATOR . $imageNewName;
            $filename_tmp = FolderEnum::TEMP->absolute() . DIRECTORY_SEPARATOR . $imageNewName;
            $filename_thumb = FolderEnum::THUMBS->absolute() . DIRECTORY_SEPARATOR . $imageNewName;
            $imageHandler->imageModified = false;

            if (!file_exists($tmp)) {
                throw new \Exception('Image doesn\'t exist:' . $tmp);
            }

            if (!rename($tmp, $filename_tmp)) {
                throw new \Exception('Failed to rename image!');
            }

            // Apply rembg service (HTTP) during image processing (preferred).
            // We'll call the Rembg::process after we created the image resource and applied EXIF rotation.

            $imageResource = $imageHandler->createFromImage($filename_tmp);
            if (!$imageResource instanceof \GdImage) {
                throw new \Exception('Error creating image resource.');
            }

            $exif = exif_read_data($filename_tmp);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:  //180°
                        $imageResource = imagerotate($imageResource, 180, 0);
                        $imageHandler->imageModified = true;
                        break;
                    case 6:  //-90°
                        $imageResource = imagerotate($imageResource, -90, 0);
                        $imageHandler->imageModified = true;
                        break;
                    case 8:  //+90°
                        $imageResource = imagerotate($imageResource, 90, 0);
                        $imageHandler->imageModified = true;
                        break;
                }
                if (!$imageResource instanceof \GdImage) {
                    throw new \Exception('Error rotating image resource.');
                }
            }
            // Apply rembg service (HTTP) if enabled in config
            if (!empty($config['rembg']['enabled'])) {
                $varsR = [
                    'isCollage' => false,
                    'isChroma' => false,
                    'tmpFile' => $filename_tmp,
                    'fileName' => $imageNewName,
                ];
                try {
                    [$imageHandler, $imageResource] = Rembg::process($imageHandler, $varsR, $config['rembg'], $imageResource);
                    $imageHandler->imageModified = true;
                } catch (\Exception $e) {
                    $logger->error('rembg service failed: ' . $e->getMessage());
                }
            }

            $thumb_size = intval(substr($config['picture']['thumb_size'], 0, -2));
            $thumbResource = $imageHandler->resizeImage($imageResource, $thumb_size);
            if (!$thumbResource instanceof \GdImage) {
                throw new \Exception('Error creating thumb resource.');
            }
            // Burn a simple banner with text into the thumbnail so it is visible across browsers
            try {
                $thumbW = imagesx($thumbResource);
                $thumbH = imagesy($thumbResource);
                // banner height as percentage of thumb height
                $bannerH = max(18, intval($thumbH * 0.12));
                // place banner approx. in the upper third of the thumbnail
                $bannerY = intval($thumbH / 3) - intval($bannerH / 2);
                if ($bannerY < 0) {
                    $bannerY = 0;
                }

                // semi-transparent black rectangle limited to banner height
                $alpha = 60; // 0 (opaque) .. 127 (transparent)
                $rectColor = imagecolorallocatealpha($thumbResource, 0, 0, 0, $alpha);
                $rectBottom = $bannerY + $bannerH;
                if ($rectBottom > $thumbH) {
                    $rectBottom = $thumbH;
                }
                imagefilledrectangle($thumbResource, 0, $bannerY, $thumbW, $rectBottom, $rectColor);

                // text settings
                $text = $config['picture']['thumb_banner_text'];
                $angle = 0;
                // try to find a nice TTF font shipped with the app
                $fontPath = \Photobooth\Utility\FontUtility::getFontPath('GreatVibes-Regular.ttf');
                if ($fontPath && is_readable($fontPath)) {
                    // choose font size relative to width
                    $fontSize = max(12, intval($thumbW * 0.08));
                    $bbox = @imagettfbbox($fontSize, $angle, $fontPath, $text);
                    if ($bbox !== false) {
                        $textW = abs($bbox[2] - $bbox[0]);
                        $textH = abs($bbox[7] - $bbox[1]);
                        $textX = intval(($thumbW - $textW) / 2);
                        // position baseline vertically centered inside the banner
                        $textY = $bannerY + intval(($bannerH + $textH) / 2);
                        $textColor = imagecolorallocate($thumbResource, 255, 255, 255);
                        imagettftext($thumbResource, $fontSize, $angle, $textX, $textY, $textColor, $fontPath, $text);
                    }
                }
            } catch (\Throwable $e) {
                // don't block thumbnail creation on banner errors
                $logger->debug('Thumbnail banner failed: ' . $e->getMessage());
            }

            $imageHandler->jpegQuality = $config['jpeg_quality']['thumb'];
            if (!$imageHandler->saveJpeg($thumbResource, $filename_thumb)) {
                $imageHandler->addErrorData('Warning: Failed to create thumbnail.');
            }
            if ($imageHandler->imageModified || ($config['jpeg_quality']['image'] >= 0 && $config['jpeg_quality']['image'] < 100)) {
                $imageHandler->jpegQuality = $config['jpeg_quality']['image'];
                if (!$imageHandler->saveJpeg($imageResource, $filename_photo)) {
                    throw new \Exception('Failed to create image.');
                }
            } else {
                if (!copy($filename_tmp, $filename_photo)) {
                    throw new \Exception('Failed to copy photo.');
                }
            }
            // Change permissions
            $picture_permissions = $config['picture']['permissions'];
            if (!chmod($filename_photo, (int)octdec($picture_permissions))) {
                $imageHandler->addErrorData('Warning: Failed to change picture permissions.');
            }

            if (!$config['picture']['keep_original']) {
                if (!unlink($filename_tmp)) {
                    $imageHandler->addErrorData('Warning: Failed to remove temporary photo.');
                }
            }
            if ($thumbResource instanceof \GdImage) {
                unset($thumbResource);
            }
            if ($imageResource instanceof \GdImage) {
                unset($imageResource);
            }
            if ($config['database']['enabled']) {
                $database->appendContentToDB($imageNewName);
                try {
                    $database->setOwnerForFile($imageNewName, session_id());
                } catch (\Exception $e) {
                    $logger->debug('Could not set owner for file: ' . $e->getMessage());
                }
            }

            // add public preview URL for client-side display
            try {
                $previews[] = PathUtility::getPublicPath($filename_photo);
            } catch (\Exception $e) {
                $logger->debug('Could not build public preview path: ' . $e->getMessage());
            }
        }
    } catch (\Exception $e) {
        // Handle the exception
        $logger->error($e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        die();
    }
    echo json_encode([
            'success' => true,
            'message' => 'File(s) successfully uploaded and proceeded.',
            'previews' => $previews,
        ]);
    exit();
}
