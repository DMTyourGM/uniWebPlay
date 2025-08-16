6

0-- Phase 2: Database Design for uniPlay Web (Microsoft SQL Server syntax)

-- Users table (students/admins)
CREATE TABLE users (
  id INT IDENTITY(1,1) PRIMARY KEY,
  email NVARCHAR(255) UNIQUE NOT NULL,
  username NVARCHAR(50) UNIQUE NOT NULL,
  password_hash NVARCHAR(255) NOT NULL, -- Store bcrypt hashes
  role NVARCHAR(10) NOT NULL DEFAULT 'student', -- Use NVARCHAR instead of ENUM
  profile_photo NVARCHAR(255) NULL,
  created_at DATETIME2 DEFAULT SYSUTCDATETIME(),
  updated_at DATETIME2 DEFAULT SYSUTCDATETIME()
);

-- Facilities table (pool, chess, carom, etc.)
CREATE TABLE facilities (
  id INT IDENTITY(1,1) PRIMARY KEY,
  name NVARCHAR(100) NOT NULL,
  hourly_rate DECIMAL(10, 2) DEFAULT 0.00,
  is_active BIT DEFAULT 1,
  created_at DATETIME2 DEFAULT SYSUTCDATETIME(),
  updated_at DATETIME2 DEFAULT SYSUTCDATETIME()
);

-- Bookings table
CREATE TABLE bookings (
  id INT IDENTITY(1,1) PRIMARY KEY,
  user_id INT NOT NULL,
  facility_id INT NOT NULL,
  start_time DATETIME2 NOT NULL,
  end_time DATETIME2 NOT NULL,
  status NVARCHAR(20) NOT NULL DEFAULT 'pending',
  created_at DATETIME2 DEFAULT SYSUTCDATETIME(),
  updated_at DATETIME2 DEFAULT SYSUTCDATETIME(),
  CONSTRAINT unique_booking UNIQUE (facility_id, start_time, end_time),
  CONSTRAINT fk_bookings_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_bookings_facilities FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE
);

-- Maintenance reports table
CREATE TABLE maintenance_reports (
  id INT IDENTITY(1,1) PRIMARY KEY,
  user_id INT NOT NULL,
  facility_id INT NOT NULL,
  report_text NVARCHAR(MAX) NOT NULL,
  photo NVARCHAR(255) NULL,
  status NVARCHAR(20) NOT NULL DEFAULT 'open',
  created_at DATETIME2 DEFAULT SYSUTCDATETIME(),
  updated_at DATETIME2 DEFAULT SYSUTCDATETIME(),
  CONSTRAINT fk_reports_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_reports_facilities FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE
);

-- Sample INSERT queries for testing

INSERT INTO users (email, username, password_hash, role) VALUES
('student1@example.com', 'student1', 'examplehashhere', 'student'),
('admin1@example.com', 'admin1', 'examplehashhere', 'admin');

INSERT INTO facilities (name, hourly_rate) VALUES
('Swimming Pool', 10.00),
('Chess Room', 5.00),
('Carom Board', 3.00);

INSERT INTO bookings (user_id, facility_id, start_time, end_time, status) VALUES
(1, 1, '2024-07-01T10:00:00', '2024-07-01T11:00:00', 'confirmed');

INSERT INTO maintenance_reports (user_id, facility_id, report_text, status) VALUES
(1, 2, 'Light bulb not working in Chess Room', 'open');

-- End of Phase 2 Database Design for MSSQL
