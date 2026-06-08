<?php

namespace App\Suggestion\Domain\Entity;

class Suggestion
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $email,
        private string $category,
        private string $title,
        private ?string $format,
        private ?string $genre,
        private ?string $year,
        private ?string $details,
        private ?\DateTimeImmutable $submittedAt = null
    ) {
        $this->submittedAt = $this->submittedAt ?? new \DateTimeImmutable();
    }

    public static function fromArray(array $data): self
    {
        return new self(
            null,
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['category'] ?? '',
            $data['title'] ?? '',
            $data['format'] ?? null,
            $data['genre'] ?? null,
            $data['year'] ?? null,
            $data['details'] ?? null,
            new \DateTimeImmutable()
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function getSubmittedAt(): ?\DateTimeImmutable
    {
        return $this->submittedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'category' => $this->category,
            'title' => $this->title,
            'format' => $this->format,
            'genre' => $this->genre,
            'year' => $this->year,
            'details' => $this->details,
            'submitted_at' => $this->submittedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public function getEmailBody(): string
    {
        $body = "Name: {$this->name}\n";
        $body .= "Email: {$this->email}\n\n";
        $body .= "Category: {$this->category}\n";
        $body .= "Title: {$this->title}\n";
        $body .= 'Format: ' . ($this->format ?? 'N/A') . "\n";
        $body .= 'Genre: ' . ($this->genre ?? 'N/A') . "\n";
        $body .= 'Year: ' . ($this->year ?? 'N/A') . "\n\n";
        $body .= "Details:\n" . ($this->details ?? 'N/A') . "\n";

        return $body;
    }
}
