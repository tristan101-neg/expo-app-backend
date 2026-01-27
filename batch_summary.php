<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require "db.php";

$batch_id = (int)$_GET['batch_id'];

$sql = "
SELECT
  COUNT(*) AS total,
  SUM(received = 1) AS received_count,
  SUM(received = 0) AS pending
FROM receivers
WHERE batch_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $batch_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

echo json_encode($data);
