<?php

/** @var array $invoice */
require BASE_PATH . '/view/layout/admin_header.php';

$statusClass = strtolower($invoice['status'] ?? 'pending');
?>

<div class="invoice-detail-page">

    <div class="invoice-header">
        <h2>Invoice Details</h2>

        <span class="status-badge <?= $statusClass ?>">
            <?= htmlspecialchars($invoice['status'] ?? 'PENDING') ?>
        </span>
    </div>

    <div class="invoice-card">

        <div class="invoice-grid">

            <div class="item">
                <label>Invoice No</label>
                <div><?= htmlspecialchars($invoice['invoice_number']) ?></div>
            </div>

            <div class="item">
                <label>User</label>
                <div><?= htmlspecialchars($invoice['user_name'] ?? $invoice['name'] ?? '') ?></div>
            </div>

            <div class="item">
                <label>Email</label>
                <div><?= htmlspecialchars($invoice['email'] ?? '') ?></div>
            </div>

            <div class="item">
                <label>Reservation ID</label>
                <div><?= htmlspecialchars($invoice['reservation_id'] ?? '') ?></div>
            </div>

            <div class="item">
                <label>Payment Intent</label>
                <div><?= htmlspecialchars($invoice['payment_intent_id'] ?? '') ?></div>
            </div>

            <div class="item">
                <label>Date</label>
                <div><?= htmlspecialchars($invoice['created_at'] ?? '') ?></div>
            </div>

        </div>

        <div class="invoice-amount">
            <span>Total Amount</span>
            <h3>
                $<?= htmlspecialchars($invoice['amount'] ?? '0') ?>
                <?= htmlspecialchars(strtoupper($invoice['currency'] ?? 'USD')) ?>
            </h3>
        </div>

        <div class="invoice-actions">
            <a class="btn-view"
                href="<?= BASE_URL ?>/Public/index.php?page=admin_invoice_download&id=<?= htmlspecialchars($invoice['id']) ?>">
                Download PDF
            </a>

            <a class="btn-back"
                href="<?= BASE_URL ?>/Public/index.php?page=admin_invoices">
                ← Back
            </a>
        </div>

    </div>

</div>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>