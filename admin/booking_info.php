<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if (!isset($_SESSION['userid']) || $_SESSION['userrole'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch guesthouses
$guesthouses = $pdo->query("SELECT * FROM guesthouses ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>All Bookings – Admin</title>
    <link rel="stylesheet" href="../assets/css/booking_info.css">
</head>

<body>

    <nav class="navbar">
        <div class="logo">
            <ion-icon name="clipboard-outline"></ion-icon>
            <h1>All Bookings (Admin)</h1>
        </div>
        <div class="nav-links">
            <a href="dashboard.php" class="nav-btn">Back to Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <h2>All Bookings Sorted by Guest House</h2>

        <?php foreach ($guesthouses as $gh): ?>
            <div class="guesthouse-section">
                <h3><?= htmlspecialchars($gh['name']) ?> (<?= htmlspecialchars($gh['location']) ?>)</h3>

                <?php
                $stmt = $pdo->prepare("
                    SELECT 
                    b.*, 
                    u.name, u.email, 
                    r.room_name, r.type, 
                    g.name AS guesthouse_name, g.location 
                    FROM bookings b
                    JOIN users u ON b.user_id = u.id
                    JOIN rooms r ON b.room_id = r.id
                    JOIN guesthouses g ON r.guesthouse_id = g.id
                    WHERE g.id = ?
                    ORDER BY b.checkin DESC
                ");

                $stmt->execute([$gh['id']]);

                $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <?php if (empty($bookings)): ?>
                    <p>No bookings for this guest house.</p>
                <?php else: ?>
                    <table>
                        <tr>
                            <th>Guest Name</th>
                            <th>Email</th>
                            <th>Room Name</th>
                            <th>Booking Date</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Beds Booked</th>
                            <th>Status</th>
                        </tr>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?= htmlspecialchars($booking['name']) ?></td>
                                <td><?= htmlspecialchars($booking['email']) ?></td>
                                <td><?= htmlspecialchars($booking['room_name']) ?></td>
                                <td><?= $booking['created_at'] ?></td>
                                <td><?= $booking['checkin'] ?></td>
                                <td><?= $booking['checkout'] ?></td>
                                <td><?= $booking['booked_beds'] ?></td>
                                <td><?= htmlspecialchars(ucfirst($booking['status'])) ?></td>
                            </tr>

                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
            <hr>
        <?php endforeach; ?>
    </div>

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>