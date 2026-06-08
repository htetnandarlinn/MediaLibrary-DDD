<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title><?= htmlspecialchars($pageTitle ?? 'Admin Dashboard') ?></title>

    <link rel="stylesheet" href="/MediaLibrary-MVC-Test/css/style.css">
    <link rel="stylesheet" href="/MediaLibrary-MVC-Test/css/invoice.css">
    <link rel="stylesheet" href="/MediaLibrary-MVC-Test/public/css/notification.css">
</head>

<body>

    <div class="page-container">
        <div class="content">

            <header class="admin-header">
                <div style="padding:10px;text-align:right;border-bottom:1px solid #eee;">
                    <button id="markAllReadBtn"
                        style="background:#10b981;color:white;border:none;padding:5px 10px;border-radius:6px;cursor:pointer;">
                        Mark all as read
                    </button>
                </div>
                <div class="wrapper admin-header-inner">

                    <h1 class="logo admin-logo">
                        <a href="<?= BASE_URL ?>/Public/index.php?page=reservation_admin">
                            <img src="<?= BASE_URL ?>/img/Brand-title.png" alt="Admin Dashboard">
                        </a>
                    </h1>

                    <nav class="admin-nav">
                        <ul>
                            <li class="<?= (($section ?? '') === 'reservation_admin') ? 'on' : '' ?>">
                                <a href="<?= BASE_URL ?>/Public/index.php?page=reservation_admin">
                                    Dashboard
                                </a>
                            </li>

                            <li class="<?= (($section ?? '') === 'reservation_admin_approvals') ? 'on' : '' ?>">
                                <a href="<?= BASE_URL ?>/Public/index.php?page=reservation_admin_approvals">
                                    Approvals
                                </a>
                            </li>

                            <li class="<?= (($section ?? '') === 'admin_invoices') ? 'on' : '' ?>">
                                <a href="<?= BASE_URL ?>/Public/index.php?page=admin_invoices">
                                    Invoices
                                </a>
                            </li>
                        </ul>
                    </nav>

                    <div class="admin-user-info">

                        <?php if (!empty($_SESSION['user'])): ?>
                            <span class="role-badge">ADMIN</span>
                        <?php endif; ?>

                        <?php
                        $adminUnread = 0;
                        $adminNotificationsForHeader = [];

                        if (!empty($_SESSION['user'])) {
                            try {
                                $db = \App\Shared\Database\Database::getConnection();

                                $notificationModel =
                                    new \App\Notification\Infrastructure\Persistence\NotificationRepository($db);

                                $adminUnread =
                                    $notificationModel->countUnreadForAdmin(
                                        $_SESSION['user']['user_id']
                                    );

                                $adminNotificationsForHeader =
                                    $notificationModel->getForAdmin(
                                        $_SESSION['user']['user_id']
                                    );

                                $adminNotificationsForHeader =
                                    array_slice($adminNotificationsForHeader, 0, 6);
                            } catch (\Throwable $e) {
                                $adminUnread = 0;
                                $adminNotificationsForHeader = [];
                            }
                        }
                        ?>

                        <div class="notification-wrapper">

                            <a href="#" id="notifToggle"
                                style="color:#fff;margin-right:8px;position:relative;">
                                🔔 Notifications

                                <span id="notifBadge" class="notif-badge" style="display:none;"></span>
                            </a>

                            <div id="notifDropdown"
                                class="notif-dropdown"
                                style="display:none;">

                                <div class="notif-dropdown-inner">

                                    <div class="notif-header">
                                        Notifications
                                        <small id="notifUnreadText" style="color:#888;font-weight:400;"></small>
                                    </div>

                                    <div class="notif-list" id="notifList">
                                        <div class="notif-empty">Loading...</div>
                                    </div>

                                    <div class="notif-footer">
                                        <a href="<?= BASE_URL ?>/Public/index.php?page=notifications">
                                            View all
                                        </a>
                                    </div>

                                </div>
                            </div>

                            <a class="btn btn-secondary admin-logout"
                                href="<?= BASE_URL ?>/Public/index.php?page=logout">
                                Logout
                            </a>

                        </div>

                    </div>
            </header>

            <script>
                (function() {

                    const toggle = document.getElementById('notifToggle');
                    const dropdown = document.getElementById('notifDropdown');
                    const badge = document.getElementById('notifBadge');
                    const list = document.getElementById('notifList');
                    const unreadText = document.getElementById('notifUnreadText');

                    // =========================
                    // BADGE UPDATE
                    // =========================
                    function updateBadge(count) {
                        if (!badge) return;

                        if (count > 0) {
                            badge.style.display = 'inline-block';
                            badge.innerText = count;
                        } else {
                            badge.style.display = 'none';
                        }
                    }

                    // =========================
                    // MARK AS READ
                    // =========================
                    function markAsRead(id) {
                        fetch("<?= BASE_URL ?>/Public/index.php?page=notification_mark_read&id=" + id)
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    loadNotifications();
                                }
                            });
                    }

                    // expose globally
                    window.markAsRead = markAsRead;

                    // =========================
                    // MARK ALL AS READ
                    // =========================
                    function markAllAsRead() {
                        fetch("<?= BASE_URL ?>/Public/index.php?page=notification_mark_all_read")
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    loadNotifications();
                                }
                            });
                    }

                    // mark all button
                    const markAllBtn = document.getElementById('markAllReadBtn');
                    if (markAllBtn) {
                        markAllBtn.addEventListener('click', markAllAsRead);
                    }

                    // =========================
                    // LOAD NOTIFICATIONS
                    // =========================
                    function loadNotifications() {

                        fetch("<?= BASE_URL ?>/Public/index.php?page=notification_header_api")
                            .then(res => res.json())
                            .then(data => {

                                // ⭐ THIS IS THE LINE YOU ASKED FOR
                                updateBadge(data.unread);

                                unreadText.innerText =
                                    data.unread > 0 ? `(${data.unread} unread)` : '';

                                if (!data.notifications || data.notifications.length === 0) {
                                    list.innerHTML = `<div class="notif-empty">No notifications</div>`;
                                    return;
                                }

                                let html = "";

                                data.notifications.forEach(n => {

                                    html += `
                        <div class="notif-item ${n.is_read ? '' : 'unread'}"
                             onclick="markAsRead(${n.id})">

                            <div class="notif-left">
                                <div class="notif-title">
                                    ${n.title ?? 'Notification'}
                                </div>

                                <div class="notif-msg">
                                    ${n.message}
                                </div>
                            </div>

                            <div class="notif-right">
                                <div class="notif-time">
                                    ${n.created_at}
                                </div>
                            </div>

                        </div>
                    `;
                                });

                                list.innerHTML = html;
                            });
                    }

                    // =========================
                    // DROPDOWN TOGGLE
                    // =========================
                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();

                        dropdown.style.display =
                            dropdown.style.display === 'block' ? 'none' : 'block';

                        if (dropdown.style.display === 'block') {
                            loadNotifications();
                        }
                    });

                    // close outside
                    document.addEventListener('click', function(e) {
                        if (!dropdown.contains(e.target) && e.target !== toggle) {
                            dropdown.style.display = 'none';
                        }
                    });

                    // ESC
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') {
                            dropdown.style.display = 'none';
                        }
                    });

                    // auto refresh
                    setInterval(loadNotifications, 5000);

                    // first load
                    loadNotifications();

                })();
            </script>

            <main id="content">