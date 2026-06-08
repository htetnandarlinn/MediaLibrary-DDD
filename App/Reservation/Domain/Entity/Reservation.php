<?php

namespace App\Reservation\Domain\Entity;

class Reservation
{
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_REJECTED = 'REJECTED';
    public const PAYMENT_PENDING = 'PENDING';
    public const PAYMENT_COMPLETED = 'COMPLETED';
    public const PAYMENT_FAILED = 'FAILED';

    public function __construct(
        private ?int $id,
        private int $userId,
        private int $mediaId,
        private string $status,
        private \DateTimeImmutable $reservedAt,
        private ?int $approvedBy = null,
        private ?\DateTimeImmutable $approvedAt = null,
        private ?\DateTimeImmutable $rejectedAt = null,
        private string $paymentStatus = self::PAYMENT_PENDING,
        private int $paymentAmountCents = 0,
        private int $paymentDays = 1,
        private ?string $stripeSessionId = null,
        private ?string $stripePaymentIntentId = null,
        private ?\DateTimeImmutable $paymentCompletedAt = null,
        private ?string $userName = null,
        private ?string $mediaTitle = null,
        private ?string $mediaCategory = null
    ) {
        $this->reservedAt = $this->reservedAt ?? new \DateTimeImmutable();
    }

    public static function create(int $userId, int $mediaId, int $paymentDays, int $paymentAmountCents): self
    {
        return new self(
            null,
            $userId,
            $mediaId,
            self::STATUS_PENDING,
            new \DateTimeImmutable(),
            null,
            null,
            null,
            self::PAYMENT_PENDING,
            $paymentAmountCents,
            $paymentDays,
            null,
            null,
            null,
            null,
            null,
            null
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['reservation_id'] ?? null,
            $data['user_id'],
            $data['media_id'],
            $data['status'],
            new \DateTimeImmutable($data['reserved_at']),
            $data['approved_by'] ?? null,
            !empty($data['approved_at']) ? new \DateTimeImmutable($data['approved_at']) : null,
            !empty($data['rejected_at']) ? new \DateTimeImmutable($data['rejected_at']) : null,
            $data['payment_status'] ?? self::PAYMENT_PENDING,
            isset($data['payment_amount_cents']) ? (int) $data['payment_amount_cents'] : 0,
            isset($data['payment_days']) ? (int) $data['payment_days'] : 1,
            $data['stripe_session_id'] ?? null,
            $data['stripe_payment_intent_id'] ?? null,
            !empty($data['payment_completed_at']) ? new \DateTimeImmutable($data['payment_completed_at']) : null,
            $data['username'] ?? null,
            $data['title'] ?? $data['media_title'] ?? null,
            $data['category'] ?? $data['media_category'] ?? null
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getMediaId(): int
    {
        return $this->mediaId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getReservedAt(): \DateTimeImmutable
    {
        return $this->reservedAt;
    }

    public function getApprovedBy(): ?int
    {
        return $this->approvedBy;
    }

    public function getApprovedAt(): ?\DateTimeImmutable
    {
        return $this->approvedAt;
    }

    public function getRejectedAt(): ?\DateTimeImmutable
    {
        return $this->rejectedAt;
    }

    public function getPaymentStatus(): string
    {
        return $this->paymentStatus;
    }

    public function getPaymentAmountCents(): int
    {
        return $this->paymentAmountCents;
    }

    public function getPaymentDays(): int
    {
        return $this->paymentDays;
    }

    public function getStripeSessionId(): ?string
    {
        return $this->stripeSessionId;
    }

    public function getStripePaymentIntentId(): ?string
    {
        return $this->stripePaymentIntentId;
    }

    public function getPaymentCompletedAt(): ?\DateTimeImmutable
    {
        return $this->paymentCompletedAt;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPaymentPending(): bool
    {
        return $this->paymentStatus === self::PAYMENT_PENDING;
    }

    public function canStartCheckout(): bool
    {
        return $this->status === self::STATUS_APPROVED &&
            in_array($this->paymentStatus, [self::PAYMENT_PENDING, self::PAYMENT_FAILED], true);
    }

    public function getMediaTitle(): ?string
    {
        return $this->mediaTitle;
    }

    public function getMediaCategory(): ?string
    {
        return $this->mediaCategory;
    }

    public function approve(int $adminId): void
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \LogicException('Only pending reservations can be approved.');
        }

        $this->status = self::STATUS_APPROVED;
        $this->approvedBy = $adminId;
        $this->approvedAt = new \DateTimeImmutable();
    }

    public function reject(int $adminId): void
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \LogicException('Only pending reservations can be rejected.');
        }

        $this->status = self::STATUS_REJECTED;
        $this->approvedBy = $adminId;
        $this->rejectedAt = new \DateTimeImmutable();
    }

    public function markPaymentCompleted(string $sessionId, ?string $paymentIntentId = null): void
    {
        $this->paymentStatus = self::PAYMENT_COMPLETED;
        $this->stripeSessionId = $sessionId;
        $this->stripePaymentIntentId = $paymentIntentId;
        $this->paymentCompletedAt = new \DateTimeImmutable();
    }

    public function markPaymentFailed(): void
    {
        $this->paymentStatus = self::PAYMENT_FAILED;
    }

    public function toArray(): array
    {
        return [
            'reservation_id' => $this->id,
            'user_id' => $this->userId,
            'media_id' => $this->mediaId,
            'status' => $this->status,
            'reserved_at' => $this->reservedAt->format('Y-m-d H:i:s'),
            'approved_by' => $this->approvedBy,
            'approved_at' => $this->approvedAt?->format('Y-m-d H:i:s'),
            'rejected_at' => $this->rejectedAt?->format('Y-m-d H:i:s'),
            'payment_status' => $this->paymentStatus,
            'payment_amount_cents' => $this->paymentAmountCents,
            'payment_days' => $this->paymentDays,
            'stripe_session_id' => $this->stripeSessionId,
            'stripe_payment_intent_id' => $this->stripePaymentIntentId,
            'payment_completed_at' => $this->paymentCompletedAt?->format('Y-m-d H:i:s'),
            'username' => $this->userName,
            'media_title' => $this->mediaTitle,
            'media_category' => $this->mediaCategory,
        ];
    }
}
