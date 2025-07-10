<?php
require_once '../includes/dbh.inc.php';
require_once '../includes/config_session.inc.php';

if (!isset($_SESSION['userrole']) || !in_array($_SESSION['userrole'], ['admin', 'clerk'])) {
    header("Location: ../login.php");
    exit();
}

$date = $_GET['date'] ?? date('Y-m-d');
$guesthouse_id = $_GET['guesthouse_id'] ?? null;

// Fetch guest houses
$gh_stmt = $pdo->query("SELECT id, name FROM guesthouses");
$guesthouses = $gh_stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare bed status data only if guesthouse selected
$room_status = [];
if ($guesthouse_id) {
    $rooms = $pdo->prepare("SELECT * FROM rooms WHERE guesthouse_id = ?");
    $rooms->execute([$guesthouse_id]);
    $rooms = $rooms->fetchAll();

    foreach ($rooms as $room) {
        $total_beds = $room['total_beds'];
        $occupiedStmt = $pdo->prepare("
            SELECT bed_no FROM bed_allocation ba
            JOIN bookings b ON ba.booking_id = b.id
            WHERE ba.room_id = ? AND ? BETWEEN b.checkin AND DATE_SUB(b.checkout, INTERVAL 1 DAY)
        ");
        $occupiedStmt->execute([$room['id'], $date]);
        $occupied_beds = $occupiedStmt->fetchAll(PDO::FETCH_COLUMN);

        $bed_statuses = [];
        for ($i = 1; $i <= $total_beds; $i++) {
            $bed_statuses[] = [
                'bed_no' => $i,
                'status' => in_array($i, $occupied_beds) ? 'Booked' : 'Available'
            ];
        }

        $room_status[] = [
            'room_type' => $room['type'],
            'room_id' => $room['id'],
            'beds' => $bed_statuses
        ];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daily Room & Bed Status</title>
    <link rel="stylesheet" href="../assets/css/status.css">
</head>
<body>
    <h1>Daily Guest House Status</h1>

    <form method="GET">
        <label>Select Date: <input type="date" name="date" value="<?= htmlspecialchars($date) ?>"></label>
        <label>Select Guest House:
            <select name="guesthouse_id">
                <option value="">-- Choose --</option>
                <?php foreach ($guesthouses as $gh): ?>
                    <option value="<?= $gh['id'] ?>" <?= $gh['id'] == $guesthouse_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($gh['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">View Status</button>
    </form>

    <?php if ($guesthouse_id): ?>
        <h2>Status for <?= $date ?></h2>
        <?php if (empty($room_status)): ?>
            <p>No rooms available in this guest house.</p>
        <?php else: ?>
            <?php foreach ($room_status as $room): ?>
                <fieldset>
                    <legend><?= htmlspecialchars($room['room_type']) ?></legend>
                    <table>
                        <tr>
                            <th>Bed No</th>
                            <th>Status</th>
                        </tr>
                        <?php foreach ($room['beds'] as $bed): ?>
                            <tr>
                                <td>Bed <?= $bed['bed_no'] ?></td>
                                <td style="color: <?= $bed['status'] === 'Booked' ? 'red' : 'green' ?>">
                                    <?= $bed['status'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </fieldset>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
