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

function recordSecurityEvent($conn, $userId, $eventType, $details = '') {
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $stmt = $conn->prepare("
        INSERT INTO security_events (user_id, event_type, ip_address, user_agent, details)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $userId,
        $eventType,
        $ip,
        $userAgent,
        $details
    ]);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyToken($_POST['csrf_token'] ?? '')) {
        header('HTTP/1.1 403 Forbidden');
        die("CSRF validation failed.");
    }

    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (isLockedOut($conn, $email)) {
        recordSecurityEvent($conn, null, 'account_locked', "Account locked out due to too many attempts for email: $email");
        header('Location: login.php?error=account_locked');
        exit();
    }

    $stmt = $conn->prepare("SELECT id, username, password, is_admin FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Successful login
        clearLoginAttempts($conn, $email);
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true;
        $_SESSION['is_admin'] = (bool)$user['is_admin'];
        
        // Record security event
        recordSecurityEvent($conn, $user['id'], 'login_success');
        
        // Update last login time
        $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")
             ->execute([$user['id']]);
        
        // Play login sound
        echo '<audio autoplay><source src="sounds/login.wav" type="audio/wav"></audio>';
        
        header('Location: dashboard.php');
        exit();
    } else {
        // Failed login
        recordFailedLogin($conn, $email);
        
        $errorDetails = "Failed login attempt for email: $email";
        if ($user) {
            $errorDetails .= " (existing user ID: {$user['id']})";
        }
        recordSecurityEvent($conn, $user['id'] ?? null, 'login_failed', $errorDetails);
        
        header('Location: login.php?error=invalid_credentials');
        exit();
    }
}
?>