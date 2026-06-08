<?php

namespace App\Catalog\Application\UseCase;

use App\Catalog\Domain\Repository\CatalogRepositoryInterface;

class GetCatalogPageUseCase
{
    private const ITEMS_PER_PAGE = 8;
    private const ALLOWED_CATEGORIES = ['books', 'movies', 'music'];

    public function __construct(
        private CatalogRepositoryInterface $repo
    ) {}

    public function execute(array $query): array
    {
        $category = $this->getCategory($query);
        $search = $this->getSearch($query);
        $page = $this->getPage($query);

        $total = $this->count($category, $search);

        $pagination = $this->paginate($total, $page);

        $items = $this->load($category, $search, $pagination);

        $foundInFull = ($search && $category)
            ? $this->count(null, $search)
            : 0;

        return [
            'catalog' => $items,
            'section' => $category,
            'search' => $search,
            'currentPage' => $pagination['currentPage'],
            'totalPages' => $pagination['totalPages'],
            'total_items' => $total,
            'found_in_full_catalog' => $foundInFull,
            'pageTitle' => $category ? ucfirst($category) : 'Full Catalog'
        ];
    }

    private function getCategory(array $q): ?string
    {
        $cat = $q['cat'] ?? null;

        return in_array($cat, self::ALLOWED_CATEGORIES, true)
            ? strtolower($cat)
            : null;
    }

    private function getSearch(array $q): ?string
    {
        $s = trim($q['s'] ?? '');
        return $s !== '' ? $s : null;
    }

    private function getPage(array $q): int
    {
        $p = filter_var($q['pg'] ?? 1, FILTER_VALIDATE_INT);
        return ($p && $p > 0) ? $p : 1;
    }

    private function paginate(int $total, int $page): array
    {
        $pages = max(1, (int) ceil($total / self::ITEMS_PER_PAGE));

        return [
            'limit' => self::ITEMS_PER_PAGE,
            'offset' => ($page - 1) * self::ITEMS_PER_PAGE,
            'currentPage' => min($page, $pages),
            'totalPages' => $pages
        ];
    }

    private function load(?string $cat, ?string $search, array $p): array
    {
        $criteria = [];

        if ($cat) {
            $criteria['category'] = $cat;
        }

        if ($search) {
            $criteria['title'] = '%' . $search . '%';
        }

        return $this->repo->getAll($criteria, $p['limit'], $p['offset']);
    }

    private function count(?string $cat, ?string $search): int
    {
        $criteria = [];

        if ($cat) {
            $criteria['category'] = $cat;
        }

        if ($search) {
            $criteria['title'] = '%' . $search . '%';
        }

        return $this->repo->count($criteria);
    }
}
