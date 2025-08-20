<?php
require_once 'config.php';

// Check if admin is logged in
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Handle maintenance request updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE maintenance_requests SET status = ? WHERE id = ?");
    $stmt->execute([$status, $request_id]);
    
    $success = "Maintenance request updated successfully!";
}

// Get all maintenance requests with facility details
$stmt = $pdo->prepare("SELECT mr.*, f.name as facility_name FROM maintenance_requests mr JOIN facilities f ON mr.facility_id = f.id ORDER BY mr.id DESC");
$stmt->execute();
$maintenance_requests = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Maintenance - UniPlay Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="responsive.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="admin_dashboard.php">UniPlay Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_manage_facilities.php">Manage Facilities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_view_bookings.php">View Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_manage_maintenance.php">Maintenance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Manage Maintenance Requests</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Facility</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($maintenance_requests as $request): ?>
                    <tr>
                        <td><?php echo $request['id']; ?></td>
                        <td><?php echo htmlspecialchars($request['facility_name']); ?></td>
                        <td><?php echo htmlspecialchars($request['description']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $request['status'] === 'pending' ? 'warning' : 'success'; ?>">
                                <?php echo ucfirst($request['status']); ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                <select name="status" class="form-select form-select-sm d-inline-block w-auto">
                                    <option value="pending" <?php echo $request['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="resolved" <?php echo $request['status'] === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
