<?php

namespace App\Notification\Application\UseCase;

use App\Notification\Domain\Repository\NotificationRepositoryInterface;

class GetNotificationsForAdminUseCase
{
    public function __construct(
        private NotificationRepositoryInterface $repository
    ) {}

    public function execute(?int $adminId = null): array
    {
        return $this->repository->getForAdmin($adminId);
    }
}
