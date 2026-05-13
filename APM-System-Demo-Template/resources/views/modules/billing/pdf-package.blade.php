@php
    $fmt = fn ($amount) => number_format(is_numeric($amount) ? (float) $amount : 0, 2);
    $parseAmount = function ($value): float {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $normalized = trim((string) $value);
        if ($normalized === '') {
            return 0.0;
        }

        $isNegative = str_starts_with($normalized, '(') && str_ends_with($normalized, ')');
        $normalized = preg_replace('/[^0-9.\-]/', '', $normalized) ?? '';
        if ($normalized === '' || $normalized === '-' || $normalized === '.') {
            return 0.0;
        }

        $amount = is_numeric($normalized) ? (float) $normalized : 0.0;

        return $isNegative ? -abs($amount) : $amount;
    };
    $numberToWords = function (float $num): string {
        $ones = [
            0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
            5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
            14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen',
            17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen',
        ];
        $tens = [
            2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
            6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety',
        ];
        $chunkToWords = function (int $n) use ($ones, $tens): string {
            $parts = [];
            if (intdiv($n, 100) > 0) {
                $parts[] = $ones[intdiv($n, 100)] . ' Hundred';
            }
            $rest = $n % 100;
            if ($rest > 0) {
                $parts[] = $rest < 20
                    ? $ones[$rest]
                    : $tens[intdiv($rest, 10)] . (($rest % 10) ? ' ' . $ones[$rest % 10] : '');
            }

            return implode(' ', $parts);
        };

        if ($num == 0.0) {
            return 'ZERO PESOS ONLY';
        }

        $whole = (int) floor($num);
        $words = [];
        $scales = ['', 'Thousand', 'Million', 'Billion'];
        for ($scaleIndex = 0; $whole > 0; $scaleIndex++) {
            $chunk = $whole % 1000;
            if ($chunk > 0) {
                array_unshift($words, trim($chunkToWords($chunk) . ' ' . ($scales[$scaleIndex] ?? '')));
            }
            $whole = intdiv($whole, 1000);
        }
        $cents = (int) round(($num - floor($num)) * 100);
        $centsText = $cents > 0 ? ' AND ' . str_pad((string) $cents, 2, '0', STR_PAD_LEFT) . '/100' : '';

        return strtoupper(implode(' ', $words) . $centsText . ' PESOS ONLY');
    };
    $title = $isService ? 'Service Invoice' : 'Billing Statement';
    $deductAdvances = !array_key_exists('deduct_advances', $data)
        || filter_var($data['deduct_advances'], FILTER_VALIDATE_BOOLEAN);
    $statementDate = !empty($data['statement_date'])
        ? \Carbon\Carbon::parse($data['statement_date'])->format('F d, Y')
        : optional($document->created_at)->format('F d, Y');
    $nonDesc = $data['non_receipted_desc'] ?? [];
    $nonAmt = $data['non_receipted_amount'] ?? [];
    $recDesc = $data['receipted_desc'] ?? [];
    $recAmt = $data['receipted_amount'] ?? [];
    $siDesc = $data['si_item_description'] ?? [];
    $siQty = $data['si_quantity'] ?? [];
    $siUnitCost = $data['si_unit_cost'] ?? [];
    $siAmount = $data['si_amount'] ?? [];
    foreach (['nonDesc', 'nonAmt', 'recDesc', 'recAmt', 'siDesc', 'siQty', 'siUnitCost', 'siAmount'] as $var) {
        if (!is_array($$var)) {
            $$var = [$$var];
        }
    }
    $nonTotal = collect($nonAmt)->sum(fn ($amount) => is_numeric($amount) ? (float) $amount : 0);
    $recTotal = collect($recAmt)->sum(fn ($amount) => is_numeric($amount) ? (float) $amount : 0);
    $grandTotal = is_numeric($data['grand_total'] ?? null)
        ? (float) $data['grand_total']
        : ($isService
            ? collect($siAmount)->sum(fn ($amount) => is_numeric($amount) ? (float) $amount : 0)
            : ($nonTotal + $recTotal));
    $extractJoNumber = function (?string $raw): ?string {
        $value = trim((string) $raw);
        if ($value === '') {
            return null;
        }

        if (preg_match('/(\d{3,})$/', $value, $matches)) {
            return $matches[1];
        }

        if (preg_match('/(\d{3,})/', $value, $matches)) {
            return $matches[1];
        }

        return null;
    };
    $serviceTotalFromData = function (array $invoiceData) use ($siAmount, $parseAmount): float {
        $totalAmountDue = $parseAmount($invoiceData['si_total_amount_due'] ?? null);
        if ($totalAmountDue > 0) {
            return $totalAmountDue;
        }

        $grandTotal = $parseAmount($invoiceData['grand_total'] ?? null);
        if ($grandTotal > 0) {
            return $grandTotal;
        }

        $amounts = $invoiceData['si_amount'] ?? $siAmount;
        if (!is_array($amounts)) {
            $amounts = [$amounts];
        }

        return collect($amounts)->sum(fn ($amount) => $parseAmount($amount));
    };
    $jobOrder = $document->jobOrder ?? null;
    $joNumber = trim((string) ($jobOrder?->number ?? ''));
    $cashAdvanceBalance = 0.0;
    if ($document->job_order_id && $joNumber !== '') {
        $cashAdvanceBalance = \App\Models\CashAdvanceRequest::query()
            ->where('status', 'Approved')
            ->where('is_personal', false)
            ->whereHas('items', fn ($query) => $query->where('jo_number', 'like', "%{$joNumber}%"))
            ->with(['items', 'liquidations' => fn ($query) => $query->where('status', 'Approved')])
            ->get()
            ->sum(function (\App\Models\CashAdvanceRequest $cashAdvance) use ($joNumber, $extractJoNumber): float {
                $advanceAmount = $cashAdvance->items
                    ->filter(fn ($item) => $extractJoNumber((string) $item->jo_number) === $joNumber)
                    ->sum(fn ($item) => is_numeric($item->amount ?? null) ? (float) $item->amount : 0.0);
                $liquidatedAmount = $cashAdvance->liquidations
                    ->filter(fn ($liquidation) => $extractJoNumber((string) $liquidation->jo_number) === $joNumber)
                    ->sum(fn ($liquidation) => is_numeric($liquidation->amount ?? null) ? (float) $liquidation->amount : 0.0);

                return max(0, $advanceAmount - $liquidatedAmount);
            });
        $cashAdvanceBalance += \App\Models\DebitCreditNote::query()
            ->where('job_order_id', $document->job_order_id)
            ->get()
            ->sum(function (\App\Models\DebitCreditNote $note): float {
                return collect($note->data['rows'] ?? [])->sum(function ($row): float {
                    $particular = strtoupper(trim((string) ($row['particular'] ?? '')));
                    $amount = $parseAmount($row['amount'] ?? null);

                    return $particular !== '' && str_contains($particular, 'ADVANCE') ? $amount : 0.0;
                });
            });
    }
    $serviceAdvanceDeduction = 0.0;
    $serviceAdvanceBalance = 0.0;
    $billingAdvanceTotal = 0.0;
    $billingAdvanceAvailable = 0.0;
    $billingAdvanceDeduction = 0.0;
    $billingAdvanceOverpayment = 0.0;
    $billingAdvanceBalance = 0.0;
    if ($deductAdvances && $isService && $cashAdvanceBalance > 0) {
        $advanceRemaining = $cashAdvanceBalance;
        $serviceInvoices = \App\Models\ServiceInvoice::query()
            ->where('job_order_id', $document->job_order_id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
        foreach ($serviceInvoices as $invoice) {
            if (array_key_exists('deduct_advances', $invoice->data ?? []) && !filter_var(($invoice->data ?? [])['deduct_advances'], FILTER_VALIDATE_BOOLEAN)) {
                continue;
            }

            $invoiceTotal = $serviceTotalFromData(is_array($invoice->data) ? $invoice->data : []);
            $deduction = min($invoiceTotal, $advanceRemaining);
            if ((int) $invoice->id === (int) $document->id) {
                $serviceAdvanceDeduction = round($deduction, 2);
                $serviceAdvanceBalance = round(max(0, $advanceRemaining - $deduction), 2);
                break;
            }
            $advanceRemaining = max(0, $advanceRemaining - $deduction);
        }
    } elseif ($deductAdvances && !$isService && $cashAdvanceBalance > 0) {
        $serviceTotal = \App\Models\ServiceInvoice::query()
            ->where('job_order_id', $document->job_order_id)
            ->get()
            ->filter(fn (\App\Models\ServiceInvoice $invoice): bool => !array_key_exists('deduct_advances', $invoice->data ?? []) || filter_var(($invoice->data ?? [])['deduct_advances'], FILTER_VALIDATE_BOOLEAN))
            ->sum(fn (\App\Models\ServiceInvoice $invoice): float => $serviceTotalFromData(is_array($invoice->data) ? $invoice->data : []));
        $advanceAfterService = max(0, $cashAdvanceBalance - min($cashAdvanceBalance, (float) $serviceTotal));
        $noteTotal = \App\Models\DebitCreditNote::query()
            ->where('job_order_id', $document->job_order_id)
            ->get()
            ->filter(fn (\App\Models\DebitCreditNote $note): bool => !array_key_exists('deduct_advances', $note->data ?? []) || filter_var(($note->data ?? [])['deduct_advances'], FILTER_VALIDATE_BOOLEAN))
            ->sum(function (\App\Models\DebitCreditNote $note): float {
                return collect($note->data['rows'] ?? [])->sum(function ($row): float {
                    $particular = strtoupper(trim((string) ($row['particular'] ?? '')));
                    if ($particular !== '' && str_contains($particular, 'ADVANCE')) {
                        return 0.0;
                    }

                    $side = strtolower((string) ($row['side'] ?? 'debit'));
                    $amount = $parseAmount($row['amount'] ?? null);

                    return $side === 'credit' ? -$amount : $amount;
                });
            });
        $remainingAdvance = max(0, $advanceAfterService - min($advanceAfterService, max(0, (float) $noteTotal)));
        $billingAdvanceTotal = $cashAdvanceBalance;
        $billingAdvanceAvailable = $remainingAdvance;
        $billingAdvanceDeduction = min($grandTotal, $remainingAdvance);
        $billingAdvanceOverpayment = max(0, $remainingAdvance - $grandTotal);
        $billingAdvanceBalance = max(0, $remainingAdvance - $billingAdvanceDeduction);
    }
    $adjustedGrandTotal = $isService
        ? max(0, $grandTotal - $serviceAdvanceDeduction)
        : max(0, $grandTotal - $billingAdvanceDeduction);
@endphp
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        h1, h2, h3 { margin: 0; }
        .header { text-align: center; margin-bottom: 18px; }
        .company { font-weight: bold; text-transform: uppercase; font-size: 16px; }
        .title { font-size: 20px; text-transform: uppercase; margin-top: 12px; }
        .meta { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .meta td { padding: 3px 4px; vertical-align: top; }
        .label { font-weight: bold; width: 120px; }
        table.lines { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.lines th, table.lines td { border: 1px solid #d1d5db; padding: 6px; vertical-align: top; }
        table.lines th { background: #f3f4f6; text-align: left; }
        .right { text-align: right; }
        .total { font-weight: bold; font-size: 13px; }
        .section { margin-top: 16px; font-weight: bold; }
        .page-break { page-break-before: always; }
        .attachment-image { max-width: 100%; max-height: 9.2in; display: block; margin: 12px auto 0; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">APM Customs Brokerage</div>
        <div>Lot 7F 2&3 Rodriguez Compound, Aurenina Village, San Dionisio, 1700 City of Paranaque</div>
        <div>Tel. Nos.: (02) 8682-6845, 8696-7798</div>
        <div class="title">{{ $title }}</div>
    </div>

    <table class="meta">
        <tr>
            <td class="label">{{ $isService ? 'Sold To' : 'Bill To' }}</td>
            <td>{{ $data['bill_to'] ?? '-' }}</td>
            <td class="label">No.</td>
            <td>{{ $document->statement_no }}</td>
        </tr>
        <tr>
            <td class="label">Date</td>
            <td>{{ $statementDate }}</td>
            <td class="label">J.O Ref.</td>
            <td>{{ $data['job_ref_no'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Address</td>
            <td>{{ $data['bill_address'] ?? '-' }}</td>
            <td class="label">TIN</td>
            <td>{{ $data['bill_tin'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Description</td>
            <td>{{ $data['description'] ?? '-' }}</td>
            <td class="label">Invoice No.</td>
            <td>{{ $data['invoice_no'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Shipper</td>
            <td>{{ $data['shipper_name'] ?? '-' }}</td>
            <td class="label">Container No.</td>
            <td>{{ $data['container_no'] ?? '-' }}</td>
        </tr>
    </table>

    @if($isService)
        <table class="lines">
            <thead>
                <tr>
                    <th>Item Description / Nature of Service</th>
                    <th class="right">Qty</th>
                    <th class="right">Unit Cost</th>
                    <th class="right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 0; $i < max(count($siDesc), count($siQty), count($siUnitCost), count($siAmount), 1); $i++)
                    @continue(trim((string)($siDesc[$i] ?? '')) === '' && !is_numeric($siAmount[$i] ?? null))
                    <tr>
                        <td>{{ $siDesc[$i] ?? '-' }}</td>
                        <td class="right">{{ $siQty[$i] ?? '-' }}</td>
                        <td class="right">{{ $fmt($siUnitCost[$i] ?? 0) }}</td>
                        <td class="right">{{ $fmt($siAmount[$i] ?? 0) }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>
        <table class="lines">
            <tr><td class="right">VATable Sales</td><td class="right">{{ $fmt($data['si_vatable_sales'] ?? 0) }}</td></tr>
            <tr><td class="right">VAT</td><td class="right">{{ $fmt($data['si_vat'] ?? 0) }}</td></tr>
            <tr><td class="right">Less: Withholding Tax</td><td class="right">{{ $fmt($data['si_less_withholding_tax'] ?? 0) }}</td></tr>
            @if($serviceAdvanceDeduction > 0)
                <tr><td class="right">Subtotal</td><td class="right">{{ $fmt($grandTotal) }}</td></tr>
                <tr><td class="right">Less: Advances of PHP {{ $fmt($serviceAdvanceDeduction) }}</td><td class="right"></td></tr>
            @endif
            <tr><td class="right total">Total Amount Due</td><td class="right total">{{ $fmt($adjustedGrandTotal) }}</td></tr>
        </table>
    @else
        <div class="section">A. Non-Receipted Charges</div>
        <table class="lines">
            <thead><tr><th>Description</th><th class="right">Amount</th></tr></thead>
            <tbody>
                @for($i = 0; $i < max(count($nonDesc), count($nonAmt), 1); $i++)
                    @continue(trim((string)($nonDesc[$i] ?? '')) === '' && !is_numeric($nonAmt[$i] ?? null))
                    <tr><td>{{ $nonDesc[$i] ?? '-' }}</td><td class="right">{{ $fmt($nonAmt[$i] ?? 0) }}</td></tr>
                @endfor
                <tr><td class="right total">Subtotal</td><td class="right total">{{ $fmt($nonTotal) }}</td></tr>
            </tbody>
        </table>
        <div class="section">B. Receipted Charges</div>
        <table class="lines">
            <thead><tr><th>Description</th><th class="right">Amount</th></tr></thead>
            <tbody>
                @for($i = 0; $i < max(count($recDesc), count($recAmt), 1); $i++)
                    @continue(trim((string)($recDesc[$i] ?? '')) === '' && !is_numeric($recAmt[$i] ?? null))
                    <tr><td>{{ $recDesc[$i] ?? '-' }}</td><td class="right">{{ $fmt($recAmt[$i] ?? 0) }}</td></tr>
                @endfor
                <tr><td class="right total">Subtotal</td><td class="right total">{{ $fmt($recTotal) }}</td></tr>
            </tbody>
        </table>
        <table class="lines">
            @if($billingAdvanceDeduction > 0 || $billingAdvanceOverpayment > 0)
                <tr><td class="right">Subtotal</td><td class="right">{{ $fmt($grandTotal) }}</td></tr>
            @endif
            @if($billingAdvanceDeduction > 0)
                <tr><td class="right">Less: Advances of PHP {{ $fmt($billingAdvanceTotal) }}</td><td class="right">({{ $fmt($billingAdvanceAvailable) }})</td></tr>
            @endif
            @if($billingAdvanceOverpayment > 0)
                <tr><td class="right">OverPayment</td><td class="right">({{ $fmt($billingAdvanceOverpayment) }})</td></tr>
            @endif
            <tr><td class="right total">Grand Total</td><td class="right total">{{ ($billingAdvanceDeduction > 0 || $billingAdvanceOverpayment > 0) ? '' : $fmt($adjustedGrandTotal) }}</td></tr>
            <tr><td class="right">Amount in Words</td><td>{{ $billingAdvanceOverpayment > 0 ? $numberToWords($billingAdvanceOverpayment) : strtoupper($data['amount_in_words'] ?? '-') }}</td></tr>
        </table>
    @endif

    <div class="section">Uploaded Attachments</div>
    @if($imageAttachments->isEmpty() && $pdfAttachments->isEmpty())
        <p class="muted">No uploaded attachments.</p>
    @else
        <ol>
            @foreach($imageAttachments as $attachment)
                <li>{{ $attachment['filename'] }}</li>
            @endforeach
            @foreach($pdfAttachments as $attachment)
                <li>{{ $attachment->filename }} <span class="muted">(PDF appended after image attachments)</span></li>
            @endforeach
        </ol>
    @endif

    @foreach($imageAttachments as $attachment)
        <div class="page-break">
            <h3>Attachment: {{ $attachment['filename'] }}</h3>
            <img class="attachment-image" src="{{ $attachment['data_uri'] }}" alt="{{ $attachment['filename'] }}">
        </div>
    @endforeach
</body>
</html>
