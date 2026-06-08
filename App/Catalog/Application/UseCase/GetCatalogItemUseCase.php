<?php

namespace App\Catalog\Application\UseCase;

use App\Catalog\Domain\Repository\CatalogRepositoryInterface;
use App\Catalog\Domain\Entity\Catalog;

class GetCatalogItemUseCase
{
    public function __construct(
        private CatalogRepositoryInterface $repo
    ) {}

    public function execute(int $id): ?array
    {
        $item = $this->repo->read($id);

        return $item instanceof Catalog
            ? $item->toArray()
            : null;
    }
}
