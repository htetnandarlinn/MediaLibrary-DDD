<?php
$pageTitle = $pageTitle ?? 'Payment Status';
$successMessage = $successMessage ?? false;
$message = $message ?? 'Payment completed.';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>

<body>
    <h1><?= htmlspecialchars($pageTitle) ?></h1>
    <p><?= htmlspecialchars($message) ?></p>

    <?php if ($successMessage && isset($invoiceId)): ?>
        <p>Your invoice ID is <?= htmlspecialchars((string) $invoiceId) ?>.</p>
    <?php endif; ?>

    <p><a href="<?= BASE_URL ?>/Public/index.php?page=reservation">Back to reservations</a></p>
</body>

</html>