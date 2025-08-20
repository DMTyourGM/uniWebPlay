<?php
require_once 'config.php';

// Check if admin is logged in
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Get dashboard statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as total_bookings FROM bookings WHERE DATE(booking_time) = CURDATE()");
$stmt->execute();
$total_bookings = $stmt->fetch()['total_bookings'];

$stmt = $pdo->prepare("SELECT COUNT(*) as active_facilities FROM facilities WHERE available = 1");
$stmt->execute();
$active_facilities = $stmt->fetch()['active_facilities'];

$stmt = $pdo->prepare("SELECT COUNT(*) as pending_maintenance FROM maintenance_requests WHERE status = 'pending'");
$stmt->execute();
$pending_maintenance = $stmt->fetch()['pending_maintenance'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - UniPlay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="responsive.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">UniPlay Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_manage_facilities.php">Manage Facilities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_view_bookings.php">View Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_manage_maintenance.php">Maintenance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Admin Dashboard</h1>
        <p class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Today's Bookings</h5>
                        <h2><?php echo $total_bookings; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Active Facilities</h5>
                        <h2><?php echo $active_facilities; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Pending Maintenance</h5>
                        <h2><?php echo $pending_maintenance; ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
