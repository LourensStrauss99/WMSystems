<?php
echo json_encode([
    'host' => $_SERVER['HTTP_HOST'] ?? 'not set',
    'server_name' => $_SERVER['SERVER_NAME'] ?? 'not set',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'not set',
    'all_server' => $_SERVER
]);
?>
