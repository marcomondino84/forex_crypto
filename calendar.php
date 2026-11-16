<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$now = time();
$defaultFrom = gmdate('Y-m-d\TH:i:s.000\Z', $now - 7 * 86400);
$defaultTo   = gmdate('Y-m-d\TH:i:s.000\Z', $now + 10 * 86400);

$from = isset($_GET['from']) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d{3})?Z$/', $_GET['from'])
    ? $_GET['from']
    : $defaultFrom;
$to = isset($_GET['to']) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d{3})?Z$/', $_GET['to'])
    ? $_GET['to']
    : $defaultTo;

$countries = 'US,EU,GB,JP,CN,DE,CA,AU,NZ,CH';
$url = 'https://economic-calendar.tradingview.com/events?from=' . urlencode($from) . '&to=' . urlencode($to) . '&countries=' . urlencode($countries);

$response = false;
$httpCode = 0;

if (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => [
            'Origin: https://www.tradingview.com',
            'Referer: https://www.tradingview.com/',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
}

if ($response === false || $httpCode >= 400) {
    $opts = [
        'http' => [
            'method' => 'GET',
            'timeout' => 10,
            'header' => "Origin: https://www.tradingview.com\r\nReferer: https://www.tradingview.com/\r\nUser-Agent: Mozilla/5.0\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);
}

if ($response === false) {
    http_response_code(502);
    echo json_encode(['status' => 'error', 'result' => []]);
    exit;
}

echo $response;
