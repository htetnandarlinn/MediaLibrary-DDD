<?php require BASE_PATH . '/view/layout/header.php'; ?>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/error.css">

<section class="container error-page">
    <div class="error-code">404</div>

    <h1>User Not found</h1>

    <p>
        People you are looking for do not exist or may have been moved.
    </p>

    <a href="<?= BASE_URL ?>/Public/index.php">
        Back to Home
    </a>
</section>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>