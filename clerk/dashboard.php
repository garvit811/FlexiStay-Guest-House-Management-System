<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

// Allow only clerks
if (!isset($_SESSION['userid']) || $_SESSION['userrole'] !== 'clerk') {
    header("Location: ../login.php");
    exit();
}



$user_id = $_SESSION['userid'];
$clerk_stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$clerk_stmt->execute([$user_id]);
$clerk = $clerk_stmt->fetch();

$clerk_name = $clerk ? htmlspecialchars($clerk['name']) : 'Clerk';


$today = date('Y-m-d');

// Fetch today's arrivals
$arrivals_stmt = $pdo->prepare("
    SELECT b.*, u.name AS guest_name, r.type AS room_type, g.name AS guesthouse_name
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN rooms r ON b.room_id = r.id
    JOIN guesthouses g ON r.guesthouse_id = g.id
    WHERE b.checkin = ? AND b.status = 'confirmed'
    ORDER BY b.checkin ASC
");
$arrivals_stmt->execute([$today]);
$today_arrivals = $arrivals_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch pending bookings
$pending_stmt = $pdo->prepare("
    SELECT 
        b.id, b.checkin, b.checkout, b.booked_beds, b.status,
        u.name AS guest_name,
        r.guesthouse_id, r.type AS room_type,
        g.name AS guesthouse_name
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN rooms r ON b.room_id = r.id
    JOIN guesthouses g ON r.guesthouse_id = g.id
    WHERE b.status = 'pending'
    ORDER BY b.checkin ASC
");
$pending_stmt->execute();
$pending_bookings = $pending_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Clerk Dashboard – FlexiStay</title>
    <link rel="stylesheet" href="../assets/css/clerk_dashboard.css">
</head>
<body>

<nav>
    <div><strong>FlexiStay – Clerk Dashboard</strong></div>
    <div>
        <a href="../auth/logout.inc.php">Logout</a>
        <a href="../admin/daily_status.php">Daily Status</a>
    </div>
</nav>

<h1>Welcome, <?= $clerk_name ?>!</h1>


<div class="section">
    <h2>Today's Arrivals (<?= $today ?>)</h2>

    <?php if (empty($today_arrivals)): ?>
        <p>No guests arriving today.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Guest</th>
                <th>Guest House</th>
                <th>Room Type</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Beds</th>
            </tr>
            <?php foreach ($today_arrivals as $arr): ?>
                <tr>
                    <td><?= htmlspecialchars($arr['guest_name']) ?></td>
                    <td><?= htmlspecialchars($arr['guesthouse_name']) ?></td>
                    <td><?= htmlspecialchars($arr['room_type']) ?></td>
                    <td><?= $arr['checkin'] ?></td>
                    <td><?= $arr['checkout'] ?></td>
                    <td><?= $arr['booked_beds'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<div class="section">
    <h2>Pending Bookings</h2>

    <?php if (empty($pending_bookings)): ?>
        <p>No pending booking requests.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Guest</th>
                <th>Guest House</th>
                <th>Room Type</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Requested Beds</th>
                <th>Action</th>
            </tr>
            <?php foreach ($pending_bookings as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['guest_name']) ?></td>
                    <td><?= htmlspecialchars($p['guesthouse_name']) ?></td>
                    <td><?= htmlspecialchars($p['room_type']) ?></td>
                    <td><?= $p['checkin'] ?></td>
                    <td><?= $p['checkout'] ?></td>
                    <td><?= $p['booked_beds'] ?></td>
                    <td>
                        <a href="../admin/allot_beds_form.php?booking_id=<?= $p['id'] ?>&guesthouse_id=<?= $p['guesthouse_id'] ?>" class="btn">Allot Beds</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
