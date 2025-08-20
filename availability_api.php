<?php
// availability_api.php - Real-time facility availability API

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config.php';

$action = $_GET['action'] ?? '';
$facility_id = $_GET['facility_id'] ?? null;
$date = $_GET['date'] ?? date('Y-m-d');
$time = $_GET['time'] ?? null;
$duration = $_GET['duration'] ?? 60;

switch ($action) {
    case 'check_availability':
        checkAvailability($facility_id, $date, $time, $duration);
        break;
    case 'get_facility_schedule':
        getFacilitySchedule($facility_id, $date);
        break;
    case 'get_available_slots':
        getAvailableSlots($facility_id, $date);
        break;
    case 'get_facilities':
        getFacilities();
        break;
    case 'get_facility_details':
        getFacilityDetails($facility_id);
        break;
    case 'search_slots':
        searchSlots($_GET);
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function checkAvailability($facility_id, $date, $time, $duration) {
    global $conn;
    
    if (!$facility_id || !$date || !$time) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        return;
    }
    
    try {
        // Check if facility exists and is available
        $facility_stmt = $conn->prepare("SELECT * FROM facilities WHERE id = ? AND is_available = TRUE");
        $facility_stmt->bind_param("i", $facility_id);
        $facility_stmt->execute();
        $facility_result = $facility_stmt->get_result();
        
        if ($facility_result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Facility not found or unavailable']);
            return;
        }
        
        $facility = $facility_result->fetch_assoc();
        
        // Check facility schedule for the day
        $day_of_week = date('N', strtotime($date));
        $schedule_stmt = $conn->prepare("SELECT * FROM facility_schedules WHERE facility_id = ? AND day_of_week = ? AND is_closed = FALSE");
        $schedule_stmt->bind_param("ii", $facility_id, $day_of_week);
        $schedule_stmt->execute();
        $schedule_result = $schedule_stmt->get_result();
        
        if ($schedule_result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Facility is closed on this day']);
            return;
        }
        
        $schedule = $schedule_result->fetch_assoc();
        
        // Check if requested time is within operating hours
        if ($time < $schedule['open_time'] || $time >= $schedule['close_time']) {
            echo json_encode(['success' => false, 'message' => 'Requested time is outside operating hours']);
            return;
        }
        
        // Check for booking conflicts
        $end_time = date('H:i:s', strtotime($time . ' + ' . $duration . ' minutes'));
        
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
        
        $conflict_stmt->bind_param("isssssss", $facility_id, $date, $time, $time, $time, $end_time, $time, $end_time);
        $conflict_stmt->execute();
        $conflict_result = $conflict_stmt->get_result();
        $conflicts = $conflict_result->fetch_assoc()['conflicts'];
        
        // Check booking slots
        $slot_stmt = $conn->prepare("
            SELECT * FROM booking_slots 
            WHERE facility_id = ? AND slot_date = ? AND slot_time = ? AND is_available = TRUE
        ");
        $slot_stmt->bind_param("iss", $facility_id, $date, $time);
        $slot_stmt->execute();
        $slot_result = $slot_stmt->get_result();
        
        $is_available = ($conflicts == 0 && $slot_result->num_rows > 0);
        
        echo json_encode([
            'success' => true,
            'available' => $is_available,
            'facility' => $facility,
            'schedule' => $schedule,
            'conflicts' => $conflicts,
            'requested_time' => $time,
            'end_time' => $end_time
        ]);
        
    } catch (Exception $e) {
        error_log("Availability check error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to check availability']);
    }
}

function getFacilitySchedule($facility_id, $date) {
    global $conn;
    
    try {
        $day_of_week = date('N', strtotime($date));
        
        $stmt = $conn->prepare("
            SELECT fs.*, f.name as facility_name, ft.name as facility_type
            FROM facility_schedules fs
            JOIN facilities f ON fs.facility_id = f.id
            JOIN facility_types ft ON f.facility_type_id = ft.id
            WHERE fs.facility_id = ? AND fs.day_of_week = ?
        ");
        $stmt->bind_param("ii", $facility_id, $day_of_week);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'No schedule found for this day']);
            return;
        }
        
        $schedule = $result->fetch_assoc();
        
        // Get existing bookings for the day
        $booking_stmt = $conn->prepare("
            SELECT start_time, end_time, status
            FROM facility_bookings
            WHERE facility_id = ? AND booking_date = ? AND status IN ('confirmed', 'pending')
            ORDER BY start_time
        ");
        $booking_stmt->bind_param("is", $facility_id, $date);
        $booking_stmt->execute();
        $booking_result = $booking_stmt->get_result();
        
        $bookings = [];
        while ($booking = $booking_result->fetch_assoc()) {
            $bookings[] = $booking;
        }
        
        echo json_encode([
            'success' => true,
            'schedule' => $schedule,
            'bookings' => $bookings,
            'date' => $date
        ]);
        
    } catch (Exception $e) {
        error_log("Schedule fetch error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to fetch schedule']);
    }
}

function getAvailableSlots($facility_id, $date) {
    global $conn;
    
    try {
        // Get facility details
        $facility_stmt = $conn->prepare("
            SELECT f.*, ft.name as facility_type_name
            FROM facilities f
            JOIN facility_types ft ON f.facility_type_id = ft.id
            WHERE f.id = ? AND f.is_available = TRUE
        ");
        $facility_stmt->bind_param("i", $facility_id);
        $facility_stmt->execute();
        $facility_result = $facility_stmt->get_result();
        
        if ($facility_result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Facility not found']);
            return;
        }
        
        $facility = $facility_result->fetch_assoc();
        
        // Get facility schedule
        $day_of_week = date('N', strtotime($date));
        $schedule_stmt = $conn->prepare("
            SELECT * FROM facility_schedules
            WHERE facility_id = ? AND day_of_week = ? AND is_closed = FALSE
        ");
        $schedule_stmt->bind_param("ii", $facility_id, $day_of_week);
        $schedule_stmt->execute();
        $schedule_result = $schedule_stmt->get_result();
        
        if ($schedule_result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Facility is closed on this day']);
            return;
        }
        
        $schedule = $schedule_result->fetch_assoc();
        
        // Generate available slots
        $open_time = strtotime($schedule['open_time']);
        $close_time = strtotime($schedule['close_time']);
        $slots = [];
        
        $current_time = $open_time;
        while ($current_time < $close_time) {
            $slot_time = date('H:i:s', $current_time);
            $end_time = date('H:i:s', $current_time + 3600); // 1 hour slots
            
            // Check if slot is available
            $slot_stmt = $conn->prepare("
                SELECT COUNT(*) as bookings
                FROM facility_bookings
                WHERE facility_id = ? AND booking_date = ? AND status IN ('confirmed', 'pending')
                AND (
                    (start_time <= ? AND end_time > ?) OR
                    (start_time < ? AND end_time >= ?)
                )
            ");
            $slot_stmt->bind_param("isssss", $facility_id, $date, $slot_time, $slot_time, $slot_time, $end_time);
            $slot_stmt->execute();
            $slot_result = $slot_stmt->get_result();
            $bookings = $slot_result->fetch_assoc()['bookings'];
            
            $is_available = ($bookings < $facility['capacity']);
            
            $slots[] = [
                'time' => $slot_time,
                'end_time' => $end_time,
                'available' => $is_available,
                'price' => $facility['hourly_rate'],
                'bookings' => $bookings,
                'capacity' => $facility['capacity']
            ];
            
            $current_time += 3600; // Move to next hour
        }
        
        echo json_encode([
            'success' => true,
            'facility' => $facility,
            'date' => $date,
            'slots' => $slots
        ]);
        
    } catch (Exception $e) {
        error_log("Available slots error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to get available slots']);
    }
}

function getFacilities() {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            SELECT f.*, ft.name as facility_type_name, ft.icon, ft.color_code
            FROM facilities f
            JOIN facility_types ft ON f.facility_type_id = ft.id
            WHERE f.is_available = TRUE
            ORDER BY ft.name, f.name
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $facilities = [];
        while ($facility = $result->fetch_assoc()) {
            $facility['amenities'] = json_decode($facility['amenities'], true);
            $facility['images'] = json_decode($facility['images'], true);
            $facilities[] = $facility;
        }
        
        echo json_encode(['success' => true, 'facilities' => $facilities]);
        
    } catch (Exception $e) {
        error_log("Facilities fetch error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to fetch facilities']);
    }
}

function getFacilityDetails($facility_id) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            SELECT f.*, ft.name as facility_type_name, ft.icon, ft.color_code
            FROM facilities f
            JOIN facility_types ft ON f.facility_type_id = ft.id
            WHERE f.id = ?
        ");
        $stmt->bind_param("i", $facility_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Facility not found']);
            return;
        }
        
        $facility = $result->fetch_assoc();
        $facility['amenities'] = json_decode($facility['amenities'], true);
        $facility['images'] = json_decode($facility['images'], true);
        
        echo json_encode(['success' => true, 'facility' => $facility]);
        
    } catch (Exception $e) {
        error_log("Facility details error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to fetch facility details']);
    }
}

function searchSlots($params) {
    global $conn;
    
    $facility_type = $params['facility_type'] ?? null;
    $date = $params['date'] ?? date('Y-m-d');
    $start_time = $params['start_time'] ?? null;
    $end_time = $params['end_time'] ?? null;
    $min_price = $params['min_price'] ?? 0;
    $max_price = $params['max_price'] ?? 9999;
    
    try {
        $where_conditions = ["f.is_available = TRUE"];
        $params = [];
        $types = "";
        
        if ($facility_type) {
            $where_conditions[] = "ft.name LIKE ?";
            $params[] = "%$facility_type%";
            $types .= "s";
        }
        
        if ($min_price) {
            $where_conditions[] = "f.hourly_rate >= ?";
            $params[] = $min_price;
            $types .= "d";
        }
        
        if ($max_price) {
            $where_conditions[] = "f.hourly_rate <= ?";
            $params[] = $max_price;
            $types .= "d";
        }
        
        $where_clause = implode(" AND ", $where_conditions);
        
        $stmt = $conn->prepare("
            SELECT f.*, ft.name as facility_type_name, ft.icon, ft.color_code
            FROM facilities f
            JOIN facility_types ft ON f.facility_type_id = ft.id
            WHERE $where_clause
            ORDER BY f.hourly_rate ASC, f.name ASC
        ");
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $facilities = [];
        while ($facility = $result->fetch_assoc()) {
            $facility['amenities'] = json_decode($facility['amenities'], true);
            $facility['images'] = json_decode($facility['images'], true);
            
            // Get availability for the date
            $day_of_week = date('N', strtotime($date));
            $schedule_stmt = $conn->prepare("
                SELECT * FROM facility_schedules
                WHERE facility_id = ? AND day_of_week = ? AND is_closed = FALSE
            ");
            $schedule_stmt->bind_param("ii", $facility['id'], $day_of_week);
            $schedule_stmt->execute();
            $schedule_result = $schedule_stmt->get_result();
            
            $facility['schedule'] = $schedule_result->fetch_assoc();
            $facility['available_slots'] = [];
            
            if ($facility['schedule']) {
                // Generate available slots for the facility
                $open_time = strtotime($facility['schedule']['open_time']);
                $close_time = strtotime($facility['schedule']['close_time']);
                
                $current_time = $open_time;
                while ($current_time < $close_time) {
                    $slot_time = date('H:i:s', $current_time);
                    $end_time = date('H:i:s', $current_time + 3600);
                    
                    $facility['available_slots'][] = [
                        'time' => $slot_time,
                        'end_time' => $end_time,
                        'price' => $facility['hourly_rate']
                    ];
                    
                    $current_time += 3600;
                }
            }
            
            $facilities[] = $facility;
        }
        
        echo json_encode([
            'success' => true,
            'facilities' => $facilities,
            'search_date' => $date
        ]);
        
    } catch (Exception $e) {
        error_log("Search slots error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to search slots']);
    }
}
?>
