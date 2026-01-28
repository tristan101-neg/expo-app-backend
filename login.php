<?php
// ================== CORS (MUST BE FIRST) ==================
header("Access-Control-Allow-Origin: *"); // or http://localhost:8081
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ================== INCLUDE DB AFTER HEADERS ==================
require "db.php";

// ================== ONLY POST ALLOWED ==================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
    exit;
}

// ================== READ JSON BODY ==================
$input = json_decode(file_get_contents("php://input"), true);

$username = trim($input['username'] ?? '');
$password = trim($input['password'] ?? '');

if ($username === '' || $password === '') {
    echo json_encode([
        "success" => false,
        "message" => "Username and password required"
    ]);
    exit;
}

// ================== QUERY USER ==================
$stmt = $conn->prepare(
    "SELECT id, role, password FROM users WHERE username = ? LIMIT 1"
);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid credentials"
    ]);
    exit;
}

// ================== VERIFY PASSWORD ==================
if (!password_verify($password, $row['password'])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid credentials"
    ]);
    exit;
}

// ================== GENERATE TOKEN ==================
$token = bin2hex(random_bytes(32));

// ================== SAVE TOKEN ==================
$update = $conn->prepare("UPDATE users SET api_token = ? WHERE id = ?");
$update->bind_param("si", $token, $row['id']);
$update->execute();

// ================== RESPONSE ==================
echo json_encode([
    "success" => true,
    "user_id" => $row['id'],
    "role"    => $row['role'],
    "token"   => $token
]);

$stmt->close();
$conn->close();
