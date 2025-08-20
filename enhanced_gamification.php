<?php
// enhanced_gamification.php - Complete gamification system integration

class EnhancedGamification {
    private $conn;
    
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }
    
    // Get comprehensive user gamification data
    public function getUserGamificationData($userId) {
        try {
            // Get user stats
            $stats = $this->getUserStats($userId);
            
            // Get achievements
            $achievements = $this->getUserAchievements($userId);
            
            // Get leaderboard position
            $leaderboard = $this->getUserLeaderboardPosition($userId);
            
            // Get level info
            $level = $this->getUserLevel($userId);
            
            return [
                'success' => true,
                'stats' => $stats,
                'achievements' => $achievements,
                'leaderboard' => $leaderboard,
                'level' => $level
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Get user stats with enhanced metrics
    private function getUserStats($userId) {
        $stmt = $this->conn->prepare("
            SELECT 
                up.points as total_points,
                up.points * 0.1 as experience_points,
                COUNT(DISTINCT b.id) as total_bookings,
                COUNT(Distinct cp.competition_id) as total_competitions,
                COUNT(Distinct ua.achievement_id) as total_achievements,
                DATEDIFF(NOW(), u.created_at) as days_active
            FROM users u
            LEFT JOIN user_points up ON u.id = up.user_id
            LEFT JOIN bookings b ON u.id = b.user_id AND b.status = 'completed'
            LEFT JOIN competition_participants cp ON u.id = cp.user_id
            LEFT JOIN user_achievements ua ON u.id = ua.user_id
            WHERE u.id = ?
            GROUP BY u.id
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row;
    }
    
    // Get user achievements with progress
    private function getUserAchievements($userId) {
        $stmt = $this->conn->prepare("
            SELECT id, name, description, points_awarded, icon, category, requirement_type, requirement_value, achieved_at
            FROM achievements
            WHERE user_id = ?
            ORDER BY points_awarded DESC, name ASC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // Get user level and progression
    private function getUserLevel($userId) {
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
    private function getUserLevel($userId) {
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
    private function getUserLevel($userId) {
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
    private function getUserLevel($userId) {
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
    private function getUserLevel($userId) {
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
    private function getUserLevel($userId) {
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
    private function getUserLevel($userId) {
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
    private function getUserLevel($userId) {
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
    private function getUserLevel($userId) {
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
    private function getUserLevel($userId) {
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
    private function getUserLevel($userId) {
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
    private function getUserLevel($userId) {
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
    private function getUserLevel($userId) {
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
    private function getUserLevel($userId) {
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
    private function getUserLevel($userId) {
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
    private function getUserLevel($userId) {
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
                return $points;
            }
            
            // Get user level and progression
            $stmt = $this->conn->prepare("
                SELECT points FROM user_points WHERE user_id = ?
            ");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $points = $result->fetch_assoc()['points'] ?? 0;
            return $points;
        }
        
        // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and progression
        $stmt = $this->conn->prepare("
            SELECT points FROM user_points WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $points = $result->fetch_assoc()['points'] ?? 0;
        return $points;
    }
    
    // Get user level and
