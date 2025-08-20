<?php
// login.php - Secure user login handling

require_once 'config.php';
require_once 'csrf.php';
session_start();

// Prevent session fixation
session_regenerate_id(true);

// Generate CSRF token for GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    getCSRFToken();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }
    
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Please fill all required fields.']);
        exit;
    }

    // Rate limiting - check failed attempts
    $ip = $_SERVER['REMOTE_ADDR'];
    $attempt_key = "login_attempts_$ip";
    $attempts = $_SESSION[$attempt_key] ?? 0;
    
    if ($attempts >= 5) {
        http_response_code(429);
        echo json_encode(['error' => 'Too many login attempts. Please try again later.']);
        exit;
    }

    try {
        // Fetch user by username
        $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Increment failed attempts
            $_SESSION[$attempt_key] = $attempts + 1;
            http_response_code(401);
            echo json_encode(['error' => 'Invalid username or password.']);
            exit;
        }

        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password_hash'])) {
            // Reset failed attempts
            unset($_SESSION[$attempt_key]);
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            $_SESSION['logged_in'] = true;
            
            // Update last login time
            $update_stmt = $conn->prepare("UPDATE users SET updated_at = NOW() WHERE id = ?");
            $update_stmt->bind_param("i", $user['id']);
            $update_stmt->execute();
            
            http_response_code(200);
            echo json_encode(['message' => 'Login successful.']);
        } else {
            // Increment failed attempts
            $_SESSION[$attempt_key] = $attempts + 1;
            http_response_code(401);
            echo json_encode(['error' => 'Invalid username or password.']);
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Login failed. Please try again.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
}
?>
