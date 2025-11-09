<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'db.php';

// Handle CORS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Read JSON input
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$user_type = $data['userType'] ?? 'user';

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Email and password required.']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT id, name, email, password, phone, user_type FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Optional: restrict admin/user login type if desired
            if ($user['user_type'] !== $user_type && $user_type !== '') {
                echo json_encode(['success' => false, 'message' => 'Invalid user type.']);
                exit;
            }

            unset($user['password']); // hide password hash
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

$conn->close();
?>
