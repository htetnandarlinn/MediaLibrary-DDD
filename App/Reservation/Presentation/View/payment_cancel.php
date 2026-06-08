<?php
$pageTitle = $pageTitle ?? 'Payment Cancelled';
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
    <p>Your payment was cancelled. No charges were made.</p>
    <p><a href="<?= BASE_URL ?>/Public/index.php?page=reservation">Return to reservations</a></p>
</body>

</html>