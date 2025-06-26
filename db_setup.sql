-- SQL script to create tables for uniPlay Web project

CREATE TABLE Users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('student', 'admin') DEFAULT 'student'
);

CREATE TABLE Facilities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  hourly_rate DECIMAL(6,2) NOT NULL
);

CREATE TABLE Bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  facility_id INT NOT NULL,
  slot DATETIME NOT NULL,
  status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
  FOREIGN KEY (user_id) REFERENCES Users(id),
  FOREIGN KEY (facility_id) REFERENCES Facilities(id)
);

CREATE TABLE MaintenanceReports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  facility_id INT NOT NULL,
  photo_url VARCHAR(255),
  status ENUM('reported', 'in_progress', 'resolved') DEFAULT 'reported',
  FOREIGN KEY (facility_id) REFERENCES Facilities(id)
);

-- Sample inserts

INSERT INTO Users (email, password, role) VALUES
('student1@example.com', 'hashed_password1', 'student'),
('admin1@example.com', 'hashed_password2', 'admin');

INSERT INTO Facilities (name, hourly_rate) VALUES
('Pool', 15.00),
('Chess Room', 5.00),
('Carom Room', 5.00);

INSERT INTO Bookings (user_id, facility_id, slot, status) VALUES
(1, 1, '2024-07-01 10:00:00', 'confirmed'),
(1, 2, '2024-07-02 14:00:00', 'pending');

INSERT INTO MaintenanceReports (facility_id, photo_url, status) VALUES
(1, 'photos/pool_leak.jpg', 'reported');
