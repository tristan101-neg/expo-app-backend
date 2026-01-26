<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include "db.php";

$result = $conn->query("SELECT id, username, is_available FROM cashiers");
$cashiers = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    "success" => true,
    "cashiers" => $cashiers
]);
?>
