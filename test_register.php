<?php
// test_register.php - Test user registration

require_once 'config.php';
require_once 'csrf.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

getCSRFToken(); // Generate CSRF token
$_POST = [
    'username' => 'testuser',
    'email' => 'testuser@example.com',
    'password' => 'TestPassword123',
    'confirm_password' => 'TestPassword123',
    'csrf_token' => $_SESSION['csrf_token']
];

$_SERVER['REQUEST_METHOD'] = 'POST'; // Simulate a POST request
// Include the registration logic
require 'register.php';
?>
