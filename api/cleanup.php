<?php

require_once '../lib/boot.php';

use Photobooth\Service\DatabaseManagerService;

header('Content-Type: application/json');

$remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
// allow only local execution for safety
if (!in_array($remoteAddr, ['127.0.0.1', '::1'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Cleanup API is only available from localhost.']);
    exit();
}

$config = \Photobooth\Service\ConfigurationService::getInstance()->getConfiguration();
if (empty($config['auto_delete']['enabled'])) {
    echo json_encode(['success' => false, 'message' => 'Auto-delete not enabled in config.']);
    exit();
}

$ttl = intval($config['auto_delete']['ttl_minutes'] ?? 45);
if ($ttl <= 0) {
    $ttl = 45;
}

$database = DatabaseManagerService::getInstance();
$deleted = $database->cleanOldFiles($ttl);

echo json_encode(['success' => true, 'deleted_count' => count($deleted), 'deleted' => $deleted]);
exit();
