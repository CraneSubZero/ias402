<?php
require_once 'csrf.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Windows XP Style</title>
    <link rel="stylesheet" href="xp-style.css">
</head>
<body>
    <div class="window">
        <div class="title-bar">
            <span>Admin Login</span>
        </div>
        <div class="window-content">
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php 
                    switch($_GET['error']) {
                        case 'invalid_credentials': echo "Invalid admin credentials"; break;
                        case 'unauthorized': echo "Unauthorized access attempt"; break;
                        default: echo "An error occurred";
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="admin-login-process.php">
                <div class="input-group">
                    <label for="admin_username">Admin Username:</label>
                    <input type="text" name="admin_username" id="admin_username" required>
                </div>
                
                <div class="input-group">
                    <label for="admin_password">Admin Password:</label>
                    <input type="password" name="admin_password" id="admin_password" required>
                </div>
                
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateToken()); ?>">
                <button type="submit" class="button">Admin Login</button>
            </form>
            
            <div class="back-to-login">
                <a href="login.php" class="button">Back to Regular Login</a>
            </div>
        </div>
    </div>
</body>
</html> 