<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

$batch_id = intval($_POST['batch_id'] ?? 0);
if ($batch_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid batch ID"]);
    exit;
}

// Get file name to delete
$stmt = $conn->prepare("SELECT file_name FROM batches WHERE id=?");
$stmt->bind_param("i", $batch_id);
$stmt->execute();
$result = $stmt->get_result();
$file = $result->fetch_assoc()['file_name'] ?? null;
$stmt->close();

// Delete batch from DB
$stmt = $conn->prepare("DELETE FROM batches WHERE id=?");
$stmt->bind_param("i", $batch_id);
$success = $stmt->execute();
$stmt->close();

if ($success) {
    if ($file && file_exists(__DIR__ . "/uploads/" . $file)) {
        unlink(__DIR__ . "/uploads/" . $file); // Delete file from server
    }
    echo json_encode(["success" => true, "message" => "Batch deleted"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to delete batch"]);
}
?>
