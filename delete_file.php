<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

include "db.php";

$batch_id = intval($_POST['batch_id'] ?? 0);
if ($batch_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid batch ID"]);
    exit;
}

// 1️⃣ Get file name from DB
$stmt = $conn->prepare("SELECT file_name FROM batches WHERE id = ?");
$stmt->bind_param("i", $batch_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $file_name = $row['file_name'];
} else {
    echo json_encode(["success" => false, "message" => "Batch not found"]);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// 2️⃣ Delete file from uploads folder
$file_path = __DIR__ . "/uploads/" . $file_name;
if (file_exists($file_path)) unlink($file_path);

// 3️⃣ Delete record from batches table
$delete = $conn->prepare("DELETE FROM batches WHERE id = ?");
$delete->bind_param("i", $batch_id);

if ($delete->execute()) {
    echo json_encode(["success" => true, "message" => "File and batch deleted"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to delete batch"]);
}

$delete->close();
$conn->close();
