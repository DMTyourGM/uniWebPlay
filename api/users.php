<?php
// api/users.php - RESTful API for user management with JWT authentication

header('Content-Type: application/json');
require_once '../config.php';
require_once '../vendor/autoload.php'; // For JWT library, if used

use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];

// Load JWT secret key from config
$jwt_secret = 'your_jwt_secret_key_here';

// Helper function to get bearer token from Authorization header
function getBearerToken() {
    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
        return null;
    }
    $matches = [];
    if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
        return $matches[1];
    }
    return null;
}

// Authenticate request using JWT
function authenticate() {
    global $jwt_secret;
    $token = getBearerToken();
    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized: No token provided']);
        exit;
    }
    try {
        $decoded = JWT::decode($token, $jwt_secret, array('HS256'));
        return $decoded->user_id;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized: Invalid token']);
        exit;
    }
}

// Connect to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

switch ($method) {
    case 'GET':
        // Get user info
        $user_id = authenticate();
        $stmt = $conn->prepare("SELECT id, username, email, profile_photo FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            exit;
        }
        $user = $result->fetch_assoc();
        echo json_encode(['user' => $user]);
        break;

    case 'POST':
        // Create new user (registration)
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['username'], $data['email'], $data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }
        $username = $data['username'];
        $email = $data['email'];
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password_hash);
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(['message' => 'User created']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create user']);
        }
        break;

    case 'PUT':
        // Update user info
        $user_id = authenticate();
        $data = json_decode(file_get_contents('php://input'), true);
        $fields = [];
        $params = [];
        $types = '';

        if (isset($data['username'])) {
            $fields[] = 'username = ?';
            $params[] = $data['username'];
            $types .= 's';
        }
        if (isset($data['email'])) {
            $fields[] = 'email = ?';
            $params[] = $data['email'];
            $types .= 's';
        }
        if (isset($data['password'])) {
            $fields[] = 'password_hash = ?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
            $types .= 's';
        }
        if (empty($fields)) {
            http_response_code(400);
            echo json_encode(['error' => 'No fields to update']);
            exit;
        }
        $params[] = $user_id;
        $types .= 'i';

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            echo json_encode(['message' => 'User updated']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update user']);
        }
        break;

    case 'DELETE':
        // Delete user
        $user_id = authenticate();
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            echo json_encode(['message' => 'User deleted']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete user']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

$conn->close();
?>
