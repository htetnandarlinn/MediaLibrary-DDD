<?php

use View\ItemView;

$random = $random ?? [];
require BASE_PATH . '/view/layout/header.php'; ?>


<main class="wrapper">
    <h2 class="title">May we suggest something?</h2>

    <ul class="catalog">
        <?php foreach ($random as $item): ?>
            <?= ItemView::render($item); ?>
        <?php endforeach; ?>
    </ul>
</main>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>