<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out | FitZone</title>
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
            text-align: center;
        }

        .logout-container {
            background: var(--darker-bg);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid #444;
            width: 400px;
            max-width: 90%;
        }

        h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        p {
            font-size: 1.1em;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .home-link {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: var(--transition);
        }

        .home-link:hover {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 480px) {
            .logout-container {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <h2>You Have Been Logged Out</h2>
        <p>Thank you for visiting FitZone. We hope to see you again soon!</p>
        <a href="../index.php" class="home-link">Back to FitZone Home</a>
    </div>
</body>
</html>
