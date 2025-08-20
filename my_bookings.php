<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get user's bookings
$stmt = $pdo->prepare("SELECT b.*, f.name as facility_name FROM bookings b JOIN facilities f ON b.facility_id = f.id WHERE b.user_id = ? ORDER BY b.booking_time DESC");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->execute([$booking_id, $_SESSION['user_id']]);
    
    $success = "Booking cancelled successfully!";
    
    // Refresh bookings
    $stmt = $pdo->prepare("SELECT b.*, f.name as facility_name FROM bookings b JOIN facilities f ON b.facility_id = f.id WHERE b.user_id = ? ORDER BY b.booking_time DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $bookings = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - UniPlay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="responsive.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">UniPlay</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="student_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="booking.php">Book Facilities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="my_bookings.php">My Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="report_issue.php">Report Issue</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>My Bookings</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (empty($bookings)): ?>
            <div class="alert alert-info">You have no bookings yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Facility</th>
                            <th>Booking Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['facility_name']); ?></td>
                            <td><?php echo date('M j, Y H:i', strtotime($booking['booking_time'])); ?></td>
                            <td>
                                <span class="badge bg-<?php echo strtotime($booking['booking_time']) > time() ? 'success' : 'secondary'; ?>">
                                    <?php echo strtotime($booking['booking_time']) > time() ? 'Upcoming' : 'Past'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if (strtotime($booking['booking_time']) > time()): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <button type="submit" name="cancel_booking" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
