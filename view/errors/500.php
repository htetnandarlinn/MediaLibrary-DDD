<?php require BASE_PATH . '/view/layout/header.php'; ?>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/error.css">

<section class="container error-page">
    <h1>⚠️ Something Went Wrong</h1>

    <p>
        Sorry, You have already registere with us.
        Please login to your account.
    </p>

    <a href="<?= BASE_URL ?>/Public/index.php">
        Back to Home
    </a>
</section>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>