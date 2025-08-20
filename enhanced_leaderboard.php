<?php
// enhanced_leaderboard.php - Enhanced leaderboard with real-time data

header('Content-Type: application/json');
require_once 'config.php';

$action = $_GET['action'] ?? '';
$category = $_GET['category'] ?? 'global';
$user_id = $_GET['user_id'] ?? null;

switch ($action) {
    case 'get_leaderboard':
        getEnhancedLeaderboard($category);
        break;
    case 'get_user_ranking':
        getUserRanking($user_id);
        break;
    case 'get_categories':
        getLeaderboardCategories();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function getEnhancedLeaderboard($category) {
    global $conn;
    
    try {
        if ($category === 'global') {
            $stmt = $conn->prepare("
                SELECT 
                    u.id,
                    u.username,
                    up.points as total_points,
                    up.points * 0.1 as experience_points,
                    COUNT(DISTINCT b.id) as total_bookings,
                    COUNT(DISTINCT cp.competition_id) as total_competitions,
                    COUNT(DISTINCT ua.achievement_id) as total_achievements,
                    RANK() OVER (ORDER BY up.points DESC) as rank_position,
                    DENSE_RANK() OVER (ORDER BY up.points DESC) as dense_rank
                FROM users u
                LEFT JOIN user_points up ON u.id = up.user_id
                LEFT JOIN bookings b ON u.id = b.user_id AND b.status = 'completed'
                LEFT JOIN competition_participants cp ON u.id = cp.user_id
                LEFT JOIN user_achievements ua ON u.id = ua.user_id
                GROUP BY u.id, u.username, up.points
                ORDER BY up.points DESC
                LIMIT 50
            ");
        } else {
            // Category-specific leaderboard
            $stmt = $conn->prepare("
                SELECT 
                    u.id,
                    u.username,
                    ucp.points as category_points,
                    ucp.category,
                    RANK() OVER (PARTITION BY ucp.category ORDER BY ucp.points DESC) as rank_position,
                    COUNT(DISTINCT cb.id) as category_bookings
                FROM users u
                JOIN user_category_points ucp ON u.id = ucp.user_id
                LEFT JOIN category_bookings cb ON u.id = cb.user_id AND cb.category = ucp.category
                WHERE ucp.category = ?
                GROUP BY u.id, u.username, ucp.points, ucp.category
                ORDER BY ucp.points DESC
                LIMIT 20
            ");
            $stmt->bind_param("s", $category);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $leaderboard = [];
        while ($row = $result->fetch_assoc()) {
            $row['medal'] = getMedal($row['rank_position']);
            $row['tier'] = getTier($row['total_points'] ?? $row['category_points']);
            $leaderboard[] = $row;
        }
        
        echo json_encode(['success' => true, 'leaderboard' => $leaderboard]);
        
    } catch (Exception $e) {
        error_log("Enhanced leaderboard error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to retrieve leaderboard']);
    }
}

function getUserRanking($user_id) {
    global $conn;
    
    if (!$user_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }
    
    try {
        // Get user's global ranking
        $stmt = $conn->prepare("
            SELECT 
                u.id,
                u.username,
                up.points as total_points,
                RANK() OVER (ORDER BY up.points DESC) as global_rank,
                COUNT(*) OVER () as total_players,
                (up.points / (SELECT MAX(points) FROM user_points)) * 100 as percentile
            FROM users u
            LEFT JOIN user_points up ON u.id = up.user_id
            WHERE u.id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $user_ranking = $result->fetch_assoc();
        
        // Get category rankings
        $stmt = $conn->prepare("
            SELECT 
                ucp.category,
                ucp.points as category_points,
                RANK() OVER (PARTITION BY ucp.category ORDER BY ucp.points DESC) as category_rank
            FROM user_category_points ucp
            WHERE ucp.user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $category_rankings = [];
        while ($row = $result->fetch_assoc()) {
            $category_rankings[] = $row;
        }
        
        $user_ranking['categories'] = $category_rankings;
        
        echo json_encode(['success' => true, 'ranking' => $user_ranking]);
        
    } catch (Exception $e) {
        error_log("User ranking error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to retrieve user ranking']);
    }
}

function getLeaderboardCategories() {
    $categories = [
        'global' => ['name' => 'Global Rankings', 'icon' => '🌍'],
        'pool' => ['name' => 'Pool', 'icon' => '🎱'],
        'chess' => ['name' => 'Chess', 'icon' => '♟️'],
        'carom' => ['name' => 'Carom', 'icon' => '🎯'],
        'table_tennis' => ['name' => 'Table Tennis', 'icon' => '🏓'],
        'board_games' => ['name' => 'Board Games', 'icon' => '🎲'],
        'badminton' => ['name' => 'Badminton', 'icon' => '🏸'],
        'basketball' => ['name' => 'Basketball', 'icon' => '🏀'],
        'volleyball' => ['name' => 'Volleyball', 'icon' => '🏐'],
        'gym' => ['name' => 'Gym', 'icon' => '💪']
    ];
    
    echo json_encode(['success' => true, 'categories' => $categories]);
}

function getMedal($rank) {
    switch ($rank) {
        case 1: return '🥇';
        case 2: return '🥈';
        case 3: return '🥉';
        default: return '#' . $rank;
    }
}

function getTier($points) {
    if ($points >= 10000) return 'Legendary';
    if ($points >= 5000) return 'Master';
    if ($points >= 2500) return 'Expert';
    if ($points >= 1000) return 'Advanced';
    if ($points >= 500) return 'Intermediate';
    return 'Beginner';
}
?>
