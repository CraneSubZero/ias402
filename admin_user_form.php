<?php
require_once 'db.php';
require_once 'csrf.php';

$user = null;
if (isset($_GET['edit'])) {
    $userId = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && verifyToken($_POST['csrf_token'] ?? '')) {
    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;
    
    if ($user) {
        // Update existing user
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, is_admin = ? WHERE id = ?");
            $stmt->execute([$username, $email, $hashedPassword, $isAdmin, $user['id']]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, is_admin = ? WHERE id = ?");
            $stmt->execute([$username, $email, $isAdmin, $user['id']]);
        }
    } else {
        // Create new user
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword, $isAdmin]);
    }
    
    header("Location: admin.php");
    exit();
}
?>

<h3><?= $user ? 'Edit User' : 'Add New User' ?></h3>
<form method="POST" action="admin_user_form.php<?= $user ? '?edit='.$user['id'] : '' ?>">
    <div class="input-group">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
    </div>
    
    <div class="input-group">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
    </div>
    
    <div class="input-group">
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" <?= !$user ? 'required' : '' ?>>
        <?php if ($user): ?>
            <small>Leave blank to keep current password</small>
        <?php endif; ?>
    </div>
    
    <?php if ($_SESSION['is_admin']): ?>
    <div class="input-group">
        <label>
            <input type="checkbox" name="is_admin" <?= ($user['is_admin'] ?? false) ? 'checked' : '' ?>>
            Administrator
        </label>
    </div>
    <?php endif; ?>
    
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateToken()) ?>">
    
    <button type="submit" class="button">Save</button>
    <button type="button" class="button" onclick="window.location.href='admin.php'">Cancel</button>
</form>