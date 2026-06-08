<?php

namespace App\Catalog\Presentation\Controller;

use App\Catalog\Application\UseCase\GetCatalogItemUseCase;
use App\Catalog\Application\UseCase\GetCatalogPageUseCase;
use App\Catalog\Application\UseCase\GetRandomCatalogUseCase;

class CatalogController
{
    public function __construct(
        private GetCatalogPageUseCase $getCatalogPageUseCase,
        private GetRandomCatalogUseCase $randomCatalogArrayUseCase,
        private GetCatalogItemUseCase $getCatalogItemUseCase
    ) {}

    /**
     * Homepage
     */
    public function home(): void
    {
        $pageTitle = 'Personal Media Library';
        $section = 'catalog';

        $random = $this->randomCatalogArrayUseCase->execute();

        require BASE_PATH . '/view/home.php';
    }

    /**
     * Catalog page (VERY THIN now)
     */
    public function index(): void
    {
        // Controller only passes request data to service
        $data = $this->getCatalogPageUseCase->execute($_GET);

        // Extract variables for view (simple MVC style)
        extract($data);

        require BASE_PATH . '/view/catalog.php';
    }

    /**
     * Show a single catalog item detail page.
     */
    public function details(): void
    {
        $pageTitle = 'Media Details';
        $section = 'catalog';

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id === false || $id === null) {
            header('Location: ' . BASE_URL . '/Public/index.php?page=catalog');
            exit;
        }

        $item = $this->getCatalogItemUseCase->execute($id);

        if ($item === null) {
            header('Location: ' . BASE_URL . '/Public/index.php?page=catalog');
            exit;
        }

        require BASE_PATH . '/App/Catalog/Presentation/View/details.php';
    }
}
