<?php
include "db.php";
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);

$username = trim($input['username'] ?? '');
$password = trim($input['password'] ?? '');

if ($username === '' || $password === '') {
    echo json_encode(["success" => false, "message" => "Username and password required"]);
    exit;
}

$stmt = $conn->prepare(
    "SELECT id, role, password FROM users WHERE username=? AND is_available=1"
);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    exit;
}

if (!password_verify($password, $row['password'])) {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    exit;
}

// ✅ Generate secure token
$token = bin2hex(random_bytes(32));

// ✅ Store token
$update = $conn->prepare("UPDATE users SET api_token=? WHERE id=?");
$update->bind_param("si", $token, $row['id']);
$update->execute();

echo json_encode([
    "success" => true,
    "user_id" => $row['id'],
    "role"    => $row['role'],
    "token"   => $token
]);

$stmt->close();
$conn->close();
