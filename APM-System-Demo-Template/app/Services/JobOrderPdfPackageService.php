<?php

namespace App\Services;

use App\Models\JobOrder;
use App\Models\JobOrderAttachment;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class JobOrderPdfPackageService
{
    public function __construct(private readonly BillingPdfPackageService $billingPdfPackageService)
    {
    }

    public function make(JobOrder $jobOrder): string
    {
        $jobOrder->loadMissing([
            'attachments',
            'client',
            'billingStatements.attachments',
            'serviceInvoices.attachments',
            'debitCreditNotes.attachments',
        ]);

        $joAttachments = $jobOrder->attachments;
        $joImageAttachments = $joAttachments
            ->filter(fn (JobOrderAttachment $attachment) => $this->isImage($attachment))
            ->map(fn (JobOrderAttachment $attachment) => [
                'filename' => $attachment->filename,
                'data_uri' => $this->dataUri($attachment),
            ])
            ->filter(fn (array $attachment) => !empty($attachment['data_uri']))
            ->values();

        $joPdfAttachments = $joAttachments
            ->filter(fn (JobOrderAttachment $attachment) => $attachment->mime_type === 'application/pdf')
            ->values();

        $mainPdf = $this->renderMainPdf($jobOrder, $joImageAttachments, $joPdfAttachments);
        $pdf = new Fpdi();

        $this->appendPdfContent($pdf, $mainPdf, 'JO package summary');

        foreach ($joPdfAttachments as $attachment) {
            $this->appendStoredPdfAttachment($pdf, $attachment);
        }

        foreach ($jobOrder->billingStatements as $statement) {
            $this->appendPdfContent(
                $pdf,
                $this->billingPdfPackageService->make($statement),
                'Billing Statement #' . ($statement->statement_no ?: $statement->id)
            );
        }

        foreach ($jobOrder->serviceInvoices as $invoice) {
            $this->appendPdfContent(
                $pdf,
                $this->billingPdfPackageService->make($invoice),
                'Service Invoice #' . ($invoice->statement_no ?: $invoice->id)
            );
        }

        foreach ($jobOrder->debitCreditNotes as $note) {
            foreach ($note->attachments->where('mime_type', 'application/pdf') as $attachment) {
                $path = Storage::disk('public')->path($attachment->path);
                if (!is_file($path)) {
                    $this->appendPlaceholderPage($pdf, 'Missing Debit/Credit Note attachment: ' . $attachment->filename);

                    continue;
                }

                try {
                    $this->appendPdfFile($pdf, $path);
                } catch (\Throwable) {
                    $this->appendPlaceholderPage($pdf, 'Debit/Credit Note attachment could not be embedded: ' . $attachment->filename);
                }
            }
        }

        return $pdf->Output('S');
    }

    public function filename(JobOrder $jobOrder): string
    {
        $joNo = preg_replace('/[^A-Za-z0-9_-]+/', '-', trim(implode('-', array_filter([
            $jobOrder->code,
            $jobOrder->mo,
            $jobOrder->number,
        ])))) ?: $jobOrder->id;

        return 'jo-' . $joNo . '-complete-package.pdf';
    }

    private function renderMainPdf(JobOrder $jobOrder, $joImageAttachments, $joPdfAttachments): string
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('job-orders.pdf-package', [
            'jobOrder' => $jobOrder,
            'joImageAttachments' => $joImageAttachments,
            'joPdfAttachments' => $joPdfAttachments,
        ])->render());
        $dompdf->setPaper('letter');
        $dompdf->render();

        return $dompdf->output();
    }

    private function appendStoredPdfAttachment(Fpdi $pdf, JobOrderAttachment $attachment): void
    {
        $path = Storage::disk('public')->path($attachment->path);
        if (!is_file($path)) {
            $this->appendPlaceholderPage($pdf, 'Missing JO attachment: ' . $attachment->filename);

            return;
        }

        try {
            $this->appendPdfFile($pdf, $path);
        } catch (\Throwable) {
            $this->appendPlaceholderPage($pdf, 'JO attachment could not be embedded: ' . $attachment->filename);
        }
    }

    private function appendPdfContent(Fpdi $pdf, string $content, string $label): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'jo-package-') . '.pdf';
        file_put_contents($tmpFile, $content);

        try {
            $this->appendPdfFile($pdf, $tmpFile);
        } catch (\Throwable) {
            $this->appendPlaceholderPage($pdf, $label . ' could not be embedded.');
        } finally {
            @unlink($tmpFile);
        }
    }

    private function appendPdfFile(Fpdi $pdf, string $path): void
    {
        $pageCount = $pdf->setSourceFile($path);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
            $orientation = ($size['width'] ?? 0) > ($size['height'] ?? 0) ? 'L' : 'P';

            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);
        }
    }

    private function appendPlaceholderPage(Fpdi $pdf, string $message): void
    {
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->MultiCell(0, 8, $message);
    }

    private function isImage(JobOrderAttachment $attachment): bool
    {
        return in_array($attachment->mime_type, ['image/jpeg', 'image/png'], true);
    }

    private function dataUri(JobOrderAttachment $attachment): ?string
    {
        $path = Storage::disk('public')->path($attachment->path);
        if (!is_file($path) || !$attachment->mime_type) {
            return null;
        }

        return 'data:' . $attachment->mime_type . ';base64,' . base64_encode((string) file_get_contents($path));
    }
}
