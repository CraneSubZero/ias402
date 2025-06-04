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
?>