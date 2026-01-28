<?php
// db.php

$DB_HOST = "localhost";   // ✅ ALWAYS localhost
$DB_USER = "if0_41001723";
$DB_PASS = "yifUlIGPu7";
$DB_NAME = "if0_41001723_cashier_db";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

$conn->set_charset("utf8mb4");
