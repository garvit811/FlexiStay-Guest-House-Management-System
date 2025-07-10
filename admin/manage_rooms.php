<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if (!isset($_SESSION['userid']) || $_SESSION['userrole'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Validate guesthouse ID
if (!isset($_GET['gh_id']) || !is_numeric($_GET['gh_id'])) {
    die("Invalid guesthouse ID.");
}

$gh_id = (int) $_GET['gh_id'];

// Fetch guesthouse details
$gh_stmt = $pdo->prepare("SELECT * FROM guesthouses WHERE id = ?");
$gh_stmt->execute([$gh_id]);
$guesthouse = $gh_stmt->fetch(PDO::FETCH_ASSOC);

if (!$guesthouse) {
    die("Guesthouse not found.");
}

// Handle room addition
if (isset($_POST['add_room'])) {
    $r_name = $_POST['r_name'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $beds = $_POST['total_beds'];

    if ($type && is_numeric($price) && is_numeric($beds)) {
        $insert = $pdo->prepare("INSERT INTO rooms (guesthouse_id, room_name, type, price, total_beds) VALUES (?, ?, ?, ?, ?)");
        $insert->execute([$gh_id, $r_name, $type, $price, $beds]);
    }
}

// Handle room update
if (isset($_POST['update_room'])) {
    $room_id = $_POST['room_id'];
    $price = $_POST['price'];
    $beds = $_POST['total_beds'];

    if (is_numeric($price) && is_numeric($beds)) {
        $update = $pdo->prepare("UPDATE rooms SET price = ?, total_beds = ? WHERE id = ? AND guesthouse_id = ?");
        $update->execute([$price, $beds, $room_id, $gh_id]);
    }
}

// Handle room deletion
if (isset($_POST['delete_room'])) {
    $room_id = $_POST['room_id'];
    $delete = $pdo->prepare("DELETE FROM rooms WHERE id = ? AND guesthouse_id = ?");
    $delete->execute([$room_id, $gh_id]);
}

// Fetch all rooms for this guesthouse
$rooms = $pdo->prepare("SELECT * FROM rooms WHERE guesthouse_id = ?");
$rooms->execute([$gh_id]);
$room_list = $rooms->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Rooms – <?= htmlspecialchars($guesthouse['name']) ?></title>
    <link rel="stylesheet" href="../assets/css/manage_rooms.css">
</head>

<body>

    <h1>Manage Rooms – <?= htmlspecialchars($guesthouse['name']) ?></h1>

    <a href="dashboard.php">← Back to Dashboard</a>
    <a href="manage_guesthouses.php">← Back to Guesthouse Management</a>

    <h2>Add New Room</h2>
    <form method="post">
        <label>Room Name:
            <input type="text" name="r_name" required>
        </label><br>
        <label>Room Type:
            <input type="text" name="type" required>
        </label><br>
        <label>Price (₹):
            <input type="number" name="price" required>
        </label><br>
        <label>Total Beds:
            <input type="number" name="total_beds" required>
        </label><br>
        <button type="submit" name="add_room">Add Room</button>
    </form>

    <h2>Existing Rooms</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Room Name</th>
            <th>Type</th>
            <th>Price (₹)</th>
            <th>Total Beds</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($room_list as $room): ?>
            <tr>
                <form method="post">
                    <td>
                        <?= htmlspecialchars($room['room_name']) ?>
                    </td>
                    <td><?= htmlspecialchars($room['type']) ?></td>
                    <td>
                        <input type="number" name="price" value="<?= $room['price'] ?>" required>
                    </td>
                    <td>
                        <input type="number" name="total_beds" value="<?= $room['total_beds'] ?>" required>
                    </td>
                    <td>
                        <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                        <button type="submit" name="update_room">Update</button>
                        <button type="submit" name="delete_room" onclick="return confirm('Delete this room?')">Delete</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
    </table>

</body>

</html>