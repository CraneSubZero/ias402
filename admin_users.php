<?php
require_once 'db.php';

// Handle delete action
if (isset($_GET['delete']) {
    $userId = (int)$_GET['delete'];
    $conn->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
    header("Location: admin.php");
    exit();
}

// Handle toggle admin
if (isset($_GET['toggle_admin'])) {
    $userId = (int)$_GET['toggle_admin'];
    $stmt = $conn->prepare("UPDATE users SET is_admin = NOT is_admin WHERE id = ?");
    $stmt->execute([$userId]);
    header("Location: admin.php");
    exit();
}

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<h3>User Accounts</h3>
<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Registered</th>
            <th>Last Login</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['id']) ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= date('Y-m-d H:i', strtotime($user['created_at'])) ?></td>
            <td><?= $user['last_login'] ? date('Y-m-d H:i', strtotime($user['last_login'])) : 'Never' ?></td>
            <td>
                <?php if ($user['is_admin']): ?>
                    <span style="color: green;">Admin</span>
                <?php else: ?>
                    <span>User</span>
                <?php endif; ?>
            </td>
            <td class="action-buttons">
                <button class="button" onclick="window.location.href='admin_user_form.php?edit=<?= $user['id'] ?>'">
                    <img src="icons/edit.png" alt="Edit" style="vertical-align: middle;">
                </button>
                <button class="button" onclick="window.location.href='?toggle_admin=<?= $user['id'] ?>'">
                    <img src="icons/admin.png" alt="Toggle Admin" style="vertical-align: middle;">
                </button>
                <button class="button" onclick="if(confirm('Delete this user?')) window.location.href='?delete=<?= $user['id'] ?>'">
                    <img src="icons/delete.png" alt="Delete" style="vertical-align: middle;">
                </button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>