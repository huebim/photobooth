<?php

namespace Photobooth\Service;

use Photobooth\Enum\FolderEnum;

/**
 * Class DatabaseManager
 *
 * Manages the database, including adding and deleting files.
 */
class DatabaseManagerService
{
    public string $databaseFile = '';
    public string $imageDirectory = '';
    private string $ownersFile = '';

    public function __construct()
    {
        $config = ConfigurationService::getInstance()->getConfiguration();
        $this->databaseFile = FolderEnum::DATA->absolute() . DIRECTORY_SEPARATOR . $config['database']['file'] . '.txt';
        $this->imageDirectory = FolderEnum::IMAGES->absolute();
        $this->ownersFile = FolderEnum::DATA->absolute() . DIRECTORY_SEPARATOR . 'db_owners.json';
    }

    /**
     * Get the list of files from the database file.
     */
    public function getContentFromDB(): array
    {
        // check if the database file is defined and non-empty
        if (!isset($this->databaseFile) || empty($this->databaseFile)) {
            throw new \Exception('Database not defined.');
        }

        try {
            // get data from database
            if (file_exists($this->databaseFile)) {
                $data = file_get_contents($this->databaseFile);
                if ($data === false) {
                    throw new \Exception('Failed to read file: ' . $this->databaseFile);
                }
                $decodedData = json_decode($data, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Failed to decode JSON: ' . json_last_error_msg());
                }

                return is_array($decodedData) ? $decodedData : [];
            } else {
                throw new \Exception('File not found: ' . $this->databaseFile);
            }
        } catch (\Exception $e) {
            // do nothing
        }

        return [];
    }

    /**
     * Returns database content filtered by session owner if provided.
     * If $sessionId is null, returns the full list.
     */
    public function getContentFromDBForSession(?string $sessionId): array
    {
        $all = $this->getContentFromDB();
        if ($sessionId === null || $sessionId === '') {
            return $all;
        }
        $owners = $this->getOwnersMap();
        $filtered = [];
        foreach ($all as $file) {
            if (isset($owners[$file]) && $owners[$file] === $sessionId) {
                $filtered[] = $file;
            }
        }
        return $filtered;
    }

    /**
     * Set the owner (session id) for a filename.
     */
    public function setOwnerForFile(string $filename, string $sessionId): void
    {
        if (!$filename) {
            throw new \Exception('Invalid filename.');
        }
        $owners = $this->getOwnersMap();
        $owners[$filename] = $sessionId;
        $encoded = json_encode($owners);
        if ($encoded === false) {
            throw new \Exception('Failed to encode owners map: ' . json_last_error_msg());
        }
        if (file_put_contents($this->ownersFile, $encoded) === false) {
            throw new \Exception('Failed to write owners file: ' . $this->ownersFile);
        }
    }

    /**
     * Read owners map from disk.
     */
    public function getOwnersMap(): array
    {
        if (!file_exists($this->ownersFile)) {
            return [];
        }
        $data = file_get_contents($this->ownersFile);
        if ($data === false || $data === '') {
            return [];
        }
        $decoded = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Get the list of images from the images directory.
     */
    public function getFilesFromDirectory(): array
    {
        // check if the directory is defined and non-empty
        if (!isset($this->imageDirectory) || empty($this->imageDirectory)) {
            throw new \Exception('Directory not defined.');
        }

        try {
            // open the directory
            $dh = opendir($this->imageDirectory);
            if ($dh === false) {
                throw new \Exception('Failed to open directory: ' . $this->imageDirectory);
            }

            // read the files in the directory
            $files = [];
            while (false !== ($filename = readdir($dh))) {
                $files[] = $filename;
            }
            closedir($dh);

            // filter the files to include only images with .jpg or .jpeg extensions
            $images = preg_grep('/\.(jpg|jpeg)$/i', $files);
            if ($images === false) {
                return [];
            }

            return $images;
        } catch (\Exception $e) {
            // do nothing
        }

        return [];
    }

    /**
     * Append a new content by name to the database file.
     */
    public function appendContentToDB(string $content): void
    {
        if (!$content) {
            throw new \Exception('Invalid content.');
        }

        // check if the database file is defined and non-empty
        if (!isset($this->databaseFile) || empty($this->databaseFile)) {
            throw new \Exception('Database not defined.');
        }

        $currContent = $this->getContentFromDB();

        if (!in_array($content, $currContent)) {
            $currContent[] = $content;
            $encoded = json_encode($currContent);
            if ($encoded === false) {
                throw new \Exception('Failed to encode database content to JSON: ' . json_last_error_msg());
            }
            if (file_put_contents($this->databaseFile, $encoded) === false) {
                throw new \Exception('Failed to write database file: ' . $this->databaseFile);
            }
        }
    }

    /**
     * Delete an entry by name from the database file.
     */
    public function deleteContentFromDB(string $content): void
    {
        if (!$content) {
            throw new \Exception('Invalid filename.');
        }

        // check if the database file is defined and non-empty
        if (!isset($this->databaseFile) || empty($this->databaseFile)) {
            throw new \Exception('Database not defined.');
        }
        $currContent = $this->getContentFromDB();

        if (in_array($content, $currContent)) {
            unset($currContent[array_search($content, $currContent)]);
            $encoded = json_encode(array_values($currContent));
            if ($encoded === false) {
                throw new \Exception('Failed to encode database content to JSON: ' . json_last_error_msg());
            }
            if (file_put_contents($this->databaseFile, $encoded) === false) {
                throw new \Exception('Failed to write database file: ' . $this->databaseFile);
            }
        }

        if (file_exists($this->databaseFile) && empty($currContent)) {
            unlink($this->databaseFile);
        }
    }

    /**
     * Check if an content exists in the database file.
     */
    public function isInDB(string $content): bool
    {
        if (!$content) {
            throw new \Exception('Invalid filename.');
        }

        // check if the database file is defined and non-empty
        if (!isset($this->databaseFile) || empty($this->databaseFile)) {
            throw new \Exception('Database not defined.');
        }

        $currContent = $this->getContentFromDB();

        return in_array($content, $currContent);
    }

    /**
     * Returns the size of the database file in bytes.
     */
    public function getDBSize(): int
    {
        if (file_exists($this->databaseFile)) {
            return (int) filesize($this->databaseFile);
        }
        return 0;
    }

    /**
     * Rebuilds the image database by scanning the image directory and creating a new database
     * file with the names of all files sorted by modification time.
     *
     * @return string The string "success" if the database was rebuilt successfully, or "error"
     *                if an error occurred during the rebuilding process.
     */
    public function rebuildDB(): string
    {
        // check if the database file is defined and non-empty
        if (!isset($this->databaseFile) || empty($this->databaseFile)) {
            throw new \Exception('Database not defined.');
        }

        // check if the file directory is defined and non-empty
        if (!isset($this->imageDirectory) || empty($this->imageDirectory)) {
            throw new \Exception('File directory not defined.');
        }

        $output = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->imageDirectory, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS)) as $value) {
            if ($value->isFile() && strtolower(pathinfo($value->getFilename(), PATHINFO_EXTENSION)) === 'jpg') {
                $output[] = [$value->getMTime(), $value->getFilename()];
            }
        }

        if (!empty($output)) {
            usort($output, function ($a, $b) {
                return $a[0] <=> $b[0];
            });
        }

        try {
            $filenames = array_column($output, 1);
            $jsonData = json_encode($filenames);
            if ($jsonData === false) {
                throw new \Exception('Error: Failed to encode filenames to JSON.');
            }

            if (file_put_contents($this->databaseFile, $jsonData) === false) {
                throw new \Exception('Error: Failed to write data to database.');
            }

            return 'success';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    /**
     * Delete files older than given TTL (in minutes) from images and thumbs and remove DB entries.
     * Returns array with deleted filenames.
     *
     * @param int $ttlMinutes
     * @return array
     */
    public function cleanOldFiles(int $ttlMinutes): array
    {
        $deleted = [];
        if ($ttlMinutes <= 0) {
            return $deleted;
        }

        $now = time();
        $threshold = $now - ($ttlMinutes * 60);

        $files = $this->getContentFromDB();
        // get excluded files from config (do not delete these)
        $config = \Photobooth\Service\ConfigurationService::getInstance()->getConfiguration();
        $excluded = [];
        if (isset($config['auto_delete']['excluded_files']) && is_array($config['auto_delete']['excluded_files'])) {
            $excluded = $config['auto_delete']['excluded_files'];
        }
        foreach ($files as $file) {
            try {
                // skip excluded files
                if (in_array($file, $excluded, true)) {
                    continue;
                }
                $imagePath = $this->imageDirectory . DIRECTORY_SEPARATOR . $file;
                $thumbPath = FolderEnum::THUMBS->absolute() . DIRECTORY_SEPARATOR . $file;

                $mtime = null;
                if (file_exists($imagePath)) {
                    $mtime = filemtime($imagePath);
                } elseif (file_exists($thumbPath)) {
                    $mtime = filemtime($thumbPath);
                }

                if ($mtime !== null && $mtime <= $threshold) {
                    // attempt to delete image
                    if (file_exists($imagePath) && is_writable($imagePath)) {
                        @unlink($imagePath);
                    }
                    // attempt to delete thumb
                    if (file_exists($thumbPath) && is_writable($thumbPath)) {
                        @unlink($thumbPath);
                    }
                    // remove from DB and owners
                    try {
                        $this->deleteContentFromDB($file);
                    } catch (\Exception $e) {
                        // ignore
                    }
                    // remove owner mapping
                    $owners = $this->getOwnersMap();
                    if (isset($owners[$file])) {
                        unset($owners[$file]);
                        @file_put_contents($this->ownersFile, json_encode($owners));
                    }
                    $deleted[] = $file;
                }
            } catch (\Exception $e) {
                // ignore individual errors and continue
            }
        }

        return $deleted;
    }

    public static function getInstance(): self
    {
        if (!isset($GLOBALS[self::class])) {
            $GLOBALS[self::class] = new self();
        }

        return $GLOBALS[self::class];
    }
}
