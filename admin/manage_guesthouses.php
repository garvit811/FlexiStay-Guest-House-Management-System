<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if (!isset($_SESSION['userid']) || $_SESSION['userrole'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Toggle guesthouse active/inactive
if (isset($_POST['toggle_gh'])) {
    $gh_id = $_POST['gh_id'];
    $action = $_POST['action'];
    $stmt = $pdo->prepare("UPDATE guesthouses SET is_active = ? WHERE id = ?");
    $stmt->execute([$action === 'deactivate' ? 0 : 1, $gh_id]);
}

// Fetch all guesthouses
$guesthouses = $pdo->query("SELECT * FROM guesthouses")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Guesthouses – Admin</title>
    <link rel="stylesheet" href="../assets/css/manage_gh.css">
</head>
<body>

<h1>Guesthouse Management</h1>

<!-- Guesthouse List -->
<h2>Guesthouse List</h2>
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Location</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($guesthouses as $gh): ?>
    <tr>
        <td><?= $gh['id'] ?></td>
        <td><?= htmlspecialchars($gh['name']) ?></td>
        <td><?= htmlspecialchars($gh['location']) ?></td>
        <td>
            <form method="post" style="display:inline;">
                <input type="hidden" name="gh_id" value="<?= $gh['id'] ?>">
                <input type="hidden" name="action" value="<?= $gh['is_active'] ? 'deactivate' : 'activate' ?>">
                <button type="submit" name="toggle_gh"
                    style="border:none;background:none;
                    color:<?= $gh['is_active'] ? 'green' : 'red' ?>;cursor:pointer;">
                    <?= $gh['is_active'] ? 'Active' : 'Inactive' ?>
                </button>
            </form>
        </td>
        <td>
            <a href="manage_rooms.php?gh_id=<?= $gh['id'] ?>">Manage Rooms</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
