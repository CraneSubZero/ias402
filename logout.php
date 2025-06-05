<?php
require_once 'session.php';
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    // Record logout time
    $conn->prepare("
        UPDATE user_sessions 
        SET logout_time = NOW() 
        WHERE user_id = ? AND session_id = ? AND logout_time IS NULL
    ")->execute([$_SESSION['user_id'], session_id()]);
}

// Play logout sound before destroying session
echo '<audio autoplay><source src="sounds/logoff.wav" type="audio/wav"></audio>';

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit();
?>