<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "db.php";

if (!isset($_GET['cashier_id'])) {
    echo json_encode([]);
    exit;
}

$cashier_id = (int) $_GET['cashier_id'];

// Get all batches assigned to this cashier
$sql = "
SELECT 
    b.id AS batch_id,
    b.file_name,
    b.total_amount,
    b.created_at
FROM batches b
WHERE b.cashier_id = ?
ORDER BY b.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cashier_id);
$stmt->execute();
$result = $stmt->get_result();

$baseUrl = "http://192.168.1.94/cashier-system/api/uploads/";

$response = [];
while ($row = $result->fetch_assoc()) {
    // Only Excel files
    $ext = strtolower(pathinfo($row['file_name'], PATHINFO_EXTENSION));
    if ($ext !== "xls" && $ext !== "xlsx") continue;

    $response[] = [
        "batch_id" => (int)$row['batch_id'],
        "file_name" => $row['file_name'],
        "total_amount" => (float)$row['total_amount'],
        "time" => $row['created_at'],
        "file_url" => $baseUrl . $row['file_name']
    ];
}

echo json_encode($response);

$stmt->close();
$conn->close();
?>
