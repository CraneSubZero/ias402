<?php
require_once 'db.php';

$attempts = $conn->query("
    SELECT l.*, u.username 
    FROM login_attempts l
    LEFT JOIN users u ON l.email = u.email
    ORDER BY l.last_attempt DESC
")->fetchAll();
?>

<h3>Login Attempts</h3>
<table class="data-table">
    <thead>
        <tr>
            <th>Email</th>
            <th>User</th>
            <th>Attempts</th>
            <th>Last Attempt</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($attempts as $attempt): ?>
        <tr>
            <td><?= htmlspecialchars($attempt['email']) ?></td>
            <td><?= htmlspecialchars($attempt['username'] ?? 'Not registered') ?></td>
            <td><?= htmlspecialchars($attempt['attempts']) ?></td>
            <td><?= date('Y-m-d H:i', strtotime($attempt['last_attempt'])) ?></td>
            <td>
                <?php if ($attempt['attempts'] >= 5 && (time() - strtotime($attempt['last_attempt'])) < 900): ?>
                    <span style="color: red;">Locked Out</span>
                <?php else: ?>
                    <span style="color: green;">Normal</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>