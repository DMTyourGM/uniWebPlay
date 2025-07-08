<?php
// book_slot.php - API endpoint to book a facility slot

header('Content-Type: application/json');
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $facility_id = $_POST['facility_id'] ?? null;
    $slot = $_POST['slot'] ?? null;

    if (!$user_id || !$facility_id || !$slot) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    // Validate facility_id against allowed indoor games facility IDs
    // Example facility IDs for indoor games: pool=1, chess=2, carom=3, table tennis=4, board games=5
    $allowed_facility_ids = [1, 2, 3, 4, 5];
    if (!in_array((int)$facility_id, $allowed_facility_ids, true)) {
        echo json_encode(['success' => false, 'message' => 'Invalid facility for booking. Only indoor games are allowed.']);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert booking
        $stmt = $conn->prepare("INSERT INTO Bookings (user_id, facility_id, slot, status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("iis", $user_id, $facility_id, $slot);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create booking');
        }
        $stmt->close();

        // Award points for booking (gamification)
        $points_awarded = 50; // Base points for booking
        $points_stmt = $conn->prepare("INSERT INTO user_points (user_id, points) VALUES (?, ?) ON DUPLICATE KEY UPDATE points = points + ?");
        $points_stmt->bind_param("iii", $user_id, $points_awarded, $points_awarded);
        $points_stmt->execute();
        $points_stmt->close();

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'success' => true, 
            'message' => 'Booking created successfully! +' . $points_awarded . ' points earned!',
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
