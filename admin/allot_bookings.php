<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

// Allow only admin or clerk
if (!isset($_SESSION['userrole']) || !in_array($_SESSION['userrole'], ['admin', 'clerk'])) {
    header("Location: ../login.php");
    exit();
}

$stmt = $pdo->prepare("
    SELECT b.*, u.name AS guest_name, r.guesthouse_id, r.type AS room_type
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN rooms r ON b.room_id = r.id
    WHERE b.status = 'pending'
    ORDER BY b.checkin ASC
");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Allot Bookings – FlexiStay</title>
    <link rel="stylesheet" href="../assets/css/allot_bookings.css">
</head>

<body>
    <h1>Pending Booking Requests</h1>

    <?php if (empty($bookings)): ?>
        <p>No pending bookings.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Guest</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Room Type</th>
                <th>Requested Beds</th>
                <th>Action</th>
            </tr>
            <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['guest_name']) ?></td>
                    <td><?= $b['checkin'] ?></td>
                    <td><?= $b['checkout'] ?></td>
                    <td><?= htmlspecialchars($b['room_type']) ?></td> <!-- NEW -->
                    <td><?= $b['booked_beds'] ?></td>
                    <td>
                        <a href="allot_beds_form.php?booking_id=<?= $b['id'] ?>&guesthouse_id=<?= $b['guesthouse_id'] ?>" class="btn">Allot Beds</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

    <?php endif; ?>
</body>

</html>