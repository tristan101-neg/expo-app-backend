<?php
require "../config/db.php";

$cashier_id = $_POST['cashier_id'];
$total_amount = $_POST['total_amount'];

$file = $_FILES['file'];
$filename = time() . "_" . basename($file['name']);
$path = "../uploads/" . $filename;

if (!move_uploaded_file($file['tmp_name'], $path)) {
    echo json_encode(["error" => "Upload failed"]);
    exit;
}

// insert batch
$conn->query("
    INSERT INTO batches (cashier_id, file_name, total_amount)
    VALUES ($cashier_id, '$filename', $total_amount)
");

$batch_id = $conn->insert_id;

// create receiver
$conn->query("
    INSERT INTO receivers (batch_id, receiver_name, amount)
    VALUES ($batch_id, 'Target Person', $total_amount)
");

// mark cashier busy
$conn->query("UPDATE cashiers SET is_available=0 WHERE id=$cashier_id");

echo json_encode(["success" => true]);
