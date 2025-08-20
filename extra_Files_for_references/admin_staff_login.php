<?php
session_start();
include('dbconnect.php');

$login_error_message = "";

// Check if any admin exists
$admin_exists = false;
$check_admin = $conn->query("SELECT user_id FROM users WHERE role = 'admin' LIMIT 1");
if ($check_admin && $check_admin->num_rows > 0) {
    $admin_exists = true;
}

if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $user_type = $_POST["user_type"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND role=?");
    $stmt->bind_param("ss", $username, $user_type);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        if ($row['role'] == 'admin') {
            header("Location: admin/dashboard.php");
            exit();
        } elseif ($row['role'] == 'staff') {
            header("Location: staff/staff_page.php");
            exit();
        }
    } else {
        $login_error_message = "Invalid username, password, or user role.";
    }

    $stmt->close();
}

// Admin Registration Logic
$reg_success = false;
$reg_error = '';

if (isset($_POST["register_admin"]) && !$admin_exists) {
    $reg_username = trim($_POST["reg_username"]);
    $reg_email = trim($_POST["reg_email"]);
    $reg_password = $_POST["reg_password"];
    $reg_confirm = $_POST["reg_confirm_password"];

    // Validation
    if ($reg_password !== $reg_confirm) {
        $reg_error = "Passwords do not match.";
    } elseif (strlen($reg_password) < 6) {
        $reg_error = "Password must be at least 6 characters long.";
    } elseif (!filter_var($reg_email, FILTER_VALIDATE_EMAIL)) {
        $reg_error = "Invalid email format.";
    } else {
        // Check if username or email already exists
        $check_sql = "SELECT user_id FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ss", $reg_username, $reg_email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $reg_error = "Username or email already exists.";
        } else {
            // Create the new admin account
            $hashed_password = password_hash($reg_password, PASSWORD_DEFAULT);
            $admin_role = 'admin';
            
            $insert_sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ssss", $reg_username, $reg_email, $hashed_password, $admin_role);
            
            if ($stmt->execute()) {
                $reg_success = true;
                $admin_exists = true;
            } else {
                $reg_error = "Error creating account: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin & Staff Login | FitZone</title>
    <style>
        :root {
            --primary-color: #ff4d4d;
            --secondary-color: #ff8533;
            --dark-bg: #1a1a1a;
            --darker-bg: #2c2c2c;
            --text-color: #ffffff;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: var(--dark-bg);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 500px;
            padding: 40px;
            background: var(--darker-bg);
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid #444;
            margin-bottom: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
            font-size: 2rem;
        }

        .error-message, .success-message {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .error-message {
            color: #ff6b6b;
            background: rgba(255, 0, 0, 0.1);
        }

        .success-message {
            color: #4caf50;
            background: rgba(0, 200, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        select {
            width: 100%;
            padding: 14px;
            margin-bottom: 20px;
            border: 2px solid #444;
            border-radius: 6px;
            background: var(--dark-bg);
            color: var(--text-color);
            font-size: 16px;
            transition: var(--transition);
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(255, 69, 0, 0.3);
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        .btn:hover {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            transform: translateY(-2px);
        }

        .links {
            text-align: center;
            margin-top: 20px;
        }

        .links a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .links a:hover {
            text-decoration: underline;
        }

        .toggle-form {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #444;
        }

        .toggle-form a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: bold;
        }

        .toggle-form a:hover {
            text-decoration: underline;
        }

        .form-section {
            display: none;
        }

        .active-form {
            display: block;
        }

        @media (max-width: 600px) {
            .login-container {
                padding: 30px 20px;
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div id="login-form" class="form-section active-form">
            <h2>Admin & Staff Login</h2>
            <?php if (!empty($login_error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($login_error_message); ?></p>
            <?php endif; ?>
            
            <?php if ($reg_success || isset($_SESSION['success_message'])): ?>
                <p class="success-message">
                    <?php 
                        echo isset($_SESSION['success_message']) ? htmlspecialchars($_SESSION['success_message']) : "Admin account created successfully! You can now login.";
                        unset($_SESSION['success_message']);
                    ?>
                </p>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['admin_register_message'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($_SESSION['admin_register_message']); unset($_SESSION['admin_register_message']); ?></p>
            <?php endif; ?>
            
            <form action="" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="user_type">Login As:</label>
                <select name="user_type" id="user_type" required>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                </select>

                <button type="submit" name="login" class="btn">Login</button>

                <div class="links">
                    <p><a href="../index.php">Back to Home</a></p>
                </div>
                
                <?php if (!$admin_exists): ?>
                <div class="toggle-form">
                    <p>No admin account exists. <a href="#" id="show-register">Register First Admin</a></p>
                </div>
                <?php endif; ?>
            </form>
        </div>
        
        <div id="register-form" class="form-section">
            <h2>Register Admin Account</h2>
            <?php if (!empty($reg_error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($reg_error); ?></p>
            <?php endif; ?>
            
            <form action="" method="POST">
                <label for="reg_username">Username:</label>
                <input type="text" id="reg_username" name="reg_username" required>
                
                <label for="reg_email">Email:</label>
                <input type="email" id="reg_email" name="reg_email" required>
                
                <label for="reg_password">Password:</label>
                <input type="password" id="reg_password" name="reg_password" required minlength="6">
                
                <label for="reg_confirm_password">Confirm Password:</label>
                <input type="password" id="reg_confirm_password" name="reg_confirm_password" required minlength="6">
                
                <button type="submit" name="register_admin" class="btn">Register Admin</button>
                
                <div class="toggle-form">
                    <p>Already have an account? <a href="#" id="show-login">Login</a></p>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const showRegister = document.getElementById('show-register');
            const showLogin = document.getElementById('show-login');
            
            if (showRegister) {
                showRegister.addEventListener('click', function(e) {
                    e.preventDefault();
                    loginForm.classList.remove('active-form');
                    registerForm.classList.add('active-form');
                });
            }
            
            if (showLogin) {
                showLogin.addEventListener('click', function(e) {
                    e.preventDefault();
                    registerForm.classList.remove('active-form');
                    loginForm.classList.add('active-form');
                });
            }
        });
    </script>
</body>
</html>
