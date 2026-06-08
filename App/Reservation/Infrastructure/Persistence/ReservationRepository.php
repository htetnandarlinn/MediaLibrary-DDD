<?php

namespace App\Reservation\Infrastructure\Persistence;

use App\Reservation\Domain\Entity\Reservation;
use App\Reservation\Domain\Repository\ReservationRepositoryInterface;
use App\Shared\Infrastructure\Persistence\BaseRepository;
use PDO;

class ReservationRepository extends BaseRepository implements ReservationRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'reservations', 'reservation_id');
        $this->ensureTableExists();
    }

    private function ensureTableExists(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS reservations (
            reservation_id INT NOT NULL AUTO_INCREMENT,
            user_id INT NOT NULL,
            media_id INT NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'PENDING',
            reserved_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            approved_by INT DEFAULT NULL,
            approved_at DATETIME DEFAULT NULL,
            rejected_at DATETIME DEFAULT NULL,
            payment_status VARCHAR(20) NOT NULL DEFAULT 'PENDING',
            payment_amount_cents INT NOT NULL DEFAULT 0,
            payment_days INT NOT NULL DEFAULT 1,
            stripe_session_id VARCHAR(255) DEFAULT NULL,
            stripe_payment_intent_id VARCHAR(255) DEFAULT NULL,
            payment_completed_at DATETIME DEFAULT NULL,
            PRIMARY KEY (reservation_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $this->db->exec($sql);
        $this->ensureSchemaIsUpToDate();
    }

    private function ensureSchemaIsUpToDate(): void
    {
        $requiredColumns = [
            'payment_status' => "ADD COLUMN payment_status VARCHAR(20) NOT NULL DEFAULT 'PENDING'",
            'payment_amount_cents' => 'ADD COLUMN payment_amount_cents INT NOT NULL DEFAULT 0',
            'payment_days' => 'ADD COLUMN payment_days INT NOT NULL DEFAULT 1',
            'stripe_session_id' => 'ADD COLUMN stripe_session_id VARCHAR(255) DEFAULT NULL',
            'stripe_payment_intent_id' => 'ADD COLUMN stripe_payment_intent_id VARCHAR(255) DEFAULT NULL',
            'payment_completed_at' => 'ADD COLUMN payment_completed_at DATETIME DEFAULT NULL'
        ];

        $statement = $this->db->query('SHOW COLUMNS FROM reservations');
        $existingColumns = array_column($statement->fetchAll(PDO::FETCH_ASSOC), 'Field');

        foreach ($requiredColumns as $column => $alterClause) {
            if (!in_array($column, $existingColumns, true)) {
                $this->db->exec(sprintf('ALTER TABLE reservations %s', $alterClause));
            }
        }
    }

    public function create(array|Reservation $reservation): int
    {
        if ($reservation instanceof Reservation) {
            $data = [
                'user_id' => $reservation->getUserId(),
                'media_id' => $reservation->getMediaId(),
                'status' => $reservation->getStatus(),
                'reserved_at' => $reservation->getReservedAt()->format('Y-m-d H:i:s'),
                'payment_status' => $reservation->getPaymentStatus(),
                'payment_amount_cents' => $reservation->getPaymentAmountCents(),
                'payment_days' => $reservation->getPaymentDays(),
                'stripe_session_id' => $reservation->getStripeSessionId(),
                'stripe_payment_intent_id' => $reservation->getStripePaymentIntentId(),
                'payment_completed_at' => $reservation->getPaymentCompletedAt()?->format('Y-m-d H:i:s')
            ];
        } else {
            $data = $reservation;
        }

        return (int) parent::create($data);
    }

    public function findById(int $id): ?Reservation
    {
        return parent::read($id);
    }

    public function findByStripeSessionId(string $sessionId): ?Reservation
    {
        $statement = $this->db->prepare('SELECT * FROM reservations WHERE stripe_session_id = :session_id LIMIT 1');
        $statement->execute(['session_id' => $sessionId]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    public function findByUserId(int $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT r.*, m.title, mt.category
             FROM reservations r
             JOIN Media m ON m.media_id = r.media_id
             JOIN Media_Types mt ON m.media_types_id = mt.media_types_id
             WHERE r.user_id = :user_id
             ORDER BY r.reserved_at DESC'
        );

        $statement->execute(['user_id' => $userId]);

        return array_map(
            fn(array $row) => $this->mapToModel($row),
            $statement->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function findPending(): array
    {
        return $this->findByStatus(Reservation::STATUS_PENDING);
    }

    public function findByStatus(string $status): array
    {
        $statement = $this->db->prepare(
            'SELECT r.*, u.username, m.title, mt.category
             FROM reservations r
             JOIN users u ON u.user_id = r.user_id
             JOIN Media m ON m.media_id = r.media_id
             JOIN Media_Types mt ON m.media_types_id = mt.media_types_id
             WHERE r.status = :status
             ORDER BY r.reserved_at DESC'
        );

        $statement->execute(['status' => $status]);

        return array_map(
            fn(array $row) => $this->mapToModel($row),
            $statement->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function findAll(): array
    {
        $statement = $this->db->prepare(
            'SELECT r.*, u.username, m.title, mt.category
             FROM reservations r
             JOIN users u ON u.user_id = r.user_id
             JOIN Media m ON m.media_id = r.media_id
             JOIN Media_Types mt ON m.media_types_id = mt.media_types_id
             ORDER BY r.reserved_at DESC'
        );

        $statement->execute();

        return array_map(
            fn(array $row) => $this->mapToModel($row),
            $statement->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function existsPendingForUserAndMedia(int $userId, int $mediaId): bool
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*) FROM reservations
             WHERE user_id = :user_id
               AND media_id = :media_id
               AND status = :status'
        );
        $statement->execute([
            'user_id' => $userId,
            'media_id' => $mediaId,
            'status' => Reservation::STATUS_PENDING
        ]);

        return (int) $statement->fetchColumn() > 0;
    }

    public function countAll(): int
    {
        return (int) $this->db
            ->query('SELECT COUNT(*) FROM reservations')
            ->fetchColumn();
    }

    public function countByStatus(string $status): int
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*) FROM reservations WHERE status = :status'
        );
        $statement->execute(['status' => $status]);

        return (int) $statement->fetchColumn();
    }

    public function findLatestApproved(int $limit = 5): array
    {
        $statement = $this->db->prepare(
            'SELECT r.*, u.username, m.title, mt.category
             FROM reservations r
             JOIN users u ON u.user_id = r.user_id
             JOIN Media m ON m.media_id = r.media_id
             JOIN Media_Types mt ON m.media_types_id = mt.media_types_id
             WHERE r.status = :status
             ORDER BY r.approved_at DESC
             LIMIT :limit'
        );

        $statement->bindValue(':status', Reservation::STATUS_APPROVED);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map(
            fn(array $row) => $this->mapToModel($row),
            $statement->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function update(int $id, array $data): bool
    {
        return parent::update($id, $data);
    }

    public function isBookMedia(int $mediaId): bool
    {
        $statement = $this->db->prepare(
            'SELECT 1
             FROM Media m
             JOIN Media_Types mt ON m.media_types_id = mt.media_types_id
             WHERE m.media_id = :media_id
               AND LOWER(mt.category) = :category
             LIMIT 1'
        );

        $statement->execute([
            'media_id' => $mediaId,
            'category' => 'books'
        ]);

        return (bool) $statement->fetchColumn();
    }

    protected function mapToModel(array $row): Reservation
    {
        return new Reservation(
            $row['reservation_id'] ?? null,
            (int) $row['user_id'],
            (int) $row['media_id'],
            $row['status'],
            new \DateTimeImmutable($row['reserved_at']),
            isset($row['approved_by']) ? (int) $row['approved_by'] : null,
            !empty($row['approved_at']) ? new \DateTimeImmutable($row['approved_at']) : null,
            !empty($row['rejected_at']) ? new \DateTimeImmutable($row['rejected_at']) : null,
            $row['payment_status'] ?? Reservation::PAYMENT_PENDING,
            isset($row['payment_amount_cents']) ? (int) $row['payment_amount_cents'] : 0,
            isset($row['payment_days']) ? (int) $row['payment_days'] : 1,
            $row['stripe_session_id'] ?? null,
            $row['stripe_payment_intent_id'] ?? null,
            !empty($row['payment_completed_at']) ? new \DateTimeImmutable($row['payment_completed_at']) : null,
            $row['username'] ?? null,
            $row['title'] ?? null,
            $row['category'] ?? null
        );
    }
}
