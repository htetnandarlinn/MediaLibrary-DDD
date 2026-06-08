<?php

namespace App\Notification\Presentation\Controller;

use App\Notification\Application\UseCase\GetNotificationsForAdminUseCase;
use App\Notification\Application\UseCase\GetNotificationsForUserUseCase;
use App\Notification\Application\UseCase\MarkNotificationReadUseCase;

class NotificationController
{
    public function __construct(
        private GetNotificationsForUserUseCase $getUserNotificationsUseCase,
        private GetNotificationsForAdminUseCase $getAdminNotificationsUseCase,
        private MarkNotificationReadUseCase $markNotificationReadUseCase
    ) {}

    public function index()
    {
        if (empty($_SESSION['user'])) {
            exit('Please login first to view notifications');
        }

        $userId = $_SESSION['user']['user_id'];
        $isAdmin = strtolower($_SESSION['user']['role'] ?? '') === 'admin';

        if ($isAdmin) {
            $notifications = $this->getAdminNotificationsUseCase->execute($userId);
            require BASE_PATH . '/App/Notification/Presentation/View/admin_notifications.php';
        } else {
            $notifications = $this->getUserNotificationsUseCase->execute($userId);
            require BASE_PATH . '/App/Notification/Presentation/View/index.php';
        }

        foreach ($notifications as $notification) {
            if (empty($notification['is_read'])) {
                try {
                    $this->markNotificationReadUseCase->execute((int) $notification['id']);
                } catch (\Throwable $e) {
                    // ignore mark-as-read failures
                }
            }
        }
    }

    public function markRead(): void
    {
        if (empty($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/Public/index.php?page=login');
            exit;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            try {
                $this->markNotificationReadUseCase->execute($id);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $back = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/Public/index.php?page=notifications';
        header('Location: ' . $back);
        exit;
    }
}
