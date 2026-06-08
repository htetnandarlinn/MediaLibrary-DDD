<?php

namespace App\Format\Domain\Entity;

/**
 * Format Domain Entity
 *
 * This represents the core business object (Format)
 * and should NOT contain database or framework logic.
 */
class Format
{
    public function __construct(
        private ?int $id,
        private string $name,
        private ?string $slug = null,
        private ?string $description = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {}

    // -------------------
    // Getters
    // -------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    // -------------------
    // Domain Methods (business logic goes here)
    // -------------------

    public function rename(string $newName): void
    {
        $this->name = $newName;
    }

    public function changeDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function hasSlug(): bool
    {
        return $this->slug !== null && $this->slug !== '';
    }

    // -------------------
    // Factory method
    // -------------------

    public static function create(
        string $name,
        ?string $slug = null,
        ?string $description = null
    ): self {
        return new self(
            id: null,
            name: $name,
            slug: $slug,
            description: $description,
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable()
        );
    }

    // -------------------
    // Convert to array (for repository or DTO mapping)
    // -------------------

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}