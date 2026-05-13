@extends('layouts.employeehub')

@section('content')
<style>
    :root {
        --lf-print-page-margin: 0.28in;
        --lf-print-side-margin: 0.22in;
        --lf-print-top-offset: 0.16in;
    }

    .lf-paper-wrap {
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
        width: 8.5in;
        max-width: 100%;
        margin: 0 auto 1rem;
        padding: 10px;
    }
    .lf-paper {
        width: 100%;
        min-height: 10.8in;
        font-family: "Arial Narrow", Arial, sans-serif;
        font-weight: 600;
        color: #1f2937;
        font-size: 13px;
        display: flex;
        flex-direction: column;
    }
    .lf-title {
        text-align: center;
        font-size: 28px;
        line-height: 1;
        margin-bottom: 3px;
        font-family: "Cooper Black", "Arial Black", sans-serif;
    }
    .lf-subtitle {
        text-align: center;
        font-size: 13px;
        letter-spacing: 0.02em;
        margin-bottom: 8px;
    }
    .lf-meta {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
    }
    .lf-meta td {
        border: 0;
        padding: 1px 0;
        vertical-align: top;
    }
    .lf-grid {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        flex: 1 1 auto;
    }
    .lf-grid th,
    .lf-grid td {
        border: 1.4px solid #222;
        padding: 3px 6px;
    }
    .lf-grid th {
        text-align: center;
        font-size: 11px;
        letter-spacing: 0.02em;
    }
    .lf-amount {
        text-align: right;
        min-width: 110px;
        font-variant-numeric: tabular-nums;
    }
    .lf-meta-label {
        font-weight: 700;
        display: inline-block;
        min-width: 96px;
    }
    .lf-meta-row {
        line-height: 1.35;
        margin: 0;
    }
    .lf-meta-right {
        word-break: break-word;
    }

    @media print {
        @page {
            size: Letter portrait;
            margin: var(--lf-print-page-margin);
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
        .lf-paper-wrap {
            border: 0 !important;
            box-shadow: none !important;
            margin: 0 auto !important;
            padding: 0 !important;
        }
        .lf-paper {
            min-height: calc(11in - (var(--lf-print-page-margin) * 2) - var(--lf-print-top-offset)) !important;
            height: calc(11in - (var(--lf-print-page-margin) * 2) - var(--lf-print-top-offset)) !important;
            font-size: 11px !important;
            width: calc(100% - (var(--lf-print-side-margin) * 2)) !important;
            margin: 0 var(--lf-print-side-margin) !important;
            margin-top: var(--lf-print-top-offset) !important;
        }
        .lf-title {
            font-size: 24px !important;
        }
        .lf-subtitle {
            font-size: 11.5px !important;
        }
    }
</style>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 no-print">
    <div>
        <h2 class="mb-1">Liquidation Form #{{ $form->liq_no ?? $form->form_no ?? ('LF-' . $form->id) }}</h2>
        <p class="text-muted mb-0">Reimbursement of Expenses / Liquidation of Cash Advances</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('cash-advances.liquidation-form') }}">Back</a>
        <button class="btn btn-primary" type="button" onclick="window.print()">Print</button>
    </div>
</div>

<div class="lf-paper-wrap">
    <div class="lf-paper">
        <div class="lf-title">APM CUSTOMS BROKERAGE</div>
        <div class="lf-subtitle">REIMBURSEMENT OF EXPENSES / LIQUIDATION OF CASH ADVANCES</div>

        <table class="lf-meta">
            <tr>
                <td style="width: 58%;">
                    <div class="lf-meta-row"><span class="lf-meta-label">NAME</span> : {{ strtoupper($form->user?->name ?? '-') }}</div>
                    <div class="lf-meta-row"><span class="lf-meta-label">JOB ORDER</span> : {{ strtoupper($form->jo_number ?? '-') }}</div>
                </td>
                <td style="width: 42%;" class="lf-meta-right">
                    <div class="lf-meta-row"><span class="lf-meta-label">DATE</span> : {{ optional($form->date)->format('F d, Y') }}</div>
                    <div class="lf-meta-row"><span class="lf-meta-label">CLIENT NAME</span> : {{ strtoupper($form->client_name ?? '-') }}</div>
                </td>
            </tr>
        </table>

        @php
            $lineItems = collect($form->line_items ?? [])
                ->map(function ($row) {
                    return [
                        'description' => strtoupper(trim((string) ($row['description'] ?? ''))),
                        'amount' => (float) ($row['amount'] ?? 0),
                        'reference' => trim((string) ($row['reference'] ?? '')),
                    ];
                })
                ->filter(fn ($row) => $row['description'] !== '' && $row['amount'] > 0)
                ->values();

            $total = (float) $form->amount;
            $targetVisibleRows = 14;
            $blankRows = max($targetVisibleRows - $lineItems->count(), 0);
        @endphp

        <table class="lf-grid">
            <thead>
                <tr>
                    <th style="width: 49%;">DESCRIPTION</th>
                    <th style="width: 16%;">AMOUNT</th>
                    <th style="width: 35%;">REMARKS / REFERENCE NO.</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lineItems as $row)
                    <tr>
                        <td>{{ $row['description'] }}</td>
                        <td class="lf-amount">{{ number_format((float) $row['amount'], 2) }}</td>
                        <td>{{ $row['reference'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">No liquidation entries with amount.</td>
                    </tr>
                @endforelse

                @for($i = 0; $i < $blankRows; $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td class="lf-amount"></td>
                        <td></td>
                    </tr>
                @endfor

                <tr>
                    <td><strong>TOTAL</strong></td>
                    <td class="lf-amount"><strong>{{ number_format($total, 2) }}</strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="d-flex justify-content-between">
                            <span><strong>Approved By:</strong> ____________________</span>
                            <span><strong>Prepared By:</strong> ____________________</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
