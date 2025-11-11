<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
include 'db.php';

// Handle preflight requests (for CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Input data
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$category = $_POST['category'] ?? '';
$location = $_POST['location'] ?? '';
$user_id = $_POST['userId'] ?? null;

// AWS S3 configuration
$bucketName = 'sahaay-reports';
$region = 'ap-south-1';

// Replace these with your actual AWS credentials
$s3 = new S3Client([
    'version' => 'latest',
    'region' => $region,
    'credentials' => [
        'key'    => 'YOUR_AWS_ACCESS_KEY',
        'secret' => 'YOUR_AWS_SECRET_KEY'
    ]
]);

// Validation
if (!$title || !$description || !$category || !$location) {
    echo json_encode(['success' => false, 'message' => 'All fields (except image) are required.']);
    exit;
}

$image_url = null;
$status = 'pending_analysis'; // Default until Lambda processes

// Handle optional image upload to S3
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileTemp = $_FILES['image']['tmp_name'];
    $fileName = time() . '_' . basename($_FILES['image']['name']);

    try {
        $result = $s3->putObject([
            'Bucket' => $bucketName,
            'Key' => $fileName,
            'SourceFile' => $fileTemp,
            'ACL' => 'public-read'
        ]);
        $image_url = $result['ObjectURL'];
    } catch (AwsException $e) {
        echo json_encode(['success' => false, 'message' => 'S3 Upload failed: ' . $e->getMessage()]);
        exit;
    }
}

try {
    $stmt = $conn->prepare("INSERT INTO issues (title, description, category, location, user_id, image_url, status, severity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $severity = 0.0; // Default before analysis
    $stmt->bind_param("ssssissd", $title, $description, $category, $location, $user_id, $image_url, $status, $severity);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Issue submitted successfully and pending analysis',
            'image_url' => $image_url,
            'status' => $status
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database insert failed: ' . $stmt->error]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

$conn->close();
?>
