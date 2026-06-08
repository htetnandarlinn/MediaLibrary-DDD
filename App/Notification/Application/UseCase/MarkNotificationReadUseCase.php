<?php

namespace App\Notification\Application\UseCase;

use App\Notification\Domain\Repository\NotificationRepositoryInterface;

class MarkNotificationReadUseCase
{
    public function __construct(
        private NotificationRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        $this->repository->markAsRead($id);
    }
}
