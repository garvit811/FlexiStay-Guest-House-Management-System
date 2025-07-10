<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if (!isset($_SESSION['userid']) || $_SESSION['userrole'] !== 'guest') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $user_id = $_SESSION['userid'];

    try {
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'");
        $stmt->execute([$booking_id, $user_id]);

        header("Location: ./my_bookings.php?cancel=success");
        exit();
    } catch (PDOException $e) {
        die("Cancel failed: " . $e->getMessage());
    }
} else {
    header("Location: ./dashboard.php");
    exit();
}
