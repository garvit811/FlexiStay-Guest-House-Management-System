<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

// Restrict access to only admin
if (!isset($_SESSION['userrole']) || $_SESSION['userrole'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Clerk – Admin</title>
    <link rel="stylesheet" href="../assets/css/add_clerk.css">
</head>
<body>
    <h1>Add New Clerk</h1>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <p class="success-msg">Clerk added successfully!</p>
    <?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
        <p class="error-msg">Error: <?= htmlspecialchars($_GET['msg'] ?? 'Unknown error.') ?></p>
    <?php endif; ?>

    <form action="../auth/clerk_signup.inc.php" method="POST" class="form-box">
        <label for="name">Clerk Name:</label>
        <input type="text" name="name" required>

        <label for="email">Email:</label>
        <input type="email" name="email" required>

        <label for="mobile">Mobile:</label>
        <input type="text" name="mobile" required>

        <label for="dob">Date of Birth:</label>
        <input type="date" name="dob" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" required>

        <button type="submit" name="submit">Add Clerk</button>
    </form>
</body>
</html>
