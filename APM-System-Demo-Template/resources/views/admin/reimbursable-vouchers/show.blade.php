@extends('layouts.employeehub')

@section('content')
    <style>
        .rv-paper-wrap {
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            width: 100%;
            max-width: 8.5in;
            margin: 0 auto;
        }
        .rv-paper {
            padding: 20px 20px 18px;
            font-family: "Courier New", Courier, monospace;
            color: #111827;
            font-size: 15px;
            line-height: 1.3;
            min-height: 10.85in;
        }
        .rv-paper table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .rv-paper th, .rv-paper td { border: 1px solid #222; padding: 7px 10px; vertical-align: top; }
        .rv-paper .center { text-align: center; }
        .rv-paper .right { text-align: right; }
        .rv-paper .rv-jo-cell {
            white-space: nowrap;
        }
        .rv-paper .title { font-size: 30px; font-weight: 700; letter-spacing: 0.04em; text-align: center; }
        .rv-paper .subtitle { font-size: 18px; font-weight: 700; letter-spacing: 0.08em; text-align: center; }
        .rv-paper .line-cell { height: 34px; }
        .rv-paper .rv-line-cell {
            white-space: normal;
            overflow: visible;
            text-overflow: clip;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .rv-paper .rv-header-cell {
            font-size: 14px;
            line-height: 1.15;
            white-space: nowrap;
        }
        .rv-paper .rv-data-row td {
            min-height: 34px;
        }
        .rv-paper .rv-head-row td { border: 0; }
        .rv-paper .rv-meta-cell {
            border-left: 0;
            border-right: 0;
            padding: 0;
        }
        .rv-paper .rv-meta-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 10px 14px;
            border-top: 1px solid #222;
        }
        .rv-paper .rv-meta-box.rv-meta-box-last {
            border-bottom: 1px solid #222;
        }
        .rv-paper .rv-meta-box.rv-meta-box-split {
            align-items: flex-start;
        }
        .rv-paper .rv-meta-group {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1 1 0;
        }
        .rv-paper .rv-meta-label {
            font-weight: 700;
            letter-spacing: 0.04em;
            white-space: nowrap;
        }
        .rv-paper .rv-meta-value {
            flex: 1 1 auto;
            text-align: left;
        }
        .rv-paper .rv-meta-value.right {
            text-align: right;
        }
        .rv-paper .rv-footer-row td { border-left: 0; border-right: 0; }
        .rv-paper .rv-sign-row td, .rv-paper .rv-sign-value-row td { border-top: 0; }
        .rv-paper .rv-sign-row td { border-bottom: 0; }
        .rv-paper .rv-footer-row-last td { border-left: 0; border-right: 0; border-bottom: 0; }
        .rv-paper .rv-words-label,
        .rv-paper .rv-sign-label {
            font-weight: 700;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }
        .rv-paper .rv-words-label {
            text-align: left;
        }
        .rv-paper .rv-words-value {
            padding-left: 10px;
        }
        .rv-paper .rv-sign-row td,
        .rv-paper .rv-sign-value-row td {
            padding-top: 8px;
            padding-bottom: 8px;
        }
        .rv-paper .rv-sign-value-row td {
            min-height: 28px;
        }
        .rv-paper .rv-no-label, .rv-paper .rv-no-value { color: #c62828; font-weight: 700; }
        .rv-paper .rv-repeat-note {
            display: block;
            margin-top: 2px;
            color: #64748b;
            font-size: 0.78em;
            line-height: 1.1;
        }
        .rv-paper .rv-group-lines {
            display: block;
        }
        .rv-paper .rv-group-line {
            display: inline;
            padding: 0;
            border-bottom: 0;
            line-height: 1.35;
        }
        .rv-paper .rv-description-cell .rv-group-lines {
            font-size: 0.9em;
            line-height: 1.22;
            letter-spacing: 0;
            white-space: normal;
            overflow-wrap: break-word;
            word-break: normal;
            text-transform: none;
        }
        .rv-paper .rv-amount-line {
            text-align: right;
            white-space: nowrap;
        }
        .rv-paper .rv-group-total {
            display: flex;
            align-items: flex-start;
            justify-content: flex-end;
            min-height: 100%;
            text-align: right;
            white-space: nowrap;
            font-weight: 700;
            line-height: 1.3;
        }
        @media print {
            @page { size: Letter portrait; margin: 0.15in; }
            html, body { background: #fff !important; }
            .eh-navbar, .eh-sidebar, .eh-sidebar-col, #sidebarOffcanvas, .chatbot-fab, .chatbot-window { display: none !important; }
            .container-fluid, .container-fluid .row, .eh-main-col {
                width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; flex: 0 0 100% !important;
            }
            .no-print { display: none !important; }
            .rv-paper-wrap {
                box-shadow: none;
                border: 0;
                border-radius: 0;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
            .rv-paper {
                padding: 0.08in 0.1in 0.06in;
                font-size: 11.5px;
                line-height: 1.18;
                min-height: 10.95in;
                margin: 0;
            }
            .rv-paper .title { font-size: 24px; }
            .rv-paper .subtitle { font-size: 16px; }
            .rv-paper th, .rv-paper td { padding: 4px 6px; }
            .rv-paper .line-cell { height: 22px; }
            .rv-paper .rv-header-cell { font-size: 11px; }
            .rv-paper .rv-data-row td { min-height: 22px; }
            .rv-paper .rv-meta-box {
                padding: 7px 10px;
            }
            .rv-paper .rv-no-label,
            .rv-paper .rv-no-value {
                color: #c62828 !important;
                -webkit-text-fill-color: #c62828 !important;
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
            }
            .rv-paper .rv-repeat-note {
                color: #64748b !important;
            }
            .rv-paper .rv-description-cell .rv-group-lines {
                font-size: 0.88em !important;
                line-height: 1.16 !important;
            }
        }
    </style>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 no-print">
        <div>
            <h2 class="mb-1">Reimbursable Voucher #{{ $voucher->cancelled_voucher_no ?: $voucher->voucher_no }}</h2>
            <p class="text-muted mb-0">Saved voucher view. You can print this now.</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('accounting.reimbursable-vouchers.index') }}">Back to Storage</a>
            <a class="btn btn-outline-primary" href="{{ route('accounting.reimbursable-vouchers.create') }}">Create New</a>
            @if(($voucher->status ?? 'active') !== 'cancelled')
                <a class="btn btn-outline-warning" href="{{ route('accounting.reimbursable-vouchers.edit', $voucher) }}">Edit</a>
                <form method="POST" action="{{ route('accounting.reimbursable-vouchers.cancel', $voucher) }}" onsubmit="return confirm('Cancel this voucher? This will free the voucher number for reuse.');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-danger">Cancel Voucher</button>
                </form>
            @endif
            <button class="btn btn-primary" type="button" onclick="window.print()">Print</button>
        </div>
    </div>

    @if(session('status') === 'reimbursable-voucher-saved')
        <div class="alert alert-success no-print">Voucher saved. Please print this copy now.</div>
    @endif
    @if(session('status') === 'reimbursable-voucher-updated')
        <div class="alert alert-success no-print">Voucher updated successfully. Please review before printing.</div>
    @endif
    @if(session('status') === 'reimbursable-voucher-cancelled-readonly')
        <div class="alert alert-warning no-print">This voucher is cancelled and now read-only.</div>
    @endif
    @if(session('status') === 'reimbursable-voucher-already-cancelled')
        <div class="alert alert-warning no-print">This voucher is already cancelled.</div>
    @endif
    @if(($voucher->status ?? 'active') === 'cancelled')
        <div class="alert alert-danger no-print mb-3">
            Cancelled voucher.
            Original voucher number:
            <strong>{{ $voucher->cancelled_voucher_no }}</strong>
            @if($voucher->cancelled_at)
                on {{ $voucher->cancelled_at->format('M d, Y h:i A') }}
            @endif
            @if($voucher->canceller?->name)
                by {{ $voucher->canceller->name }}.
            @endif
            This number can be used again for a new voucher.
        </div>
    @endif

    <div class="rv-paper-wrap">
        <div class="rv-paper">
            <table>
                <colgroup>
                    <col style="width: 14%;">
                    <col style="width: 14%;">
                    <col style="width: 12%;">
                    <col style="width: 18%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                    <col style="width: 12%;">
                </colgroup>
                <tr class="rv-head-row">
                    <td colspan="8" class="title">APM CUSTOMS BROKERAGE</td>
                </tr>
                <tr class="rv-head-row">
                    <td colspan="8" class="subtitle">REIMBURSABLE VOUCHER</td>
                </tr>
                <tr>
                    <td colspan="8" class="rv-meta-cell">
                        <div class="rv-meta-box">
                            <div class="rv-meta-label">Voucher No.</div>
                            <div class="rv-meta-value right rv-no-value">{{ $voucher->voucher_no }}</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" class="rv-meta-cell">
                        <div class="rv-meta-box rv-meta-box-split">
                            <div class="rv-meta-group">
                                <div class="rv-meta-label">Payee</div>
                                <div class="rv-meta-value">{{ $voucher->payee ?? '' }}</div>
                            </div>
                            <div class="rv-meta-group">
                                <div class="rv-meta-label">Ref. No.</div>
                                <div class="rv-meta-value">{{ $voucher->ref_no ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" class="rv-meta-cell">
                        <div class="rv-meta-box rv-meta-box-last">
                            <div class="rv-meta-label">Date</div>
                            <div class="rv-meta-value">{{ optional($voucher->voucher_date)->format('m/d/Y') }}</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="center rv-header-cell">JO NO.</th>
                    <th class="center rv-header-cell">CLIENT</th>
                    <th class="center rv-header-cell">REQUESTED BY</th>
                    <th class="center rv-header-cell">DESCRIPTION</th>
                    <th class="center rv-header-cell">AMOUNT</th>
                    <th class="center rv-header-cell">LIQ. NO.</th>
                    <th class="center rv-header-cell">RV NO/CV NO.</th>
                    <th class="center rv-header-cell">REMARKS</th>
                </tr>
                @php
                    $items = $voucher->items;
                    $groupedRows = [];
                    $carryJoNo = null;
                    $carryClient = null;
                    $carryLiqNo = null;
                    $carryPayee = null;
                    $carryRvCvNo = null;

                    foreach ($items as $item) {
                        $rawJoNo = trim((string) ($item?->jo_no ?? ''));
                        $rawClient = trim((string) ($item?->client_name ?? ''));
                        $rawLiqNo = trim((string) ($item?->liq_no ?? ''));
                        $rawPayee = trim((string) ($item?->payee ?? ''));
                        $rawRvCvNo = trim((string) ($item?->rv_cv_no ?? ''));
                        $rawRemarks = trim((string) ($item?->remarks ?? ''));
                        if ($rawRvCvNo === '') {
                            $rawRvCvNo = trim((string) ($voucher->voucher_no ?? ''));
                        }

                        if ($rawJoNo !== '') {
                            $carryJoNo = $rawJoNo;
                        }
                        if ($rawClient !== '') {
                            $carryClient = $rawClient;
                        }
                        if ($rawLiqNo !== '') {
                            $carryLiqNo = $rawLiqNo;
                        }
                        if ($rawPayee !== '') {
                            $carryPayee = $rawPayee;
                        }
                        if ($rawRvCvNo !== '') {
                            $carryRvCvNo = $rawRvCvNo;
                        }

                        $displayJoNo = $rawJoNo !== '' ? $rawJoNo : $carryJoNo;
                        $displayClient = $rawClient !== '' ? $rawClient : $carryClient;
                        $displayLiqNo = $rawLiqNo !== '' ? $rawLiqNo : $carryLiqNo;
                        $displayPayee = $rawPayee !== '' ? $rawPayee : $carryPayee;
                        $displayRvCvNo = $rawRvCvNo !== '' ? $rawRvCvNo : $carryRvCvNo;

                        $groupKey = implode('|', [
                            $displayJoNo,
                            $displayClient,
                            $displayPayee,
                            $displayLiqNo,
                            $displayRvCvNo,
                        ]);

                        if (!isset($groupedRows[$groupKey])) {
                            $groupedRows[$groupKey] = [
                                'jo_no' => $displayJoNo,
                                'client' => $displayClient,
                                'payee' => $displayPayee,
                                'liq_no' => $displayLiqNo,
                                'rv_cv_no' => $displayRvCvNo,
                                'descriptions' => [],
                                'remarks' => [],
                                'total_amount' => 0,
                            ];
                        }

                        $groupedRows[$groupKey]['descriptions'][] = $item?->description;
                        if ($rawRemarks !== '') {
                            $groupedRows[$groupKey]['remarks'][] = $rawRemarks;
                        }
                        $deductionType = strtolower(trim((string) ($item?->deduction_type ?? 'none')));
                        $descriptionUpper = strtoupper(trim((string) ($item?->description ?? '')));
                        if ($deductionType === 'none') {
                            if (str_contains($descriptionUpper, 'LESS') && str_contains($descriptionUpper, 'ADVANCE')) {
                                $deductionType = 'advance';
                            } elseif (str_contains($descriptionUpper, 'LESS') && str_contains($descriptionUpper, 'PENALTY')) {
                                $deductionType = 'penalty';
                            }
                        }
                        $lineAmount = (float) ($item?->amount ?? 0);
                        $signedAmount = $deductionType === 'penalty' ? -abs($lineAmount) : $lineAmount;
                        $groupedRows[$groupKey]['total_amount'] += $signedAmount;
                    }

                    $groupedRows = array_values($groupedRows);
                    $rowsToRender = count($groupedRows);
                @endphp
                @for($i = 0; $i < $rowsToRender; $i++)
                    @php $row = $groupedRows[$i] ?? null; @endphp
                    <tr class="rv-data-row">
                        <td class="rv-line-cell rv-jo-cell">
                            {{ str_replace(' - ', '-', (string) ($row['jo_no'] ?? '')) }}
                        </td>
                        <td class="rv-line-cell">
                            {{ $row['client'] ?? '' }}
                        </td>
                        <td class="rv-line-cell">{{ $row['payee'] ?? '' }}</td>
                        <td class="rv-line-cell rv-description-cell">
                            @if($row)
                                <div class="rv-group-lines">
                                    {{ implode(', ', array_filter($row['descriptions'])) }}
                                </div>
                            @endif
                        </td>
                        <td class="right rv-line-cell">
                            @if($row)
                                @php $rowTotal = (float) ($row['total_amount'] ?? 0); @endphp
                                <div class="rv-group-total">{{ $rowTotal < 0 ? '(' . number_format(abs($rowTotal), 2) . ')' : number_format($rowTotal, 2) }}</div>
                            @endif
                        </td>
                        <td class="rv-line-cell">{{ $row['liq_no'] ?? '' }}</td>
                        <td class="rv-line-cell">{{ $row['rv_cv_no'] ?? '' }}</td>
                        <td class="rv-line-cell">{{ implode(', ', array_unique(array_filter($row['remarks'] ?? []))) }}</td>
                    </tr>
                @endfor
                <tr>
                    <td colspan="4"><strong>TOTAL</strong></td>
                    @php $voucherTotal = (float) $voucher->total_amount; @endphp
                    <td class="right"><strong>{{ $voucherTotal < 0 ? '(' . number_format(abs($voucherTotal), 2) . ')' : number_format($voucherTotal, 2) }}</strong></td>
                    <td colspan="3"></td>
                </tr>
                <tr class="rv-footer-row">
                    <td colspan="2" class="rv-words-label">AMOUNTING IN WORDS</td>
                    <td colspan="6" class="rv-words-value">{{ $voucher->amount_in_words }}</td>
                </tr>
                <tr class="rv-footer-row rv-sign-row">
                    <td colspan="2" class="rv-sign-label">PREPARED BY</td>
                    <td colspan="2" class="rv-sign-label center">APPROVED BY</td>
                    <td colspan="4" class="rv-sign-label center">RECEIVED PAYMENT:</td>
                </tr>
                <tr class="rv-footer-row rv-sign-value-row rv-footer-row-last">
                    <td colspan="2" class="center">{{ $voucher->prepared_by }}</td>
                    <td colspan="2" class="center">{{ $voucher->approved_by }}</td>
                    <td colspan="4" class="center">{{ $voucher->received_payment }}</td>
                </tr>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
@if(session('status') === 'reimbursable-voucher-saved')
<script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>
@endif
@endpush
