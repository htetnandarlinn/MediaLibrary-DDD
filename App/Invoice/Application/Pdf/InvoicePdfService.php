<?php

namespace App\Invoice\Application\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;

class InvoicePdfService
{
    public function generate(array $invoice): Dompdf
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        ob_start();
        $this->renderHtml($invoice);
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf;
    }

    private function renderHtml(array $invoice): void
    {
        // Fallback variables to match the dynamic array if keys change
        $invoiceNumber = $invoice['invoice_number'] ?? 'INV-2024-00125';
        $status = strtoupper($invoice['status'] ?? 'PAID');
        $amount = number_format((float) ($invoice['amount'] ?? 25.0), 2);
        $currency = strtoupper($invoice['currency'] ?? 'USD');
        $currencySign = $currency === 'USD' ? '$' : $currency . ' ';
        $createdAt = $invoice['created_at'] ?? 'May 20, 2024';
        $paymentIntent = $invoice['payment_intent_id'] ?? 'pi_3N7X9e21234567890';
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                @page { margin: 0px; }
                body {
                    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                    color: #2D3748;
                    margin: 0;
                    padding: 40px;
                    font-size: 13px;
                    line-height: 1.4;
                    background-color: #ffffff;
                }
                
                /* Layout Utilities */
                .w-100 { width: 100%; }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                table { border-collapse: collapse; width: 100%; }
                td { vertical-align: top; }
                
                /* Header Elements */
                .logo-text {
                    font-size: 26px;
                    font-weight: bold;
                    color: #0F172A;
                }
                .logo-subtext {
                    font-size: 13px;
                    color: #64748B;
                    margin-top: 2px;
                }
                .company-details {
                    margin-top: 15px;
                    font-size: 12px;
                    color: #475569;
                    line-height: 1.6;
                }
                .company-name {
                    color: #DE3962;
                    font-weight: bold;
                    font-size: 14px;
                    margin-bottom: 4px;
                }
                
                .invoice-title {
                    font-size: 28px;
                    font-weight: 700;
                    color: #1E293B;
                    letter-spacing: 1px;
                }
                .invoice-num {
                    font-size: 16px;
                    font-weight: bold;
                    color: #DE3962;
                    margin-top: 5px;
                    margin-bottom: 20px;
                }
                
                .meta-table td {
                    padding: 4px 0;
                    font-size: 12px;
                }
                .meta-label { font-weight: bold; color: #1E293B; }
                .meta-value { color: #475569; text-align: right; }
                
                .status-badge {
                    background-color: #15803D;
                    color: white;
                    padding: 3px 10px;
                    border-radius: 4px;
                    font-weight: bold;
                    font-size: 11px;
                    display: inline-block;
                }
                
                .divider {
                    border-top: 1px solid #DE3962;
                    margin: 25px 0;
                }
                
                /* Info Blocks (Billed To / Summary) */
                .info-card {
                    background-color: #F8FAFC;
                    border: 1px solid #F1F5F9;
                    border-radius: 6px;
                    padding: 20px;
                    min-height: 120px;
                }
                .card-title {
                    font-weight: bold;
                    font-size: 12px;
                    color: #1E293B;
                    margin-bottom: 12px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                .card-content {
                    font-size: 12px;
                    color: #475569;
                    line-height: 1.6;
                }
                .customer-name {
                    font-weight: bold;
                    color: #0F172A;
                    font-size: 13px;
                }
                
                /* Items Table */
                .items-table {
                    margin-top: 30px;
                }
                .items-table th {
                    background-color: #DE3962;
                    color: white;
                    text-transform: uppercase;
                    font-size: 11px;
                    font-weight: bold;
                    padding: 10px 12px;
                    text-align: left;
                    letter-spacing: 0.5px;
                }
                .items-table td {
                    padding: 15px 12px;
                    border-bottom: 1px dashed #E2E8F0;
                }
                .item-title {
                    font-weight: bold;
                    color: #0F172A;
                    font-size: 13px;
                    margin-bottom: 4px;
                }
                .item-desc {
                    color: #64748B;
                    font-size: 11px;
                    line-height: 1.4;
                }
                
                /* Totals Table Section */
                .totals-table td {
                    padding: 6px 12px;
                    font-size: 13px;
                }
                .totals-label { color: #475569; text-align: right; width: 80%; }
                .totals-value { color: #0F172A; text-align: right; font-weight: 500; }
                
                .total-row td {
                    border-top: 1px solid #0F172A;
                    padding-top: 12px;
                }
                .final-total-label {
                    color: #DE3962;
                    font-weight: bold;
                    font-size: 14px;
                }
                .final-total-value {
                    color: #DE3962;
                    font-weight: bold;
                    font-size: 15px;
                }
                
                /* Bottom Blocks */
                .notes-card {
                    background-color: #F8FAFC;
                    border: 1px solid #F1F5F9;
                    border-radius: 6px;
                    padding: 15px 20px;
                    margin-top: 30px;
                    font-size: 12px;
                    color: #475569;
                    line-height: 1.5;
                }
                .notes-title {
                    font-weight: bold;
                    color: #1E293B;
                    margin-bottom: 5px;
                    text-transform: uppercase;
                    font-size: 11px;
                }
                
                /* Footer Section */
                .footer-divider {
                    border-top: 1px solid #F1F5F9;
                    margin-top: 40px;
                    margin-bottom: 15px;
                }
                .thank-you-text {
                    font-family: 'Georgia', serif;
                    font-style: italic;
                    font-size: 22px;
                    color: #DE3962;
                }
                .appreciation-text {
                    font-size: 12px;
                    color: #64748B;
                    margin-top: 5px;
                }
                .help-title {
                    font-weight: bold;
                    color: #DE3962;
                    font-size: 11px;
                    text-transform: uppercase;
                    margin-bottom: 4px;
                }
                .help-text {
                    font-size: 11px;
                    color: #64748B;
                    line-height: 1.4;
                }
            </style>
        </head>
        <body>

            <table class="w-100">
                <tr>
                    <td style="width: 55%;">
                        <div class="logo-text">Media Library</div>
                        <div class="logo-subtext">Store. Manage. Share.</div>
                        
                        <div class="company-details">
                            <div class="company-name">Media Library Inc.</div>
                            123 Creative Drive<br>
                            Los Angeles, CA 90001<br>
                            USA<br>
                            <span style="margin-top: 5px; display:block;">
                                info@medialibrary.com<br>
                                +1 (323) 456-7890<br>
                                www.medialibrary.com
                            </span>
                        </div>
                    </td>
                    
                    <td style="width: 45%; text-align: right;">
                        <div class="invoice-title">INVOICE</div>
                        <div class="invoice-num"><?= $invoiceNumber ?></div>
                        
                        <table class="meta-table" style="float: right; width: 240px;">
                            <tr>
                                <td class="meta-label">Invoice Date</td>
                                <td style="width:15px; text-align:center;">:</td>
                                <td class="meta-value"><?= $createdAt ?></td>
                            </tr>
                            <tr>
                                <td class="meta-label">Payment Method</td>
                                <td style="text-align:center;">:</td>
                                <td class="meta-value">Stripe</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Transaction ID</td>
                                <td style="text-align:center;">:</td>
                                <td class="meta-value" style="font-size:10px; word-break: break-all;"><?= $paymentIntent ?></td>
                            </tr>
                            <tr>
                                <td class="meta-label" style="padding-top:8px;">Status</td>
                                <td style="text-align:center; padding-top:8px;">:</td>
                                <td class="meta-value" style="padding-top:8px;">
                                    <span class="status-badge"><?= $status ?></span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <div class="divider"></div>

            <table class="w-100">
                <tr>
                    <td style="width: 48%;">
                        <div class="info-card">
                            <div class="card-title">
                                <span style="background-color: #DE3962; color: white; display: inline-block; width: 14px; height: 14px; border-radius: 50%; font-size: 9px; text-align: center; line-height: 14px; margin-right: 6px; vertical-align: middle;">👤</span>
                                BILLED TO
                            </div>
                            <div class="card-content">
                                <div class="customer-name"><?= htmlspecialchars($invoice['username'] ?? 'John Doe') ?></div>
                                <div style="margin-top: 4px; color: #475569; font-size: 12px; line-height: 1.5;">
                                    <?= htmlspecialchars($invoice['email'] ?? 'john.doe@example.com') ?><br>
                                    User ID: <?= $invoice['user_id'] ?? 'N/A' ?><br>
                                    Reservation ID: <?= $invoice['reservation_id'] ?? 'N/A' ?><br>
                                    <span style="color: #64748B; font-size: 11px;">United States</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    
                    <td style="width: 4%;"></td> 
                    
                    <td style="width: 48%;">
                        <div class="info-card">
                            <div class="card-title">
                                <span style="background-color: #DE3962; color: white; display: inline-block; width: 14px; height: 14px; border-radius: 50%; font-size: 9px; text-align: center; line-height: 14px; margin-right: 6px; vertical-align: middle;">📄</span>
                                INVOICE SUMMARY
                            </div>
                            <div class="card-content" style="color: #475569; font-size: 12px; line-height: 1.6;">
                                Thank you for subscribing to Media Library. Here is the summary of your subscription and payment.
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <table class="items-table w-100">
                <thead>
                    <tr>
                        <th style="width: 8%; text-align: center;">#</th>
                        <th style="width: 52%;">Description</th>
                        <th style="width: 10%; text-align: center;">Qty</th>
                        <th style="width: 15%; text-align: right;">Unit Price</th>
                        <th style="width: 15%; text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center; color: #475569;">1</td>
                        <td>
                            <div class="item-title">Media Library Subscription</div>
                            <div class="item-desc">
                                Pro Plan - Monthly Subscription (Includes unlimited storage, advanced search, and premium support)
                            </div>
                        </td>
                        <td style="text-align: center; color: #475569;">1</td>
                        <td style="text-align: right; color: #475569;"><?= $currencySign . $amount ?></td>
                        <td style="text-align: right; font-weight: bold; color: #0F172A;"><?= $currencySign . $amount ?></td>
                    </tr>
                </tbody>
            </table>

            <table class="totals-table w-100" style="margin-top: 15px;">
                <tr>
                    <td class="totals-label">Subtotal</td>
                    <td class="totals-value"><?= $currencySign . $amount ?></td>
                </tr>
                <tr>
                    <td class="totals-label">Tax (0%)</td>
                    <td class="totals-value"><?= $currencySign ?>0.00</td>
                </tr>
                <tr class="total-row">
                    <td class="totals-label final-total-label">TOTAL</td>
                    <td class="totals-value final-total-value"><?= $currencySign . $amount ?></td>
                </tr>
            </table>

            <div class="notes-card">
                <div class="notes-title">Notes</div>
                This invoice has been paid in full. If you have any questions, please contact our support team.
            </div>

            <div class="footer-divider"></div>

            <table class="w-100">
                <tr>
                    <td style="width: 50%;">
                        <div class="thank-you-text">Thank you!</div>
                        <div class="appreciation-text">We appreciate your business.</div>
                    </td>
                    <td style="width: 50%; text-align: right;">
                        <div class="help-title">Need Help?</div>
                        <div class="help-text">
                            Our support team is here to help you.<br>
                            support@medialibrary.com<br>
                            +1 (323) 456-7890
                        </div>
                    </td>
                </tr>
            </table>

        </body>
        </html>
        <?php
    }
}
