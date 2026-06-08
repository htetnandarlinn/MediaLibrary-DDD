<?php require BASE_PATH . '/view/layout/header.php'; ?>

<style>
    /* Overall Page & Typography Styles */

   
</style>

<h1 >My Invoices</h1>

<?php if (empty($invoices)): ?>
    <p class="no-data">No invoices found.</p>
<?php else: ?>
    <div class="table-container">
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Amount</th>
                    <th>Currency</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $invoice): ?>
                    <tr>
                        <td class="font-semibold"><?= htmlspecialchars($invoice['invoice_number']) ?></td>
                        <td><?= htmlspecialchars($invoice['amount']) ?></td>
                        <td class="text-muted"><?= htmlspecialchars($invoice['currency']) ?></td>
                        <td>
                            <?php if ($invoice['status'] === 'PAID'): ?>
                                <span class="badge badge-paid">PAID</span>
                            <?php else: ?>
                                <span class="badge badge-pending"><?= htmlspecialchars($invoice['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted"><?= htmlspecialchars($invoice['created_at']) ?></td>
                        <td>
                            <a class="btn-view" href="<?= BASE_URL ?>/Public/index.php?page=invoice_show&id=<?= urlencode($invoice['id']) ?>">
                                View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>