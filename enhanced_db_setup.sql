-- Enhanced UniWebPlay Database Schema
-- Complete facilities management with real-time availability

USE uniwebplay;

-- Drop existing tables if they exist (for clean setup)
DROP TABLE IF EXISTS facility_bookings;
DROP TABLE IF EXISTS booking_slots;
DROP TABLE IF EXISTS facility_schedules;
DROP TABLE IF EXISTS facilities;
DROP TABLE IF EXISTS facility_types;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS maintenance_requests;

-- Facility Types (comprehensive list of recreational activities)
CREATE TABLE IF NOT EXISTS facility_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    color_code VARCHAR(7) DEFAULT '#007bff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert comprehensive facility types
INSERT INTO facility_types (name, description, icon, color_code) VALUES
('Swimming Pool', 'Olympic size swimming pool with 8 lanes', 'fas fa-swimmer', '#007bff'),
('Chess Arena', 'Professional chess boards and tournament setup', 'fas fa-chess', '#28a745'),
('Carom Room', 'Professional carom boards with proper lighting', 'fas fa-circle', '#dc3545'),
('Table Tennis', 'ITTF approved tables and equipment', 'fas fa-table-tennis', '#ffc107'),
('Board Games', 'Collection of modern and classic board games', 'fas fa-dice', '#17a2b8'),
('Badminton Court', 'Indoor badminton courts with professional flooring', 'fas fa-feather-alt', '#6f42c1'),
('Basketball Court', 'Full-size indoor basketball court', 'fas fa-basketball-ball', '#fd7e14'),
('Volleyball Court', 'Beach volleyball court with sand flooring', 'fas fa-volleyball-ball', '#20c997'),
('Gymnasium', 'Fully equipped gym with modern equipment', 'fas fa-dumbbell', '#e83e8c'),
('Yoga Studio', 'Peaceful yoga and meditation space', 'fas fa-om', '#6c757d'),
('Squash Court', 'Professional squash courts with glass walls', 'fas fa-square', '#6610f2'),
('Tennis Court', 'Indoor tennis courts with synthetic surface', 'fas fa-tennis-ball', '#d63384');

-- Facilities (individual facility instances)
CREATE TABLE IF NOT EXISTS facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    facility_type_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    capacity INT DEFAULT 1,
    hourly_rate DECIMAL(10,2) DEFAULT 0.00,
    is_available BOOLEAN DEFAULT TRUE,
    location VARCHAR(255),
    amenities JSON,
    images JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (facility_type_id) REFERENCES facility_types(id) ON DELETE CASCADE,
    INDEX idx_facility_type (facility_type_id),
    INDEX idx_availability (is_available)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample facilities
INSERT INTO facilities (facility_type_id, name, description, capacity, hourly_rate, location, amenities) VALUES
(1, 'Olympic Pool A', 'Main competition pool, 8 lanes, 50m', 50, 15.00, 'Building A, Ground Floor', JSON_ARRAY('Changing Rooms', 'Showers', 'Lockers', 'Towels')),
(1, 'Training Pool B', 'Shallow training pool, 25m', 30, 10.00, 'Building A, Ground Floor', JSON_ARRAY('Changing Rooms', 'Showers', 'Lockers')),
(2, 'Chess Arena 1', 'Professional tournament setup', 4, 5.00, 'Building B, First Floor', JSON_ARRAY('Digital Clocks', 'Score Sheets', 'Silent Environment')),
(2, 'Chess Arena 2', 'Casual chess playing area', 2, 3.00, 'Building B, First Floor', JSON_ARRAY('Digital Clocks', 'Comfortable Seating')),
(3, 'Carom Board 1', 'Professional carom board', 4, 4.00, 'Building C, Second Floor', JSON_ARRAY('Proper Lighting', 'Score Board', 'Chalk')),
(3, 'Carom Board 2', 'Training carom board', 4, 3.00, 'Building C, Second Floor', JSON_ARRAY('Proper Lighting', 'Basic Equipment')),
(4, 'Table Tennis Arena 1', 'ITTF approved table', 4, 8.00, 'Building D, Ground Floor', JSON_ARRAY('Professional Balls', 'Paddles Available', 'Score Board')),
(4, 'Table Tennis Arena 2', 'Training table', 4, 6.00, 'Building D, Ground Floor', JSON_ARRAY('Basic Equipment', 'Score Board')),
(5, 'Board Games Lounge', 'Modern board games collection', 20, 5.00, 'Building E, First Floor', JSON_ARRAY('Comfortable Seating', 'Refreshments', 'WiFi'));

-- Facility Schedules (weekly schedule for each facility)
CREATE TABLE IF NOT EXISTS facility_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    facility_id INT NOT NULL,
    day_of_week TINYINT CHECK (day_of_week BETWEEN 1 AND 7), -- 1=Monday, 7=Sunday
    open_time TIME NOT NULL,
    close_time TIME NOT NULL,
    is_closed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE,
    UNIQUE KEY unique_schedule (facility_id, day_of_week),
    INDEX idx_facility_day (facility_id, day_of_week)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert standard schedules (9 AM - 9 PM)
INSERT INTO facility_schedules (facility_id, day_of_week, open_time, close_time)
SELECT f.id, d.day_num, '09:00:00', '21:00:00'
FROM facilities f
CROSS JOIN (
    SELECT 1 as day_num UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 
    UNION SELECT 5 UNION SELECT 6 UNION SELECT 7
) d;

-- Booking Slots (pre-generated time slots for booking)
CREATE TABLE IF NOT EXISTS booking_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    facility_id INT NOT NULL,
    slot_date DATE NOT NULL,
    slot_time TIME NOT NULL,
    duration_minutes INT DEFAULT 60,
    max_bookings INT DEFAULT 1,
    current_bookings INT DEFAULT 0,
    is_available BOOLEAN DEFAULT TRUE,
    price DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE,
    UNIQUE KEY unique_slot (facility_id, slot_date, slot_time),
    INDEX idx_availability (facility_id, slot_date, is_available),
    INDEX idx_date_time (slot_date, slot_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Enhanced Bookings table
CREATE TABLE IF NOT EXISTS facility_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    facility_id INT NOT NULL,
    booking_slot_id INT,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    duration_minutes INT DEFAULT 60,
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed', 'no_show') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'refunded', 'failed') DEFAULT 'pending',
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_slot_id) REFERENCES booking_slots(id) ON DELETE SET NULL,
    INDEX idx_user_bookings (user_id, booking_date),
    INDEX idx_facility_date (facility_id, booking_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('credit_card', 'debit_card', 'paypal', 'stripe') DEFAULT 'credit_card',
    transaction_id VARCHAR(255),
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    refund_amount DECIMAL(10,2) DEFAULT 0.00,
    refund_date TIMESTAMP NULL,
    FOREIGN KEY (booking_id) REFERENCES facility_bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_payments (user_id),
    INDEX idx_booking_payments (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Enhanced maintenance requests
CREATE TABLE IF NOT EXISTS maintenance_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    facility_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('reported', 'in_progress', 'resolved', 'closed') DEFAULT 'reported',
    images JSON,
    assigned_to INT UNSIGNED,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_facility_status (facility_id, status),
    INDEX idx_priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User preferences for notifications
CREATE TABLE IF NOT EXISTS user_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    email_notifications BOOLEAN DEFAULT TRUE,
    sms_notifications BOOLEAN DEFAULT FALSE,
    booking_reminders BOOLEAN DEFAULT TRUE,
    marketing_emails BOOLEAN DEFAULT FALSE,
    language VARCHAR(5) DEFAULT 'en',
    timezone VARCHAR(50) DEFAULT 'UTC',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_prefs (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create indexes for performance
CREATE INDEX idx_bookings_date_range ON facility_bookings(booking_date, start_time, end_time);
CREATE INDEX idx_slots_facility_date ON booking_slots(facility_id, slot_date);
CREATE INDEX idx_facility_availability ON facilities(is_available);
