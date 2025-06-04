<?php
require_once 'session.php';

// Play logout sound before destroying session
echo '<audio autoplay><source src="sounds/logoff.wav" type="audio/wav"></audio>';

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit();
?>