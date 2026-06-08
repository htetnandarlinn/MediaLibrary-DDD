<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Media Library') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/notification.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>

<body>

    <?php

    use App\Notification\Infrastructure\Persistence\NotificationRepository;
    use App\Shared\Database\Database;

    $unreadCount = 0;

    if (!empty($_SESSION['user'])) {
        try {
            $db = Database::getConnection();
            $notificationModel = new NotificationRepository($db);

            $userId = $_SESSION['user']['user_id'] ?? null;

            if ($userId) {
                if (strtolower($_SESSION['user']['role'] ?? '') === 'admin') {
                    $unreadCount = $notificationModel->countUnreadForAdmin($userId);
                } else {
                    $unreadCount = $notificationModel->countUnread($userId);
                }
            }
        } catch (Exception $e) {
            $unreadCount = 0;
        }
    }
    ?>

    <div class="page-container">
        <div class="content">

            <header class="header">
                <div class="wrapper">

                    <!-- LOGO -->
                    <h1 class="logo">
                        <a href="<?= BASE_URL ?>/Public/index.php">
                            <img src="<?= BASE_URL ?>/img/Brand-title.png" alt="Media Library">
                        </a>
                    </h1>

                    <!-- NAVIGATION -->
                    <ul class="nav">

                        <li class="<?= ($section === 'books') ? 'on' : '' ?>">
                            <a href="<?= BASE_URL ?>/Public/index.php?page=catalog&cat=books">
                                <img src="<?= BASE_URL ?>/img/book.png"> Books
                            </a>
                        </li>

                        <li class="<?= ($section === 'movies') ? 'on' : '' ?>">
                            <a href="<?= BASE_URL ?>/Public/index.php?page=catalog&cat=movies">
                                <img src="<?= BASE_URL ?>/img/movie.png"> Movies
                            </a>
                        </li>

                        <li class="<?= ($section === 'music') ? 'on' : '' ?>">
                            <a href="<?= BASE_URL ?>/Public/index.php?page=catalog&cat=music">
                                <img src="<?= BASE_URL ?>/img/music.png"> Music
                            </a>
                        </li>

                        <li class="<?= ($section === 'suggest') ? 'on' : '' ?>">
                            <a href="<?= BASE_URL ?>/Public/index.php?page=suggest">
                                <img src="<?= BASE_URL ?>/img/suggestion.png"> Suggest
                            </a>
                        </li>

                        <?php if (!empty($_SESSION['user'])): ?>

                            <li class="<?= ($section === 'reservation') ? 'on' : '' ?>">
                                <a href="<?= BASE_URL ?>/Public/index.php?page=reservation">
                                    <img src="<?= BASE_URL ?>/img/book.png"> Reservations
                                </a>
                            </li>

                            <li class="<?= ($section === 'invoices') ? 'on' : '' ?>">
                                <a href="<?= BASE_URL ?>/Public/index.php?page=invoices">
                                    Invoices
                                </a>
                            </li>

                            <!-- 🔔 NOTIFICATIONS -->
                            <li>
                                <a href="<?= BASE_URL ?>/Public/index.php?page=notifications">
                                    🔔 Notifications

                                    <?php if ($unreadCount > 0): ?>
                                        <span class="notif-badge"><?= $unreadCount ?></span>
                                    <?php endif; ?>

                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['user'])): ?>
                            <li class="user-name">
                                <img src="<?= BASE_URL ?>/img/admin.png">
                                <?= htmlspecialchars($_SESSION['user']['username']) ?>

                                <?php if (strtolower($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                                    <span class="role-badge">ADMIN</span>
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($_SESSION['user'])): ?>

                            <?php if (strtolower($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                                <li class="<?= ($section === 'reservation_admin') ? 'on' : '' ?>">
                                    <a href="<?= BASE_URL ?>/Public/index.php?page=reservation_admin">
                                        <img src="<?= BASE_URL ?>/img/approval.png"> Admin Dashboard
                                    </a>
                                </li>
                            <?php endif; ?>

                            <li class="<?= ($section === 'logout') ? 'on' : '' ?>">
                                <a href="<?= BASE_URL ?>/Public/index.php?page=logout">
                                    <img src="<?= BASE_URL ?>/img/logout.png">Logout
                                </a>
                            </li>

                        <?php else: ?>
                            <li class="<?= ($section === 'login') ? 'on' : '' ?>">
                                <a href="<?= BASE_URL ?>/Public/index.php?page=login">
                                    <img src="<?= BASE_URL ?>/img/login.png">Login
                                </a>
                            </li>
                        <?php endif; ?>

                    </ul>

                </div>
            </header>

            <!-- SEARCH BAR -->
            <?php if (empty($hideSearch)): ?>
                <div class="search">
                    <div class="wrapper">
                        <form method="get" action="<?= BASE_URL ?>/Public/index.php">
                            <input type="hidden" name="page" value="catalog">

                            <?php if (!empty($section)): ?>
                                <input type="hidden" name="cat" value="<?= htmlspecialchars($section) ?>">
                            <?php endif; ?>

                            <label for="s">Search:</label>
                            <input type="text" name="s" id="s">
                            <input type="submit" value="Go">
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <main id="content">