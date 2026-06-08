<?php

/** @var array $invoices */

// Layout header (ADMIN HEADER)

require BASE_PATH . '/view/layout/admin_header.php';
?>
<div class="invoice-page">

    <h2>All Invoices</h2>

    <table class="invoice-table">
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>User</th>
                <th>Email</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

            <?php if (!empty($invoices)): ?>

                <?php foreach ($invoices as $inv): ?>
                    <tr>
                        <td><?= htmlspecialchars($inv['invoice_number']) ?></td>
                        <td><?= htmlspecialchars($inv['user_name']) ?></td>
                        <td><?= htmlspecialchars($inv['email']) ?></td>
                        <td>$<?= htmlspecialchars($inv['amount']) ?></td>

                        <td>
                            <span class="status status-<?= strtolower($inv['status']) ?>">
                                <?= htmlspecialchars($inv['status']) ?>
                            </span>
                        </td>

                        <td><?= htmlspecialchars($inv['created_at']) ?></td>

                        <td>
                            <a class="btn-view"
                                href="<?= BASE_URL ?>/Public/index.php?page=admin_invoice_view&id=<?= urlencode($inv['id']) ?>">
                                View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

            <?php else: ?>
                <tr>
                    <td colspan="7">No invoices found</td>
                </tr>
            <?php endif; ?>

    </table>

</div>

<?php
// Layout footer (ADMIN FOOTER)
require BASE_PATH . '/view/layout/footer.php';
?>