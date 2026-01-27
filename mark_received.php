<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["receiver_id"])) {
  http_response_code(400);
  echo json_encode(["error" => "Missing receiver_id"]);
  exit;
}

$receiver_id = (int)$data["receiver_id"];

/* 🔒 CHECK BATCH STATUS */
$check = $conn->prepare("
  SELECT b.status 
  FROM receivers r
  JOIN batches b ON b.id = r.batch_id
  WHERE r.id = ?
");
$check->bind_param("i", $receiver_id);
$check->execute();
$res = $check->get_result()->fetch_assoc();

if (!$res || $res["status"] === "closed") {
  http_response_code(403);
  echo json_encode(["error" => "Batch already closed"]);
  exit;
}

/* ✅ MARK RECEIVED */
$stmt = $conn->prepare("
  UPDATE receivers 
  SET received = 1 
  WHERE id = ?
");
$stmt->bind_param("i", $receiver_id);
$stmt->execute();

echo json_encode(["success" => true]);
