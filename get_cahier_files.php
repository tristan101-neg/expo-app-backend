<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once "db.php";

if (!isset($_GET['cashier_id'])) {
    echo json_encode([]);
    exit;
}

$cashier_id = (int)$_GET['cashier_id'];

$sql = "
SELECT 
    b.id AS batch_id,
    b.file_name,
    b.total_amount,
    b.created_at,
    br.received_at
FROM batches b
LEFT JOIN batch_receivers br ON br.batch_id = b.id AND br.cashier_id = ?
ORDER BY b.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cashier_id);
$stmt->execute();
$result = $stmt->get_result();

$baseUrl = "http://192.168.1.94/cashier-system/api/uploads/";
$response = [];

while ($row = $result->fetch_assoc()) {
    $response[] = [
        "batch_id" => (int)$row['batch_id'],
        "file_name" => $row['file_name'],
        "file_url" => $row['file_name'] ? $baseUrl . $row['file_name'] : null,
        "total_amount" => (float)$row['total_amount'],
        "created_at" => $row['created_at'],
        "received_at" => $row['received_at'] // null if not received
    ];
}

echo json_encode($response);

$stmt->close();
$conn->close();
