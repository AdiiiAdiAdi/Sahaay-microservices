<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'db.php';

// Handle preflight (CORS) requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$phone = $data['phone'] ?? '';
$user_type = $data['userType'] ?? 'user'; // default to 'user'

if (!$name || !$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'All fields required.']);
    exit;
}

$hashed = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, user_type) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $hashed, $phone, $user_type);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Registration successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $stmt->error]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

$conn->close();
?>
