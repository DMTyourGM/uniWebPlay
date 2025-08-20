<?php
require_once 'config.php';

// Check if admin is logged in
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_facility'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        
        $stmt = $pdo->prepare("INSERT INTO facilities (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $description]);
    } elseif (isset($_POST['update_facility'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $available = isset($_POST['available']) ? 1 : 0;
        
        $stmt = $pdo->prepare("UPDATE facilities SET name = ?, description = ?, available = ? WHERE id = ?");
        $stmt->execute([$name, $description, $available, $id]);
    } elseif (isset($_POST['delete_facility'])) {
        $id = $_POST['id'];
        
        $stmt = $pdo->prepare("DELETE FROM facilities WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// Get all facilities
$stmt = $pdo->prepare("SELECT * FROM facilities ORDER BY name");
$stmt->execute();
$facilities = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Facilities - UniPlay Admin</title>
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
                        <a class="nav-link active" href="admin_manage_facilities.php">Manage Facilities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_view_bookings.php">View Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Manage Facilities</h1>
        
        <!-- Add Facility Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Add New Facility</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Facility Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <button type="submit" name="add_facility" class="btn btn-primary">Add Facility</button>
                </form>
            </div>
        </div>

        <!-- Facilities List -->
        <div class="card">
            <div class="card-header">
                <h5>All Facilities</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($facilities as $facility): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($facility['name']); ?></td>
                                <td><?php echo htmlspecialchars($facility['description']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $facility['available'] ? 'success' : 'danger'; ?>">
                                        <?php echo $facility['available'] ? 'Available' : 'Unavailable'; ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $facility['id']; ?>">
                                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($facility['name']); ?>">
                                        <input type="hidden" name="description" value="<?php echo htmlspecialchars($facility['description']); ?>">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" name="available" <?php echo $facility['available'] ? 'checked' : ''; ?> onchange="this.form.submit()">
                                        </div>
                                        <button type="submit" name="update_facility" class="btn btn-sm btn-warning">Update</button>
                                        <button type="submit" name="delete_facility" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
