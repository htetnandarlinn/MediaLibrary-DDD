<?php require BASE_PATH . '/view/layout/admin_header.php'; ?>

<div class="section page admin-approval-page">
    <div class="wrapper">
        <div class="page-heading">
            <h1>Reservation Approval Center</h1>
            <p class="page-subtitle">Review all pending reservation requests and update their status.</p>
        </div>

        <?php if (!empty($successMessage)): ?>
            <p class="message"><?= htmlspecialchars($successMessage) ?></p>
        <?php endif; ?>

        <?php if (!empty($errors['general'])): ?>
            <p class="message"><?= htmlspecialchars($errors['general']) ?></p>
        <?php endif; ?>

        <section class="admin-return-card card">
            <!-- <h2>Admin Dashboard</h2>
            <p>Return to the admin overview and quick links page.</p> -->
            <a href="<?= BASE_URL ?>/Public/index.php?page=reservation_admin" class="btn">Back to Dashboard</a>
        </section>

        <section class="reservation-approvals card admin-approvals-panel">
            <?php if (empty($reservations)): ?>
                <p>No pending reservations at the moment.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="reservation-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Book</th>
                                <th>Category</th>
                                <th>Requested</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td data-label="User"><?= htmlspecialchars($reservation->getUserName() ?? 'Unknown') ?></td>
                                    <td data-label="Book"><?= htmlspecialchars($reservation->getMediaTitle() ?? 'Unknown') ?></td>
                                    <td data-label="Category"><?= htmlspecialchars($reservation->getMediaCategory() ?? 'N/A') ?></td>
                                    <td data-label="Requested"><?= htmlspecialchars($reservation->getReservedAt()->format('Y-m-d H:i')) ?></td>
                                    <td class="actions-cell" data-label="Action">
                                        <form method="post" action="<?= BASE_URL ?>/Public/index.php?page=reservation_admin_approvals" class="action-form">
                                            <input type="hidden" name="reservation_id" value="<?= (int) $reservation->getId() ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn">Approve</button>
                                        </form>
                                        <form method="post" action="<?= BASE_URL ?>/Public/index.php?page=reservation_admin_approvals" class="action-form">
                                            <input type="hidden" name="reservation_id" value="<?= (int) $reservation->getId() ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-secondary">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>