<?php
require_once '../includes/dbh.inc.php';
require_once '../includes/functions.inc.php';
require_once '../includes/config_session.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';


    if ($email == 'soni@admin.com' && $password == 'admin') 
    {
        $_SESSION['userid'] = 0; 
        $_SESSION['username'] = 'Admin';
        $_SESSION['userrole'] = 'admin';
        header("Location: ../admin/dashboard.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['userid'] = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['userrole'] = $user['role'];

            // Redirect based on role
            switch ($user['role']) {
                case 'guest':
                    header("Location: ../guest/dashboard.php");
                    break;
                case 'clerk':
                    header("Location: ../clerk/dashboard.php");
                    break;
            }
            exit();
        } else {
            header("Location: ../login.php?error=invalidcredentials");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: ../login.php?error=sqlerror");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
