@extends('layouts.employeehub')

@section('content')
@php
    $data = $note->data ?? [];
    $rows = collect($data['rows'] ?? [])->values();
    $maxPrintableRows = 12;
    $printRows = $rows->take($maxPrintableRows);
    $hiddenRowCount = max(0, $rows->count() - $maxPrintableRows);
    $fmt = function ($amount) {
        $num = is_numeric($amount) ? (float) $amount : 0;
        return number_format($num, 2);
    };
    $totalDebit = (float) ($data['total_debit'] ?? $rows->where('side', 'debit')->sum('amount'));
    $totalCredit = (float) ($data['total_credit'] ?? $rows->where('side', 'credit')->sum('amount'));
    $netTotal = (float) ($data['net_total'] ?? ($totalDebit - $totalCredit));
    $baseNoteTotal = isset($baseNoteTotal) && is_numeric($baseNoteTotal) ? (float) $baseNoteTotal : max(0, $netTotal);
    $noteAdvanceDeduction = isset($noteAdvanceDeduction) && is_numeric($noteAdvanceDeduction) ? (float) $noteAdvanceDeduction : 0.0;
    $noteAdvanceTotal = isset($noteAdvanceTotal) && is_numeric($noteAdvanceTotal) ? (float) $noteAdvanceTotal : $noteAdvanceDeduction;
    $noteAdvanceBalance = isset($noteAdvanceBalance) && is_numeric($noteAdvanceBalance) ? (float) $noteAdvanceBalance : 0.0;
    $adjustedNoteTotal = isset($adjustedNoteTotal) && is_numeric($adjustedNoteTotal) ? (float) $adjustedNoteTotal : max(0, $baseNoteTotal - $noteAdvanceDeduction);
    $noteDate = !empty($data['note_date'])
        ? \Carbon\Carbon::parse($data['note_date'])->format('F d, Y')
        : optional($note->note_date)->format('F d, Y');
@endphp

<style>
    .dcn-paper-wrap {
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        max-width: 8in;
        margin: 0 auto 1.25rem;
    }
    .dcn-paper {
        font-family: "Trebuchet MS", Tahoma, sans-serif;
        color: #1f2937;
        letter-spacing: 0;
        padding: 0.32in 0.14in 0.16in;
        font-size: 11.2px;
        line-height: 1.25;
    }
    .dcn-brand {
        text-align: center;
        margin-bottom: 0.06in;
    }
    .dcn-brand-title {
        font-family: "Blippo", "Cooper Black", "Arial Black", Impact, "Trebuchet MS", sans-serif;
        font-weight: 800;
        letter-spacing: 0.04em;
        font-size: 27px;
        line-height: 1;
        text-transform: uppercase;
    }
    .dcn-brand-line {
        font-size: 11px;
        font-weight: 600;
        line-height: 1.1;
    }
    .dcn-note-title {
        text-align: center;
        font-weight: 800;
        font-size: 26px;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        margin: 0.06in 0 0.08in;
    }

    .dcn-meta-grid {
        display: grid;
        grid-template-columns: 58% 42%;
        column-gap: 12px;
    }
    .dcn-meta-grid table,
    .dcn-meta-bottom table {
        width: 100%;
        border-collapse: collapse;
    }
    .dcn-meta-grid td,
    .dcn-meta-bottom td {
        padding: 1px 4px;
        vertical-align: top;
        font-weight: 700;
        text-transform: uppercase;
    }
    .dcn-label {
        width: 100px;
        white-space: nowrap;
    }
    .dcn-colon {
        width: 10px;
        text-align: center;
    }
    .dcn-rule {
        border-top: 2px solid #2d2d2d;
        margin: 5px 0 4px;
    }
    .dcn-meta-bottom {
        display: grid;
        grid-template-columns: 58% 42%;
        column-gap: 12px;
    }

    .dcn-ledger {
        width: 100%;
        border-collapse: collapse;
        margin-top: 4px;
        border-top: 2px solid #2d2d2d;
        border-bottom: 2px solid #2d2d2d;
    }
    .dcn-ledger th,
    .dcn-ledger td {
        padding: 2px 6px;
        vertical-align: top;
        border: 0;
        font-weight: 700;
    }
    .dcn-ledger th {
        text-transform: uppercase;
        font-size: 11.2px;
        border-bottom: 2px solid #2d2d2d;
    }
    .dcn-ledger th:nth-child(2),
    .dcn-ledger td:nth-child(2) {
        border-left: 2px solid #2d2d2d;
    }
    .dcn-ledger th:nth-child(3),
    .dcn-ledger td:nth-child(3) {
        border-left: 2px solid #2d2d2d;
    }
    .dcn-desc-cell {
        text-transform: uppercase;
        padding-top: 4px;
        padding-bottom: 2px;
    }
    .dcn-desc-cell div {
        margin-bottom: 1px;
    }
    .dcn-line-item {
        text-transform: uppercase;
    }
    .dcn-section-line {
        font-weight: 800;
        text-transform: uppercase;
    }
    .dcn-section-nonreceipted {
        font-weight: 800;
        text-transform: uppercase;
    }
    .dcn-desc-bottom {
        display: grid;
        grid-template-columns: 1fr 0.9fr;
        column-gap: 18px;
        margin-top: 2px;
    }
    .dcn-desc-right {
        text-align: left;
    }
    .dcn-filler td {
        height: 470px;
        padding: 0;
    }

    .dcn-footer-line {
        display: grid;
        grid-template-columns: 58% 21% 21%;
        border-top: 2px solid #2d2d2d;
    }
    .dcn-footer-line > div {
        padding: 2px 6px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .dcn-footer-line > div:nth-child(2),
    .dcn-footer-line > div:nth-child(3) {
        border-left: 2px solid #2d2d2d;
        text-align: right;
    }
    .dcn-words {
        border-top: 2px solid #2d2d2d;
        padding: 2px 6px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .dcn-sign-head {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        border-top: 2px solid #2d2d2d;
        padding-top: 2px;
        margin-top: 2px;
        font-weight: 700;
    }
    .dcn-sign-head div {
        padding: 0 6px;
    }
    .dcn-sign-values {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        column-gap: 26px;
        margin-top: 42px;
    }
    .dcn-sign-values .line {
        border-top: 2px solid #2d2d2d;
        text-align: center;
        padding-top: 3px;
        font-weight: 800;
        text-transform: uppercase;
    }
    .dcn-overflow-note {
        font-style: italic;
        font-weight: 700;
        text-transform: none;
    }

    @media print {
        @page {
            size: Letter portrait;
            margin: 0.35in;
        }
        .no-print,
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
            background: #fff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .dcn-paper-wrap {
            border: 0 !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            max-width: none !important;
            margin: 0 auto !important;
            padding: 0 !important;
        }
        .dcn-paper {
            width: 7.58in !important;
            max-width: 7.58in !important;
            margin: 0 auto !important;
            padding: 0.28in 0 0 !important;
            font-size: 11px !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .dcn-section-nonreceipted {
            text-decoration: underline !important;
            text-underline-offset: 1px;
        }
        .dcn-filler td {
            height: 4.25in !important;
        }
        body.dcn-template-print-active .dcn-brand,
        body.dcn-template-print-active .dcn-note-title {
            display: none !important;
        }
        body.dcn-template-print-active .dcn-sign-head,
        body.dcn-template-print-active .dcn-label,
        body.dcn-template-print-active .dcn-colon,
        body.dcn-template-print-active .dcn-ledger th {
            visibility: hidden !important;
        }
        body.dcn-template-print-active .dcn-rule,
        body.dcn-template-print-active .dcn-ledger,
        body.dcn-template-print-active .dcn-ledger th,
        body.dcn-template-print-active .dcn-ledger td,
        body.dcn-template-print-active .dcn-footer-line,
        body.dcn-template-print-active .dcn-footer-line > div,
        body.dcn-template-print-active .dcn-words,
        body.dcn-template-print-active .dcn-sign-values .line {
            border-color: transparent !important;
        }
        body.dcn-template-print-active .dcn-paper {
            position: relative !important;
            width: 7.8in !important;
            max-width: 7.8in !important;
            height: 10.3in !important;
            min-height: auto !important;
            padding: 1.45in 0.06in 0.06in !important;
            /* Top-only adjustment knob: change this when aligning header fields. */
            --dcn-top-adjust: 1.7rem !important;
            font-size: 10.8px !important;
            line-height: 1.2 !important;
            letter-spacing: 0 !important;
        }
        body.dcn-template-print-active .dcn-meta-grid,
        body.dcn-template-print-active .dcn-meta-bottom {
            font-size: 12px !important;
            line-height: 1.05 !important;
            position: absolute !important;
            left: 0.22in !important;
            right: 0.08in !important;
            transform: none !important;
        }
        body.dcn-template-print-active .dcn-meta-grid {
            top: calc(1.86in - 2rem + var(--dcn-top-adjust)) !important;
            margin-bottom: 0 !important;
        }
        body.dcn-template-print-active .dcn-meta-bottom {
            top: calc(2.43in - 1rem + var(--dcn-top-adjust)) !important;
            margin-bottom: 0 !important;
        }
        body.dcn-template-print-active .dcn-meta-grid .dcn-meta-left {
            transform: translateX(-0.16in) !important;
        }
        body.dcn-template-print-active .dcn-meta-grid .dcn-meta-right,
        body.dcn-template-print-active .dcn-meta-bottom .dcn-meta-left,
        body.dcn-template-print-active .dcn-meta-bottom .dcn-meta-right {
            transform: translate(0, 0) !important;
        }
        body.dcn-template-print-active .dcn-bus-style-value {
            font-size: 10px !important;
            line-height: 1 !important;
            white-space: normal !important;
            overflow-wrap: anywhere !important;
        }
        body.dcn-template-print-active .dcn-desc-cell {
            font-size: 10px !important;
            line-height: 1.05 !important;
        }
        body.dcn-template-print-active .dcn-ledger {
            position: absolute !important;
            left: 0.06in !important;
            right: 0.06in !important;
            /* LOCKED: description/particulars placement */
            top: calc(1.45in + 0.44in + 7.5rem) !important;
            margin-top: 0 !important;
            height: auto !important;
            overflow: hidden !important;
        }
        body.dcn-template-print-active .dcn-ledger tbody tr {
            height: auto !important;
        }
        body.dcn-template-print-active .dcn-ledger td {
            padding: 0.5px 6px !important;
            line-height: 1.05 !important;
            font-size: 11.5px !important;
            font-weight: 700 !important;
        }
        body.dcn-template-print-active .dcn-line-item,
        body.dcn-template-print-active .dcn-section-line,
        body.dcn-template-print-active .dcn-section-nonreceipted {
            line-height: 1.05 !important;
        }
        body.dcn-template-print-active .dcn-filler td {
            height: auto !important;
        }
        body.dcn-template-print-active .dcn-total-block {
            position: absolute !important;
            left: 0.06in !important;
            right: 0.06in !important;
            /* LOCKED: total amount + amount in words placement */
            bottom: calc(0.34in + 6.2rem) !important;
            margin: 0 !important;
        }
        body.dcn-template-print-active .dcn-footer-line {
            grid-template-columns: 58% 21% 21% !important;
            border-top: 0 !important;
        }
        body.dcn-template-print-active .dcn-footer-line > div {
            padding: 0 !important;
            line-height: 1.1 !important;
            font-size: 13px !important;
            font-weight: 800 !important;
        }
        body.dcn-template-print-active .dcn-footer-line > div:first-child,
        body.dcn-template-print-active .dcn-words-label {
            visibility: hidden !important;
        }
        body.dcn-template-print-active .dcn-words {
            padding: 0 !important;
            margin-top: 0 !important;
            transform: translateY(12px) !important;
            text-align: center !important;
            font-size: 13px !important;
            font-weight: 800 !important;
            line-height: 1.1 !important;
            white-space: nowrap !important;
        }
        body.dcn-template-print-active .dcn-sign-block {
            position: absolute !important;
            left: 0.06in !important;
            right: 0.06in !important;
            /* LOCKED: signature placement */
            bottom: calc(0.06in + 2rem) !important;
            margin: 0 !important;
        }
        body.dcn-template-print-active .dcn-sign-head {
            display: none !important;
        }
        body.dcn-template-print-active .dcn-sign-values {
            margin-top: 0 !important;
        }
        body.dcn-template-print-active .dcn-sign-values .line {
            padding-top: 0 !important;
            font-size: 12px !important;
            font-weight: 800 !important;
        }
    }
</style>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 no-print">
    <div>
        <h2 class="mb-1">Debit / Credit Note</h2>
        <p class="text-muted mb-0">Printable document.</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="{{ route('billing.notes.documents') }}">Back</a>
        <a class="btn btn-outline-warning" href="{{ route('billing.notes.edit', $note) }}">Edit</a>
        <button class="btn btn-primary" type="button" data-print-mode="plain">Print Clear Paper</button>
        <button class="btn btn-outline-primary" type="button" data-print-mode="template">Print With Template</button>
    </div>
</div>

@if(session('status') === 'debit-credit-note-updated')
    <div class="alert alert-success no-print">Debit/Credit note updated successfully.</div>
@endif
@if(session('status') === 'billing-attachments-uploaded')
    <div class="alert alert-success no-print">Scanned document uploaded successfully.</div>
@endif

@include('partials.scanner-upload', [
    'scannerId' => 'debitCreditNoteScanner' . $note->id,
    'modalTitle' => 'Scan Document to Debit/Credit Note #' . ($note->note_no ?? $note->id),
    'description' => 'Choose a connected scanner, scan the document, and APM will save it directly to this Debit/Credit Note.',
    'uploadUrl' => route('billing.notes.attachments.store', $note),
    'documentLabel' => 'debit-credit-note-' . ($note->note_no ?? $note->id),
])

@if($note->attachments->count())
    <div class="alert alert-light border no-print">
        <div class="fw-semibold mb-1">Attachments stored for this Debit/Credit Note</div>
        <div class="d-flex flex-wrap gap-2">
            @foreach($note->attachments as $attachment)
                <a class="badge text-bg-light border text-decoration-none" href="{{ \Illuminate\Support\Facades\Storage::url($attachment->path) }}" target="_blank" rel="noopener">
                    {{ $attachment->filename }}
                </a>
            @endforeach
        </div>
    </div>
@endif

<div class="dcn-paper-wrap p-4 p-md-5">
    <div class="dcn-paper">
        <div class="dcn-brand">
            <div class="dcn-brand-title">APM Customs Brokerage</div>
            <div class="dcn-brand-line">Lot 7F 2&3 Rodriguez Compound, Aurenina Village, San Dionisio, 1700 City of Paranaque,</div>
            <div class="dcn-brand-line">NCR, Fourth District, Philippines * ANNABELLA P. MANIEGO - Prop.</div>
            <div class="dcn-brand-line">Tel Nos.: (02) 8-682-6845 * (02) 8-696-7798</div>
            <div class="dcn-brand-line">VAT Reg. TIN: 120-291-938-00000</div>
        </div>

        <div class="dcn-note-title">Debit / Credit Note</div>

        <div class="dcn-meta-grid">
            <table class="dcn-meta-left">
                <tr>
                    <td class="dcn-label">Bill To</td>
                    <td class="dcn-colon">:</td>
                    <td>{{ $data['bill_to'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="dcn-label">Address</td>
                    <td class="dcn-colon">:</td>
                    <td>{{ $data['bill_address'] ?? '-' }}</td>
                </tr>
            </table>
            <table class="dcn-meta-right">
                <tr>
                    <td class="dcn-label">Date</td>
                    <td class="dcn-colon">:</td>
                    <td>{{ strtoupper($noteDate) }}</td>
                </tr>
                <tr>
                    <td class="dcn-label">TIN</td>
                    <td class="dcn-colon">:</td>
                    <td>{{ $data['bill_tin'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="dcn-label">Bus. Style</td>
                    <td class="dcn-colon">:</td>
                    <td class="dcn-bus-style-value">{{ $data['bill_business_style'] ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="dcn-rule"></div>

        <div class="dcn-meta-bottom">
            <table class="dcn-meta-left">
                <tr>
                    <td class="dcn-label">Vessel/Voy.</td>
                    <td class="dcn-colon">:</td>
                    <td>{{ $data['vessel_voyage'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="dcn-label">B/L No.</td>
                    <td class="dcn-colon">:</td>
                    <td>{{ $data['bl_no'] ?? '-' }}</td>
                </tr>
            </table>
            <table class="dcn-meta-right">
                <tr>
                    <td class="dcn-label">Vol./Meas.</td>
                    <td class="dcn-colon">:</td>
                    <td>{{ $data['vol_meas'] ?? '-' }}{{ !empty($data['vol_meas_unit']) ? ' ' . strtoupper($data['vol_meas_unit']) : '' }}</td>
                </tr>
                <tr>
                    <td class="dcn-label">Job Ref. No.</td>
                    <td class="dcn-colon">:</td>
                    <td>{{ $data['job_ref_no'] ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <table class="dcn-ledger">
            <thead>
                <tr>
                    <th style="width:58%;">Particulars</th>
                    <th style="width:21%;" class="text-center">Debit</th>
                    <th style="width:21%;" class="text-center">Credit</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="dcn-desc-cell">
                        <div>DESCRIPTION : {{ $data['description'] ?? '-' }}</div>
                        <div>SHIPPER'S NAME : {{ $data['shipper_name'] ?? '-' }}</div>
                        <div class="dcn-desc-bottom">
                            <div>INVOICE NO. : {{ $data['invoice_no'] ?? '-' }}</div>
                            <div class="dcn-desc-right">
                                <div>PORT : {{ $data['port'] ?? '-' }}</div>
                                <div>CTNR. NO : {{ $data['container_no'] ?? ($note->jobOrder?->no_of_container ?? '-') }}</div>
                            </div>
                        </div>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                @foreach($printRows as $row)
                    @php
                        $particular = trim((string) ($row['particular'] ?? '-'));
                        $side = strtolower((string) ($row['side'] ?? 'debit'));
                        $amount = (float) ($row['amount'] ?? 0);
                        $isSection = preg_match('/^a\\.?\\s*(?:receipted\\s*\\/\\s*)?reimburse?able(?:\\s+(?:voucher|charges))?$/i', $particular) === 1;
                        $isNonReceiptedSection = preg_match('/^b\\.?\\s*non\\s*-?\\s*receipted\\s*charges$/i', $particular) === 1;
                    @endphp
                    <tr>
                        <td class="{{ $isSection ? 'dcn-section-line' : ($isNonReceiptedSection ? 'dcn-section-nonreceipted' : 'dcn-line-item') }}">{{ $particular }}</td>
                        <td class="text-end">{{ ($isSection || $isNonReceiptedSection) ? '' : ($side === 'debit' ? $fmt($amount) : '') }}</td>
                        <td class="text-end">{{ ($isSection || $isNonReceiptedSection) ? '' : ($side === 'credit' ? $fmt($amount) : '') }}</td>
                    </tr>
                @endforeach
                @if($hiddenRowCount > 0)
                    <tr>
                        <td class="dcn-overflow-note">... {{ $hiddenRowCount }} more line(s) not shown in print to keep one-page layout</td>
                        <td></td>
                        <td></td>
                    </tr>
                @endif
                <tr class="dcn-filler">
                    <td></td><td></td><td></td>
                </tr>
            </tbody>
        </table>

        <div class="dcn-total-block">
            @if($noteAdvanceDeduction > 0)
                <div class="dcn-footer-line">
                    <div>SUBTOTAL</div>
                    <div>{{ $fmt($baseNoteTotal) }}</div>
                    <div>{{ $fmt(0) }}</div>
                </div>
                <div class="dcn-footer-line">
                    <div>LESS: ADVANCES OF PHP {{ $fmt($noteAdvanceTotal) }}</div>
                    <div></div>
                    <div>{{ $fmt($noteAdvanceDeduction) }}</div>
                </div>
                <div class="dcn-footer-line">
                    <div>TOTAL AMOUNT</div>
                    <div>{{ $fmt($adjustedNoteTotal) }}</div>
                    <div>{{ $fmt(0) }}</div>
                </div>
            @else
                <div class="dcn-footer-line">
                    <div>TOTAL AMOUNT</div>
                    <div>{{ $fmt($totalDebit) }}</div>
                    <div>{{ $fmt($totalCredit) }}</div>
                </div>
            @endif

            <div class="dcn-words"><span class="dcn-words-label">AMOUNT IN WORDS:</span> <span class="dcn-words-value">{{ strtoupper($adjustedAmountInWords ?? $data['amount_in_words'] ?? '-') }}</span></div>
        </div>

        <div class="dcn-sign-block">
            <div class="dcn-sign-head">
                <div>Prepared by:</div>
                <div>Approved by:</div>
                <div>Received by:</div>
            </div>

            <div class="dcn-sign-values">
                <div class="line">A.D.E</div>
                <div class="line">A.P.M</div>
                <div class="line">-</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const body = document.body;
        const plainButton = document.querySelector('[data-print-mode="plain"]');
        const templateButton = document.querySelector('[data-print-mode="template"]');

        plainButton?.addEventListener('click', () => {
            body.classList.remove('dcn-template-print-active');
            window.print();
        });

        templateButton?.addEventListener('click', () => {
            body.classList.add('dcn-template-print-active');
            window.print();
            setTimeout(() => body.classList.remove('dcn-template-print-active'), 500);
        });
    })();
</script>
@endpush
