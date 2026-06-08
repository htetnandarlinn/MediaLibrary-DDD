<?php

namespace App\Invoice\Domain\Entity;

class Invoice
{
    public function __construct(
        private int $id,
        private int $userId,
        private string $invoiceNumber,
        private string $status,
        private float $total,
        private string $createdAt
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
