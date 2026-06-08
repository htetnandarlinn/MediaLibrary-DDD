<?php

namespace App\Catalog\Domain\Repository;

use App\Catalog\Domain\Entity\Catalog;
use App\Shared\Contract\BaseInterface;

interface CatalogRepositoryInterface extends BaseInterface
{
    public function getRandomCatalog(): array;

    public function read(int $id): ?Catalog;
}
