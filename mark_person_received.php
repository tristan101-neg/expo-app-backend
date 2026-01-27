<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once "db.php";

$batch_id = $_POST['batch_id'] ?? null;
$cashier_id = $_POST['cashier_id'] ?? null;
$name = $_POST['name'] ?? null;

if (!$batch_id || !$cashier_id || !$name) {
    echo json_encode(["success"=>false,"message"=>"Missing data"]);
    exit;
}

// Update receivers table
$stmt = $conn->prepare("UPDATE receivers SET received = 1 WHERE batch_id = ? AND receiver_name = ?");
$stmt->bind_param("is",$batch_id,$name);
$stmt->execute();
$stmt->close();

echo json_encode(["success"=>true]);
$conn->close();
