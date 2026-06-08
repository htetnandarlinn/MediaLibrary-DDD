<?php

namespace App\Notification\Domain\Repository;

interface NotificationRepositoryInterface
{
    public function create(?int $userId, string $title, string $message, string $type = 'info', ?string $link = null, ?int $adminUserId = null): bool;

    public function getByUser(int $userId): array;

    public function getForAdmin(?int $adminId = null): array;

    public function countUnread(int $userId): int;

    public function countUnreadForAdmin(?int $adminId = null): int;

    public function markAsRead(int $id): bool;

    public function markAllAsRead(int $userId, bool $isAdmin = false): bool;
}
