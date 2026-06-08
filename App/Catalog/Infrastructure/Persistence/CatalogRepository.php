<?php

namespace App\Catalog\Infrastructure\Persistence;

use App\Catalog\Domain\Entity\Catalog;
use App\Catalog\Domain\Repository\CatalogRepositoryInterface;
use App\Shared\Infrastructure\Persistence\BaseRepository;
use PDO;

class CatalogRepository extends BaseRepository implements CatalogRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct(
            $db,
            'view_catalog',
            'media_id'
        );
    }

    protected function mapToModel(array $row): Catalog
    {
        return new Catalog(
            $row['media_id'],
            $row['title'] ?? null,
            $row['description'] ?? null,
            $row['img'] ?? null,
            $row
        );
    }

    public function getRandomCatalog(): array
    {
        $result = $this->db->query(
            'SELECT * FROM view_random'
        );

        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn(array $row) => $this->mapToModel($row),
            $rows
        );
    }

    public function read(int $id): ?Catalog
    {
        $statement = $this->db->prepare(
            'SELECT * FROM view_item_detail WHERE media_id = ?'
        );

        $statement->execute([$id]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return null;
        }

        $item = $rows[0];

        foreach ($rows as $row) {
            if (
                empty($row['role']) ||
                empty($row['fullname'])
            ) {
                continue;
            }

            $role = strtolower($row['role']);

            $item[$role][] =
                $row['fullname'];
        }

        return $this->mapToModel($item);
    }
}
