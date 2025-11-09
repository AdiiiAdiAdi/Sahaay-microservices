<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'db.php';

// Handle preflight requests (for CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$category = $_POST['category'] ?? '';
$location = $_POST['location'] ?? '';
$user_id = $_POST['userId'] ?? null;
$image_url = null;

// Validate
if (!$title || !$description || !$category || !$location) {
    echo json_encode(['success' => false, 'message' => 'All fields (except image) are required.']);
    exit;
}

// Handle optional image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/uploads/'; // use folder inside /app;
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $filename = time() . '_' . basename($_FILES['image']['name']);
    $target = $upload_dir . $filename;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        // Assuming public access via container port
	$image_url = "http://43.204.140.219:8002/uploads/" . $filename;    
}
}

try {
    $stmt = $conn->prepare("INSERT INTO issues (title, description, category, location, user_id, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $title, $description, $category, $location, $user_id, $image_url);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Issue submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database insert failed: ' . $stmt->error]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

$conn->close();
?>
