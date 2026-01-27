<?php
$file = $_GET['file'] ?? '';
$filepath = __DIR__ . '/uploads/' . basename($file);

// Check if file exists
if (!file_exists($filepath)) {
    http_response_code(404);
    echo "File not found";
    exit;
}

// Only allow Excel files
$allowedExtensions = ['xls', 'xlsx'];
$ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExtensions)) {
    http_response_code(403);
    echo "Only Excel files can be opened";
    exit;
}

// Serve Excel file inline
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: inline; filename="' . basename($filepath) . '"');
readfile($filepath);
exit;
?>
