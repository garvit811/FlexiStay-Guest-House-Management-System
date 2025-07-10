<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if (!isset($_SESSION['userid']) || $_SESSION['userrole'] !== 'guest') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['gh']) || !is_numeric($_GET['gh'])) {
    die("Invalid guest house.");
}

$gh_id = $_GET['gh'];
$checkinDate = $_GET['checkin'] ?? null;
$checkoutDate = $_GET['checkout'] ?? null;

// Fetch guest house info
try {
    $stmt = $pdo->prepare("SELECT * FROM guesthouses WHERE id = ?");
    $stmt->execute([$gh_id]);
    $guesthouse = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$guesthouse) {
        die("Guest house not found.");
    }

    // Fetch rooms
    $room_stmt = $pdo->prepare("SELECT * FROM rooms WHERE guesthouse_id = ?");
    $room_stmt->execute([$gh_id]);
    $rooms = $room_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Guest House – FlexiStay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/book.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">
        <ion-icon class="l_icn" name="logo-foursquare"></ion-icon>
        <h1>FlexiStay</h1>
    </div>
    <div class="nav-links">
        <a href="dashboard.php" class="nav-btn">Dashboard</a>
        <a href="my_bookings.php" class="nav-btn">My Bookings</a>
        <a href="../auth/logout.inc.php" class="nav-btn">Logout</a>
    </div>
</nav>

<div class="guesthouse-listing">
    <h2>Book Your Stay at <?= htmlspecialchars($guesthouse['name']) ?></h2>

    <!-- Step 1: Ask for dates first -->
    <form method="GET" class="booking-form" style="margin-bottom: 2rem;">
        <input type="hidden" name="gh" value="<?= $gh_id ?>">

        <label for="checkin">Check-in Date:</label>
        <input type="date" name="checkin" id="checkin" value="<?= htmlspecialchars($checkinDate) ?>" required>

        <label for="checkout">Check-out Date:</label>
        <input type="date" name="checkout" id="checkout" value="<?= htmlspecialchars($checkoutDate) ?>" required>

        <button type="submit" class="book-now-btn" style="margin-top: 1rem;">Check Availability</button>
    </form>

    <?php if ($checkinDate && $checkoutDate): ?>
        <form action="./process_booking.inc.php" method="POST" class="booking-form">
            <input type="hidden" name="guesthouse_id" value="<?= $gh_id ?>">
            <input type="hidden" name="checkin" value="<?= $checkinDate ?>">
            <input type="hidden" name="checkout" value="<?= $checkoutDate ?>">

            <h3>Select Rooms and Beds:</h3>

            <?php if (!empty($rooms)): ?>
                <?php foreach ($rooms as $room): ?>
                    <?php
                    // Check for beds booked during overlapping dates
                    $stmt = $pdo->prepare("
                        SELECT COALESCE(SUM(booked_beds), 0) AS total_booked 
                        FROM bookings 
                        WHERE room_id = ? 
                        AND status IN ('pending', 'confirmed')
                        AND NOT (checkout <= ? OR checkin >= ?)
                    ");
                    $stmt->execute([$room['id'], $checkinDate, $checkoutDate]);
                    $booked = $stmt->fetchColumn();
                    $available = $room['total_beds'] - $booked;
                    ?>
                    <div style="margin: 15px 0; padding: 15px; border: 1px solid #ccc; border-radius: 10px;">
                        <h4><?= htmlspecialchars($room['type']) ?> – ₹<?= $room['price'] ?> per bed</h4>
                        <p>Available Beds: <?= $available ?></p>

                        <?php if ($available > 0): ?>
                            <label for="beds_<?= $room['id'] ?>">Number of Beds to Book:</label>
                            <select name="beds[<?= $room['id'] ?>]" id="beds_<?= $room['id'] ?>">
                                <option value="0">0</option>
                                <?php for ($i = 1; $i <= $available; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        <?php else: ?>
                            <p style="color: red;">Fully booked</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No rooms available.</p>
            <?php endif; ?>

            <button type="submit" class="book-now-btn" style="margin-top: 1.5rem;">Submit Booking</button>
        </form>
    <?php endif; ?>
</div>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
