<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$batch_id = isset($data['batch_id']) ? (int)$data['batch_id'] : 0;
$cashier_id = isset($data['cashier_id']) ? (int)$data['cashier_id'] : 0;
$receivers = isset($data['receivers']) ? $data['receivers'] : [];

if (!$batch_id || !$cashier_id || !is_array($receivers)) {
    echo json_encode(["success"=>false,"message"=>"Invalid input", "received" => $data]);
    exit;
}

try {
    foreach ($receivers as $r) {
        $name = trim($r['name']);
        $amount = floatval($r['amount']);
        if ($name === '') continue;

        // Check if receiver exists
        $stmt = $conn->prepare("SELECT id FROM receivers WHERE batch_id=? AND receiver_name=?");
        $stmt->bind_param("is", $batch_id, $name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            // Insert into receivers
            $insert = $conn->prepare("INSERT INTO receivers (batch_id, receiver_name, received) VALUES (?, ?, 0)");
            $insert->bind_param("is", $batch_id, $name);
            $insert->execute();
            $insert->close();

            // Insert into batch_receivers to store amount
            $insertAmount = $conn->prepare("INSERT INTO batch_receivers (batch_id, cashier_id, received_at) VALUES (?, ?, NULL)");
            $insertAmount->bind_param("ii", $batch_id, $cashier_id);
            $insertAmount->execute();
            $insertAmount->close();
        }
        $stmt->close();
    }

    echo json_encode(["success"=>true]);
} catch (Exception $e) {
    echo json_encode(["success"=>false, "message"=>$e->getMessage()]);
}
