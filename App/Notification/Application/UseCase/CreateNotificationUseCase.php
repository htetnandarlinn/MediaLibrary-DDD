<?php

namespace App\Notification\Application\UseCase;

use App\Notification\Domain\Repository\NotificationRepositoryInterface;

class CreateNotificationUseCase
{
    public function __construct(
        private NotificationRepositoryInterface $repository
    ) {}

    public function execute(
        ?int $userId,
        string $title,
        string $message,
        string $type = 'info',
        ?string $link = null,
        ?int $adminUserId = null
    ): bool {
        return $this->repository->create($userId, $title, $message, $type, $link, $adminUserId);
    }
}
