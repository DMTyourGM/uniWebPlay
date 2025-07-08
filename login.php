<?php
// login.php - Handle user login

require_once 'config.php'; // Assuming config.php has DB connection setup

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Please fill all required fields.']);
        exit;
    }

    // Fetch user by username
    $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid username or password.']);
        exit;
    }

    $stmt->bind_result($userId, $hashedPassword);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
        // Start session and set user info
        session_start();
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;

        http_response_code(200);
        echo json_encode(['message' => 'Login successful.']);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid username or password.']);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
}
?>
