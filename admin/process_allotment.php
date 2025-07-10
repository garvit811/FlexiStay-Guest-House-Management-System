<?php
require_once '../includes/dbh.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $beds = $_POST['beds'] ?? [];

    if (count($beds) === 0) {
        die("No beds selected.");
    }

    // Fetch required data
    $stmt = $pdo->prepare("SELECT booked_beds, checkin, checkout FROM bookings WHERE id = ?");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        die("Booking not found.");
    }

    $booked_beds = $booking['booked_beds'];
    $checkin = $booking['checkin'];
    $checkout = $booking['checkout'];

    if (count($beds) != $booked_beds) {
        die("Please select exactly $booked_beds beds.");
    }

    // Insert only if bed is free during this booking's date range
    $insert = $pdo->prepare("INSERT INTO bed_allocation (booking_id, room_id, bed_no) VALUES (?, ?, ?)");

    foreach ($beds as $entry) {
        [$room_id, $bed_no] = explode('-', $entry);

        // Check if this bed is already allotted in any overlapping booking
        $conflict = $pdo->prepare("
            SELECT COUNT(*) FROM bed_allocation ba
            JOIN bookings b ON ba.booking_id = b.id
            WHERE ba.room_id = ? AND ba.bed_no = ?
            AND NOT (b.checkout <= ? OR b.checkin >= ?)
        ");
        $conflict->execute([$room_id, $bed_no, $checkin, $checkout]);
        $conflict_count = $conflict->fetchColumn();

        if ($conflict_count > 0) {
            die("Error: Bed $bed_no in Room $room_id is already allocated during these dates.");
        }

        // ✅ Bed is free – insert
        try {
            $insert->execute([$booking_id, $room_id, $bed_no]);
        } catch (PDOException $e) {
            die("DB Error during insert: " . $e->getMessage());
        }
    }

    // Update booking status
    $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?")->execute([$booking_id]);

    header("Location: allot_bookings.php?status=success");
    exit();
}
