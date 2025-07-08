<?php
// report_issue.php - API endpoint to report maintenance issues

header('Content-Type: application/json');
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $facility_id = $_POST['facility_id'] ?? null;
    $issue_description = $_POST['issue'] ?? null;
    $photo_url = $_POST['photo_url'] ?? null;

    if (!$user_id || !$facility_id || !$issue_description) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert maintenance report
        $stmt = $conn->prepare("INSERT INTO MaintenanceReports (user_id, facility_id, issue_description, photo_url, status) VALUES (?, ?, ?, ?, 'reported')");
        $stmt->bind_param("iiss", $user_id, $facility_id, $issue_description, $photo_url);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create maintenance report');
        }
        $stmt->close();

        // Award points for reporting issue (gamification)
        $points_awarded = 25; // Points for helping maintain facilities
        $points_stmt = $conn->prepare("INSERT INTO user_points (user_id, points) VALUES (?, ?) ON DUPLICATE KEY UPDATE points = points + ?");
        $points_stmt->bind_param("iii", $user_id, $points_awarded, $points_awarded);
        $points_stmt->execute();
        $points_stmt->close();

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'success' => true, 
            'message' => 'Issue reported successfully! +' . $points_awarded . ' points for helping maintain facilities!',
            'points_awarded' => $points_awarded
        ]);
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
