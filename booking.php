<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get all facilities
$stmt = $pdo->prepare("SELECT * FROM facilities WHERE available = 1 ORDER BY name");
$stmt->execute();
$facilities = $stmt->fetchAll();

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_facility'])) {
    $facility_id = $_POST['facility_id'];
    $booking_time = $_POST['booking_time'];
    
    // Check for double booking
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE facility_id = ? AND booking_time = ?");
    $stmt->execute([$facility_id, $booking_time]);
    $existing_bookings = $stmt->fetchColumn();
    
    if ($existing_bookings > 0) {
        $error = "This time slot is already booked. Please choose another time.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, facility_id, booking_time) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $facility_id, $booking_time]);
        $success = "Booking confirmed successfully!";
    }
}

// Get user's bookings
$stmt = $pdo->prepare("SELECT b.*, f.name as facility_name FROM bookings b JOIN facilities f ON b.facility_id = f.id WHERE b.user_id = ? ORDER BY b.booking_time DESC");
$stmt->execute([$_SESSION['user_id']]);
$user_bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Facilities - UniPlay</title>
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
                        <a class="nav-link active" href="booking.php">Book Facilities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_bookings.php">My Bookings</a>
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
        <h1>Book a Facility</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Available Facilities</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="facility_id" class="form-label">Select Facility</label>
                                <select class="form-select" id="facility_id" name="facility_id" required>
                                    <option value="">Choose a facility...</option>
                                    <?php foreach ($facilities as $facility): ?>
                                        <option value="<?php echo $facility['id']; ?>">
                                            <?php echo htmlspecialchars($facility['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="booking_time" class="form-label">Booking Time</label>
                                <input type="datetime-local" class="form-control" id="booking_time" name="booking_time" required>
                            </div>
                            
                            <button type="submit" name="book_facility" class="btn btn-primary">Book Facility</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Your Bookings</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($user_bookings)): ?>
                            <p>No bookings yet.</p>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($user_bookings as $booking): ?>
                                    <div class="list-group-item">
                                        <h6><?php echo htmlspecialchars($booking['facility_name']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y H:i', strtotime($booking['booking_time'])); ?>
                                        </small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
