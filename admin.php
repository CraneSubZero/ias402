<?php
require_once 'session.php';
require_once 'db.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Windows XP Style</title>
    <link rel="stylesheet" href="xp-style.css">
    <style>
        .admin-container {
            display: flex;
            min-height: calc(100vh - 60px);
        }
        
        .sidebar {
            width: 200px;
            background-color: #ECE9D8;
            border-right: 2px solid #7A7A7A;
            padding: 10px;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            padding: 8px 10px;
            cursor: pointer;
            border-bottom: 1px solid #B5B5B5;
        }
        
        .sidebar-menu li:hover {
            background-color: #D3E3F7;
        }
        
        .sidebar-menu li.active {
            background-color: #B5D3FF;
            font-weight: bold;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #FFFFFF;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .data-table th, .data-table td {
            border: 1px solid #B5B5B5;
            padding: 8px;
            text-align: left;
        }
        
        .data-table th {
            background-color: #ECE9D8;
            font-weight: bold;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #F6F6F6;
        }
        
        .data-table tr:hover {
            background-color: #E3F0FF;
        }
        
        .action-buttons .button {
            margin: 2px;
            padding: 2px 6px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="window" style="margin-bottom: 20px;">
                <div class="title-bar">
                    <span>Admin Menu</span>
                </div>
                <div class="window-content">
                    <ul class="sidebar-menu">
                        <li class="active" onclick="loadContent('users')">
                            <img src="icons/user.png" alt="" style="vertical-align: middle; margin-right: 5px;">
                            User Management
                        </li>
                        <li onclick="loadContent('sessions')">
                            <img src="icons/clock.png" alt="" style="vertical-align: middle; margin-right: 5px;">
                            Session Logs
                        </li>
                        <li onclick="loadContent('attempts')">
                            <img src="icons/warning.png" alt="" style="vertical-align: middle; margin-right: 5px;">
                            Login Attempts
                        </li>
                        <li onclick="loadContent('security')">
                            <img src="icons/security.png" alt="" style="vertical-align: middle; margin-right: 5px;">
                            Security Events
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="window">
                <div class="title-bar">
                    <span>Quick Actions</span>
                </div>
                <div class="window-content">
                    <button class="button" onclick="showAddUserForm()">
                        <img src="icons/add_user.png" alt="" style="vertical-align: middle; margin-right: 5px;">
                        Add User
                    </button>
                    <button class="button" onclick="window.location.href='dashboard.php'">
                        <img src="icons/back.png" alt="" style="vertical-align: middle; margin-right: 5px;">
                        Back to Dashboard
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="window">
                <div class="title-bar">
                    <span id="content-title">User Management</span>
                </div>
                <div class="window-content">
                    <div id="content-area">
                        <!-- Content will be loaded here via AJAX -->
                        <?php include 'admin_users.php'; ?>
                    </div>
                </div>
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
        
        function loadContent(type) {
            // Update active menu item
            document.querySelectorAll('.sidebar-menu li').forEach(item => {
                item.classList.remove('active');
            });
            event.currentTarget.classList.add('active');
            
            // Update title
            const titles = {
                'users': 'User Management',
                'sessions': 'Session Logs',
                'attempts': 'Login Attempts',
                'security': 'Security Events'
            };
            document.getElementById('content-title').textContent = titles[type];
            
            // Load content via AJAX
            fetch(`admin_${type}.php`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('content-area').innerHTML = data;
                });
        }
        
        function showAddUserForm() {
            fetch('admin_user_form.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('content-area').innerHTML = data;
                });
        }
    </script>
</body>
</html>