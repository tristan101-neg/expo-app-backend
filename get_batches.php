<?php
require "../config/db.php";

$cashier_id = $_GET['cashier_id'];

$result = $conn->query("
    SELECT b.id, b.file_name, b.total_amount, r.received
    FROM batches b
    JOIN receivers r ON r.batch_id=b.id
    WHERE b.cashier_id=$cashier_id
");

$data = [];
while($row=$result->fetch_assoc()){
    $data[]=$row;
}

echo json_encode($data);
