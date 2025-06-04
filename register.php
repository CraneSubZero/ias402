<?php
require_once 'csrf.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Windows XP Style</title>
    <link rel="stylesheet" href="xp-style.css">
</head>
<body>
    <div class="window">
        <div class="title-bar">
            <span>Create New Account</span>
        </div>
        <div class="window-content">
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php 
                    switch($_GET['error']) {
                        case 'invalid_username': echo "Username must be 3-50 characters"; break;
                        case 'invalid_email': echo "Invalid email address"; break;
                        case 'password_too_short': echo "Password must be at least 6 characters"; break;
                        case 'email_exists': echo "Email already registered"; break;
                        case 'username_exists': echo "Username already taken"; break;
                        case 'registration_failed': echo "Registration failed. Please try again."; break;
                        default: echo "An error occurred";
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success']) && $_GET['success'] == 'registration_complete'): ?>
                <div class="success-message">
                    Registration successful! You can now login.
                </div>
            <?php endif; ?>
            
            <form method="POST" action="registration.php">
                <div class="input-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" placeholder="john_doe" required minlength="3" maxlength="50">
                </div>
                
                <div class="input-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" placeholder="user@example.com" required>
                </div>
                
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="••••••••" required minlength="6">
                </div>
                
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateToken()); ?>">
                <button type="submit" class="button">Register</button>
            </form>
        </div>
    </div>
</body>
</html>