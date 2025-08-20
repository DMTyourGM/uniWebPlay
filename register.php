<?php
// register.php - Secure user registration handling

require_once 'config.php';
require_once 'csrf.php';
session_start();

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
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $profilePhotoPath = null;

    // Validate inputs
    $errors = [];
    
    if (empty($username)) {
        $errors[] = 'Username is required.';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = 'Username must be between 3 and 50 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores.';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['error' => implode(' ', $errors)]);
        exit;
    }

    try {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            http_response_code(409);
            echo json_encode(['error' => 'Username or email already exists.']);
            exit;
        }
        $stmt->close();

        // Handle profile photo upload if provided
        if (isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/profile_photos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileTmpPath = $_FILES['profilePhoto']['tmp_name'];
            $fileName = basename($_FILES['profilePhoto']['name']);
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
            
            // Validate file extension
            if (!in_array($fileExt, $allowedExts)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid file format. Only JPG, JPEG, PNG, and GIF files are allowed.']);
                exit;
            }
            
            // Validate file size (max 2MB)
            if ($_FILES['profilePhoto']['size'] > 2 * 1024 * 1024) {
                http_response_code(400);
                echo json_encode(['error' => 'File size too large. Maximum 2MB allowed.']);
                exit;
            }
            
            // Generate unique filename
            $newFileName = uniqid('profile_', true) . '.' . $fileExt;
            $destPath = $uploadDir . $newFileName;
            
            // Move uploaded file
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $profilePhotoPath = $destPath;
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to upload profile photo.']);
                exit;
            }
        }

        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, profile_photo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $passwordHash, $profilePhotoPath);
        
        if ($stmt->execute()) {
            $userId = $conn->insert_id;
            
            // Create initial user points record for gamification
            $pointsStmt = $conn->prepare("INSERT INTO user_points (user_id, points) VALUES (?, 0)");
            $pointsStmt->bind_param("i", $userId);
            $pointsStmt->execute();
            $pointsStmt->close();
            
            // Auto-login after registration
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['logged_in'] = true;
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Account created successfully! Welcome to the competition!',
                'redirect' => 'home.html'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to register user. Please try again.']);
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Registration failed. Please try again.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
}
?>
