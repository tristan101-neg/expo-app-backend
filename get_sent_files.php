<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include "db.php";

$sql = "
SELECT b.id AS batch_id, b.file_name, b.total_amount, b.cashier_id, c.username AS cashier_name, b.created_at
FROM batches b
LEFT JOIN cashiers c ON b.cashier_id = c.id
ORDER BY b.created_at DESC
";

$result = $conn->query($sql);
$files = [];

while ($row = $result->fetch_assoc()) {
    $files[] = [
        "batch_id" => (int)$row['batch_id'],
        "file_name" => $row['file_name'],
        "amount" => (float)$row['total_amount'],
        "cashierId" => $row['cashier_id'],
        "cashierName" => $row['cashier_name'],
        "time" => $row['created_at'],
        "file_url" => "http://192.168.1.94/cashier-system/api/uploads/" . $row['file_name']
    ];
}

echo json_encode($files);
$conn->close();
?>
