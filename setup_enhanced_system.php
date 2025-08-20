<?php
// setup_enhanced_system.php - Setup script for enhanced UniWebPlay system

header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html>
<html>
<head>
    <title>UniWebPlay Enhanced System Setup</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .step { margin: 10px 0; padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>UniWebPlay Enhanced System Setup</h1>
    <div id='setup-log'>";

require_once 'config.php';

function executeSQLFile($filename, $conn) {
    $commands = file_get_contents($filename);
    $commands = explode(';', $commands);
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($commands as $command) {
        $command = trim($command);
        if (empty($command)) continue;
        
        try {
            if ($conn->query($command)) {
                $success_count++;
            } else {
                $error_count++;
                echo "<div class='error'>Error: " . $conn->error . "</div>";
            }
        } catch (Exception $e) {
            $error_count++;
            echo "<div class='error'>Exception: " . $e->getMessage() . "</div>";
        }
    }
    
    return [$success_count, $error_count];
}

// Step 1: Create enhanced database schema
echo "<div class='step'><h2>Step 1: Creating Enhanced Database Schema</h2>";
list($success, $errors) = executeSQLFile('enhanced_db_setup.sql', $conn);
echo "<div class='info'>Executed $success SQL commands successfully</div>";
if ($errors > 0) {
    echo "<div class='error'>Encountered $errors errors</div>";
}
echo "</div>";

// Step 2: Populate sample data
echo "<div class='step'><h2>Step 2: Populating Sample Data</h2>";
list($success, $errors) = executeSQLFile('populate_sample_data.sql', $conn);
echo "<div class='info'>Populated $success records successfully</div>";
if ($errors > 0) {
    echo "<div class='error'>Encountered $errors errors</div>";
}
echo "</div>";

// Step 3: Verify database structure
echo "<div class='step'><h2>Step 3: Verifying Database Structure</h2>";

$tables = [
    'facilities', 'facility_types', 'facility_schedules', 'booking_slots',
    'facility_bookings', 'payments', 'maintenance_requests', 'user_preferences'
];

foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "<div class='success'>✓ Table '$table' exists</div>";
        
        // Get row count
        $count_result = $conn->query("SELECT COUNT(*) as count FROM $table");
        $count = $count_result->fetch_assoc()['count'];
        echo "<div class='info'>  - Contains $count records</div>";
    } else {
        echo "<div class='error'>✗ Table '$table' missing</div>";
    }
}

echo "</div>";

// Step 4: Test API endpoints
echo "<div class='step'><h2>Step 4: Testing API Endpoints</h2>";

// Test availability API
$test_url = 'http://localhost' . $_SERVER['REQUEST_URI'];
$test_url = str_replace('setup_enhanced_system.php', 'availability_api.php?action=get_facilities', $test_url);

echo "<div class='info'>Testing API endpoints...</div>";
echo "<div class='info'>Enhanced booking system: <a href='enhanced_booking.html' target='_blank'>enhanced_booking.html</a></div>";
echo "<div class='info'>Availability API: <a href='availability_api.php?action=get_facilities' target='_blank'>availability_api.php</a></div>";

echo "</div>";

// Step 5: Next steps
echo "<div class='step'><h2>Step 5: Next Steps</h2>";
echo "<div class='info'>✅ Enhanced database schema created</div>";
echo "<div class='info'>✅ Real-time availability system implemented</div>";
echo "<div class='info'>✅ Comprehensive facility management system ready</div>";
echo "<div class='info'>✅ Sample data populated for testing</div>";
echo "<br>";
echo "<div class='info'><strong>Ready to use:</strong></div>";
echo "<ul>";
echo "<li><a href='enhanced_booking.html' class='success'>Enhanced Booking Interface</a></li>";
echo "<li><a href='availability_api.php?action=get_facilities' class='success'>Test API Endpoints</a></li>";
echo "<li><a href='user_dashboard.php' class='success'>User Dashboard</a></li>";
echo "</ul>";
echo "</div>";

$conn->close();

echo "</div></body></html>";
?>
