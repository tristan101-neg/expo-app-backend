<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "db.php";

if (!isset($_GET["batch_id"])) {
  echo json_encode([]);
  exit;
}

$batch_id = (int)$_GET["batch_id"];

$stmt = $conn->prepare("
  SELECT receiver_name, amount, received 
  FROM receivers 
  WHERE batch_id = ?
  ORDER BY receiver_name ASC
");
$stmt->bind_param("i", $batch_id);
$stmt->execute();

$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
  $data[] = $row;
}

echo json_encode($data);
