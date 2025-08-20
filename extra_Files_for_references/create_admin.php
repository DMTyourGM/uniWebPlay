<?php
// This is a script to create an admin user
// It should be run once and then deleted or secured

include('dbconnect.php');

$admin_username = 'admin';
$admin_password = 'admin123'; // You should change this to a secure password
$admin_email = 'admin@example.com';
$admin_role = 'admin';

// Check if admin user already exists
$check_sql = "SELECT user_id FROM users WHERE username = ? OR email = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ss", $admin_username, $admin_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<p>Admin user already exists.</p>";
} else {
    // Hash the password
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    
    // Insert admin user
    $insert_sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("ssss", $admin_username, $admin_email, $hashed_password, $admin_role);
    
    if ($stmt->execute()) {
        echo "<p>Admin user created successfully.</p>";
        echo "<p>Username: {$admin_username}<br>Password: {$admin_password}</p>";
        echo "<p>Please delete or secure this file after use.</p>";
    } else {
        echo "<p>Error creating admin user: " . $stmt->error . "</p>";
    }
}

$stmt->close();
$conn->close();
?> 