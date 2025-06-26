<?php
// report_issue.php - API endpoint to report maintenance issues

header('Content-Type: application/json');
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $facility_id = $_POST['facility_id'] ?? null;
    $photo_url = $_POST['photo_url'] ?? null;

    if (!$facility_id) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO MaintenanceReports (facility_id, photo_url, status) VALUES (?, ?, 'reported')");
    $stmt->bind_param("is", $facility_id, $photo_url);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Maintenance report created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create maintenance report']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
