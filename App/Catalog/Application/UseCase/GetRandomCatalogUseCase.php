<?php

namespace App\Catalog\Application\UseCase;

use App\Catalog\Domain\Repository\CatalogRepositoryInterface;

class GetRandomCatalogUseCase
{
    public function __construct(
        private CatalogRepositoryInterface $repo
    ) {}

    public function execute(): array
    {
        return $this->repo->getRandomCatalog();
    }
}
