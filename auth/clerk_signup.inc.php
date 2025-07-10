<?php
require_once '../includes/dbh.inc.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $mobile = trim($_POST["mobile"]);
    $dob = $_POST["dob"];

    // Basic validations
    if (empty($name) || empty($email) || empty($password) || empty($mobile) || empty($dob)) {
        header("Location: ../admin/add_clerk.php?status=error&msg=All fields are required");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../admin/add_clerk.php?status=error&msg=Invalid email format");
        exit();
    }

    if ($password !== $confirm_password) {
        header("Location: ../admin/add_clerk.php?status=error&msg=Passwords do not match");
        exit();
    }

    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            header("Location: ../admin/add_clerk.php?status=error&msg=Email already registered");
            exit();
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert into DB
        $insert = $pdo->prepare("INSERT INTO users (name, email, password, dob, mobile, role) VALUES (?, ?, ?, ?, ?, 'clerk')");
        $insert->execute([$name, $email, $hashedPassword, $dob, $mobile]);

        header("Location: ../admin/add_clerk.php?status=success");
        exit();

    } catch (PDOException $e) {
        header("Location: ../admin/add_clerk.php?status=error&msg=" . urlencode($e->getMessage()));
        exit();
    }

} else {
    header("Location: ../admin/add_clerk.php");
    exit();
}
