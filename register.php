<?php
// register.php - Handle user registration

require_once 'config.php'; // Assuming config.php has DB connection setup

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $profilePhotoPath = null;

    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Please fill all required fields.']);
        exit;
    }

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
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

        if (!in_array($fileExt, $allowedExts)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid profile photo format.']);
            exit;
        }

        $newFileName = uniqid('profile_', true) . '.' . $fileExt;
        $destPath = $uploadDir . $newFileName;

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
        // Create initial user points record for gamification
        $userId = $conn->insert_id;
        $pointsStmt = $conn->prepare("INSERT INTO user_points (user_id, points) VALUES (?, 0)");
        $pointsStmt->bind_param("i", $userId);
        $pointsStmt->execute();
        $pointsStmt->close();
        
        // Return success response
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully! Welcome to the competition!',
            'redirect' => 'home.html'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to register user.']);
    }
    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
}
?>
