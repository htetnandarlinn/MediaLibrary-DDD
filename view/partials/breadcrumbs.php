<?php if (!empty($item) && is_array($item) && isset($item['category'], $item['title'])): ?>
<div class="breadcrumbs">
            <a href="index.php?page=catalog">Full Catalog</a>
            <span class="separator">&gt;</span>

            <a href="index.php?page=catalog&cat=<?= urlencode(strtolower($item['category'])) ?>">
                <?= htmlspecialchars($item['category']); ?>
            </a>
            <span class="separator">&gt;</span>

            <span class="current">
                <?= htmlspecialchars($item['title']); ?>
            </span>
</div>
<?php endif; ?>