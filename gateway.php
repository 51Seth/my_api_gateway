<?php
require_once 'config.php';

// Get request path
$path = $_GET['request_path'] ?? '';
$headers = getallheaders();
$apiKey = $headers['X-API-Key'] ?? $_SERVER['HTTP_X_API_KEY'] ?? null;

// API Key check
if (!isset($valid_api_keys[$apiKey])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid or missing API Key']);
    log_request($apiKey, $path, 401);
    exit;
}

// Rate limiting
$rateFile = "ratelimit_data/{$apiKey}.json";
$now = time();
$rateData = ['timestamp' => $now, 'count' => 0];

if (file_exists($rateFile)) {
    $rateData = json_decode(file_get_contents($rateFile), true);
    if ($now - $rateData['timestamp'] > 60) {
        $rateData = ['timestamp' => $now, 'count' => 1];
    } elseif ($rateData['count'] >= 10) {
        http_response_code(429);
        echo json_encode(['error' => 'Rate limit exceeded']);
        log_request($apiKey, $path, 429);
        exit;
    } else {
        $rateData['count']++;
    }
} else {
    $rateData['count'] = 1;
}
file_put_contents($rateFile, json_encode($rateData));

// Route path
$responseCode = 200;
switch ($path) {
    case 'users':
        include 'services/service_users.php';
        break;
    case 'products':
        include 'services/service_products.php';
        break;
    default:
        $responseCode = 404;
        http_response_code($responseCode);
        echo json_encode(['error' => 'Not Found']);
}

log_request($apiKey, $path, http_response_code());

// Logging
function log_request($apiKey, $path, $status) {
    $log = sprintf("[%s] - IP: %s - API Key: %s - Path: %s - Status: %d\n",
        date('Y-m-d H:i:s'),
        $_SERVER['REMOTE_ADDR'],
        $apiKey ?: 'None',
        $path,
        $status
    );
    file_put_contents('logs/gateway.log', $log, FILE_APPEND);
}
