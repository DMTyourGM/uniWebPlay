<?php
// gamification_api.php - API endpoint for gamification features

header('Content-Type: application/json');
session_start();
include 'config.php';

$action = $_GET['action'] ?? '';
$user_id = $_GET['user_id'] ?? null;

// Validate user session and authorization for user-specific actions
$logged_in_user_id = $_SESSION['user_id'] ?? null;
$user_specific_actions = ['user_points', 'user_achievements', 'user_stats', 'user_activity_feed'];

if (in_array($action, $user_specific_actions)) {
    if (!$logged_in_user_id) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit;
    }
    if (!$user_id || intval($user_id) !== intval($logged_in_user_id)) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit;
    }
}

switch ($action) {
    case 'user_points':
        getUserPoints($user_id);
        break;
    case 'user_achievements':
        getUserAchievements($user_id);
        break;
    case 'user_stats':
        getUserStats($user_id);
        break;
    case 'user_activity_feed':
        getUserActivityFeed($user_id);
        break;
    case 'leaderboard':
        getLeaderboard();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function getUserPoints($user_id) {
    global $conn;
    
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }
    
    $stmt = $conn->prepare("SELECT points FROM user_points WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'points' => $row['points']]);
    } else {
        // Create user points record if doesn't exist
        $insert_stmt = $conn->prepare("INSERT INTO user_points (user_id, points) VALUES (?, 0)");
        $insert_stmt->bind_param("i", $user_id);
        $insert_stmt->execute();
        echo json_encode(['success' => true, 'points' => 0]);
    }
    
    $stmt->close();
}

function getUserStats($user_id) {
    global $conn;

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }

    // Fetch points
    $points = 0;
    $stmt = $conn->prepare("SELECT points FROM user_points WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $points = intval($row['points']);
    }
    $stmt->close();

    // Fetch bookings count
    $bookings = 0;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM bookings WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $bookings = intval($row['count']);
    }
    $stmt->close();

    // Fetch competitions count
    $competitions = 0;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM competitions WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $competitions = intval($row['count']);
    }
    $stmt->close();

    // Fetch skill level info (assuming skill_levels table with min_points, max_points)
    $skill_level_name = 'N/A';
    $min_points = 0;
    $max_points = 100;
    $current_points = $points;

    $stmt = $conn->prepare("
        SELECT name, min_points, max_points 
        FROM skill_levels 
        WHERE ? BETWEEN min_points AND max_points
        LIMIT 1
    ");
    $stmt->bind_param("i", $points);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $skill_level_name = $row['name'];
        $min_points = intval($row['min_points']);
        $max_points = intval($row['max_points']);
    }
    $stmt->close();

    echo json_encode([
        'success' => true,
        'user_stats' => [
            'total_points' => $points,
            'total_bookings' => $bookings,
            'total_competitions' => $competitions,
            'skill_level_name' => $skill_level_name,
            'min_points' => $min_points,
            'max_points' => $max_points,
            'current_points' => $current_points
        ]
    ]);
}

function getUserAchievements($user_id) {
    global $conn;
    
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }
    
    $stmt = $conn->prepare("
        SELECT a.name, a.description, a.points_awarded, ua.achieved_at 
        FROM user_achievements ua 
        JOIN achievements a ON ua.achievement_id = a.id 
        WHERE ua.user_id = ? 
        ORDER BY ua.achieved_at DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $achievements = [];
    while ($row = $result->fetch_assoc()) {
        $achievements[] = $row;
    }
    
    echo json_encode(['success' => true, 'achievements' => $achievements]);
}

function getUserActivityFeed($user_id) {
    global $conn;

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }

    $stmt = $conn->prepare("
        SELECT description, created_at 
        FROM user_activity 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 20
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = [
            'description' => $row['description'],
            'created_at' => $row['created_at']
        ];
    }

    echo json_encode(['success' => true, 'activities' => $activities]);
}

function getLeaderboard() {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT u.username, up.points 
        FROM user_points up 
        JOIN users u ON up.user_id = u.id 
        ORDER BY up.points DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $leaderboard = [];
    while ($row = $result->fetch_assoc()) {
        $leaderboard[] = $row;
    }
    
    echo json_encode(['success' => true, 'leaderboard' => $leaderboard]);
}

function getUserAchievements($user_id) {
    global $conn;
    
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }
    
    $stmt = $conn->prepare("
        SELECT a.name, a.description, a.points_awarded, ua.achieved_at 
        FROM user_achievements ua 
        JOIN achievements a ON ua.achievement_id = a.id 
        WHERE ua.user_id = ? 
        ORDER BY ua.achieved_at DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $achievements = [];
    while ($row = $result->fetch_assoc()) {
        $achievements[] = $row;
    }
    
    echo json_encode(['success' => true, 'achievements' => $achievements]);
}

function getLeaderboard() {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT u.username, up.points 
        FROM user_points up 
        JOIN users u ON up.user_id = u.id 
        ORDER BY up.points DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $leaderboard = [];
    while ($row = $result->fetch_assoc()) {
        $leaderboard[] = $row;
    }
    
    echo json_encode(['success' => true, 'leaderboard' => $leaderboard]);
}
?>
