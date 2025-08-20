<?php
// gamification_api.php - Secure API endpoints for gamification features

header('Content-Type: application/json');
session_start();
require_once 'config.php';

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$action = $_GET['action'] ?? '';
$user_id = $_GET['user_id'] ?? null;

// Validate user session and authorization for user-specific actions
$logged_in_user_id = $_SESSION['user_id'] ?? null;
$user_specific_actions = ['user_points', 'user_achievements', 'user_stats', 'user_activity_feed'];

if (in_array($action, $user_specific_actions)) {
    if (!$logged_in_user_id) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit;
    }
    if (!$user_id || intval($user_id) !== intval($logged_in_user_id)) {
        http_response_code(403);
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
    case 'get_username':
        getUserProfile($logged_in_user_id);
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function getUserPoints($user_id) {
    global $conn;
    
    if (!$user_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }
    
    try {
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
        
    } catch (Exception $e) {
        error_log("Get user points error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to retrieve user points']);
    }
}

function getUserStats($user_id) {
    global $conn;

    if (!$user_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }

    try {
        // Get user points
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

        // Get bookings count
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

        // Get competitions count
        $competitions = 0;
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM competition_participants WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $competitions = intval($row['count']);
        }
        $stmt->close();

        echo json_encode([
            'success' => true,
            'user_stats' => [
                'total_points' => $points,
                'total_bookings' => $bookings,
                'total_competitions' => $competitions
            ]
        ]);
        
    } catch (Exception $e) {
        error_log("Get user stats error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to retrieve user stats']);
    }
}

function getUserAchievements($user_id) {
    global $conn;
    
    if (!$user_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }
    
    try {
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
        
    } catch (Exception $e) {
        error_log("Get user achievements error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to retrieve user achievements']);
    }
}

function getUserActivityFeed($user_id) {
    global $conn;

    if (!$user_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }

    try {
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
            $activities[] = $row;
        }

        echo json_encode(['success' => true, 'activities' => $activities]);
        
    } catch (Exception $e) {
        error_log("Get user activity feed error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to retrieve user activity feed']);
    }
}

function getLeaderboard() {
    global $conn;
    
    try {
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
        
    } catch (Exception $e) {
        error_log("Get leaderboard error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to retrieve leaderboard']);
    }
}

function getUserProfile($user_id) {
    global $conn;
    
    if (!$user_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }
    
    try {
        $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(['success' => true, 'username' => $row['username']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Get user profile error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to retrieve user profile']);
    }
}
?>
