<?php

namespace App\Notification\Application\UseCase;

use App\Notification\Domain\Repository\NotificationRepositoryInterface;

class GetNotificationsForUserUseCase
{
    public function __construct(
        private NotificationRepositoryInterface $repository
    ) {}

    public function execute(int $userId): array
    {
        return $this->repository->getByUser($userId);
    }
}
