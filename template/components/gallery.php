<?php

ob_start();

use Photobooth\Service\LanguageService;
use Photobooth\Utility\ComponentUtility;
use Photobooth\Utility\PathUtility;

$languageService = LanguageService::getInstance();

// If not requested from localhost, deny access to gallery output
$remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($remoteAddr, ['127.0.0.1', '::1'])) {
    http_response_code(403);
    echo '<div class="forbidden">' . $languageService->translate('gallery_forbidden') . '</div>';
    ob_end_flush();
    return;
}

?>
<div id="gallery" class="gallery rotarygroup">
    <div class="gallery-header">
        <div class="gallery-title"><h1><?= $languageService->translate('gallery') ?></h1></div>
        <div class="gallery-actions">
            <?= ComponentUtility::renderButton('close', $config['icons']['close'], 'gallery__close') ?>
            <?= ComponentUtility::renderButton('reload', $config['icons']['refresh'], 'gallery__refresh', true, ['class' => 'hidden']) ?>
        </div>
    </div>
    <div class="gallery-body">
        <?php include PathUtility::getAbsolutePath('template/components/gallery.images.php'); ?>
    </div>
</div>
<?php ob_end_flush(); ?>
