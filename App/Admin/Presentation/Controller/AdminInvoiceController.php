<?php

namespace App\Admin\Presentation\Controller;

use App\Invoice\Application\Pdf\InvoicePdfService;
use App\Invoice\Application\UseCase\GetAllInvoicesUseCase;
use App\Invoice\Application\UseCase\GetInvoiceByIdUseCase;
use App\Notification\Application\UseCase\CreateNotificationUseCase;

class AdminInvoiceController
{
    public function __construct(
        private GetAllInvoicesUseCase $getAllInvoicesUseCase,
        private GetInvoiceByIdUseCase $getInvoiceByIdUseCase,
        private CreateNotificationUseCase $createNotificationUseCase
    ) {}

    public function view()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            die('Invalid invoice ID');
        }

        $invoice = $this->getInvoiceByIdUseCase->execute($id);

        if (!$invoice) {
            die('Invoice not found');
        }

        try {
            $this->createNotificationUseCase->execute(
                $invoice['user_id'],
                'Invoice Viewed',
                'Invoice #' . $invoice['invoice_number'] . ' was viewed by admin.',
                'invoice'
            );
        } catch (\Throwable $e) {
            // ignore notification failures
        }

        require BASE_PATH . '/view/admin/invoices/view.php';
    }

    public function index()
    {
        $invoices = $this->getAllInvoicesUseCase->execute();

        require BASE_PATH . '/view/admin/invoices/index.php';
    }

    public function download()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            die('Invalid invoice ID');
        }

        $invoice = $this->getInvoiceByIdUseCase->execute($id);

        if (!$invoice) {
            die('Invoice not found');
        }

        try {
            $this->createNotificationUseCase->execute(
                $invoice['user_id'],
                'Invoice Downloaded',
                'Invoice #' . $invoice['invoice_number'] . ' was downloaded by admin.',
                'invoice'
            );
        } catch (\Throwable $e) {
            // ignore
        }

        $pdfService = new InvoicePdfService();
        $dompdf = $pdfService->generate($invoice);

        $filename = 'invoice_' . $invoice['invoice_number'] . '.pdf';

        $dompdf->stream($filename, [
            'Attachment' => true
        ]);
    }
}
