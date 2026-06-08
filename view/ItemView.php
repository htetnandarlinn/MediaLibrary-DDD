<?php
namespace View;

use App\Catalog\Domain\Entity\Catalog;

class ItemView
{
    public static function render(Catalog $item): string
    {
        $id = htmlspecialchars($item->getId(), ENT_QUOTES, 'UTF-8');  // or 'id' if DB says so
        $img = htmlspecialchars($item->getImage(), ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars($item->getTitle(), ENT_QUOTES, 'UTF-8');

        return "
        <li>
            <a href='" . BASE_URL . "/Public/index.php?page=details&id={$id}'>
                <img src='" . BASE_URL . "/{$img}' alt='{$title}' />
                <p>View Details</p>
            </a>
        </li>";
    }
}
