<?php
// enhanced_book_slot.php - Enhanced booking system with real-time availability

header('Content-Type: application/json');
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $facility_id = $_POST['facility_id'] ?? null;
    $booking_date = $_POST['booking_date'] ?? null;
    $start_time = $_POST['start_time'] ?? null;
    $duration_minutes = $_POST['duration_minutes'] ?? 60;
    $special_requests = $_POST['special_requests'] ?? '';
    
    // Validate required parameters
    if (!$user_id || !$facility_id || !$booking_date || !$start_time) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Validate user exists
        $user_stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        if ($user_stmt->get_result()->num_rows === 0) {
            throw new Exception('Invalid user ID');
        }
        $user_stmt->close();
        
        // Validate facility exists and is available
        $facility_stmt = $conn->prepare("SELECT * FROM facilities WHERE id = ? AND is_available = TRUE");
        $facility_stmt->bind_param("i", $facility_id);
        $facility_stmt->execute();
        $facility_result = $facility_stmt->get_result();
        if ($facility_result->num_rows === 0) {
            throw new Exception('Facility not found or unavailable');
        }
        $facility = $facility_result->fetch_assoc();
        $facility_stmt->close();
        
        // Validate facility schedule
        $day_of_week = date('N', strtotime($booking_date));
        $schedule_stmt = $conn->prepare("
            SELECT * FROM facility_schedules 
            WHERE facility_id = ? AND day_of_week = ? AND is_closed = FALSE
        ");
        $schedule_stmt->bind_param("ii", $facility_id, $day_of_week);
        $schedule_stmt->execute();
        if ($schedule_stmt->get_result()->num_rows === 0) {
            throw new Exception('Facility is closed on this day');
        }
        $schedule_stmt->close();
        
        // Calculate end time
        $end_time = date('H:i:s', strtotime($start_time . ' + ' . $duration_minutes . ' minutes'));
        
        // Check for booking conflicts
        $conflict_stmt = $conn->prepare("
            SELECT COUNT(*) as conflicts 
            FROM facility_bookings 
            WHERE facility_id = ? 
            AND booking_date = ? 
            AND status IN ('confirmed', 'pending')
            AND (
                (start_time <= ? AND end_time > ?) OR
                (start_time < ? AND end_time >= ?) OR
                (start_time >= ? AND end_time <= ?)
            )
        ");
        
        $conflict_stmt->bind_param("isssssss", $facility_id, $booking_date, $start_time, $start_time, $start_time, $end_time, $start_time, $end_time);
        $conflict_stmt->execute();
        $conflicts = $conflict_stmt->get_result()->fetch_assoc()['conflicts'];
        $conflict_stmt->close();
        
        if ($conflicts > 0) {
            throw new Exception('Time slot is already booked');
        }
        
        // Calculate total amount
        $total_amount = ($facility['hourly_rate'] * $duration_minutes) / 60;
        
        // Create booking
        $booking_stmt = $conn->prepare("
            INSERT INTO facility_bookings 
            (user_id, facility_id, booking_date, start_time, end_time, duration_minutes, total_amount, special_requests) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $booking_stmt->bind_param("iissssds", $user_id, $facility_id, $booking_date, $start_time, $end_time, $duration_minutes, $total_amount, $special_requests);
        
        if (!$booking_stmt->execute()) {
            throw new Exception('Failed to create booking');
        }
        
        $booking_id = $conn->insert_id;
        $booking_stmt->close();
        
        // Award points for booking (gamification)
        $points_awarded = 50; // Base points for booking
        $points_stmt = $conn->prepare("
            INSERT INTO user_points (user_id, points) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE points = points + ?
        ");
        $points_stmt->bind_param("iii", $user_id, $points_awarded, $points_awarded);
        $points_stmt->execute();
        $points_stmt->close();
        
        // Check for booking achievement
        $booking_count_stmt = $conn->prepare("SELECT COUNT(*) as total_bookings FROM facility_bookings WHERE user_id = ? AND status = 'confirmed'");
        $booking_count_stmt->bind_param("i", $user_id);
        $booking_count_stmt->execute();
        $total_bookings = $booking_count_stmt->get_result()->fetch_assoc()['total_bookings'];
        $booking_count_stmt->close();
        
        // Award "First Booking" achievement
        if ($total_bookings == 1) {
            $achievement_stmt = $conn->prepare("
                INSERT INTO user_achievements (user_id, achievement_id) 
                SELECT ?, id FROM achievements WHERE name = 'First Booking'
            ");
            $achievement_stmt->bind_param("i", $user_id);
            $achievement_stmt->execute();
            $achievement_stmt->close();
        }
        
        // Award "Regular User" achievement (5 bookings)
        if ($total_bookings == 5) {
            $achievement_stmt = $conn->prepare("
                INSERT INTO user_achievements (user_id, achievement_id) 
                SELECT ?, id FROM achievements WHERE name = 'Regular User'
            ");
            $achievement_stmt->bind_param("i", $user_id);
            $achievement_stmt->execute();
            $achievement_stmt->close();
        }
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Booking created successfully! +' . $points_awarded . ' points earned!',
            'booking_id' => $booking_id,
            'points_awarded' => $points_awarded,
            'total_amount' => $total_amount,
            'booking_details' => [
                'facility_name' => $facility['name'],
                'booking_date' => $booking_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'duration' => $duration_minutes . ' minutes',
                'amount' => '$' . number_format($total_amount, 2)
            ]
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
