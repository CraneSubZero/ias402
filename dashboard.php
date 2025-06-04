<?php
require_once 'session.php';

if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Windows XP Style</title>
    <link rel="stylesheet" href="xp-style.css">
    <style>
        .desktop-icon {
            display: inline-block;
            width: 72px;
            margin: 10px;
            text-align: center;
            cursor: pointer;
        }
        .desktop-icon img {
            width: 32px;
            height: 32px;
        }
        .desktop-icon span {
            display: block;
            font-size: 11px;
            margin-top: 5px;
        }
        .taskbar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to right, #245EDB, #4B8CF5, #245EDB);
            height: 30px;
            display: flex;
            align-items: center;
            padding: 0 10px;
            z-index: 1000;
        }
        .start-button {
            background-image: url('icons/start.png');
            background-size: contain;
            background-repeat: no-repeat;
            width: 45px;
            height: 22px;
            cursor: pointer;
        }
        .clock {
            color: white;
            font-size: 11px;
            margin-left: auto;
        }
    </style>
</head>
<body>
    <!-- Desktop Icons -->
    <div style="position: absolute; top: 10px; left: 10px;">
        <div class="desktop-icon" onclick="window.location.href='profile.php'">
            <img src="icons/user.png" alt="User">
            <span>My Account</span>
        </div>
        <div class="desktop-icon" onclick="alert('Coming soon!')">
            <img src="icons/computer.png" alt="Computer">
            <span>My Computer</span>
        </div>
    </div>

    <!-- Main Window -->
    <div class="window" style="width: 500px; margin: 50px auto;">
        <div class="title-bar">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
        <div class="window-content">
            <h3 style="margin-top: 0;">System Dashboard</h3>
            <p>You have successfully logged into the system.</p>
            
            <div style="margin-top: 20px;">
                <button class="button" onclick="window.location.href='profile.php'">
                    <img src="icons/user.png" alt="" style="vertical-align: middle; margin-right: 5px;"> View Profile
                </button>
                <button class="button" onclick="logout()" style="float: right;">
                    <img src="icons/logoff.png" alt="" style="vertical-align: middle; margin-right: 5px;"> Logout
                </button>
            </div>
        </div>
    </div>

    <!-- Taskbar -->
    <div class="taskbar">
        <div class="start-button" onclick="document.getElementById('start-menu').style.display='block'"></div>
        <div class="clock" id="clock"></div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            document.getElementById('clock').textContent = timeString;
        }
        
        setInterval(updateClock, 1000);
        updateClock();
        
        function logout() {
            if(confirm('Are you sure you want to log out?')) {
                // Play logout sound
                const audio = new Audio('sounds/logoff.wav');
                audio.play().then(() => {
                    window.location.href = 'logout.php';
                }).catch(e => {
                    window.location.href = 'logout.php';
                });
            }
        }
    </script>
    
    <!-- Play startup sound -->
    <audio autoplay>
        <source src="sounds/startup.wav" type="audio/wav">
    </audio>
</body>
</html>