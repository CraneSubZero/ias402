<?php
require_once 'db.php';

$sessions = $conn->query("
    SELECT s.*, u.username 
    FROM user_sessions s
    LEFT JOIN users u ON s.user_id = u.id
    ORDER BY s.login_time DESC
")->fetchAll();
?>

<h3>Session Logs</h3>
<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>IP Address</th>
            <th>Login Time</th>
            <th>Logout Time</th>
            <th>Duration</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sessions as $session): ?>
        <tr>
            <td><?= htmlspecialchars($session['id']) ?></td>
            <td><?= htmlspecialchars($session['username'] ?? 'Guest') ?></td>
            <td><?= htmlspecialchars($session['ip_address']) ?></td>
            <td><?= date('Y-m-d H:i', strtotime($session['login_time'])) ?></td>
            <td><?= $session['logout_time'] ? date('Y-m-d H:i', strtotime($session['logout_time'])) : 'Active' ?></td>
            <td>
                <?php if ($session['logout_time']): ?>
                    <?= gmdate("H:i:s", strtotime($session['logout_time']) - strtotime($session['login_time'])) ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>