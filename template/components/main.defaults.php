<?php

use Photobooth\Service\DatabaseManagerService;

if (isset($photoswipe) && $photoswipe) {
    $database = DatabaseManagerService::getInstance();
    if ($config['database']['enabled']) {
        // If visitor is on localhost show all images, otherwise show only images owned by this session
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
        $isLocal = in_array($remoteAddr, ['127.0.0.1', '::1']);
        if ($isLocal) {
            $images = $database->getContentFromDB();
        } else {
            $images = $database->getContentFromDBForSession(session_id());
        }
    } else {
        $images = $database->getFilesFromDirectory();
    }
    $imagelist = $config['gallery']['newest_first'] === true && !empty($images) ? array_reverse($images) : $images;
    if (isset($randomImage) && $randomImage && !empty($imagelist)) {
        shuffle($imagelist);
    }
}
