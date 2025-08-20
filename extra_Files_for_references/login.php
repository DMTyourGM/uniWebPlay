<?php
session_start();
include('dbconnect.php');

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Store redirect parameter if available
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';

$login_error_message = "";
$login_success = false;

if (isset($_POST["log"])) {
    $usern = trim($_POST["username"]);
    $passw = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $login_error_message = "Database error: Could not prepare statement.";
    } else {
        $stmt->bind_param("s", $usern);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
                if (password_verify($passw, $row['password'])) {
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['role'] = $row['role'];
                    $login_success = true;
                    
                    if (!empty($redirect)) {
                        header("Location: " . $redirect);
                    } else {
                        header("Location: ../index.php");
                    }
                    exit();
                } else {
                    $login_error_message = "Wrong username or password.";
                }
        } else {
            $login_error_message = "Wrong username or password.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FitZone</title>
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
            flex-direction: column;
            line-height: 1.6;
        }

        header {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            color: var(--text-color);
            padding: 40px 0;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            border-bottom: 2px solid var(--primary-color);
        }

        header h1 {
            margin: 0;
            font-size: 3em;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        nav {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            padding: 15px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        nav li {
            margin: 0 15px;
        }

        nav a {
            color: var(--text-color);
            text-decoration: none;
            font-weight: bold;
            padding: 8px 15px;
            border-radius: 4px;
            transition: var(--transition);
        }

        nav a:hover, nav a.active {
            background: var(--primary-color);
            color: var(--text-color);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            flex: 1;
        }

        .login-card {
            max-width: 500px;
            margin: 0 auto;
            background: var(--darker-bg);
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            border: 1px solid #444;
        }

        .text-center {
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #444;
            border-radius: 6px;
            background: var(--darker-bg);
            color: var(--text-color);
            transition: var(--transition);
        }

        input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(255, 69, 0, 0.3);
        }

        .btn {
            display: block;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn:hover {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            transform: translateY(-2px);
        }

        .status-message {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }

        .error {
            background: #8b0000;
            color: white;
        }

        .success {
            background: #006400;
            color: white;
        }

        .auth-links {
            margin-top: 20px;
            text-align: center;
        }

        .auth-links a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .auth-links a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        footer {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            color: var(--text-color);
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            border-top: 2px solid var(--primary-color);
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 2em;
            }

            nav ul {
                flex-direction: column;
                align-items: center;
            }

            nav li {
                margin: 5px 0;
            }

            .login-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>FitZone</h1>
        <p>Your Ultimate Fitness Destination</p>
    </header>
    <nav>
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="login.php" class="active">Login</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="classes.php">Classes</a></li>
            <li><a href="trainers.php">Trainers</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="login-card">
            <h2 class="text-center">Login to FitZone</h2>
            
            <?php if ($login_success): ?>
                <div class="status-message success">
                    Login successful! Redirecting...
                </div>
                <script>
                    setTimeout(function() {
                        <?php if (!empty($redirect)): ?>
                            window.location.href = "<?php echo htmlspecialchars($redirect); ?>.php";
                        <?php else: ?>
                            window.location.href = "../index.php";
                        <?php endif; ?>
                    }, 1500);
                </script>
            <?php elseif (!empty($login_error_message)): ?>
                <div class="status-message error"><?php echo htmlspecialchars($login_error_message); ?></div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" name="log" class="btn">Login</button>

                <div class="auth-links">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                    <p>Admin or Staff? <a href="admin_staff_login.php">Login here</a></p>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> FitZone. All rights reserved.</p>
    </footer>
</body>
</html>
