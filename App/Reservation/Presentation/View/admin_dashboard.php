<?php require BASE_PATH . '/view/layout/admin_header.php'; ?>

<div class="section page admin-dashboard-page">
    <div class="wrapper">
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <p class="page-subtitle">Quickly review reservation activity and access approval tools.</p>
        </div>

        <section class="dashboard-grid">
            <!-- Notifications moved to header dropdown -->
            <a class="dashboard-card card dashboard-card-link" href="<?= BASE_URL ?>/Public/index.php?page=reservation_admin_requests&filter=pending">
                <h2>Pending</h2>
                <p class="dashboard-value"><?= (int) ($pendingCount ?? 0) ?></p>
                <p class="dashboard-note">Requests waiting for approval right now.</p>
                <span class="dashboard-link-text">View Pending</span>
            </a>

            <a class="dashboard-card card dashboard-card-link" href="<?= BASE_URL ?>/Public/index.php?page=reservation_admin_requests&filter=approved">
                <h2>Approved</h2>
                <p class="dashboard-value"><?= (int) ($approvedCount ?? 0) ?></p>
                <p class="dashboard-note">Reservations successfully approved.</p>
                <span class="dashboard-link-text">View Approved</span>
            </a>

            <a class="dashboard-card card dashboard-card-link" href="<?= BASE_URL ?>/Public/index.php?page=reservation_admin_requests&filter=rejected">
                <h2>Rejected</h2>
                <p class="dashboard-value"><?= (int) ($rejectedCount ?? 0) ?></p>
                <p class="dashboard-note">Reservations that have been denied.</p>
                <span class="dashboard-link-text">View Rejected</span>
            </a>

            <a class="dashboard-card card dashboard-card-link" href="<?= BASE_URL ?>/Public/index.php?page=reservation_admin_requests&filter=all">
                <h2>Total Requests</h2>
                <p class="dashboard-value"><?= (int) ($totalReservations ?? 0) ?></p>
                <p class="dashboard-note">All reservations tracked in the system.</p>
                <span class="dashboard-link-text">View All</span>
            </a>
        </section>

        <section class="dashboard-latest card">
            <h2>Latest Approved Reservations</h2>

            <?php if (empty($latestApproved)): ?>
                <p>No reservations have been approved yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="reservation-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Book</th>
                                <th>Category</th>
                                <th>Approved At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($latestApproved as $reservation): ?>
                                <tr>
                                    <td data-label="User"><?= htmlspecialchars($reservation->getUserName() ?? 'Unknown') ?></td>
                                    <td data-label="Book"><?= htmlspecialchars($reservation->getMediaTitle() ?? 'Unknown') ?></td>
                                    <td data-label="Category"><?= htmlspecialchars($reservation->getMediaCategory() ?? 'N/A') ?></td>
                                    <td data-label="Approved At"><?= htmlspecialchars($reservation->getApprovedAt()?->format('Y-m-d H:i') ?? '-') ?></td>
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