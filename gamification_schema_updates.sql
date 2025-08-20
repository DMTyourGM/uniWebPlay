-- Enhanced Gamification Database Schema Updates
-- Add comprehensive gamification features to UniWebPlay

USE uniwebplay;

-- Enhanced achievements system
CREATE TABLE IF NOT EXISTS achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    points_awarded INT DEFAULT 0,
    icon VARCHAR(50),
    category VARCHAR(50),
    requirement_type ENUM('bookings', 'points', 'competitions', 'days_active', 'streak'),
    requirement_value INT DEFAULT 1,
    rarity ENUM('common', 'rare', 'epic', 'legendary') DEFAULT 'common',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_rarity (rarity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert comprehensive achievements
INSERT INTO achievements (name, description, points_awarded, icon, category, requirement_type, requirement_value, rarity) VALUES
-- Booking Achievements
('First Booking', 'Make your first facility booking', 50, '🎯', 'bookings', 'bookings', 1, 'common'),
('Regular Booker', 'Complete 10 bookings', 100, '📅', 'bookings', 'bookings', 10, 'common'),
('Booking Master', 'Complete 50 bookings', 500, '🏆', 'bookings', 'bookings', 50, 'rare'),
('Booking Legend', 'Complete 100 bookings', 1000, '👑', 'bookings', 'bookings', 100, 'epic'),

-- Points Achievements
('Point Collector', 'Earn 100 points', 50, '💰', 'points', 'points', 100, 'common'),
('Point Hoarder', 'Earn 1000 points', 200, '💎', 'points', 'points', 1000, 'rare'),
('Point Millionaire', 'Earn 5000 points', 1000, '🏦', 'points', 'points', 5000, 'epic'),
('Point Emperor', 'Earn 10000 points', 2500, '👑', 'points', 'points', 10000, 'legendary'),

-- Competition Achievements
('First Competition', 'Participate in your first competition', 100, '🏅', 'competitions', 'competitions', 1, 'common'),
('Competitor', 'Participate in 10 competitions', 300, '🥈', 'competitions', 'competitions', 10, 'rare'),
('Champion', 'Win 5 competitions', 1000, '🥇', 'competitions', 'competitions', 5, 'epic'),

-- Activity Achievements
('Week Warrior', 'Active for 7 consecutive days', 100, '📅', 'activity', 'days_active', 7, 'common'),
('Monthly Master', 'Active for 30 consecutive days', 500, '📆', 'activity', 'days_active', 30, 'rare'),
('Yearly Legend', 'Active for 365 consecutive days', 2000, '🎂', 'activity', 'days_active', 365, 'legendary'),

-- Streak Achievements
('Streak Starter', '3-day booking streak', 50, '🔥', 'streak', 'streak', 3, 'common'),
('Streak Keeper', '7-day booking streak', 200, '🔥🔥', 'streak', 'streak', 7, 'rare'),
('Streak Master', '30-day booking streak', 1000, '🔥🔥🔥', 'streak', 'streak', 30, 'epic');

-- User achievements tracking
CREATE TABLE IF NOT EXISTS user_achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    achievement_id INT NOT NULL,
    achieved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_achievement (user_id, achievement_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User levels system
CREATE TABLE IF NOT EXISTS user_levels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    level INT DEFAULT 1,
    experience_points INT DEFAULT 0,
    total_points_earned INT DEFAULT 0,
    next_level_points INT DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_level (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Category-specific points
CREATE TABLE IF NOT EXISTS user_category_points (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    category VARCHAR(50) NOT NULL,
    points INT DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_category (user_id, category),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User badges system
CREATE TABLE IF NOT EXISTS user_badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    badge_name VARCHAR(100) NOT NULL,
    badge_icon VARCHAR(50),
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Enhanced user activity tracking
CREATE TABLE IF NOT EXISTS user_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    activity_type ENUM('booking', 'competition', 'achievement', 'points', 'login', 'streak'),
    description TEXT,
    points_earned INT DEFAULT 0,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_activity (user_id, activity_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daily challenges
CREATE TABLE IF NOT EXISTS daily_challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    challenge_type ENUM('booking', 'competition', 'streak', 'points'),
    requirement_value INT DEFAULT 1,
    points_reward INT DEFAULT 50,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User challenge participation
CREATE TABLE IF NOT EXISTS user_challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    challenge_id INT NOT NULL,
    progress INT DEFAULT 0,
    is_completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL,
    points_earned INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (challenge_id) REFERENCES daily_challenges(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_challenge (user_id, challenge_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Leaderboard snapshots for historical tracking
CREATE TABLE IF NOT EXISTS leaderboard_snapshots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) DEFAULT 'global',
    user_id INT UNSIGNED NOT NULL,
    rank_position INT NOT NULL,
    points INT NOT NULL,
    snapshot_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_category_date (category, snapshot_date),
    INDEX idx_user_category (user_id, category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Competition results
CREATE TABLE IF NOT EXISTS competition_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    competition_id INT NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    position INT NOT NULL,
    points_earned INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_competition_user (competition_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create indexes for performance
CREATE INDEX idx_user_points_total ON user_points(points DESC);
CREATE INDEX idx_user_achievements_user ON user_achievements(user_id);
CREATE INDEX idx_user_activity_recent ON user_activity(user_id, created_at DESC);
CREATE INDEX idx_user_levels_level ON user_levels(level DESC);
CREATE INDEX idx_category_points_category ON user_category_points(category, points DESC);

-- Insert sample daily challenges
INSERT INTO daily_challenges (title, description, challenge_type, requirement_value, points_reward, start_date, end_date) VALUES
('Daily Booker', 'Make 1 booking today', 'booking', 1, 25, CURDATE(), CURDATE()),
('Streak Builder', 'Maintain your booking streak', 'streak', 1, 50, CURDATE(), CURDATE()),
('Point Collector', 'Earn 100 points today', 'points', 100, 75, CURDATE(), CURDATE());

-- Create triggers for automatic level updates
DELIMITER //

CREATE TRIGGER update_user_level_after_points
AFTER UPDATE ON user_points
FOR EACH ROW
BEGIN
    DECLARE new_level INT;
    DECLARE new_exp INT;
    DECLARE next_level_points INT;
    
    SET new_level = FLOOR(SQRT(NEW.points / 100)) + 1;
    SET new_exp = NEW.points * 0.1;
    SET next_level_points = POW(new_level, 2) * 100;
    
    INSERT INTO user_levels (user_id, level, experience_points, total_points_earned, next_level_points)
    VALUES (NEW.user_id, new_level, new_exp, NEW.points, next_level_points)
    ON DUPLICATE KEY UPDATE
        level = new_level,
        experience_points = new_exp,
        total_points_earned = NEW.points,
        next_level_points = next_level_points;
END//

CREATE TRIGGER log_user_activity_after_booking
AFTER INSERT ON bookings
FOR EACH ROW
BEGIN
    IF NEW.status = 'completed' THEN
        INSERT INTO user_activity (user_id, activity_type, description, points_earned)
        VALUES (NEW.user_id, 'booking', CONCAT('Booked ', NEW.facility_name), 10);
    END IF;
END//

CREATE TRIGGER log_user_activity_after_competition
AFTER INSERT ON competition_results
FOR EACH ROW
BEGIN
    INSERT INTO user_activity (user_id, activity_type, description, points_earned)
    VALUES (NEW.user_id, 'competition', CONCAT('Finished ', NEW.position, ' in competition'), NEW.points_earned);
END//

DELIMITER ;

-- Create stored procedures for leaderboard management
DELIMITER //

CREATE PROCEDURE update_leaderboard_snapshot(IN category_param VARCHAR(50))
BEGIN
    INSERT INTO leaderboard_snapshots (category, user_id, rank_position, points, snapshot_date)
    SELECT 
        category_param,
        u.id,
        RANK() OVER (ORDER BY COALESCE(up.points, 0) DESC),
        COALESCE(up.points, 0),
        CURDATE()
    FROM users u
    LEFT JOIN user_points up ON u.id = up.user_id
    WHERE u.is_active = 1;
END//

CREATE PROCEDURE award_achievement_check(IN user_id_param INT)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE achievement_id_var INT;
    DECLARE requirement_type_var VARCHAR(20);
    DECLARE requirement_value_var INT;
    DECLARE points_awarded_var INT;
    
    DECLARE achievement_cursor CURSOR FOR
        SELECT id, requirement_type, requirement_value, points_awarded
        FROM achievements
        WHERE id NOT IN (
            SELECT achievement_id FROM user_achievements WHERE user_id = user_id_param
        );
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN achievement_cursor;
    
    read_loop: LOOP
        FETCH achievement_cursor INTO achievement_id_var, requirement_type_var, requirement_value_var, points_awarded_var;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Check achievement conditions
        CASE requirement_type_var
            WHEN 'bookings' THEN
                IF (SELECT COUNT(*) FROM bookings WHERE user_id = user_id_param AND status = 'completed') >= requirement_value_var THEN
                    INSERT INTO user_achievements (user_id, achievement_id) VALUES (user_id_param, achievement_id_var);
                    UPDATE user_points SET points = points + points_awarded_var WHERE user_id = user_id_param;
                END IF;
            WHEN 'points' THEN
                IF (SELECT COALESCE(points, 0) FROM user_points WHERE user_id = user_id_param) >= requirement_value_var THEN
                    INSERT INTO user_achievements (user_id, achievement_id) VALUES (user_id_param, achievement_id_var);
                    UPDATE user_points SET points = points + points_awarded_var WHERE user_id = user_id_param;
                END IF;
            WHEN 'competitions' THEN
                IF (SELECT COUNT(*) FROM competition_participants WHERE user_id = user_id_param) >= requirement_value_var THEN
                    INSERT INTO user_achievements (user_id, achievement_id) VALUES (user_id_param, achievement_id_var);
                    UPDATE user_points SET points = points + points_awarded_var WHERE user_id = user_id_param;
                END IF;
        END CASE;
    END LOOP;
    
    CLOSE achievement_cursor;
END//

DELIMITER ;

-- Insert initial user levels for existing users
INSERT INTO user_levels (user_id, level, experience_points, total_points_earned, next_level_points)
SELECT u.id, 1, 0, COALESCE(up.points, 0), 100
FROM users u
LEFT JOIN user_points up ON u.id = up.user_id
WHERE u.id NOT IN (SELECT user_id FROM user_levels);
