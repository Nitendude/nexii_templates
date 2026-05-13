@extends('layouts.employeehub')

@section('content')
<style>
    .billing-paper-wrap {
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        max-width: 8in;
        margin: 0 auto 1.25rem;
    }
    .billing-paper {
        font-family: "Trebuchet MS", Tahoma, sans-serif;
        color: #1f2937;
        letter-spacing: 0.01em;
        padding: 0.15in;
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
    @media print {
        @page {
            size: Letter portrait;
            margin: 0.45in;
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
            width: 7.55in !important;
            max-width: 7.55in !important;
            margin: 0 auto !important;
            padding: 0 !important;
            font-size: 12.5px !important;
            line-height: 1.3 !important;
            letter-spacing: 0 !important;
        }
        .billing-paper-wrap {
            background: #ffffff !important;
            box-shadow: none;
            border: 0 !important;
            border-radius: 0;
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
            width: 13px !important;
            height: 13px !important;
            line-height: 11px !important;
            font-size: 10px !important;
        }
        .si-grid th,
        .si-grid td {
            padding: 4px 6px !important;
        }
    }
</style>

@php
    $data = $statement->data ?? [];
    $isService = ($statement->document_type ?? ($data['document_type'] ?? 'billing_statement')) === 'service_invoice';
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

    $grandTotal = is_numeric($data['grand_total'] ?? null) ? (float) $data['grand_total'] : ($nonTotal + $recTotal);
    $statementDate = !empty($data['statement_date'])
        ? \Carbon\Carbon::parse($data['statement_date'])->format('F d, Y')
        : optional($statement->created_at)->format('F d, Y');
    $isConverge = str_contains(strtoupper(($data['bill_to'] ?? '') . ' ' . ($data['bill_business_style'] ?? '')), 'CONVERGE');
@endphp

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 no-print">
        <div>
            <h2 class="mb-1">{{ $isService ? 'Service Invoice' : 'Billing Statement' }} #{{ $statement->statement_no }}</h2>
            <p class="text-muted mb-0">Formatted output based on your document template.</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ $isService ? route('billing.service-invoices.documents') : route('billing.documents') }}">Back to Documents</a>
            <a class="btn btn-outline-primary" href="{{ $isService ? route('billing.service-invoices') : route('billing') }}">Back</a>
            <button class="btn btn-primary" type="button" onclick="window.print()">Print</button>
        </div>
    </div>

    <div class="billing-paper-wrap p-4 p-md-5">
        <div class="billing-paper">
            <div class="text-center mb-2">
                <div class="fw-bold text-uppercase fs-4 apm-brand-font">APM Customs Brokerage</div>
                <div class="small fw-semibold">Lot 7F 2&3 Rodriguez Compound, Aurenina Village, San Dionisio, 1700 City of Paranaque</div>
                <div class="small fw-semibold">NCR, Fourth District, Philippines</div>
                <div class="small fw-semibold">Tel. Nos.: (02) 8682-6845, 8696-7798</div>
                <div class="small fw-semibold">VAT Reg. TIN: 120-291-938-00000</div>
            </div>

            <div class="d-flex justify-content-center align-items-center mb-3">
                <div class="billing-title fs-3 apm-brand-font">{{ $isService ? 'SERVICE INVOICE' : 'BILLING STATEMENT' }}</div>
            </div>

            <table class="meta-table-top w-100 mb-2">
                @if($isService)
                    <tr>
                        <td class="meta-label">Sold To</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value" style="width:48%;">{{ $data['bill_to'] ?? '-' }}</td>
                        <td class="meta-label meta-label-right">Date</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value meta-right-value meta-nowrap">{{ $statementDate }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Registered Name</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value">{{ $data['si_registered_name'] ?? '-' }}</td>
                        <td class="meta-label meta-label-right">Sales Type</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value meta-right-value">
                            @php $salesType = strtolower($data['si_sales_type'] ?? ''); @endphp
                            <div class="si-top-check">
                                <div class="meta-nowrap"><span class="box">{{ $salesType === 'cash' ? '✓' : '' }}</span> CASH SALES</div>
                                <div class="meta-nowrap"><span class="box">{{ $salesType === 'charge' ? '✓' : '' }}</span> CHARGE SALES</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label">TIN</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value">{{ $data['bill_tin'] ?? '-' }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="meta-label">Business Address</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value">{{ $data['bill_address'] ?? '-' }}</td>
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
                            <td>{{ strtoupper($data['si_item_description'] ?? 'BROKERAGE FEE AS PER CAO 1-2001') }}</td>
                            <td class="text-center">
                                {{ $fmt($data['si_quantity'] ?? 0) }}{{ !empty($data['vol_meas_unit']) ? ' ' . strtoupper($data['vol_meas_unit']) : '' }}
                            </td>
                            <td class="text-end">{{ $fmt($data['si_unit_cost'] ?? 0) }}</td>
                            <td class="text-end">{{ $fmt($data['si_amount'] ?? 0) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mb-3 mt-2">
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

                <table class="si-bottom-grid mb-2">
                    <tr>
                        <td style="width:50%;">
                            <div>VATable Sales</div>
                            <div>VAT</div>
                            <div>Zero Rated Sales</div>
                            <div>VAT-Exempt Sales</div>
                        </td>
                        <td style="width:50%;">
                            <div class="d-flex justify-content-between"><span>Total Sales (VAT Inclusive)</span><strong>{{ $fmt($data['si_total_sales'] ?? 0) }}</strong></div>
                            <div class="d-flex justify-content-between"><span>Less: VAT</span><strong>{{ $fmt($serviceVatAmount) }}</strong></div>
                            <div class="d-flex justify-content-between"><span>Amount: Net of VAT</span><strong>{{ $fmt($data['si_amount_net_vat'] ?? 0) }}</strong></div>
                            <div class="d-flex justify-content-between"><span>Less: Withholding Tax</span><strong>({{ $fmt($data['si_less_withholding_tax'] ?? 0) }})</strong></div>
                            <div class="d-flex justify-content-between"><span>Add: VAT</span><strong>{{ $fmt($serviceVatAmount) }}</strong></div>
                            <div class="d-flex justify-content-between border-top border-dark mt-1 pt-1"><span class="fw-bold">TOTAL AMOUNT DUE</span><strong>{{ $fmt($data['si_total_amount_due'] ?? $grandTotal) }}</strong></div>
                        </td>
                    </tr>
                </table>

                <div class="si-amount-words">{{ strtoupper($data['amount_in_words'] ?? '-') }}</div>

                <table class="w-100 si-sign">
                    <tr>
                        <td style="width:33%;">
                            <div>Prepared By:</div>
                            <div class="line">{{ strtoupper($data['prepared_by'] ?? '-') }}</div>
                        </td>
                        <td style="width:33%;">
                            <div>Checked By:</div>
                            <div class="line">{{ strtoupper($data['approved_by'] ?? '-') }}</div>
                        </td>
                        <td style="width:34%;">
                            <div>Received By:</div>
                            <div class="line">{{ strtoupper($data['received_by'] ?? '-') }}</div>
                            <div class="mt-1 text-center fw-semibold">Cashier / Authorized Representative</div>
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
