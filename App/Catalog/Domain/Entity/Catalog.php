<?php

namespace App\Catalog\Domain\Entity;

class Catalog
{
    private array $data;

    public function __construct(
        private mixed $media_id,
        private ?string $title = null,
        private ?string $description = null,
        private ?string $image = null,
        array $data = []
    ) {
        $this->data = $data;
    }

    public function getId()
    {
        return $this->media_id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return array_merge($this->data, [
            'media_id' => $this->media_id,
            'title' => $this->title,
            'image' => $this->image,
            'description' => $this->description
        ]);
    }
}
