<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ob_start();
session_start();

include 'dbconnect.php';

$registrationSuccess = false;
$registrationError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $usern = trim($_POST['username']);
    $passw = $_POST['pwsd'];
    $confirm_passw = $_POST['confirmpassword'];
    $email = $_POST['emailid'];
    $role = 'member';

    try {
        if ($passw !== $confirm_passw) {
            $registrationError = "Passwords do not match.";
        } elseif (strlen($passw) < 3) {
            $registrationError = "Password must be at least 3 characters long.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $registrationError = "Invalid email format.";
        } else {
            $check_sql = "SELECT user_id FROM users WHERE username = ? OR email = ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("ss", $usern, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $registrationError = "Username or email already exists. Please choose a different one.";
            } else {
                $hashed_password = password_hash($passw, PASSWORD_DEFAULT);
                $insert_sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("ssss", $usern, $email, $hashed_password, $role);

                if ($stmt->execute()) {
                    $registrationSuccess = true;
                    $_SESSION['user_id'] = $conn->insert_id;
                    $_SESSION['username'] = $usern;

                    if (isset($_SESSION['intended_plan'])) {
                        $intended_plan = $_SESSION['intended_plan'];
                        unset($_SESSION['intended_plan']);
                        header("Location: membership_payment.php?plan=" . urlencode($intended_plan));
                        exit();
                    } else {
                        header("Location: ../index.php");
                        exit();
                    }
                } else {
                    $registrationError = "Error during registration: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    } catch (mysqli_sql_exception $e) {
        error_log("MySQLi error: " . $e->getMessage());
        $registrationError = "Database error occurred. Please try again later.";
    }
}

$conn->close();
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register | FitZone</title>
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

        .register-card {
            background: var(--darker-bg);
            border-radius: 8px;
            padding: 30px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            border: 1px solid #444;
            max-width: 500px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
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
            padding-top: 20px;
            border-top: 1px solid #444;
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

            .register-card {
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
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php" class="active">Register</a></li>
            <li><a href="classes.php">Classes</a></li>
            <li><a href="trainers.php">Trainers</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="register-card">
            <h2 style="text-align: center; margin-bottom: 30px;">Create Your Account</h2>

            <?php if ($registrationSuccess): ?>
                <div class="status-message success">
                    Registration successful! Redirecting...
                </div>
            <?php endif; ?>

            <?php if ($registrationError): ?>
                <div class="status-message error"><?php echo htmlspecialchars($registrationError); ?></div>
            <?php endif; ?>

            <form name="myform" method="post" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required>
                </div>

                <div class="form-group">
                    <label for="emailid">Email</label>
                    <input type="email" name="emailid" id="emailid" required>
                </div>

                <div class="form-group">
                    <label for="pwsd">Password (min 3 characters)</label>
                    <input type="password" name="pwsd" id="pwsd" minlength="3" required>
                </div>

                <div class="form-group">
                    <label for="confirmpassword">Confirm Password</label>
                    <input type="password" name="confirmpassword" id="confirmpassword" minlength="3" required>
                </div>

                <button type="submit" name="signup" class="btn">Register Now</button>

                <div class="auth-links">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                    <p>Admin or Staff? <a href="admin_staff_login.php">Login here</a></p>
                    <p>Need to register as an admin? <a href="admin/admin_register.php">Register Admin</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Admin Registration Section -->
    <div class="container" style="margin-top: 30px;">
        <div class="register-card" style="text-align: center; background: rgba(44, 44, 44, 0.7); border: 1px solid var(--primary-color);">
            <h2 style="margin-bottom: 20px;">Administrator Registration</h2>
            <p style="margin-bottom: 20px;">If you need to register as an administrator, click the button below:</p>
            <a href="admin/admin_register.php" class="btn" style="display: inline-block; width: auto; padding: 12px 30px; margin: 0 auto;">Register Admin Account</a>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> FitZone. All rights reserved.</p>
    </footer>
</body>
</html>
