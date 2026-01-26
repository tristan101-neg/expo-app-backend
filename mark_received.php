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

// Update receivers table
$stmt = $conn->prepare("UPDATE receivers SET received=1 WHERE batch_id=?");
$stmt->bind_param("i", $batch_id);
$success = $stmt->execute();
$stmt->close();

if ($success) {
    echo json_encode(["success" => true, "message" => "Marked as received"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to mark as received"]);
}
?>
