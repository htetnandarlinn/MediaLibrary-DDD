<?php

require BASE_PATH . '/view/Layout/header.php';
?>
<?php if (empty($item) || !is_array($item)): ?>
    <?php header('Location: ' . BASE_URL . '/Public/index.php?page=catalog');
    exit; ?>
<?php endif; ?>
<div class="section page">
    <div class="wrapper">

        <?php require BASE_PATH . '/view/partials/breadcrumbs.php'; ?>
        <div class="media-container">

            <div class="media-picture">
                <img src="<?= BASE_URL . '/' . htmlspecialchars($item['img']); ?>"
                    alt="<?= htmlspecialchars($item['title']); ?>" />
            </div>
            <div class="media-details">
                <h1><?= htmlspecialchars($item['title']); ?></h1>

                <table>
                    <tr>
                        <th>Category</th>
                        <td><?= htmlspecialchars($item['category']); ?></td>
                    </tr>
                    <tr>
                        <th>Genre</th>
                        <td><?= htmlspecialchars($item['genre']); ?></td>
                    </tr>
                    <tr>
                        <th>Format</th>
                        <td><?= htmlspecialchars($item['format']); ?></td>
                    </tr>
                    <tr>
                        <th>Year</th>
                        <td><?= htmlspecialchars($item['year']); ?></td>
                    </tr>

                    <?php if (strtolower($item['category']) === 'books'): ?>
                        <tr>
                            <th>Authors</th>
                            <td><?= implode(', ', $item['author'] ?? []); ?></td>
                        </tr>
                        <tr>
                            <th>Publisher</th>
                            <td><?= htmlspecialchars($item['publisher'] ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th>ISBN</th>
                            <td><?= htmlspecialchars($item['isbn'] ?? ''); ?></td>
                        </tr>

                    <?php elseif (strtolower($item['category']) === 'movies'): ?>
                        <tr>
                            <th>Director</th>
                            <td><?= implode(', ', $item['director'] ?? []); ?></td>
                        </tr>
                        <tr>
                            <th>Stars</th>
                            <td><?= implode(', ', $item['star'] ?? []); ?></td>
                        </tr>

                    <?php elseif (strtolower($item['category']) === 'music'): ?>
                        <tr>
                            <th>Artist</th>
                            <td><?= implode(', ', $item['artist'] ?? []); ?></td>
                        </tr>
                    <?php endif; ?>
                </table>

                <?php if (in_array(strtolower($item['category']), ['books', 'movies', 'music'], true)): ?>
                    <?php
                    $reserveLabel = 'item';
                    $category = strtolower($item['category']);
                    if ($category === 'books') {
                        $reserveLabel = 'book';
                    } elseif ($category === 'movies') {
                        $reserveLabel = 'movie';
                    } elseif ($category === 'music') {
                        $reserveLabel = 'music item';
                    }
                    ?>
                    <div class="details-actions">
                        <a class="btn" href="<?= BASE_URL ?>/Public/index.php?page=reservation&media_id=<?= (int) $item['media_id'] ?>">
                            Reserve this <?= htmlspecialchars($reserveLabel) ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/view/Layout/footer.php'; ?>