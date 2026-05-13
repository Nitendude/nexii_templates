@extends('layouts.employeehub')

@section('content')
<style>
    .billing-paper-wrap {
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        width: 8.5in;
        min-height: 11in;
        max-width: 8.5in;
        margin: 0 auto 1.25rem;
    }
    .billing-paper {
        font-family: "Trebuchet MS", Tahoma, sans-serif;
        color: #1f2937;
        letter-spacing: 0.01em;
        min-height: 100%;
        padding: 0.35in 0.15in 0.15in;
        /* Left metadata block position (does NOT move Date) */
        --si-meta-offset-x-print: -0.1in;   /* + right, - left */
        --si-meta-offset-y-print: 0.30in;   /* + down, - up */
        --si-meta-line-gap-print: 0.05in;   /* extra vertical gap between lines */
        --si-desc-offset-print: 0.85in;     /* DESCRIPTION block offset */
        --si-totals-offset-x-print: 0in;    /* totals block: + right, - left */
        --si-totals-offset-y-print: 0.4in;/* totals block: + down, - up */
        --si-totals-line-gap-print: 4px;    /* totals lines spacing */
        --si-amount-words-offset-x-print: 0in; /* amount in words: + right, - left */
        --si-amount-words-offset-y-print: 0.4in; /* amount in words: + down, - up */
        --si-sign-offset-x-print: 0in;      /* signature names: + right, - left */
        --si-sign-offset-y-print: 0.6in;  /* signature names: + down, - up */
    }
    .apm-brand-font {
        font-family: "Blippo", "Cooper Black", "Arial Black", Impact, "Trebuchet MS", sans-serif;
        letter-spacing: 0.03em;
    }
    .billing-title {
        font-weight: 800;
        letter-spacing: 0.06em;
        font-size: 1.95rem;
        line-height: 1.05;
    }
    .meta-table td {
        padding: 2px 4px;
        vertical-align: top;
    }
    .meta-table-top td {
        padding: 1px 4px;
        vertical-align: top;
    }
    .meta-table,
    .meta-table-top {
        table-layout: auto;
    }
    .meta-label {
        width: 130px;
        font-weight: 700;
        white-space: nowrap;
    }
    .meta-label-right {
        padding-left: 24px;
        width: 138px;
    }
    .meta-colon {
        width: 10px;
        font-weight: 700;
    }
    .meta-value {
        font-weight: 700;
        text-transform: uppercase;
        word-break: normal;
        overflow-wrap: normal;
        white-space: normal;
    }
    .meta-right-value {
        min-width: 260px;
    }
    .meta-nowrap {
        white-space: nowrap;
    }
    .hr-line {
        border-top: 2px solid #2d2d2d;
        margin: 6px 0;
    }
    .hr-tight {
        border-top: 2px solid #2d2d2d;
        margin: 4px 0 6px;
    }
    .billing-section-title {
        font-weight: 800;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .expense-table {
        width: 100%;
        border-collapse: collapse;
    }
    .expense-table td {
        padding: 2px 0;
        vertical-align: top;
        font-weight: 400;
    }
    .expense-amount {
        width: 170px;
        text-align: right;
        padding-left: 12px;
    }
    .billing-foot td {
        padding: 2px 0;
        font-weight: 700;
    }
    .si-top-check .box {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 1.5px solid #2d2d2d;
        margin-right: 6px;
        vertical-align: middle;
        text-align: center;
        line-height: 12px;
        font-size: 11px;
        font-weight: 800;
    }
    .si-top-check {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        line-height: 1.1;
    }
    .si-grid {
        width: 100%;
        border-collapse: collapse;
        margin-top: 6px;
    }
    .si-grid th,
    .si-grid td {
        border: 1px solid #2d2d2d;
        padding: 6px 8px;
        vertical-align: top;
    }
    .si-grid th {
        text-transform: uppercase;
        font-weight: 800;
        font-size: 0.95rem;
    }
    .si-bottom-grid {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
    }
    .si-bottom-grid td {
        padding: 2px 0;
    }
    .si-amount-words {
        border-top: 2px solid #2d2d2d;
        border-bottom: 2px solid #2d2d2d;
        padding: 6px 0;
        margin: 8px 0 10px;
    }
    .si-amount-words-line {
        display: flex;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
    }
    .si-amount-words-label {
        font-weight: 700;
    }
    .si-amount-words-blank {
        display: inline-block;
        min-width: 220px;
        border-bottom: 2px solid #2d2d2d;
        height: 0.95em;
    }
    .si-amount-words-text {
        flex: 1 1 auto;
        text-align: center;
        font-weight: 800;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .si-sign td {
        padding-top: 10px;
        vertical-align: top;
    }
    .si-sign .line {
        border-top: 2px solid #2d2d2d;
        margin-top: 16px;
        padding-top: 3px;
        font-weight: 800;
    }
    .si-static {
        font-weight: 700;
    }
    .si-print-label {
        font-weight: 700;
    }
    .si-value {
        font-weight: 700;
    }
    .si-brokerage-value {
        font-weight: 800;
    }
    .si-amount-num {
        display: inline-block;
        min-width: 120px;
        text-align: right;
        font-variant-numeric: tabular-nums;
    }
    .si-grid-line {
        display: block;
        margin-bottom: 0.18rem;
    }
    .si-grid-line:last-child {
        margin-bottom: 0;
    }
    @media print {
        @page {
            size: Letter portrait;
            margin: 0.35in;
        }
        .no-print {
            display: none !important;
        }
        .eh-navbar,
        .eh-sidebar,
        .eh-sidebar-col,
        #sidebarOffcanvas,
        .chatbot-fab,
        .chatbot-window {
            display: none !important;
        }
        .container-fluid,
        .container-fluid .row,
        .eh-main-col {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            flex: 0 0 100% !important;
        }
        body {
            background: #ffffff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .billing-paper-wrap,
        .billing-paper,
        .billing-paper * {
            box-sizing: border-box !important;
        }
        .billing-paper {
            width: 7.8in !important;
            max-width: 7.8in !important;
            min-height: auto !important;
            margin: 0 auto !important;
            padding: 1.45in 0.06in 0.06in !important;
            font-size: 10.8px !important;
            line-height: 1.2 !important;
            letter-spacing: 0 !important;
        }
        .billing-paper-wrap {
            background: #ffffff !important;
            box-shadow: none;
            border: 0 !important;
            border-radius: 0;
            width: auto !important;
            min-height: auto !important;
            max-width: none !important;
            padding: 0 !important;
            margin: 0 auto !important;
        }
        .billing-title {
            font-size: 26px !important;
            letter-spacing: 0.03em;
        }
        .meta-label {
            width: 104px !important;
            font-size: 12px !important;
        }
        .meta-colon {
            width: 8px !important;
        }
        .meta-table td,
        .meta-table-top td {
            padding: 2px 3px !important;
        }
        .meta-label-right {
            padding-left: 24px !important;
            width: 118px !important;
        }
        .meta-right-value {
            min-width: 230px !important;
        }
        .small {
            font-size: 11px !important;
        }
        .hr-line,
        .hr-tight {
            margin: 7px 0 !important;
        }
        .expense-table td {
            padding: 1px 0 !important;
            line-height: 1.2 !important;
        }
        .expense-amount {
            width: 130px !important;
        }
        .billing-foot td {
            font-size: 12.5px !important;
        }
        .border-top {
            margin-top: 14px !important;
        }
        .billing-paper-wrap,
        .billing-paper {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .meta-nowrap {
            white-space: nowrap !important;
        }
        .si-top-check .box {
            border: 0 !important;
            width: auto !important;
            height: auto !important;
            line-height: 1 !important;
            font-size: 10px !important;
            margin-right: 1px !important;
        }
        .si-grid th,
        .si-grid td {
            padding: 4px 6px !important;
        }
        .si-grid tbody tr {
            height: auto !important;
        }
        .si-bottom-grid {
            margin-top: 6px !important;
        }
        .si-amount-words {
            margin: 6px 0 8px !important;
        }
        .si-sign td {
            padding-top: 8px !important;
        }

        /* Service Invoice pre-printed paper mode: print data overlay only */
        .si-generated-only,
        .si-grid thead,
        .si-amount-words-label,
        .si-sign td > div:first-child,
        .si-static {
            display: none !important;
        }
        .si-print-label {
            display: inline !important;
            visibility: visible !important;
        }
        /* Keep metadata column spacing, but hide generated label text */
        .meta-label,
        .meta-colon {
            display: table-cell !important;
            visibility: hidden !important;
        }
        .meta-table-top {
            width: 100% !important;
            table-layout: fixed !important;
            margin-bottom: 0.05in !important;
            margin-top: 0.01in !important;
        }
        .si-meta-left-move {
            transform: translate(var(--si-meta-offset-x-print), var(--si-meta-offset-y-print)) !important;
        }
        .meta-table-top tr:nth-child(2) .si-meta-left-move {
            transform: translate(
                var(--si-meta-offset-x-print),
                calc(var(--si-meta-offset-y-print) + var(--si-meta-line-gap-print))
            ) !important;
        }
        .meta-table-top tr:nth-child(3) .si-meta-left-move {
            transform: translate(
                var(--si-meta-offset-x-print),
                calc(var(--si-meta-offset-y-print) + (var(--si-meta-line-gap-print) * 2))
            ) !important;
        }
        .meta-table-top tr:nth-child(4) .si-meta-left-move {
            transform: translate(
                var(--si-meta-offset-x-print),
                calc(var(--si-meta-offset-y-print) + (var(--si-meta-line-gap-print) * 3))
            ) !important;
        }
        .meta-table-top tr > td:nth-child(1) { width: 13% !important; }
        .meta-table-top tr > td:nth-child(2) { width: 2% !important; }
        .meta-table-top tr > td:nth-child(3) { width: 45% !important; }
        .meta-table-top tr > td:nth-child(4) { width: 14% !important; }
        .meta-table-top tr > td:nth-child(5) { width: 2% !important; }
        .meta-table-top tr > td:nth-child(6) { width: 24% !important; }
        .hr-line,
        .hr-tight,
        .si-grid th,
        .si-grid td,
        .si-amount-words,
        .si-sign .line,
        .border-top {
            border: 0 !important;
        }
        .meta-table-top td,
        .meta-table td {
            padding: 0 !important;
            line-height: 1 !important;
        }
        .meta-value {
            font-weight: 700 !important;
            text-transform: uppercase !important;
        }
        .meta-value.si-value {
            font-size: 10px !important;
            white-space: nowrap !important;
            letter-spacing: 0 !important;
        }
        /* Override only the left SI metadata block to print larger */
        .meta-value.si-value.si-meta-left-move {
            font-size: 12px !important;
            line-height: 1.05 !important;
        }
        .meta-value.si-value.si-date-value {
            font-size: 17px !important;
            line-height: 1 !important;
        }
        .si-long-field {
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            max-width: 100% !important;
        }
        .si-date-value {
            overflow: visible !important;
            text-overflow: clip !important;
            min-width: 1.45in !important;
            display: inline-block !important;
        }
        .meta-table-top tr {
            height: 0.148in !important;
        }
        .si-grid,
        .si-bottom-grid {
            margin-top: 1px !important;
        }
        .si-grid td {
            padding: 1px 0 !important;
        }
        .si-grid tbody tr {
            height: 4.35in !important;
        }
        .si-grid tbody td {
            vertical-align: middle !important;
        }
        .si-grid-line {
            margin-bottom: 0.08in !important;
        }
        .si-grid-line:last-child {
            margin-bottom: 0 !important;
        }
        .si-bottom-grid td {
            padding: 0 !important;
            line-height: 1.15 !important;
        }
        .si-bottom-grid {
            width: 100% !important;
            table-layout: fixed !important;
        }
        .si-bottom-grid td:first-child {
            width: 48% !important;
        }
        .si-bottom-grid td:last-child {
            width: 52% !important;
        }
        .si-amount-num {
            min-width: 128px !important;
            text-align: right !important;
            font-size: 13px !important;
            font-weight: 800 !important;
            padding-right: 0.02in !important;
        }
        .si-bottom-grid .d-flex {
            justify-content: flex-end !important;
            gap: 0 !important;
            padding: var(--si-totals-line-gap-print) 0 !important;
        }
        /* Remove forced Bootstrap spacing on TOTAL AMOUNT DUE row in print */
        .si-bottom-grid .d-flex.border-top {
            margin-top: 0 !important;
            padding-top: 0 !important;
            border-top: 0 !important;
        }
        .si-bottom-grid {
            transform: translate(var(--si-totals-offset-x-print), var(--si-totals-offset-y-print)) !important;
        }
        .si-date-value {
            text-align: right !important;
            padding-right: 0.04in !important;
            font-size: 17px !important;
            font-weight: 800 !important;
            line-height: 1 !important;
            display: table-cell !important;
            vertical-align: bottom !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            transform: translateY(0.19in) !important;
        }
        /* Align first row (Sold To value) better to its printed guide line */
        .meta-table-top tr:first-child td:nth-child(3) .si-value {
            display: inline-block !important;
            transform: translateY(0.04in) !important;
        }
        .meta-table-top tr:nth-child(2) td:nth-child(3) .si-value,
        .meta-table-top tr:nth-child(3) td:nth-child(3) .si-value,
        .meta-table-top tr:nth-child(4) td:nth-child(3) .si-value {
            display: inline-block !important;
            transform: translateY(0.01in) !important;
        }
        .si-desc-block {
            margin-top: 0 !important;
            margin-bottom: 0.02in !important;
            margin-left: 0.02in !important;
            transform: translateY(var(--si-desc-offset-print)) !important;
        }
        .si-desc-block .small {
            font-size: 12.2px !important;
            line-height: 1.16 !important;
        }
        .si-brokerage-value {
            font-size: 13px !important;
            font-weight: 800 !important;
        }
        .si-sign td {
            vertical-align: bottom !important;
        }
        .si-sign {
            width: 100% !important;
            table-layout: fixed !important;
        }
        .si-sign td:nth-child(1),
        .si-sign td:nth-child(2),
        .si-sign td:nth-child(3) {
            width: 33.333% !important;
        }
        .si-sign .line {
            font-size: 14px !important;
            font-weight: 800 !important;
            margin-top: 7px !important;
        }
        .si-amount-words-line,
        .si-amount-words-text {
            font-size: 13.4px !important;
            font-weight: 800 !important;
        }
        .si-amount-words {
            transform: translate(var(--si-amount-words-offset-x-print), var(--si-amount-words-offset-y-print)) !important;
        }
        .si-sign {
            transform: translate(var(--si-sign-offset-x-print), var(--si-sign-offset-y-print)) !important;
            margin-top: 0 !important;
        }
        /* Fine tune horizontal overlay alignment on pre-printed SI */
        .billing-paper {
            transform: translate(0.08in, 0.12in) !important;
            transform-origin: top left !important;
        }
    }
</style>

@php
    $data = $statement->data ?? [];
    $isService = true;
    $isDraft = (bool) ($isDraft ?? false);
    $nonDesc = $data['non_receipted_desc'] ?? [];
    $nonAmt = $data['non_receipted_amount'] ?? [];
    $recDesc = $data['receipted_desc'] ?? [];
    $recAmt = $data['receipted_amount'] ?? [];

    $fmt = function ($amount) {
        $num = is_numeric($amount) ? (float) $amount : 0;
        return number_format($num, 2);
    };
    $serviceVatAmount = is_numeric($data['si_less_vat'] ?? null)
        ? (float) $data['si_less_vat']
        : (is_numeric($data['si_vat'] ?? null) ? (float) $data['si_vat'] : 0);

    $nonTotal = 0;
    foreach ($nonAmt as $amt) {
        $nonTotal += is_numeric($amt) ? (float) $amt : 0;
    }

    $recTotal = 0;
    foreach ($recAmt as $amt) {
        $recTotal += is_numeric($amt) ? (float) $amt : 0;
    }

    $serviceLineAmounts = $data['si_amount'] ?? [];
    if (!is_array($serviceLineAmounts)) { $serviceLineAmounts = [$serviceLineAmounts]; }
    $baseServiceTotal = isset($baseServiceTotal) && is_numeric($baseServiceTotal)
        ? (float) $baseServiceTotal
        : (is_numeric($data['si_total_amount_due'] ?? null)
            ? (float) $data['si_total_amount_due']
            : (is_numeric($data['grand_total'] ?? null)
                ? (float) $data['grand_total']
                : collect($serviceLineAmounts)->sum(fn ($amount) => is_numeric($amount) ? (float) $amount : 0.0)));
    $serviceAdvanceDeduction = isset($serviceAdvanceDeduction) && is_numeric($serviceAdvanceDeduction)
        ? (float) $serviceAdvanceDeduction
        : 0.0;
    $serviceAdvanceBalance = isset($serviceAdvanceBalance) && is_numeric($serviceAdvanceBalance)
        ? (float) $serviceAdvanceBalance
        : 0.0;
    $adjustedServiceTotal = isset($adjustedServiceTotal) && is_numeric($adjustedServiceTotal)
        ? (float) $adjustedServiceTotal
        : max(0, $baseServiceTotal - $serviceAdvanceDeduction);
    $grandTotal = $adjustedServiceTotal;
    $statementDate = !empty($data['statement_date'])
        ? \Carbon\Carbon::parse($data['statement_date'])->format('F d, Y')
        : optional($statement->created_at)->format('F d, Y');
    $isConverge = str_contains(strtoupper(($data['bill_to'] ?? '') . ' ' . ($data['bill_business_style'] ?? '')), 'CONVERGE');
    $siDesc = $data['si_item_description'] ?? [];
    $siQty = $data['si_quantity'] ?? [];
    $siUnitCost = $data['si_unit_cost'] ?? [];
    $siAmount = $data['si_amount'] ?? [];
    if (!is_array($siDesc)) { $siDesc = [$siDesc]; }
    if (!is_array($siQty)) { $siQty = [$siQty]; }
    if (!is_array($siUnitCost)) { $siUnitCost = [$siUnitCost]; }
    if (!is_array($siAmount)) { $siAmount = [$siAmount]; }
    $siRowCount = max(count($siDesc), count($siQty), count($siUnitCost), count($siAmount), 1);
    $pdfUrl = !$isDraft ? route('service-invoices.pdf', $statement) : null;
    $gmailSubject = rawurlencode('Service Invoice #' . ($statement->statement_no ?? ''));
    $gmailBody = rawurlencode(
        "Good day,\n\nPlease see the service invoice PDF package for JO " . ($data['job_ref_no'] ?? '-') . ".\n\nDownload PDF with attachments: " . ($pdfUrl ?? '') . "\n\nNote: Gmail cannot auto-attach files from a website link. Please download the PDF package and attach it before sending.\n\nThank you."
    );
    $gmailUrl = !$isDraft ? "https://mail.google.com/mail/?view=cm&fs=1&su={$gmailSubject}&body={$gmailBody}" : null;
@endphp

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 no-print">
        <div>
            <h2 class="mb-1">{{ $isDraft ? 'Draft ' : '' }}{{ $isService ? 'Service Invoice' : 'Billing Statement' }} #{{ $statement->statement_no }}</h2>
            <p class="text-muted mb-0">
                {{ $isDraft ? 'Draft preview only. This document has not been saved to the database.' : 'Formatted output based on your document template.' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            @if($isDraft)
                <button class="btn btn-outline-secondary" type="button" onclick="window.close()">Close Draft</button>
            @else
                <a class="btn btn-outline-secondary" href="{{ $isService ? route('service-invoices.documents') : route('billing.documents') }}">Back to Documents</a>
                <a class="btn btn-outline-primary" href="{{ $isService ? route('service-invoices') : route('billing') }}">Back</a>
                <a class="btn btn-outline-warning" href="{{ route('service-invoices.edit', $statement) }}">Edit</a>
                <a class="btn btn-outline-success" href="{{ $pdfUrl }}">Download PDF + Attachments</a>
                <a class="btn btn-outline-danger" href="{{ $gmailUrl }}" target="_blank" rel="noopener">Email via Gmail</a>
            @endif
            <button class="btn btn-primary" type="button" onclick="window.print()">Print</button>
        </div>
    </div>

    @if(session('status') === 'billing-attachments-uploaded')
        <div class="alert alert-success no-print">Scanned document uploaded successfully.</div>
    @endif

    @if(!$isDraft)
        @include('partials.scanner-upload', [
            'scannerId' => 'serviceInvoiceScanner' . $statement->id,
            'modalTitle' => 'Scan Document to Service Invoice #' . $statement->statement_no,
            'description' => 'Choose a connected scanner, scan the document, and APM will save it directly to this Service Invoice.',
            'uploadUrl' => route('service-invoices.attachments.store', $statement),
            'documentLabel' => 'service-invoice-' . ($statement->statement_no ?? $statement->id),
        ])
    @endif

    @if(!$isDraft && $statement->attachments->count())
        <div class="alert alert-light border no-print">
            <div class="fw-semibold mb-1">Attachments included in downloadable PDF</div>
            <div class="d-flex flex-wrap gap-2">
                @foreach($statement->attachments as $attachment)
                    <a class="badge text-bg-light border text-decoration-none" href="{{ \Illuminate\Support\Facades\Storage::url($attachment->path) }}" target="_blank" rel="noopener">
                        {{ $attachment->filename }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="billing-paper-wrap p-4 p-md-5">
        <div class="billing-paper">
            <div class="text-center mb-2 si-generated-only">
                <div class="fw-bold text-uppercase fs-4 apm-brand-font">APM Customs Brokerage</div>
                <div class="small fw-semibold">Lot 7F 2&3 Rodriguez Compound, Aurenina Village, San Dionisio, 1700 City of Paranaque</div>
                <div class="small fw-semibold">NCR, Fourth District, Philippines</div>
                <div class="small fw-semibold">Tel. Nos.: (02) 8682-6845, 8696-7798</div>
                <div class="small fw-semibold">VAT Reg. TIN: 120-291-938-00000</div>
            </div>

            <div class="d-flex justify-content-center align-items-center mb-3 si-generated-only">
                <div class="billing-title fs-3 apm-brand-font">{{ $isDraft ? 'DRAFT ' : '' }}{{ $isService ? 'SERVICE INVOICE' : 'BILLING STATEMENT' }}</div>
            </div>

            <table class="meta-table-top w-100 mb-2">
                @if($isService)
                    <tr>
                        <td class="meta-label">Sold To</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value si-value si-long-field si-meta-left-move" style="width:48%;">{{ $data['bill_to'] ?? '-' }}</td>
                        <td class="meta-label meta-label-right">Date</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value meta-right-value meta-nowrap si-value si-date-value">{{ $statementDate }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Registered Name</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value si-value si-long-field si-meta-left-move">{{ $data['si_registered_name'] ?? '-' }}</td>
                        <td class="meta-label meta-label-right">Sales Type</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value meta-right-value si-value">
                            @php $salesType = strtolower($data['si_sales_type'] ?? ''); @endphp
                            <div class="si-top-check">
                                <div class="meta-nowrap"><span class="box">{{ $salesType === 'cash' ? '✓' : '' }}</span> <span class="si-static">CASH SALES</span></div>
                                <div class="meta-nowrap"><span class="box">{{ $salesType === 'charge' ? '✓' : '' }}</span> <span class="si-static">CHARGE SALES</span></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label">TIN</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value si-value si-meta-left-move">{{ $data['bill_tin'] ?? '-' }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="meta-label">Business Address</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value si-value si-long-field si-meta-left-move">{{ $data['bill_address'] ?? '-' }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @else
                    <tr>
                        <td class="meta-label">Bill To</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value" style="width:48%;">{{ $data['bill_to'] ?? '-' }}</td>
                        <td class="meta-label meta-label-right">Date</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value meta-right-value meta-nowrap">{{ $statementDate }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Address</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value">{{ $data['bill_address'] ?? '-' }}</td>
                        <td class="meta-label meta-label-right">TIN</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value meta-right-value meta-nowrap">{{ $data['bill_tin'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="meta-label meta-label-right">Bus. Style</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value meta-right-value">{{ $data['bill_business_style'] ?? '-' }}</td>
                    </tr>
                @endif
            </table>

            @if(!$isService)
                <div class="hr-tight"></div>
                <table class="meta-table-top w-100">
                    <tr>
                        <td class="meta-label">Vessel/Voy.</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value" style="width:48%;">{{ $data['vessel_voyage'] ?? '-' }}</td>
                                <td class="meta-label meta-label-right">Vol./Meas.</td>
                                <td class="meta-colon">:</td>
                                <td class="meta-value meta-right-value meta-nowrap">
                                    {{ $data['vol_meas'] ?? '-' }}{{ !empty($data['vol_meas_unit']) ? ' ' . strtoupper($data['vol_meas_unit']) : '' }}
                                </td>
                            </tr>
                    <tr>
                        <td class="meta-label">B/L No.</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value">{{ $data['bl_no'] ?? '-' }}</td>
                        <td class="meta-label meta-label-right meta-nowrap">Job Ref. No.</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value meta-right-value meta-nowrap">{{ $data['job_ref_no'] ?? '-' }}</td>
                    </tr>
                </table>
                <div class="hr-tight mb-3"></div>
            @endif

            @if($isService)
                <div class="mb-2 si-desc-block">
                    <div class="small fw-bold"><span class="si-print-label">DESCRIPTION : </span><span class="fw-semibold si-value">{{ $data['description'] ?? '-' }}</span></div>
                    <div class="small fw-bold"><span class="si-print-label">SHIPPERS NAME : </span><span class="fw-semibold si-value">{{ $data['shipper_name'] ?? '-' }}</span></div>
                    <div class="row g-2">
                        <div class="col-md-5 small fw-bold"><span class="si-print-label">INVOICE NO. : </span><span class="fw-semibold si-value">{{ $data['invoice_no'] ?? '-' }}</span></div>
                        <div class="col-md-4">
                            <div class="small fw-bold"><span class="si-print-label">PORT : </span><span class="fw-semibold si-value">{{ $data['port'] ?? '-' }}</span></div>
                            <div class="small fw-bold mt-1"><span class="si-print-label">CTNR NO. : </span><span class="fw-semibold si-value">{{ $data['container_no'] ?? '-' }}</span></div>
                        </div>
                    </div>
                </div>

                <table class="si-grid">
                    <thead>
                        <tr>
                            <th style="width:48%;">Item Description / Nature of Service</th>
                            <th style="width:16%;" class="text-center">Quantity</th>
                            <th style="width:18%;" class="text-center">Unit Cost</th>
                            <th style="width:18%;" class="text-center">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="height:110px;">
                            <td class="si-value si-brokerage-value">
                                @for($i = 0; $i < $siRowCount; $i++)
                                    @php $lineDesc = trim((string) ($siDesc[$i] ?? '')); @endphp
                                    <span class="si-grid-line">{{ strtoupper($lineDesc !== '' ? $lineDesc : 'BROKERAGE FEE AS PER CAO 1-2001') }}</span>
                                @endfor
                            </td>
                            <td class="text-center">
                                @for($i = 0; $i < $siRowCount; $i++)
                                    @php $lineQty = (float) ($siQty[$i] ?? 0); @endphp
                                    <span class="si-grid-line si-value">{{ $lineQty == 0.0 ? '' : $fmt($lineQty) . (!empty($data['vol_meas_unit']) ? ' ' . strtoupper($data['vol_meas_unit']) : '') }}</span>
                                @endfor
                            </td>
                            <td class="text-end">
                                @for($i = 0; $i < $siRowCount; $i++)
                                    @php $lineUnit = (float) ($siUnitCost[$i] ?? 0); @endphp
                                    <span class="si-grid-line si-value">{{ $lineUnit == 0.0 ? '' : $fmt($lineUnit) }}</span>
                                @endfor
                            </td>
                            <td class="text-end">
                                @for($i = 0; $i < $siRowCount; $i++)
                                    @php $lineAmt = (float) ($siAmount[$i] ?? 0); @endphp
                                    <span class="si-grid-line si-value si-amount-num">{{ $fmt($lineAmt) }}</span>
                                @endfor
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="hr-line my-2"></div>
                <table class="si-bottom-grid mb-2">
                    <tr>
                        <td style="width:50%;">
                            <div class="si-static">VATable Sales</div>
                            <div class="si-static">VAT</div>
                            <div class="si-static">Zero Rated Sales</div>
                            <div class="si-static">VAT-Exempt Sales</div>
                        </td>
                        <td style="width:50%;">
                            <div class="d-flex justify-content-between"><span class="si-static">Total Sales (VAT Inclusive)</span><strong class="si-value si-amount-num">{{ $fmt($data['si_total_sales'] ?? 0) }}</strong></div>
                            <div class="d-flex justify-content-between"><span class="si-static">Less: VAT</span><strong class="si-value si-amount-num">{{ $fmt($serviceVatAmount) }}</strong></div>
                            <div class="d-flex justify-content-between"><span class="si-static">Amount: Net of VAT</span><strong class="si-value si-amount-num">{{ $fmt($data['si_amount_net_vat'] ?? 0) }}</strong></div>
                            <div class="d-flex justify-content-between"><span class="si-static">Less: Withholding Tax</span><strong class="si-value si-amount-num">({{ $fmt($data['si_less_withholding_tax'] ?? 0) }})</strong></div>
                            <div class="d-flex justify-content-between"><span class="si-static">Add: VAT</span><strong class="si-value si-amount-num">{{ $fmt($serviceVatAmount) }}</strong></div>
                            @if($serviceAdvanceDeduction > 0)
                                <div class="d-flex justify-content-between"><span class="si-static">Subtotal</span><strong class="si-value si-amount-num">{{ $fmt($baseServiceTotal) }}</strong></div>
                                <div class="d-flex justify-content-between"><span class="si-static">Less: Advances of PHP {{ $fmt($serviceAdvanceDeduction) }}</span><strong class="si-value si-amount-num"></strong></div>
                            @endif
                            <div class="d-flex justify-content-between border-top border-dark mt-1 pt-1"><span class="fw-bold si-static">TOTAL AMOUNT DUE</span><strong class="si-value si-amount-num">{{ $fmt($adjustedServiceTotal) }}</strong></div>
                        </td>
                    </tr>
                </table>

                <div class="si-amount-words">
                    <div class="si-amount-words-line">
                        <span class="si-amount-words-label">Received the Amount of</span>
                        <span class="si-amount-words-text si-value">{{ strtoupper($adjustedAmountInWords ?? $data['amount_in_words'] ?? '-') }}</span>
                    </div>
                </div>

                <table class="w-100 si-sign">
                    <tr>
                        <td style="width:33%;">
                            <div>Prepared By:</div>
                            <div class="line si-value">{{ strtoupper($data['prepared_by'] ?? '-') }}</div>
                        </td>
                        <td style="width:33%;">
                            <div>Checked By:</div>
                            <div class="line si-value">{{ strtoupper($data['approved_by'] ?? '-') }}</div>
                        </td>
                        <td style="width:34%;">
                            <div>Received By:</div>
                            <div class="line si-value">{{ strtoupper($data['received_by'] ?? '-') }}</div>
                            <div class="mt-1 text-center fw-semibold si-static">Cashier / Authorized Representative</div>
                        </td>
                    </tr>
                </table>
            @else
                <div class="mb-3">
                    <div class="small fw-bold">DESCRIPTION : <span class="fw-semibold">{{ $data['description'] ?? '-' }}</span></div>
                    <div class="small fw-bold">SHIPPER'S NAME : <span class="fw-semibold">{{ $data['shipper_name'] ?? '-' }}</span></div>
                    <div class="row g-2">
                        <div class="col-md-5 small fw-bold">INVOICE NO. : <span class="fw-semibold">{{ $data['invoice_no'] ?? '-' }}</span></div>
                        <div class="col-md-4">
                            <div class="small fw-bold">PORT : <span class="fw-semibold">{{ $data['port'] ?? '-' }}</span></div>
                            <div class="small fw-bold mt-1">CTNR. NO : <span class="fw-semibold">{{ $data['container_no'] ?? '-' }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="billing-section-title mb-1">I. Brokerage Reimbursable Expenses:</div>
                @if(!$isConverge)
                    <div class="billing-section-title mb-1">A. Non-Receipted Charges</div>
                    <table class="expense-table mb-3">
                        <tbody>
                            @forelse($nonDesc as $index => $desc)
                                @if(!empty(trim((string) $desc)) || !empty($nonAmt[$index]))
                                    <tr>
                                        <td>{{ strtoupper($desc) }}</td>
                                        <td class="expense-amount">{{ $fmt($nonAmt[$index] ?? 0) }}</td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td>-</td>
                                    <td class="expense-amount">0.00</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
                
                @if(count($recDesc) > 0)
                    <div class="billing-section-title mb-1">B. Receipted Charges</div>
                    <table class="expense-table mb-3">
                        <tbody>
                            @foreach($recDesc as $index => $desc)
                                @if(!empty(trim((string) $desc)) || !empty($recAmt[$index]))
                                    <tr>
                                        <td>{{ strtoupper($desc) }}</td>
                                        <td class="expense-amount">{{ $fmt($recAmt[$index] ?? 0) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif

            @if(!$isService)
                <div class="hr-line mt-4"></div>
                <table class="billing-foot w-100 mb-2">
                    <tr>
                        <td style="width: 170px;">TOTAL AMOUNT</td>
                        <td class="text-end">PHP {{ $fmt($grandTotal) }}</td>
                    </tr>
                </table>
                <div class="hr-line my-2"></div>
                <table class="billing-foot w-100 mb-2">
                    <tr>
                        <td style="width: 170px;">AMOUNT IN WORDS:</td>
                        <td class="text-center">{{ strtoupper($data['amount_in_words'] ?? '-') }}</td>
                    </tr>
                </table>
                <div class="hr-line mb-3"></div>

                <div class="row g-3 mt-4">
                    <div class="col-md-4">
                        <div class="fw-bold">Prepared by:</div>
                        <div class="mt-4 border-top border-dark pt-1 fw-semibold">{{ strtoupper($data['prepared_by'] ?? '-') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="fw-bold">Approved by:</div>
                        <div class="mt-4 border-top border-dark pt-1 fw-semibold">{{ strtoupper($data['approved_by'] ?? '-') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="fw-bold">Received by:</div>
                        <div class="mt-4 border-top border-dark pt-1 fw-semibold">{{ strtoupper($data['received_by'] ?? '-') }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
