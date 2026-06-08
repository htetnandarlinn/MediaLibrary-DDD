<?php

namespace App\Notification\Infrastructure\Persistence;

use App\Notification\Domain\Repository\NotificationRepositoryInterface;
use PDO;

class NotificationRepository implements NotificationRepositoryInterface
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;

        try {
            $this->ensureTableExists();
        } catch (\Throwable $e) {
            error_log('NotificationRepository schema check failed: ' . $e->getMessage());
        }
    }

    private function ensureTableExists(): void
    {
        $stmt = $this->db->query("SHOW TABLES LIKE 'notifications'");
        $exists = (bool) $stmt->fetch();

        if (!$exists) {
            $sql = "CREATE TABLE notifications (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id INT NULL,
                admin_user_id INT NULL,
                title VARCHAR(255) NULL,
                message TEXT NOT NULL,
                type VARCHAR(50) NOT NULL DEFAULT 'info',
                is_read TINYINT(1) NOT NULL DEFAULT 0,
                link VARCHAR(255) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

            $this->db->exec($sql);
            return;
        }

        $required = [
            'admin_user_id' => 'ADD COLUMN admin_user_id INT NULL',
            'title' => 'ADD COLUMN title VARCHAR(255) NULL',
            'type' => "ADD COLUMN type VARCHAR(50) NOT NULL DEFAULT 'info'",
            'is_read' => 'ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0',
            'link' => 'ADD COLUMN link VARCHAR(255) NULL',
            'created_at' => 'ADD COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP'
        ];

        $colsStmt = $this->db->query('SHOW COLUMNS FROM notifications');
        $existing = array_column($colsStmt->fetchAll(PDO::FETCH_ASSOC), 'Field');

        foreach ($required as $col => $alter) {
            if (!in_array($col, $existing, true)) {
                $this->db->exec("ALTER TABLE notifications $alter");
            }
        }
    }

    public function create(?int $userId, string $title, string $message, string $type = 'info', ?string $link = null, ?int $adminUserId = null): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO notifications (user_id, admin_user_id, title, message, type, link)
             VALUES (?, ?, ?, ?, ?, ?)'
        );

        return $stmt->execute([
            $userId,
            $adminUserId,
            $title,
            $message,
            $type,
            $link
        ]);
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare('
            SELECT *
            FROM notifications
            WHERE user_id = ?
            ORDER BY created_at DESC
        ');

        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getForAdmin(?int $adminId = null): array
    {
        if ($adminId === null) {
            $stmt = $this->db->prepare(
                'SELECT * FROM notifications
                 WHERE user_id IS NULL
                 ORDER BY created_at DESC'
            );
            $stmt->execute();
        } else {
            $stmt = $this->db->prepare(
                'SELECT * FROM notifications
                 WHERE (user_id IS NULL OR admin_user_id = :admin_id)
                 ORDER BY created_at DESC'
            );
            $stmt->execute(['admin_id' => $adminId]);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countUnread(int $userId): int
    {
        $stmt = $this->db->prepare('
            SELECT COUNT(*)
            FROM notifications
            WHERE user_id = ?
            AND is_read = 0
        ');

        $stmt->execute([$userId]);

        return (int) $stmt->fetchColumn();
    }

    public function countUnreadForAdmin(?int $adminId = null): int
    {
        if ($adminId === null) {
            $stmt = $this->db->prepare(
                'SELECT COUNT(*)
                 FROM notifications
                 WHERE user_id IS NULL
                 AND is_read = 0'
            );
            $stmt->execute();
        } else {
            $stmt = $this->db->prepare(
                'SELECT COUNT(*)
                 FROM notifications
                 WHERE (user_id IS NULL OR admin_user_id = :admin_id)
                 AND is_read = 0'
            );
            $stmt->execute(['admin_id' => $adminId]);
        }

        return (int) $stmt->fetchColumn();
    }

    // ✅ UPDATED (clean + consistent)
    public function markAsRead(int $id): bool
    {
        $stmt = $this->db->prepare('
            UPDATE notifications
            SET is_read = 1
            WHERE id = ?
        ');

        return $stmt->execute([$id]);
    }

    // ✅ UPDATED: mark all as read for the current viewer
    public function markAllAsRead(int $userId, bool $isAdmin = false): bool
    {
        if ($isAdmin) {
            $stmt = $this->db->prepare('
                UPDATE notifications
                SET is_read = 1
                WHERE user_id IS NULL
                   OR admin_user_id = ?
            ');
            return $stmt->execute([$userId]);
        }

        $stmt = $this->db->prepare('
            UPDATE notifications
            SET is_read = 1
            WHERE user_id = ?
        ');

        return $stmt->execute([$userId]);
    }
}
