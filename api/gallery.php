<?php

require_once '../lib/boot.php';

use Photobooth\Service\DatabaseManagerService;

$database = DatabaseManagerService::getInstance();

// Restrict gallery API to localhost only
$remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($remoteAddr, ['127.0.0.1', '::1'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Gallery API is only available from localhost.']);
    exit();
}

// Check if there is a request for the status of the database
if (isset($_GET['status'])) {
    // Request for DB-Status,
    // Currently reports back the DB-Size to give the Client the ability
    // to detect changes
    $resp = ['dbsize' => $database->getDBSize()];
    exit(json_encode($resp));
} else {
    http_response_code(400);
    echo 'Invalid request.';
    exit();
}

// Provide a JSON list of images (respecting session ownership)
if (isset($_GET['list'])) {
    $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
    $isLocal = in_array($remoteAddr, ['127.0.0.1', '::1']);
    if ($config['database']['enabled']) {
        if ($isLocal) {
            $images = $database->getContentFromDB();
        } else {
            $images = $database->getContentFromDBForSession(session_id());
        }
    } else {
        $images = $database->getFilesFromDirectory();
    }
    // respect newest_first setting
    if ($config['gallery']['newest_first'] === true && !empty($images)) {
        $images = array_reverse($images);
    }
    header('Content-Type: application/json');
    echo json_encode(['images' => array_values($images)]);
    exit();
}
