<?php
// db.php
$DB_HOST = "sql308.yzz.me";
$DB_USER = "	yzzme_40994126";
$DB_PASS = ""; // Set your password if any
$DB_NAME = "yzzme_40994126_cashier_db";

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
