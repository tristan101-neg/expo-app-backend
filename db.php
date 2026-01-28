<?php
// db.php
$DB_HOST = "sql200.infinityfree.com";
$DB_USER = "if0_41001723";
$DB_PASS = "yifUlIGPu7"; // Set your password if any
$DB_NAME = "if0_41001723_cashier_db";

// Create connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        "success" => false,
        "message" => "Database connection failed: " . $conn->connect_error
    ]));
}

$conn->set_charset("utf8mb4");
?>
