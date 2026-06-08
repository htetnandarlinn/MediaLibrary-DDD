<?php

namespace App\Format\Application\UseCase;



use App\Format\Domain\Repository\FormatRepositoryInterface;

class GetCategoriesUseCase
{
    public function __construct(
        private FormatRepositoryInterface $repository
    ) {}

    /**
     * Get all categories related to formats
     */
    public function execute(): array
    {
        return $this->repository->getCategories();
    }
}
