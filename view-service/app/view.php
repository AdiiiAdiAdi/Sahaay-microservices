<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'db.php';

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $sql = "SELECT id, title, description, category, location, status, upvotes, image_url, created_at 
            FROM issues 
            ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $issues = [];
        while ($row = $result->fetch_assoc()) {
            $issues[] = $row;
        }

        echo json_encode(['success' => true, 'count' => count($issues), 'issues' => $issues]);
    } else {
        echo json_encode(['success' => true, 'count' => 0, 'issues' => []]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
