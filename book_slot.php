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

    $stmt = $conn->prepare("INSERT INTO Bookings (user_id, facility_id, slot, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("iis", $user_id, $facility_id, $slot);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Booking created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create booking']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
