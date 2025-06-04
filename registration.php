<?php
session_start();
require_once 'db.php';
require_once 'csrf.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyToken($_POST['csrf_token'] ?? '')) {
        header('HTTP/1.1 403 Forbidden');
        die("CSRF validation failed.");
    }

    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validation
    if (empty($username) || strlen($username) < 3 || strlen($username) > 50) {
        header('Location: register.php?error=invalid_username');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: register.php?error=invalid_email');
        exit();
    }

    if (strlen($password) < 6) {
        header('Location: register.php?error=password_too_short');
        exit();
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        header('Location: register.php?error=email_exists');
        exit();
    }

    // Check if username exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->fetch()) {
        header('Location: register.php?error=username_exists');
        exit();
    }

    // Create account
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    
    try {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);
        
        header('Location: login.php?success=registration_complete');
        exit();
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        header('Location: register.php?error=registration_failed');
        exit();
    }
}
?>