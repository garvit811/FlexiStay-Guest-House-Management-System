<?php
require_once '../includes/dbh.inc.php';
require_once '../includes/functions.inc.php';
require_once '../includes/config_session.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dob = $_POST['dob'] ?? null;
    $mobile = trim($_POST['mobile'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        header("Location: ../login.php?error=emptyfields");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../login.php?error=invalidemail");
        exit();
    }

    if ($password !== $confirm) {
        header("Location: ../login.php?error=passwordmismatch");
        exit();
    }

    try {
        // Check if user already exists
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->rowCount() > 0) {
            header("Location: ../login.php?error=usertaken");
            exit();
        }

        // Insert user
        $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, dob, mobile, password, role) VALUES (?, ?, ?, ?, ?, 'guest')");
        $stmt->execute([$name, $email, $dob, $mobile, $hashedPwd]);

        header("Location: ../login.php?signup=success");
        exit();
    } catch (PDOException $e) {
        header("Location: ../login.php?error=sqlerror");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
