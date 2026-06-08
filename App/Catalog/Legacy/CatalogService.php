<?php

namespace App\Catalog\Legacy;

use App\Catalog\Domain\Entity\Catalog;
use App\Catalog\Domain\Repository\CatalogRepositoryInterface;
use App\Catalog\Infrastructure\Persistence\CatalogRepository;
use App\Shared\Database\Database;

class CatalogService
{
    private const ITEMS_PER_PAGE = 8;

    private const ALLOWED_CATEGORIES = [
        'books',
        'movies',
        'music'
    ];

    private CatalogRepositoryInterface $repo;

    public function __construct(?CatalogRepositoryInterface $repo = null)
    {
        if ($repo === null) {
            $db = Database::getConnection();
            $repo = new CatalogRepository($db);
        }

        $this->repo = $repo;
    }

    /*
     * =========================================================
     * MAIN METHOD (Controller calls ONLY this)
     * =========================================================
     */
    public function getCatalogPage(array $queryParams): array
    {
        $section = $this->getCategory($queryParams);
        $search = $this->getSearchTerm($queryParams);
        $currentPage = $this->getCurrentPage($queryParams);

        $totalItems = $this->getCatalogCount($section, $search) ?? 0;

        $pagination = $this->buildPagination($totalItems, $currentPage);

        $catalog = $this->loadCatalogData(
            $section,
            $search,
            $pagination['limit'],
            $pagination['offset']
        );

        $foundInFullCatalog = 0;

        if ($search !== null && $section !== null) {
            $foundInFullCatalog = $this->getCatalogCount(null, $search) ?? 0;
        }

        return [
            'catalog' => $catalog,
            'section' => $section,
            'search' => $search,
            'currentPage' => $pagination['currentPage'],
            'totalPages' => $pagination['totalPages'],
            'current_page' => $pagination['currentPage'],
            'total_pages' => $pagination['totalPages'],
            'total_items' => $totalItems,
            'found_in_full_catalog' => $foundInFullCatalog,
            'pageTitle' => $this->buildPageTitle($section)
        ];
    }

    /*
     * =========================================================
     * FILTER: CATEGORY
     * =========================================================
     */
    private function getCategory(array $queryParams): ?string
    {
        $category = $queryParams['cat'] ?? null;

        if (
            $category !== null &&
            in_array($category, self::ALLOWED_CATEGORIES, true)
        ) {
            return strtolower($category);
        }

        return null;
    }

    /*
     * =========================================================
     * FILTER: SEARCH
     * =========================================================
     */
    private function getSearchTerm(array $queryParams): ?string
    {
        $search = trim($queryParams['s'] ?? '');

        return $search !== '' ? $search : null;
    }

    /*
     * =========================================================
     * PAGINATION: CURRENT PAGE
     * =========================================================
     */
    private function getCurrentPage(array $queryParams): int
    {
        $page = filter_var(
            $queryParams['pg'] ?? 1,
            FILTER_VALIDATE_INT
        );

        if ($page === false || $page === null || $page < 1) {
            return 1;
        }

        return $page;
    }

    /*
     * =========================================================
     * PAGINATION LOGIC
     * =========================================================
     */
    private function buildPagination(?int $totalItems, int $currentPage): array
    {
        $totalItems = $totalItems ?? 0;

        $totalPages = max(
            1,
            (int) ceil($totalItems / self::ITEMS_PER_PAGE)
        );

        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $offset = ($currentPage - 1) * self::ITEMS_PER_PAGE;

        return [
            'limit' => self::ITEMS_PER_PAGE,
            'offset' => $offset,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ];
    }

    /*
     * =========================================================
     * LOAD DATA (DECISION LOGIC MOVED HERE)
     * =========================================================
     */
    private function loadCatalogData(
        ?string $section,
        ?string $search,
        int $limit,
        int $offset
    ): array {
        if ($search !== null && $section !== null) {
            return $this->searchCatalogArray(
                $search,
                $section,
                $limit,
                $offset
            );
        }

        if ($search !== null) {
            return $this->searchCatalogArray(
                $search,
                null,
                $limit,
                $offset
            );
        }

        if ($section !== null) {
            return $this->categoryCatalogArray(
                $section,
                $limit,
                $offset
            );
        }

        return $this->fullCatalogArray(
            $limit,
            $offset
        );
    }

    /*
     * =========================================================
     * PAGE TITLE
     * =========================================================
     */
    private function buildPageTitle(?string $section): string
    {
        return $section ? ucfirst($section) : 'Full Catalog';
    }

    /*
     * =========================================================
     * DATABASE METHODS (already existed in your system)
     * =========================================================
     */

    public function getCatalogCount($section, $search)
    {
        $criteria = [];

        if ($section !== null) {
            $criteria['category'] = $section;
        }

        if ($search !== null) {
            $criteria['title'] = '%' . $search . '%';
        }

        return $this->repo->count($criteria);
    }

    public function searchCatalogArray($search, $section, $limit, $offset)
    {
        $criteria = [];

        if ($section !== null) {
            $criteria['category'] = $section;
        }

        if ($search !== null) {
            $criteria['title'] = '%' . $search . '%';
        }

        return $this->repo->getAll($criteria, $limit, $offset);
    }

    public function categoryCatalogArray($section, $limit, $offset)
    {
        return $this->repo->getAll(
            [
                'category' => $section
            ],
            $limit,
            $offset
        );
    }

    public function fullCatalogArray($limit, $offset)
    {
        return $this->repo->getAll([], $limit, $offset);
    }

    public function randomCatalogArray()
    {
        return $this->repo->getRandomCatalog();
    }

    public function singleItemArray($id)
    {
        $item = $this->repo->read($id);

        return $item instanceof Catalog ? $item->toArray() : $item;
    }
}
