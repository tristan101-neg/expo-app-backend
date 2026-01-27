<?php
header("Access-Control-Allow-Origin: *");
require_once "db.php";

$batch_id = isset($_GET['batch_id']) ? (int)$_GET['batch_id'] : null;
$cashier_id = isset($_GET['cashier_id']) ? (int)$_GET['cashier_id'] : null;

if (!$batch_id || !$cashier_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT receiver_name AS name, 0 AS amount, received 
        FROM receivers 
        WHERE batch_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $batch_id);
$stmt->execute();
$result = $stmt->get_result();
$receivers = [];
while ($row = $result->fetch_assoc()) {
    $receivers[] = $row;
}
echo json_encode($receivers);
