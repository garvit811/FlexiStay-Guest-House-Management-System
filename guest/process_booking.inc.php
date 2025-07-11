<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if (!isset($_SESSION['userid']) || $_SESSION['userrole'] !== 'guest') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['userid'];
    $guesthouse_id = $_POST['guesthouse_id'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $beds = $_POST['beds'] ?? [];

    if (empty($checkin) || empty($checkout) || strtotime($checkout) <= strtotime($checkin)) {
        die("Invalid dates selected.");
    }


    $pdo->beginTransaction();
    try {
        $anyBooked = false;

        foreach ($beds as $room_id => $bed_count) {
            $room_id = intval($room_id);
            $bed_count = intval($bed_count);

            if ($bed_count > 0) {
                // Get total beds in the room
                $stmt = $pdo->prepare("SELECT total_beds FROM rooms WHERE id = ?");
                $stmt->execute([$room_id]);
                $total_beds = $stmt->fetchColumn();

                if (!$total_beds) {
                    throw new Exception("Room ID $room_id not found.");
                }

                // Calculate overlapping booked beds
                $stmt = $pdo->prepare("
                    SELECT COALESCE(SUM(booked_beds), 0)
                    FROM bookings
                    WHERE room_id = ?
                      AND status IN ('pending', 'confirmed')
                      AND NOT (checkout <= ? OR checkin >= ?)
                ");
                $stmt->execute([$room_id, $checkin, $checkout]);
                $booked = $stmt->fetchColumn();

                $available = $total_beds - $booked;

                if ($bed_count > $available) {
                    throw new Exception("Not enough beds available in Room ID $room_id.");
                }

                // Insert booking
                $insert = $pdo->prepare("
                    INSERT INTO bookings (user_id, room_id, checkin, checkout, booked_beds, status)
                    VALUES (?, ?, ?, ?, ?, 'pending')
                ");
                $insert->execute([$user_id, $room_id, $checkin, $checkout, $bed_count]);
                $anyBooked = true;
            }
        }

        if (!$anyBooked) {
            throw new Exception("No beds selected.");
        }

        $pdo->commit();
        header("Location: ./my_bookings.php?success=1");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Booking failed: " . $e->getMessage());
    }
} else {
    header("Location: ../guest_dashboard.php");
    exit();
}
