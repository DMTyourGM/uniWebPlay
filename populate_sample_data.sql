-- Populate UniWebPlay with sample data for testing

USE uniwebplay;

-- Insert achievements
INSERT INTO achievements (name, description, points_awarded) VALUES
('First Booking', 'Book your first facility slot', 50),
('Regular User', 'Complete 5 bookings', 100),
('Frequent User', 'Complete 10 bookings', 200),
('Master Booker', 'Complete 25 bookings', 500),
('Pool Champion', 'Book swimming pool 5 times', 150),
('Chess Master', 'Book chess arena 5 times', 150),
('Table Tennis Pro', 'Book table tennis 5 times', 150),
('Carom Expert', 'Book carom room 5 times', 150),
('Board Game Enthusiast', 'Book board games 5 times', 150),
('Early Bird', 'Book a slot before 9 AM', 75),
('Night Owl', 'Book a slot after 8 PM', 75),
('Weekend Warrior', 'Book a slot on weekend', 100),
('Maintenance Reporter', 'Report your first maintenance issue', 25),
('Photo Reporter', 'Report maintenance with photo', 50),
('Community Helper', 'Help resolve 5 maintenance issues', 200);

-- Insert sample users (passwords are hashed - 'password123')
INSERT INTO users (username, email, password_hash) VALUES
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('mike_wilson', 'mike@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('sarah_jones', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('admin_user', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert user points
INSERT INTO user_points (user_id, points) VALUES
(1, 250),
(2, 180),
(3, 320),
(4, 150),
(5, 500);

-- Insert sample bookings
INSERT INTO facility_bookings (user_id, facility_id, booking_date, start_time, end_time, duration_minutes, total_amount, status) VALUES
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00:00', '11:00:00', 60, 15.00, 'confirmed'),
(2, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:00:00', '15:00:00', 60, 5.00, 'confirmed'),
(3, 5, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '16:00:00', '17:00:00', 60, 4.00, 'confirmed'),
(1, 7, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '09:00:00', '10:00:00', 60, 8.00, 'confirmed'),
(4, 9, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '18:00:00', '19:00:00', 60, 5.00, 'confirmed');

-- Insert user achievements
INSERT INTO user_achievements (user_id, achievement_id) VALUES
(1, 1), -- First Booking
(1, 2), -- Regular User
(1, 5), -- Pool Champion
(2, 1), -- First Booking
(2, 6), -- Chess Master
(3, 1), -- First Booking
(3, 2), -- Regular User
(3, 7), -- Table Tennis Pro
(4, 1), -- First Booking
(5, 1), -- First Booking
(5, 2), -- Regular User
(5, 3), -- Frequent User
(5, 4); -- Master Booker

-- Insert sample maintenance requests
INSERT INTO maintenance_requests (user_id, facility_id, title, description, priority, status) VALUES
(1, 1, 'Pool Lane Rope Damaged', 'Lane rope number 3 is frayed and needs replacement', 'medium', 'reported'),
(2, 3, 'Chess Clock Not Working', 'Digital clock on board 2 is not keeping time correctly', 'high', 'in_progress'),
(3, 5, 'Carom Board Sticker Peeling', 'The sticker on carom board 1 is peeling off', 'low', 'resolved'),
(4, 7, 'Table Tennis Net Broken', 'The net on table 1 is torn and needs replacement', 'medium', 'reported');

-- Insert sample payments
INSERT INTO payments (booking_id, user_id, amount, payment_method, payment_status, transaction_id) VALUES
(1, 1, 15.00, 'credit_card', 'completed', 'TXN123456789'),
(2, 2, 5.00, 'paypal', 'completed', 'TXN987654321'),
(3, 3, 4.00, 'stripe', 'completed', 'TXN456789123'),
(4, 1, 8.00, 'credit_card', 'completed', 'TXN789123456'),
(5, 4, 5.00, 'debit_card', 'completed', 'TXN321654987');

-- Insert user preferences
INSERT INTO user_preferences (user_id, email_notifications, booking_reminders, language) VALUES
(1, TRUE, TRUE, 'en'),
(2, TRUE, TRUE, 'en'),
(3, FALSE, TRUE, 'en'),
(4, TRUE, FALSE, 'en'),
(5, TRUE, TRUE, 'en');

-- Create booking slots for the next 30 days
-- This will be populated by the application automatically
-- Sample for today and tomorrow
INSERT INTO booking_slots (facility_id, slot_date, slot_time, max_bookings, price) VALUES
(1, CURDATE(), '09:00:00', 50, 15.00),
(1, CURDATE(), '10:00:00', 50, 15.00),
(1, CURDATE(), '11:00:00', 50, 15.00),
(1, CURDATE(), '12:00:00', 50, 15.00),
(1, CURDATE(), '13:00:00', 50, 15.00),
(1, CURDATE(), '14:00:00', 50, 15.00),
(1, CURDATE(), '15:00:00', 50, 15.00),
(1, CURDATE(), '16:00:00', 50, 15.00),
(1, CURDATE(), '17:00:00', 50, 15.00),
(1, CURDATE(), '18:00:00', 50, 15.00),
(1, CURDATE(), '19:00:00', 50, 15.00),
(1, CURDATE(), '20:00:00', 50, 15.00);

-- Insert competitions
INSERT INTO competitions (name, description, start_date, end_date) VALUES
('Swimming Championship', 'Annual swimming competition with multiple categories', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 14 DAY)),
('Chess Tournament', 'Open chess tournament for all skill levels', DATE_ADD(CURDATE(), INTERVAL 10 DAY), DATE_ADD(CURDATE(), INTERVAL 17 DAY)),
('Table Tennis League', 'Weekly table tennis league matches', DATE_ADD(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 31 DAY));

-- Insert competition participants
INSERT INTO competition_participants (competition_id, user_id, points_earned) VALUES
(1, 1, 100),
(1, 2, 75),
(1, 3, 50),
(2, 2, 120),
(2, 4, 80),
(3, 3, 200),
(3, 1, 150),
(3, 5, 180);

-- Insert user activity
INSERT INTO user_activity (user_id, description) VALUES
(1, 'Booked Olympic Pool A for 1 hour'),
(2, 'Joined Chess Tournament'),
(3, 'Completed Table Tennis League match'),
(4, 'Reported maintenance issue'),
(5, 'Achieved Master Booker status');
