<?php
// award_points.php - API endpoint to award points to a user

header('Content-Type: application/json');
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $points_awarded = $_POST['points'] ?? null;

    if (!$user_id || !$points_awarded) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    // Check if user_points record exists
    $check_stmt = $conn->prepare("SELECT points FROM user_points WHERE user_id = ?");
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Update existing points
        $update_stmt = $conn->prepare("UPDATE user_points SET points = points + ?, last_updated = NOW() WHERE user_id = ?");
        $update_stmt->bind_param("ii", $points_awarded, $user_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        // Insert new points record
        $insert_stmt = $conn->prepare("INSERT INTO user_points (user_id, points, last_updated) VALUES (?, ?, NOW())");
        $insert_stmt->bind_param("ii", $user_id, $points_awarded);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
    $check_stmt->close();

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Points awarded successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
