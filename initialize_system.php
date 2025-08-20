<?php
// System initialization script
require_once 'config.php';

// Create sample data
try {
    // Insert sample facilities
    $facilities = [
        ['Basketball Court', 'Full-size indoor basketball court with professional flooring'],
        ['Tennis Court', 'Outdoor tennis court with night lighting'],
        ['Swimming Pool', 'Olympic-size swimming pool with 8 lanes'],
        ['Gymnasium', 'Fully equipped gym with cardio and weight training areas'],
        ['Football Field', 'Standard football field with artificial turf']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO facilities (name, description) VALUES (?, ?)");
    foreach ($facilities as $facility) {
        $stmt->execute($facility);
    }
    
    // Create admin user
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['admin', 'admin@university.edu', $admin_password, 'admin']);
    
    // Create sample student
    $student_password = password_hash('student123', PASSWORD_DEFAULT);
    $stmt->execute(['student', 'student@university.edu', $student_password, 'student']);
    
    echo "System initialized successfully!<br>";
    echo "Admin login: admin@university.edu / admin123<br>";
    echo "Student login: student@university.edu / student123<br>";
    echo "<a href='index.php'>Go to homepage</a>";
    
} catch (PDOException $e) {
    echo "Error initializing system: " . $e->getMessage();
}
?>
