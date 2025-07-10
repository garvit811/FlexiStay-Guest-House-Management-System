<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if (!isset($_SESSION['userid']) || $_SESSION['userrole'] !== 'guest') {
    header("Location: ./login.php");
    exit();
}

$user_id = $_SESSION['userid'];

try {
    $stmt = $pdo->prepare("
        SELECT b.id AS booking_id, gh.name AS guesthouse_name, r.type AS room_type,
               b.booked_beds, b.checkin, b.checkout, b.status
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        JOIN guesthouses gh ON r.guesthouse_id = gh.id
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching bookings: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Bookings – FlexiStay</title>
    <link rel="stylesheet" href="../assets/css/mybookings.css">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <ion-icon class="l_icn" name="home-outline"></ion-icon>
            <h1>FlexiStay</h1>
        </div>
        <div class="nav-links">
            <a href="./dashboard.php" class="nav-btn">Dashboard</a>
            <a href="../auth/logout.inc.php" class="nav-btn">Logout</a>
        </div>
    </nav>

    <!-- Booking Table -->
    <div class="guesthouse-listing">
        <h2>My Bookings</h2>

        <?php if (!empty($bookings)): ?>
            <table style="width: 100%; border-collapse: collapse; margin-top: 2rem;">
                <thead>
                    <tr style="background-color: #e0f7fa;">
                        <th>Guest House</th>
                        <th>Room Type</th>
                        <th>Beds</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                        <tr style="text-align: center; border-bottom: 1px solid #ccc;">
                            <td><?= htmlspecialchars($b['guesthouse_name']) ?></td>
                            <td><?= htmlspecialchars($b['room_type']) ?></td>
                            <td><?= $b['booked_beds'] ?></td>
                            <td><?= $b['checkin'] ?></td>
                            <td><?= $b['checkout'] ?></td>
                            <td><?= ucfirst($b['status']) ?></td>
                            <td>
                                <?php if ($b['status'] === 'pending'): ?>
                                    <form action="./cancel_booking.inc.php" method="post" style="display:inline;" onsubmit="return confirm('Cancel this booking?');">
                                        <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
                                        <button type="submit" class="book-now-btn" style="padding: 4px 10px; font-size: 0.9rem;">Cancel</button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($b['status'] === 'confirmed'): ?>
                                    <a href="./download_invoice.php?booking_id=<?= $b['booking_id'] ?>" target="_blank" class="book-now-btn" style="padding: 4px 10px; font-size: 0.9rem; margin-left: 6px;">
                                        Invoice
                                    </a>
                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-gh-msg">You have not made any bookings yet.</p>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>