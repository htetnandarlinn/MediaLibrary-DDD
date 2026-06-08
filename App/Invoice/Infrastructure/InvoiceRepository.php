<?php

namespace App\Invoice\Infrastructure;

use App\Invoice\Domain\Repository\InvoiceRepositoryInterface;
use PDO;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getByUserId(int $userId): array
    {
        $stmt = $this->db->prepare('
            SELECT i.*, u.username AS user_name, u.email
            FROM invoices i
            LEFT JOIN users u ON u.user_id = i.user_id
            WHERE i.user_id = ?
            ORDER BY i.id DESC
        ');

        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $sql = '
            SELECT i.*, u.username AS user_name, u.email
            FROM invoices i
            LEFT JOIN users u ON u.user_id = i.user_id
            WHERE i.id = :id
        ';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        return $invoice === false ? null : $invoice;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('
            SELECT i.*, u.username AS user_name, u.email
            FROM invoices i
            LEFT JOIN users u ON u.user_id = i.user_id
            ORDER BY i.id DESC
        ');

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByReservationIdOrPaymentIntent(int $reservationId, ?string $paymentIntentId): ?array
    {
        $sql = '
            SELECT * FROM invoices
            WHERE reservation_id = :reservation_id'
            . (!empty($paymentIntentId) ? ' OR payment_intent_id = :payment_intent_id' : '')
            . ' LIMIT 1';

        $params = ['reservation_id' => $reservationId];

        if (!empty($paymentIntentId)) {
            $params['payment_intent_id'] = $paymentIntentId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        return $invoice === false ? null : $invoice;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO invoices (invoice_number, reservation_id, user_id, payment_intent_id, amount, currency, status, created_at)
             VALUES (:invoice_number, :reservation_id, :user_id, :payment_intent_id, :amount, :currency, :status, NOW())'
        );

        $stmt->execute([
            'invoice_number' => $data['invoice_number'],
            'reservation_id' => $data['reservation_id'],
            'user_id' => $data['user_id'],
            'payment_intent_id' => $data['payment_intent_id'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'status' => $data['status'] ?? 'PAID'
        ]);

        return (int) $this->db->lastInsertId();
    }
}
