<?php

namespace App\Notification\Presentation\Controller;

use App\Notification\Application\UseCase\GetNotificationsForAdminUseCase;
use App\Notification\Application\UseCase\MarkAllNotificationsReadUseCase;
use App\Notification\Application\UseCase\MarkNotificationReadUseCase;

class NotificationApiController
{
    public function __construct(
        private GetNotificationsForAdminUseCase $getNotificationsForAdminUseCase,
        private MarkNotificationReadUseCase $markNotificationReadUseCase,
        private MarkAllNotificationsReadUseCase $markAllNotificationsReadUseCase
    ) {}

    public function adminHeaderData()
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['user'])) {
            echo json_encode([
                'unread' => 0,
                'notifications' => []
            ]);
            return;
        }

        $userId = $_SESSION['user']['user_id'];
        $isAdmin = strtolower($_SESSION['user']['role'] ?? '') === 'admin';

        $list = $this->getNotificationsForAdminUseCase->execute($userId);
        $unread = count(array_filter($list, fn($notification) => empty($notification['is_read'])));

        echo json_encode([
            'unread' => $unread,
            'notifications' => array_slice($list, 0, 6)
        ]);
    }

    public function markRead()
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['user'])) {
            echo json_encode(['success' => false]);
            return;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            echo json_encode(['success' => false]);
            return;
        }

        try {
            $this->markNotificationReadUseCase->execute($id);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false]);
            return;
        }

        echo json_encode(['success' => true]);
    }

    public function markAllRead()
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['user'])) {
            echo json_encode(['success' => false]);
            return;
        }

        $userId = $_SESSION['user']['user_id'];
        $isAdmin = strtolower($_SESSION['user']['role'] ?? '') === 'admin';

        try {
            $this->markAllNotificationsReadUseCase->execute($userId, $isAdmin);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false]);
            return;
        }

        echo json_encode(['success' => true]);
    }
}
