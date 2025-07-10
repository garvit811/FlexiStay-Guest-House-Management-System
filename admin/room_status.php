<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if (!isset($_SESSION['userid']) || $_SESSION['userrole'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$data = [];
$date_headers = [];

if ($start_date && $end_date && strtotime($end_date) >= strtotime($start_date)) {
    $period = new DatePeriod(
        new DateTime($start_date),
        new DateInterval('P1D'),
        (new DateTime($end_date))->modify('+1 day')
    );

    foreach ($period as $date) {
        $date_headers[] = $date->format('Y-m-d');
    }

    $gh_stmt = $pdo->query("SELECT * FROM guesthouses");
    $guesthouses = $gh_stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($guesthouses as $gh) {
        $room_stmt = $pdo->prepare("SELECT * FROM rooms WHERE guesthouse_id = ?");
        $room_stmt->execute([$gh['id']]);
        $rooms = $room_stmt->fetchAll(PDO::FETCH_ASSOC);

        $room_list = [];

        foreach ($rooms as $room) {
            for ($i = 1; $i <= $room['total_beds']; $i++) {
                $bed_row = [
                    'room_type' => $room['type'],
                    'bed_no' => $i,
                    'statuses' => []
                ];

                foreach ($date_headers as $d) {
                    $occupied_stmt = $pdo->prepare("
                        SELECT 1 FROM bed_allocation ba
                        JOIN bookings b ON b.id = ba.booking_id
                        WHERE ba.room_id = ? AND ba.bed_no = ?
                        AND NOT (b.checkout < ? OR b.checkin > ?)
                        AND b.status = 'confirmed'
                        LIMIT 1
                    ");
                    $occupied_stmt->execute([$room['id'], $i, $d, $d]);
                    $is_booked = $occupied_stmt->fetchColumn();

                    $bed_row['statuses'][$d] = $is_booked ? 'Booked' : 'Free';
                }

                $room_list[] = $bed_row;
            }
        }

        $gh['beds'] = $room_list;
        $data[] = $gh;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Room Bed Status (Transposed) – Admin</title>
    <link rel="stylesheet" href="../assets/css/room_status.css">
</head>

<body>

    <h1>Room Bed Status (Date-wise Columns)</h1>

    <form method="get">
        <label>Start Date:
            <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>
        </label>
        <label>End Date:
            <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required>
        </label>
        <button type="submit">View Status</button>
    </form>

    <?php if (!empty($data)): ?>
        <?php foreach ($data as $gh): ?>
            <h2>Guest House: <?= htmlspecialchars($gh['name']) ?></h2>

            <?php if (!empty($gh['beds'])): ?>
                <div class="table-scroll-x">

                    <table border="1" cellspacing="0" cellpadding="6">
                        <thead>
                            <tr>
                                <th>Room (Bed)</th>
                                <?php foreach ($date_headers as $date): ?>
                                    <th><?= $date ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($gh['beds'] as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['room_type']) ?> (Bed <?= $row['bed_no'] ?>)</td>
                                    <?php foreach ($date_headers as $date): ?>
                                        <td style="color: <?= $row['statuses'][$date] === 'Booked' ? 'red' : 'green' ?>">
                                            <?= $row['statuses'][$date] ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color: gray;">No rooms available in this guest house.</p>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php elseif ($start_date && $end_date): ?>
        <p>No data available for selected dates.</p>
    <?php endif; ?>

</body>

</html>