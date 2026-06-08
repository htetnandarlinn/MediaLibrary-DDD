<?php require BASE_PATH . '/view/layout/header.php'; ?>

<div class="notification-page">

    <h1>Notifications</h1>

    <?php if (!empty($notifications)): ?>
        <div class="notification-actions">
            <button id="markAllReadBtn" type="button">Mark all as read</button>
        </div>
    <?php endif; ?>

    <?php if (empty($notifications)): ?>

        <div class="empty-notification">
            No notifications found.
        </div>

    <?php else: ?>

        <?php foreach ($notifications as $notification): ?>

            <?php $type = strtolower($notification['type'] ?? 'info'); ?>
            <div class="notification-card <?= htmlspecialchars($type) ?> <?= empty($notification['is_read']) ? 'unread' : 'read' ?>"
                data-notif-id="<?= htmlspecialchars($notification['id']) ?>">

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

<script>
    (function() {
        const apiBase = '<?= BASE_URL ?>/Public/index.php?page=';

        function markAsRead(id) {
            return fetch(apiBase + 'notification_mark_read&id=' + id)
                .then(res => res.json())
                .then(data => data.success);
        }

        function markAllAsRead() {
            return fetch(apiBase + 'notification_mark_all_read')
                .then(res => res.json())
                .then(data => data.success);
        }

        function initNotificationInteractions() {
            const markAllBtn = document.getElementById('markAllReadBtn');
            if (markAllBtn) {
                markAllBtn.addEventListener('click', function() {
                    markAllAsRead().then(success => {
                        if (success) {
                            window.location.reload();
                        }
                    });
                });
            }

            document.querySelectorAll('.notification-card.unread').forEach(card => {
                card.addEventListener('click', function() {
                    const id = this.dataset.notifId;
                    if (!id) {
                        return;
                    }

                    markAsRead(id).then(success => {
                        if (success) {
                            this.classList.remove('unread');
                            this.classList.add('read');
                        }
                    });
                });
            });
        }

        initNotificationInteractions();
    })();
</script>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>