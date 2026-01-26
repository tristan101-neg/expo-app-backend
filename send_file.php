<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

if (!isset($_POST['cashier_id'], $_FILES['file'], $_POST['amount'])) {
    echo json_encode(["success" => false, "message" => "Missing data"]);
    exit;
}

$cashier_id = (int)$_POST['cashier_id'];
$amount = (float)$_POST['amount'];
$file = $_FILES['file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["success" => false, "message" => "File upload error"]);
    exit;
}

$uploadDir = __DIR__ . "/uploads/";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$filename = time() . "_" . basename($file['name']);
$targetPath = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo json_encode(["success" => false, "message" => "Failed to save file"]);
    exit;
}

// Insert batch
$stmt = $conn->prepare("INSERT INTO batches (cashier_id, file_name, total_amount) VALUES (?, ?, ?)");
$stmt->bind_param("isd", $cashier_id, $filename, $amount);
$stmt->execute();
$batch_id = $conn->insert_id;
$stmt->close();

echo json_encode([
    "success" => true,
    "batch_id" => $batch_id,
    "file" => $filename
]);
?>
