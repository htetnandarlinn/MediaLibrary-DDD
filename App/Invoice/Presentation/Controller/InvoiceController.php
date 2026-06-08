<?php

namespace App\Invoice\Presentation\Controller;

use App\Invoice\Application\Pdf\InvoicePdfService;
use App\Invoice\Application\UseCase\GetInvoiceByIdUseCase;
use App\Invoice\Application\UseCase\GetInvoicesForUserUseCase;
use App\Notification\Application\UseCase\CreateNotificationUseCase;

class InvoiceController
{
    public function __construct(
        private GetInvoicesForUserUseCase $getInvoicesForUserUseCase,
        private GetInvoiceByIdUseCase $getInvoiceByIdUseCase,
        private CreateNotificationUseCase $createNotificationUseCase
    ) {}

    public function index()
    {
        if (empty($_SESSION['user'])) {
            exit('Please login first');
        }

        $userId = $_SESSION['user']['user_id'];
        $invoices = $this->getInvoicesForUserUseCase->execute($userId);

        require BASE_PATH . '/App/Invoice/Presentation/Views/index.php';
    }

    public function show()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            exit('Invoice not found');
        }

        $invoice = $this->getInvoiceByIdUseCase->execute($id);

        if (!$invoice) {
            exit('Invoice not found');
        }

        try {
            $this->createNotificationUseCase->execute(
                $invoice['user_id'],
                'Invoice Viewed',
                'Invoice #' . $invoice['invoice_number'] . ' was viewed.',
                'invoice'
            );
        } catch (\Throwable $e) {
            // ignore notification failures
        }

        $invoice['user_name'] = $invoice['user_name'] ?? 'Unknown User';
        $invoice['user_email'] = $invoice['email'] ?? $invoice['user_email'] ?? 'N/A';

        require BASE_PATH . '/App/Invoice/Presentation/Views/show.php';
    }

    public function download()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            exit('Invoice ID missing');
        }

        $invoice = $this->getInvoiceByIdUseCase->execute($id);

        if (!$invoice) {
            exit('Invoice not found');
        }

        try {
            $this->createNotificationUseCase->execute(
                $invoice['user_id'],
                'Invoice Downloaded',
                'Invoice #' . $invoice['invoice_number'] . ' was downloaded.',
                'invoice'
            );
        } catch (\Throwable $e) {
            // ignore notification failures
        }

        $invoice['user_name'] = $invoice['user_name'] ?? 'Unknown User';
        $invoice['user_email'] = $invoice['email'] ?? $invoice['user_email'] ?? 'N/A';
        $invoice['username'] = $invoice['user_name'];
        $invoice['email'] = $invoice['user_email'];

        $pdfService = new InvoicePdfService();
        $dompdf = $pdfService->generate($invoice);

        $filename = $invoice['invoice_number'] . '.pdf';

        $dompdf->stream($filename, [
            'Attachment' => true
        ]);
    }
}
