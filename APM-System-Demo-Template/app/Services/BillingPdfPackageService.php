<?php

namespace App\Services;

use App\Models\BillingAttachment;
use App\Models\BillingStatement;
use App\Models\ServiceInvoice;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class BillingPdfPackageService
{
    public function make(Model $document): string
    {
        $document->loadMissing(['attachments', 'jobOrder']);

        $attachments = $document->attachments;
        $imageAttachments = $attachments
            ->filter(fn (BillingAttachment $attachment) => $this->isImage($attachment))
            ->map(fn (BillingAttachment $attachment) => [
                'filename' => $attachment->filename,
                'data_uri' => $this->dataUri($attachment),
            ])
            ->filter(fn (array $attachment) => !empty($attachment['data_uri']))
            ->values();

        $pdfAttachments = $attachments
            ->filter(fn (BillingAttachment $attachment) => $attachment->mime_type === 'application/pdf')
            ->values();

        $mainPdf = $this->renderMainPdf($document, $imageAttachments, $pdfAttachments);

        if ($pdfAttachments->isEmpty()) {
            return $mainPdf;
        }

        return $this->appendPdfAttachments($mainPdf, $pdfAttachments);
    }

    public function filename(Model $document): string
    {
        $prefix = $document instanceof ServiceInvoice ? 'service-invoice' : 'billing-statement';

        return $prefix . '-' . ($document->statement_no ?? $document->id) . '-with-attachments.pdf';
    }

    private function renderMainPdf(Model $document, $imageAttachments, $pdfAttachments): string
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('modules.billing.pdf-package', [
            'document' => $document,
            'data' => $document->data ?? [],
            'isService' => $document instanceof ServiceInvoice,
            'imageAttachments' => $imageAttachments,
            'pdfAttachments' => $pdfAttachments,
        ])->render());
        $dompdf->setPaper('letter');
        $dompdf->render();

        return $dompdf->output();
    }

    private function appendPdfAttachments(string $mainPdf, $pdfAttachments): string
    {
        $tmpMain = tempnam(sys_get_temp_dir(), 'billing-main-') . '.pdf';
        file_put_contents($tmpMain, $mainPdf);

        $pdf = new Fpdi();
        $this->appendPdfFile($pdf, $tmpMain);

        foreach ($pdfAttachments as $attachment) {
            $path = Storage::disk('public')->path($attachment->path);
            if (!is_file($path)) {
                continue;
            }

            try {
                $this->appendPdfFile($pdf, $path);
            } catch (\Throwable) {
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->MultiCell(0, 8, 'Attachment could not be embedded: ' . $attachment->filename);
            }
        }

        @unlink($tmpMain);

        return $pdf->Output('S');
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

    private function isImage(BillingAttachment $attachment): bool
    {
        return in_array($attachment->mime_type, ['image/jpeg', 'image/png'], true);
    }

    private function dataUri(BillingAttachment $attachment): ?string
    {
        $path = Storage::disk('public')->path($attachment->path);
        if (!is_file($path) || !$attachment->mime_type) {
            return null;
        }

        return 'data:' . $attachment->mime_type . ';base64,' . base64_encode((string) file_get_contents($path));
    }
}
