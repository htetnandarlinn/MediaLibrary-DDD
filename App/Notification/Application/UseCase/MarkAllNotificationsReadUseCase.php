<?php

namespace App\Notification\Application\UseCase;

use App\Notification\Domain\Repository\NotificationRepositoryInterface;

class MarkAllNotificationsReadUseCase
{
    public function __construct(
        private NotificationRepositoryInterface $repository
    ) {}

    public function execute(int $userId, bool $isAdmin = false): void
    {
        $this->repository->markAllAsRead($userId, $isAdmin);
    }
}
