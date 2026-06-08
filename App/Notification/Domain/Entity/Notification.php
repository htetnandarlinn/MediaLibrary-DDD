<?php

namespace App\Notification\Domain\Entity;

class Notification
{
    public function __construct(
        private ?int $id,
        private ?int $userId,
        private ?int $adminUserId,
        private string $title,
        private string $message,
        private string $type,
        private bool $isRead,
        private ?string $link,
        private string $createdAt
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getAdminUserId(): ?int
    {
        return $this->adminUserId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
