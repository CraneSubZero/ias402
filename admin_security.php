<?php
require_once 'db.php';

$events = $conn->query("
    SELECT s.*, u.username 
    FROM security_events s
    LEFT JOIN users u ON s.user_id = u.id
    ORDER BY s.created_at DESC
    LIMIT 100
")->fetchAll();
?>

<h3>Security Events</h3>
<table class="data-table">
    <thead>
        <tr>
            <th>Time</th>
            <th>User</th>
            <th>Event Type</th>
            <th>IP Address</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($events as $event): ?>
        <tr>
            <td><?= date('Y-m-d H:i', strtotime($event['created_at'])) ?></td>
            <td><?= htmlspecialchars($event['username'] ?? 'System') ?></td>
            <td>
                <?php 
                $eventTypes = [
                    'login_success' => 'Login Success',
                    'login_failed' => 'Login Failed',
                    'password_change' => 'Password Changed',
                    'account_lock' => 'Account Locked'
                ];
                echo $eventTypes[$event['event_type']] ?? $event['event_type'];
                ?>
            </td>
            <td><?= htmlspecialchars($event['ip_address']) ?></td>
            <td><?= htmlspecialchars($event['details']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>