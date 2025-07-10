<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

// Ensure only admin can access
if (!isset($_SESSION['userrole']) || $_SESSION['userrole'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_clerk'])) {
    $clerk_id = $_POST['clerk_id'] ?? null;

    if ($clerk_id && is_numeric($clerk_id)) {
        try {
            // Ensure the user is a clerk before deletion
            $check = $pdo->prepare("SELECT role FROM users WHERE id = ?");
            $check->execute([$clerk_id]);
            $user = $check->fetch();

            if ($user && $user['role'] === 'clerk') {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$clerk_id]);
                $msg = "Clerk deleted successfully.";
            } else {
                $msg = "Invalid clerk ID.";
            }
        } catch (PDOException $e) {
            $msg = "Error deleting clerk: " . $e->getMessage();
        }
    }
}

// Fetch all clerks
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'clerk' ORDER BY created_at DESC");
    $stmt->execute();
    $clerks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching clerks: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Clerks – Admin | FlexiStay</title>
    <link rel="stylesheet" href="../assets/css/view_clerks.css">
</head>
<body>

<h1>Registered Clerks</h1>

<?php if (!empty($msg)): ?>
    <p class="message"><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

<?php if (count($clerks) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>DOB</th>
                <th>Registered On</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clerks as $clerk): ?>
                <tr>
                    <td><?= $clerk['id'] ?></td>
                    <td><?= htmlspecialchars($clerk['name']) ?></td>
                    <td><?= htmlspecialchars($clerk['email']) ?></td>
                    <td><?= htmlspecialchars($clerk['mobile']) ?></td>
                    <td><?= $clerk['dob'] ?></td>
                    <td><?= $clerk['created_at'] ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this clerk?');">
                            <input type="hidden" name="clerk_id" value="<?= $clerk['id'] ?>">
                            <button type="submit" name="delete_clerk" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No clerks have been registered yet.</p>
<?php endif; ?>

</body>
</html>
