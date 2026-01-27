<?php
// get_file.php
header("Access-Control-Allow-Origin: *"); // Allow all origins
header("Content-Type: application/octet-stream");

if (!isset($_GET['file'])) {
    http_response_code(400);
    echo "Missing file parameter";
    exit;
}

$filename = basename($_GET['file']); // sanitize input
$filepath = __DIR__ . "/uploads/" . $filename;

if (!file_exists($filepath)) {
    http_response_code(404);
    echo "File not found";
    exit;
}

readfile($filepath);
