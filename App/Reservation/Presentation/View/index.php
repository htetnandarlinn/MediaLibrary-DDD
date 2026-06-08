<?php require BASE_PATH . '/view/layout/header.php'; ?>

<div class="section page reservation-page user-reservation-page">
    <div class="wrapper">
        <h1>My Reservations</h1>
        <p class="page-subtitle">Track your reservation requests and see when they are processed.</p>

        <?php if (!empty($successMessage)): ?>
            <p class="message"><?= htmlspecialchars($successMessage) ?></p>
        <?php endif; ?>

        <?php if (!empty($errors['general'])): ?>
            <p class="message"><?= htmlspecialchars($errors['general']) ?></p>
        <?php endif; ?>

        <?php if (!empty($_SESSION['user']) && strtolower($_SESSION['user']['role'] ?? '') === 'admin'): ?>
            <section class="admin-link-card card">
                <h2>Admin Dashboard</h2>
                <p>Manage pending reservations and approvals from the admin overview page.</p>
                <a href="<?= BASE_URL ?>/Public/index.php?page=reservation_admin" class="btn">Go to Admin Dashboard</a>
            </section>
        <?php endif; ?>

        <?php if (isset($mediaId) && $mediaId !== false): ?>
            <section class="reservation-confirm card">
                <h2>Confirm Reservation</h2>
                <p>Choose how many days you want to reserve this item for, then continue to payment.</p>
                <form method="post" action="<?= BASE_URL ?>/Public/index.php?page=reservation" class="reservation-form">
                    <input type="hidden" name="media_id" value="<?= (int) $mediaId ?>">
                    <div class="form-group">
                        <label for="days">Days to reserve</label>
                        <input
                            id="days"
                            type="number"
                            name="days"
                            min="1"
                            max="30"
                            value="<?= htmlspecialchars($days ?? 1) ?>"
                            required />
                        <?php if (!empty($errors['days'])): ?>
                            <span class="field-error"><?= htmlspecialchars($errors['days']) ?></span>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn">Reserve and Pay</button>
                </form>
            </section>
        <?php endif; ?>

        <section class="reservation-history card">
            <h2>Reservation History</h2>

            <?php if (empty($reservations)): ?>
                <p>No reservations found yet. Start by reserving an item!</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="reservation-table">
                        <thead>
                            <tr>
                                <th>Book</th>
                                <th>Category</th>
                                <th>Days</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Action</th>
                                <th>Requested</th>
                                <th>Processed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td data-label="Book"><?= htmlspecialchars($reservation->getMediaTitle() ?? 'Unknown') ?></td>
                                    <td data-label="Category"><?= htmlspecialchars($reservation->getMediaCategory() ?? 'N/A') ?></td>
                                    <td data-label="Days"><?= htmlspecialchars($reservation->getPaymentDays()) ?></td>
                                    <td data-label="Amount">
                                        $<?= number_format($reservation->getPaymentAmountCents() / 100, 2) ?>
                                    </td>
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
                                    <td data-label="Action">
                                        <?php if ($reservation->getStatus() === \App\Reservation\Domain\Entity\Reservation::STATUS_APPROVED && in_array($reservation->getPaymentStatus(), [\App\Reservation\Domain\Entity\Reservation::PAYMENT_PENDING, \App\Reservation\Domain\Entity\Reservation::PAYMENT_FAILED], true)): ?>
                                            <a href="<?= BASE_URL ?>/Public/index.php?page=checkout&reservation_id=<?= (int) $reservation->getId() ?>" class="btn">Pay Now</a>
                                        <?php elseif ($reservation->getPaymentStatus() === \App\Reservation\Domain\Entity\Reservation::PAYMENT_COMPLETED): ?>
                                            <span class="status-badge status-completed">Paid</span>
                                        <?php elseif ($reservation->getStatus() === \App\Reservation\Domain\Entity\Reservation::STATUS_PENDING): ?>
                                            <span class="status-badge status-pending">Awaiting approval</span>
                                        <?php else: ?>
                                            <span class="status-badge status-<?= strtolower($reservation->getPaymentStatus()) ?>">
                                                <?= htmlspecialchars($reservation->getPaymentStatus()) ?>
                                            </span>
                                        <?php endif; ?>
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