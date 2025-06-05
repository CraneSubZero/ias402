<?php
session_start();
require_once 'csrf.php';
require_once 'Db.php';

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyToken($_POST['csrf_token'])) {
    header('Location: admin-login.php?error=unauthorized');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_username = filter_input(INPUT_POST, 'admin_username', FILTER_SANITIZE_STRING);
    $admin_password = $_POST['admin_password'];

    try {
        // Connect to the admin database
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$admin_username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($admin_password, $admin['password'])) {
            // Set admin session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['is_admin'] = true;
            
            // Redirect to admin dashboard
            header('Location: admin-dashboard.php');
            exit();
        } else {
            header('Location: admin-login.php?error=invalid_credentials');
            exit();
        }
    } catch (PDOException $e) {
        // Log the error and redirect
        error_log("Admin login error: " . $e->getMessage());
        header('Location: admin-login.php?error=system_error');
        exit();
    }
} else {
    header('Location: admin-login.php');
    exit();
}
?> 