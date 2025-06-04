<?php
require_once 'csrf.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Windows XP Style</title>
    <link rel="stylesheet" href="xp-style.css">
</head>
<body>
    <div class="window">
        <div class="title-bar">
            <span>Login to System</span>
        </div>
        <div class="window-content">
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php 
                    switch($_GET['error']) {
                        case 'invalid_credentials': echo "Invalid email or password"; break;
                        default: echo "An error occurred";
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="loginprocess.php">
                <div class="input-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" placeholder="user@example.com" required>
                </div>
                
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="••••••••" required>
                </div>
                
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateToken()); ?>">
                <button type="submit" class="button">Login</button>
            </form>
        </div>
    </div>
</body>
</html>