<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';
require_once '../vendor/autoload.php'; // Adjust path if not using Composer

use Dompdf\Dompdf;

if (!isset($_GET['booking_id']) || !is_numeric($_GET['booking_id'])) {
    die("Invalid booking ID.");
}

$booking_id = $_GET['booking_id'];

// Fetch booking details
$stmt = $pdo->prepare("
    SELECT b.*, u.name AS guest_name, u.email, u.mobile, r.type AS room_type, r.price, r.guesthouse_id, g.name AS guesthouse_name
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN rooms r ON b.room_id = r.id
    JOIN guesthouses g ON r.guesthouse_id = g.id
    WHERE b.id = ?
");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die("Booking not found.");
}

$checkin = new DateTime($booking['checkin']);
$checkout = new DateTime($booking['checkout']);
$interval = $checkin->diff($checkout);
$days = $interval->days;


// Generate invoice HTML
$html = "
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; padding: 20px; }
        h1 { color: #1a365d; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        .footer { margin-top: 40px; font-size: 0.9rem; color: #888; }
    </style>
</head>
<body>
    <h1>FlexiStay Booking Invoice</h1>
    <p><strong>Booking ID:</strong> {$booking['id']}</p>
    <p><strong>Guest Name:</strong> {$booking['guest_name']}</p>
    <p><strong>Email:</strong> {$booking['email']}</p>
    <p><strong>Phone:</strong> {$booking['mobile']}</p>

    <table>
        <tr><th>Guest House</th><td>{$booking['guesthouse_name']}</td></tr>
        <tr><th>Room Type</th><td>{$booking['room_type']}</td></tr>
        <tr><th>Price per Bed</th><td>₹{$booking['price']}</td></tr>
        <tr><th>Beds Booked</th><td>{$booking['booked_beds']}</td></tr>
        <tr><th>Check-In</th><td>{$booking['checkin']}</td></tr>
        <tr><th>Check-Out</th><td>{$booking['checkout']}</td></tr>
        <tr><th>Days</th><td>{$days}</td></tr>
        <tr><th>Status</th><td>{$booking['status']}</td></tr>
        <tr><th>Total</th><td><strong>₹" . ($booking['booked_beds'] * $booking['price'] * $days) . "</strong></td></tr>
    </table>

    <div class='footer'>
        <p>Thank you for booking with FlexiStay. We look forward to hosting you!</p>
    </div>
</body>
</html>
";

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output as download
$dompdf->stream("FlexiStay_Invoice_{$booking['id']}.pdf", ["Attachment" => true]);
exit;
