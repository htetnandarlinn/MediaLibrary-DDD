<?php

use App\Catalog\Presentation\Controller\CatalogApiController;
use App\Suggestion\Presentation\Controller\SuggestApiController;

switch ($page) {
    case 'api/catalog':
        $controller = new CatalogApiController($catalogPageService, $catalogItemService);
        $controller->index();
        break;

    case 'api/suggest':
        $controller = new SuggestApiController($sendSuggestionUseCase);
        $controller->index();
        break;
}
