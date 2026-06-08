<?php

namespace App\Format\Application\UseCase;

use App\Format\Domain\Repository\FormatRepositoryInterface;

class GetGenresUseCase
{
    public function __construct(
        private FormatRepositoryInterface $repository
    ) {}

    /**
     * Get all genres linked to formats
     */
    public function execute(): array
    {
        return $this->repository->getGenres();
    }
}
