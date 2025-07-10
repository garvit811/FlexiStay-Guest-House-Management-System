<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if (!isset($_SESSION['userid']) || $_SESSION['userrole'] !== 'guest') {
    header("Location: login.php");
    exit();
}

try {
    // Only fetch active guesthouses
    $stmt = $pdo->query("SELECT * FROM guesthouses WHERE is_active = 1");
    $guest_houses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Guest Dashboard – FlexiStay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/guest_dashboard.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="logo">
        <ion-icon class="l_icn" name="logo-foursquare"></ion-icon>
        <h1>FlexiStay</h1>
    </div>
    <div class="nav-links">
        <a href="./my_bookings.php" class="nav-btn">My Bookings</a>
        <a href="../auth/logout.inc.php" class="nav-btn">Logout</a>
    </div>
</nav>

<!-- Hero Section -->
<div class="hero_section">
    <div class="hero_left">
        <img src="../assets/images/hero.png" alt="Hero Image">
    </div>
    <div class="hero_right">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
        <p>Browse and book from our collection of company-owned guest houses.</p>
    </div>
</div>

<!-- Guest House Listings -->
<div class="guesthouse-listing">
    <h2>Available Guest Houses</h2>

    <?php if (!empty($guest_houses)): ?>
        <div class="guest-house-grid">
            <?php foreach ($guest_houses as $gh): ?>
                <div class="guest-house-card">
                    <h3><?= htmlspecialchars($gh['name']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($gh['description'])) ?></p>

                    <?php
                    try {
                        $roomStmt = $pdo->prepare("SELECT * FROM rooms WHERE guesthouse_id = :ghid");
                        $roomStmt->bindParam(':ghid', $gh['id']);
                        $roomStmt->execute();
                        $rooms = $roomStmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        $rooms = [];
                    }
                    ?>

                    <?php if (!empty($rooms)): ?>
                        <ul>
                            <?php foreach ($rooms as $room): ?>
                                <li><?= htmlspecialchars($room['type']) ?> – ₹<?= $room['price'] ?> (<?= $room['total_beds'] ?> beds)</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No rooms listed.</p>
                    <?php endif; ?>

                    <a href="book.php?gh=<?= $gh['id'] ?>" class="book-now-btn">Book Now</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="no-gh-msg">No guest houses available. Please check back later.</p>
    <?php endif; ?>
</div>

<!-- Features Section -->
<div class="feature_section">
    <h2>Why FlexiStay?</h2>
    <div class="feature_cards">
        <div class="f_card">
            <img src="../assets/images/SearchDesktop.svg" alt="Search Icon">
            <div>
                <h3>Explore Freely</h3>
                <p>Browse properties without filters – view all options easily.</p>
            </div>
        </div>
        <div class="f_card">
            <img src="../assets/images/CompareDesktop.svg" alt="Compare Icon">
            <div>
                <h3>Compare Comfortably</h3>
                <p>View types, prices, and locations side-by-side before booking.</p>
            </div>
        </div>
        <div class="f_card">
            <img src="../assets/images/SaveDesktop.svg" alt="Save Icon">
            <div>
                <h3>One Brand. One Standard.</h3>
                <p>All guest houses follow a single standard for quality and service.</p>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
    <div class="footer-content">
        <div class="footer-left">
            <div class="logo">
                <ion-icon class="l_icn" name="logo-foursquare"></ion-icon>
                <h2>FlexiStay</h2>
            </div>
            <p>FlexiStay is a platform for our network of company-owned guest houses. Consistent experience, flexible booking.</p>
        </div>
        <div class="footer-right">
            <h3>Contact Us</h3>
            <p>Email: <a href="mailto:support@flexistay.com">support@flexistay.com</a></p>
            <p>Phone: <a href="tel:+919999999999">+91 99999 99999</a></p>
            <p>Address: 123 Guest House Lane, Jodhpur, India</p>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
