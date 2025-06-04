<?php
// loginprocess.php
require_once 'session.php'; // First include
require_once 'db.php';
require_once 'csrf.php';

define('MAX_ATTEMPTS', 5);
define('LOCK_TIME', 15 * 60); // 15 minutes

function isLockedOut($conn, $email) {
    $stmt = $conn->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE email = ?");
    $stmt->execute([$email]);
    $result = $stmt->fetch();
    
    if ($result && $result['attempts'] >= MAX_ATTEMPTS && 
        (time() - strtotime($result['last_attempt'])) < LOCK_TIME) {
        return true;
    }
    return false;
}

function recordFailedLogin($conn, $email) {
    $stmt = $conn->prepare("INSERT INTO login_attempts (email, attempts) VALUES (?, 1)
        ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = CURRENT_TIMESTAMP");
    $stmt->execute([$email]);
}

function clearLoginAttempts($conn, $email) {
    $stmt = $conn->prepare("DELETE FROM login_attempts WHERE email = ?");
    $stmt->execute([$email]);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyToken($_POST['csrf_token'] ?? '')) {
        header('HTTP/1.1 403 Forbidden');
        die("CSRF validation failed.");
    }

    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (isLockedOut($conn, $email)) {
        header('Location: login.php?error=account_locked');
        exit();
    }

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        clearLoginAttempts($conn, $email);
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true;
        
        // Play login sound
        echo '<audio autoplay><source src="sounds/login.wav" type="audio/wav"></audio>';
        
        header('Location: dashboard.php');
        exit();
    } else {
        recordFailedLogin($conn, $email);
        header('Location: login.php?error=invalid_credentials');
        exit();
    }
}
?>