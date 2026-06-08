<?php

/** @var array|null $invoice */
?>

<?php if (!$invoice): ?>

    <div class="invoice-wrapper">
        <h2>Invoice Not Found</h2>
        <a href="<?= BASE_URL ?>/Public/index.php?page=invoices">
            ← Back to Invoices
        </a>
    </div>

<?php else: ?>

    <style>
        .invoice-wrapper {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #d85062;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .company-name {
            font-size: 32px;
            font-weight: bold;
            color: #d85062;
        }

        .company-tagline {
            color: #777;
            margin-top: 5px;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h1 {
            margin: 0;
            font-size: 36px;
        }

        .invoice-number {
            color: #d85062;
            font-size: 20px;
            font-weight: bold;
        }

        .invoice-content {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 25px;
        }

        .info-box {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
        }

        .info-box h3 {
            margin-top: 0;
            color: #d95b72;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .invoice-table th {
            background: #d85062;
            color: #fff;
            padding: 12px;
            text-align: left;
        }

        .invoice-table td {
            border: 1px solid #ddd;
            padding: 12px;
        }

        .total-section {
            text-align: right;
            margin-top: 20px;
        }

        .total-section h2 {
            color: #d95b72;
        }

        .status-paid {
            background: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 13px;
        }

        .status-pending {
            background: #ffc107;
            color: black;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 13px;
        }

        .status-failed {
            background: #dc3545;
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 13px;
        }

        .action-buttons {
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            margin-right: 10px;
        }

        .btn-back {
            background: #6c757d;
        }

        .btn-download {
            background: #d85062;
        }

        .footer-note {
            margin-top: 30px;
            text-align: center;
            color: #777;
        }
    </style>

    <div class="invoice-wrapper">

        <div class="invoice-header">

            <div>
                <div class="company-name">Media Library</div>
                <div class="company-tagline">
                    Store • Manage • Share
                </div>
            </div>

            <div class="invoice-title">
                <h1>INVOICE</h1>
                <div class="invoice-number">
                    #<?= htmlspecialchars($invoice['invoice_number']) ?>
                </div>
            </div>

        </div>

        <div class="invoice-content">

            <div class="info-box">
                <h3>Customer Information</h3>
                <p>
                    <strong>User:</strong>
                    <?= htmlspecialchars($invoice['user_name'] ?? '') ?>
                </p>

                <p>
                    <strong>Email:</strong>
                    <?= htmlspecialchars($invoice['email'] ?? '') ?>
                </p>
            </div>

            <div class="info-box">
                <h3>Invoice Details</h3>

                <p>
                    <strong>Reservation ID:</strong>
                    <?= htmlspecialchars($invoice['reservation_id']) ?>
                </p>

                <p>
                    <strong>Date:</strong>
                    <?= htmlspecialchars($invoice['created_at']) ?>
                </p>

                <p>
                    <strong>Payment Intent:</strong><br>
                    <?= htmlspecialchars($invoice['payment_intent_id']) ?>
                </p>
            </div>

        </div>

        <table class="invoice-table">

            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>Media Library Reservation Payment</td>


                    <td>
                        $<?= htmlspecialchars($invoice['amount']) ?>
                        <?= htmlspecialchars($invoice['currency']) ?>
                    </td>

                    <td>

                        <?php if (strtoupper($invoice['status']) === 'PAID'): ?>

                            <span class="status-paid">
                                PAID
                            </span>

                        <?php elseif (strtoupper($invoice['status']) === 'PENDING'): ?>

                            <span class="status-pending">
                                PENDING
                            </span>

                        <?php else: ?>

                            <span class="status-failed">
                                <?= htmlspecialchars($invoice['status']) ?>
                            </span>

                        <?php endif; ?>

                    </td>

                </tr>

            </tbody>

        </table>

        <div class="total-section">
            <h2>
                Total:
                $<?= htmlspecialchars($invoice['amount']) ?>
            </h2>
        </div>

        <div class="action-buttons">

            <a class="btn btn-back"
                href="<?= BASE_URL ?>/Public/index.php?page=invoices">
                ← Back to Invoices
            </a>

            <a class="btn btn-download"
                href="<?= BASE_URL ?>/Public/index.php?page=invoice_download&id=<?= urlencode($invoice['id']) ?>">
                Download PDF
            </a>

        </div>

        <div class="footer-note">
            Thank you for using Media Library.
        </div>

    </div>

<?php endif; ?>