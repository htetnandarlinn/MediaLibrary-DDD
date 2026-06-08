<?php

namespace App\Format\Application\UseCase;

use App\Format\Application\DTO\FormatDTO;
use App\Format\Domain\Repository\FormatRepositoryInterface;

class GetFormatsUseCase
{
    public function __construct(
        private FormatRepositoryInterface $repository
    ) {}

    /**
     * @return FormatDTO[]
     */
    public function execute(): array
    {
        $results = $this->repository->getAllFormats();

        $formats = [];

        foreach ($results as $format) {
            // IMPORTANT: $format is Entity, not array
            $formats[] = new FormatDTO(
                id: $format->getId(),
                name: $format->getName(),
                slug: $format->getSlug(),
                description: $format->getDescription(),
                createdAt: $format->getCreatedAt()?->format('Y-m-d H:i:s'),
                updatedAt: $format->getUpdatedAt()?->format('Y-m-d H:i:s'),
            );
        }

        return $formats;
    }
}
