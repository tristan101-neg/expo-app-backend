<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "db.php";

if (!isset($_GET["cashier_id"])) {
  echo json_encode([]);
  exit;
}

$cashier_id = (int)$_GET["cashier_id"];

$sql = "
SELECT
  b.id AS batch_id,
  b.file_name,
  b.total_amount,
  b.status,
  r.id AS receiver_id,
  r.receiver_name,
  r.amount,
  r.received
FROM batches b
LEFT JOIN receivers r ON r.batch_id = b.id
WHERE b.cashier_id = ?
ORDER BY b.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cashier_id);
$stmt->execute();
$result = $stmt->get_result();

$batches = [];

while ($row = $result->fetch_assoc()) {
  $id = $row["batch_id"];

  if (!isset($batches[$id])) {
    $batches[$id] = [
      "batch_id" => $id,
      "file_name" => $row["file_name"],
      "total_amount" => $row["total_amount"],
      "status" => $row["status"],
      "receivers" => []
    ];
  }

  if ($row["receiver_id"]) {
    $batches[$id]["receivers"][] = [
      "id" => $row["receiver_id"],
      "name" => $row["receiver_name"],
      "amount" => $row["amount"],
      "received" => (bool)$row["received"]
    ];
  }
}

echo json_encode(array_values($batches));
