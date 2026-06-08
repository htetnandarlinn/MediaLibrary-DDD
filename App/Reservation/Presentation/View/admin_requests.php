<?php require BASE_PATH . '/view/layout/admin_header.php'; ?>

<div class="section page admin-requests-page">
    <div class="wrapper">
        <div class="page-heading">
            <h1>Reservation Requests</h1>
            <p class="page-subtitle">Showing <?= htmlspecialchars($statusLabel) ?>.</p>
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

        <section class="reservation-list card">
            <?php if (empty($reservations)): ?>
                <!-- <p>No reservation requests found for this selection.</p> -->
            <?php else: ?>
                <div class="table-responsive">
                    <table class="reservation-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Book</th>
                                <th>Category</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Requested</th>
                                <th>Processed</th>
                                <th>Handled By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td data-label="User"><?= htmlspecialchars($reservation->getUserName() ?? 'Unknown') ?></td>
                                    <td data-label="Book"><?= htmlspecialchars($reservation->getMediaTitle() ?? 'Unknown') ?></td>
                                    <td data-label="Category"><?= htmlspecialchars($reservation->getMediaCategory() ?? 'N/A') ?></td>
                                    <td data-label="Payment">
                                        <span class="status-badge status-<?= strtolower($reservation->getPaymentStatus()) ?>">
                                            <?= htmlspecialchars($reservation->getPaymentStatus()) ?>
                                        </span>
                                    </td>
                                    <td data-label="Status">
                                        <span class="status-badge status-<?= strtolower($reservation->getStatus()) ?>">
                                            <?= htmlspecialchars($reservation->getStatus()) ?>
                                        </span>
                                    </td>
                                    <td data-label="Requested"><?= htmlspecialchars($reservation->getReservedAt()->format('Y-m-d H:i')) ?></td>
                                    <td data-label="Processed">
                                        <?php if ($reservation->getStatus() === \App\Reservation\Domain\Entity\Reservation::STATUS_APPROVED): ?>
                                            <?= htmlspecialchars($reservation->getApprovedAt()?->format('Y-m-d H:i') ?? 'Approved') ?>
                                        <?php elseif ($reservation->getStatus() === \App\Reservation\Domain\Entity\Reservation::STATUS_REJECTED): ?>
                                            <?= htmlspecialchars($reservation->getRejectedAt()?->format('Y-m-d H:i') ?? 'Rejected') ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Handled By">
                                        <?= $reservation->getApprovedBy() ? htmlspecialchars((string) $reservation->getApprovedBy()) : '-' ?>
                                    </td>
                                    <td class="actions-cell" data-label="Action">
                                        <?php if ($reservation->getStatus() === \App\Reservation\Domain\Entity\Reservation::STATUS_PENDING): ?>
                                            <div class="request-action-buttons">
                                                <form method="post" action="<?= BASE_URL ?>/Public/index.php?page=reservation_admin_requests<?= $filter ? '&filter=' . urlencode($filter) : '' ?>" class="action-form">
                                                    <input type="hidden" name="reservation_id" value="<?= (int) $reservation->getId() ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn">Approve</button>
                                                </form>
                                                <form method="post" action="<?= BASE_URL ?>/Public/index.php?page=reservation_admin_requests<?= $filter ? '&filter=' . urlencode($filter) : '' ?>" class="action-form">
                                                    <input type="hidden" name="reservation_id" value="<?= (int) $reservation->getId() ?>">
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="btn btn-secondary">Reject</button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
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