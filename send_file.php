<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (!isset($_POST['cashier_id'], $_POST['amount'], $_FILES['file'])) {
    echo json_encode(["success" => false, "message" => "Missing data"]);
    exit;
}

$cashier_id = (int)$_POST['cashier_id'];
$amount = (float)$_POST['amount'];
$file = $_FILES['file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["success" => false, "message" => "Upload error"]);
    exit;
}

/* Upload */
$uploadDir = __DIR__ . "/uploads/";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$filename = time() . "_" . basename($file['name']);
$targetPath = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo json_encode(["success" => false, "message" => "File save failed"]);
    exit;
}

/* ✅ ALWAYS CREATE NEW BATCH */
$insert = $conn->prepare("
    INSERT INTO batches (cashier_id, file_name, total_amount, created_at)
    VALUES (?, ?, ?, NOW())
");
$insert->bind_param("isd", $cashier_id, $filename, $amount);
$insert->execute();
$batch_id = $conn->insert_id;
$insert->close();

/* ✅ LINK CASHIER */
$link = $conn->prepare("
    INSERT INTO batch_receivers (batch_id, cashier_id)
    VALUES (?, ?)
");
$link->bind_param("ii", $batch_id, $cashier_id);
$link->execute();
$link->close();

$conn->close();

echo json_encode([
    "success" => true,
    "batch_id" => $batch_id
]);
