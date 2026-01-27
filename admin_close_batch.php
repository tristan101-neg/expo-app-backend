<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["batch_id"])) {
  http_response_code(400);
  echo json_encode(["error" => "Missing batch_id"]);
  exit;
}

$batch_id = (int)$data["batch_id"];

$stmt = $conn->prepare("UPDATE batches SET status='closed' WHERE id=?");
$stmt->bind_param("i", $batch_id);
$stmt->execute();

echo json_encode(["success" => true]);
