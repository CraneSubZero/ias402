<?php
// session.php - Should be included first in every page
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400, // 1 day
        'cookie_secure'   => true,  // Requires HTTPS
        'cookie_httponly' => true, // Prevent JS access
        'use_strict_mode' => true   // Better security
    ]);
}

// Track session activity
if (isset($_SESSION['logged_in'])) {
    require_once 'db.php';
    
    // Update last activity
    $_SESSION['last_activity'] = time();
    
    // Record new session if not already recorded
    if (!isset($_SESSION['session_recorded'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $conn->prepare("
            INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            session_id(),
            $ip,
            $userAgent
        ]);
        
        $_SESSION['session_recorded'] = true;
    }
    
    // Update user's last login time
    if (!isset($_SESSION['login_time_updated'])) {
        $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")
             ->execute([$_SESSION['user_id']]);
        $_SESSION['login_time_updated'] = true;
    }
}