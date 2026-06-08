<?php require BASE_PATH . '/view/layout/admin_header.php'; ?>

<div class="notification-page">

    <h1>Notifications</h1>

    <?php if (empty($notifications)): ?>

        <div class="empty-notification">
            No notifications found.
        </div>

    <?php else: ?>

        <?php foreach ($notifications as $notification): ?>

            <?php $type = strtolower($notification['type'] ?? 'info'); ?>
            <div class="notification-card <?= htmlspecialchars($type) ?>">

                <div class="notification-content">
                    <div class="notification-title"><?= htmlspecialchars($notification['title'] ?? 'Notification') ?></div>
                    <div class="notification-message"><?= nl2br(htmlspecialchars($notification['message'] ?? '')) ?></div>
                </div>

                <div class="notification-meta">
                    <div class="notification-date"><?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($notification['created_at'] ?? 'now'))) ?></div>
                </div>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>