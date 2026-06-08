<?php
$total_pages = $total_pages ?? 0;
$current_page = $current_page ?? 1;
$section = $section ?? null;
$search = $search ?? null;
?>
<?php
$total_pages = $total_pages ?? 0;
$current_page = $current_page ?? 1;
$section = $section ?? null;
$search = $search ?? null;
?>
<?php if ($total_pages > 1): ?>
    <div class="pagination">
        Pages:
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>

            <?php if ($i == $current_page): ?>
                <span><?= $i ?></span>
            <?php else: ?>
                <?php
                $query = [
                    'page' => 'catalog',
                    'pg' => $i
                ];
                if (!empty($section))
                    $query['cat'] = strtolower($section);
                if (!empty($search))
                    $query['s'] = $search;
                ?>
                <a href="index.php?<?= http_build_query($query) ?>">
                    <?= $i ?>
                </a>
            <?php endif; ?>

        <?php endfor; ?>
    </div>
<?php endif; ?>