<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if (!isset($_SESSION['userrole']) || $_SESSION['userrole'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gh_name'], $_POST['gh_location'], $_POST['gh_description'])) {
    $gh_name = trim($_POST['gh_name']);
    $gh_location = trim($_POST['gh_location']);
    $gh_description = trim($_POST['gh_description']);

    $stmt = $pdo->prepare("INSERT INTO guesthouses (name, location, description) VALUES (?, ?, ?)");
    $stmt->execute([$gh_name, $gh_location, $gh_description]);

    $gh_id = $pdo->lastInsertId();

    // Redirect to manage_rooms.php for this new guesthouse
    header("Location: manage_rooms.php?gh_id=" . $gh_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Guest House – Admin</title>
    <link rel="stylesheet" href="../assets/css/add_guesthouse_rooms_beds.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">
        <ion-icon name="home-outline"></ion-icon>
        <h1>Add Guest House</h1>
    </div>
    <div class="nav-links">
        <a href="dashboard.php" class="nav-btn">Back to Dashboard</a>
    </div>
</nav>

<div class="container">
    <h2>Create New Guest House</h2>
    <form method="POST" class="gh-form">
        <label for="gh_name">Name:</label>
        <input type="text" name="gh_name" id="gh_name" required>

        <label for="gh_location">Location:</label>
        <input type="text" name="gh_location" id="gh_location" required>

        <label for="gh_description">Description:</label>
        <textarea name="gh_description" id="gh_description" rows="3" required></textarea>

        <button type="submit" class="btn">Create & Manage Rooms</button>
    </form>
</div>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
