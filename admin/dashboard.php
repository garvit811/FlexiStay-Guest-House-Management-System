<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

// Only allow admin
if (!isset($_SESSION['userrole']) || $_SESSION['userrole'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}

// Fetch metrics
function getCount($pdo, $table, $condition = '') {
    $query = "SELECT COUNT(*) FROM $table";
    if ($condition) $query .= " WHERE $condition";
    return $pdo->query($query)->fetchColumn();
}

$total_guesthouses = getCount($pdo, 'guesthouses');
$total_rooms = getCount($pdo, 'rooms');
$total_guests = getCount($pdo, 'users', "role = 'guest'");
$total_clerks = getCount($pdo, 'users', "role = 'clerk'");
$todays_bookings = getCount($pdo, 'bookings', "DATE(checkin) = CURDATE()");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard – FlexiStay</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">
        <ion-icon class="l_icn" name="shield-checkmark-outline"></ion-icon>
        <h1>Admin Panel</h1>
    </div>
    <div class="nav-links">
        <a href="../auth/logout.inc.php" class="nav-btn">Logout</a>
    </div>
</nav>

<div class="guesthouse-listing">
    <h2>Admin Dashboard</h2>

    <div class="guest-house-grid">
        <div class="guest-house-card"><h3>Total Guest Houses</h3><p><?= $total_guesthouses ?></p></div>
        <div class="guest-house-card"><h3>Total Rooms</h3><p><?= $total_rooms ?></p></div>
        <div class="guest-house-card"><h3>Total Users</h3><p><?= $total_guests ?></p></div>
        <div class="guest-house-card total_clerks"h3>Total Clerks</h3><p><?= $total_clerks ?></p></div>
        <div class="guest-house-card"><h3>Today's Bookings</h3><p><?= $todays_bookings ?></p></div>
    </div>

    <h2 style="margin-top:3rem;">Quick Actions</h2>
    <div class="feature_cards">
        <a href="add_guesthouse.php" class="f_card">
            <h3>Add Guesthouse</h3>
            <p>Create a new property and its rooms</p>
        </a>
        <a href="add_clerk.php" class="f_card">
            <h3>Add Clerk</h3>
            <p>Register a new clerk account</p>
        </a>
        <a href="allot_bookings.php" class="f_card">
            <h3>Allot Bookings</h3>
            <p>Manually assign beds to guests</p>
        </a>
        <a href="daily_status.php" class="f_card">
            <h3>Daily Status</h3>
            <p>View all bookings made today</p>
        </a>
        <a href="room_status.php" class="f_card">
            <h3>Occupancy Tracker</h3>
            <p>Monitor and analyze real-time room occupancy</p>
        </a>
        <a href="./manage_guesthouses.php" class="f_card">
            <h3>Manage Guesthouses</h3>
            <p>Easily control your guesthouse operations</p>
        </a>
        <a href="./booking_info.php" class="f_card">
            <h3>Booking Info</h3>
            <p>Track all your bookings</p>
        </a>
    </div>
</div>

<!-- Scripts -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script>
    total_clerks = document.querySelector('.total_clerks');
    total_clerks.addEventListener('click', function() {
        window.location.href = 'view_clerks.php';
    });
</script>
</body>
</html>
