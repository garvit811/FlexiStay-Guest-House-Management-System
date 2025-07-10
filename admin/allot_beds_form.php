<?php
require_once '../includes/dbh.inc.php';

$booking_id = $_GET['booking_id'];
$guesthouse_id = $_GET['guesthouse_id'];

$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();

if (!$booking) {
    die("Booking not found");
}

// Fetch available beds from rooms in that guesthouse
$availableBeds = [];

$rooms = $pdo->prepare("SELECT * FROM rooms WHERE guesthouse_id = ?");
$rooms->execute([$guesthouse_id]);
$rooms = $rooms->fetchAll();

foreach ($rooms as $room) {
    $room_id = $room['id'];
    $beds = range(1, $room['total_beds']);

    // Fetch already allocated beds for these dates
    $occupiedStmt = $pdo->prepare("
        SELECT bed_no FROM bed_allocation ba
        JOIN bookings b ON b.id = ba.booking_id
        WHERE ba.room_id = ? AND NOT (b.checkout <= ? OR b.checkin >= ?)
    ");
    $occupiedStmt->execute([$room_id, $booking['checkin'], $booking['checkout']]);
    $occupied = $occupiedStmt->fetchAll(PDO::FETCH_COLUMN);

    $freeBeds = array_diff($beds, $occupied);
    if (!empty($freeBeds)) {
        $availableBeds[] = [
            'room_id' => $room_id,
            'room_name' => $room['type'],
            'beds' => $freeBeds
        ];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Allot Beds – FlexiStay</title>
    <link rel="stylesheet" href="../assets/css/allot_beds.css">
</head>
<body>
    <h1>Allot Beds for Booking #<?= $booking_id ?></h1>
    <p>Check-in: <?= $booking['checkin'] ?> | Check-out: <?= $booking['checkout'] ?> | Requested Beds: <?= $booking['booked_beds'] ?></p>

    <form action="process_allotment.php" method="POST">
        <input type="hidden" name="booking_id" value="<?= $booking_id ?>">

        <?php foreach ($availableBeds as $room): ?>
            <fieldset>
                <legend><?= htmlspecialchars($room['room_name']) ?></legend>
                <?php foreach ($room['beds'] as $bed): ?>
                    <label>
                        <input type="checkbox" name="beds[]" value="<?= $room['room_id'] ?>-<?= $bed ?>">
                        Bed <?= $bed ?>
                    </label>
                <?php endforeach; ?>
            </fieldset>
        <?php endforeach; ?>

        <button type="submit">Confirm Allotment</button>
    </form>
</body>
</html>
