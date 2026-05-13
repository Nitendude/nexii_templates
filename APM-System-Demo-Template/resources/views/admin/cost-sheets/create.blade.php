@extends('layouts.employeehub')

@section('content')
    @php
        $isAutoView = filled($defaultClientName ?? null) && filled($defaultJoNumber ?? null);
    @endphp
    <style>
        .cs-wrap {
            background: #fff;
            border: 1px solid #6b7280;
            border-radius: 10px;
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 12px 12px 10px;
            overflow: auto;
        }
        .cs-sheet {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
            font-family: "Arial Narrow", Arial, sans-serif;
            font-size: 12.5px;
            line-height: 1.05;
            --cs-row-height: 20px;
        }
        .cs-sheet td, .cs-sheet th {
            border: 0;
            padding: 1px 4px;
            vertical-align: middle;
            height: var(--cs-row-height);
        }
        .cs-title {
            font-size: 46px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-align: center;
            height: 50px;
        }
        .cs-subtitle {
            font-size: 18px;
            font-weight: 700;
            text-align: center;
            height: 26px;
        }
        .cs-title-row td,
        .cs-subtitle-row td {
            border-bottom: 1px solid #c7cfdb;
        }
        .cs-right { text-align: right; }
        .cs-center { text-align: center; }
        .cs-bold { font-weight: 700; }
        .cs-section { font-size: 18px; font-weight: 700; }
        .cs-input {
            display: block;
            width: 100%;
            min-width: 0;
            height: calc(var(--cs-row-height) - 2px);
            line-height: calc(var(--cs-row-height) - 2px);
            box-sizing: border-box;
            margin: 0;
            border: 0;
            border-bottom: 1px solid #667085;
            outline: 0;
            background: transparent;
            font: inherit;
            font-weight: 700;
            text-transform: uppercase;
            padding: 1px 2px;
            appearance: none;
            -webkit-appearance: none;
            border-radius: 0;
        }
        .cs-input::-webkit-outer-spin-button,
        .cs-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .cs-cost-label {
            white-space: nowrap;
        }
        .cs-input[type="number"],
        .cs-input[inputmode="decimal"] { text-align: right; }
        .cs-input::placeholder { color: #94a3b8; opacity: 1; }
        .cs-input:focus { border-bottom-color: #2563eb; }
        .cs-input.num { text-align: right; font-variant-numeric: tabular-nums; }
        .cs-total-line {
            border-bottom: 1px solid #667085;
        }
        .cs-text-view {
            border-bottom: 0;
            background: transparent;
            font-weight: 700;
            pointer-events: none;
        }
        .cs-multiline-view {
            width: 100%;
            min-width: 0;
            height: calc(var(--cs-row-height) - 2px);
            line-height: calc(var(--cs-row-height) - 2px);
            box-sizing: border-box;
            border-bottom: 1px solid #667085;
            background: transparent;
            font: inherit;
            font-weight: 700;
            text-transform: uppercase;
            padding: 1px 2px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .cs-hidden-control {
            display: none !important;
        }
        .cs-total {
            font-weight: 800;
            background: #eef4ff;
            border: 1px solid #8fa4bf !important;
        }
        .cs-readonly {
            background: #eef4ff !important;
            font-weight: 800;
            border-bottom: 0;
        }
        .cs-amount-highlight {
            background: #e8f6ff;
            border: 1px solid #a9b9cf !important;
        }
        .cs-rule-top td { border-top: 1px solid #a9b6c8; }
        .cs-section-row td { padding-top: 8px; }
        .cs-print-page {
            border: 1px solid #6b7280;
            padding: 6px 6px 4px;
        }
        .cs-other-btn {
            min-width: 120px;
        }
        @media print {
            @page {
                size: Letter portrait;
                margin: 0.25in;
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
            .cs-wrap {
                border: 0 !important;
                border-radius: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
                width: auto !important;
                max-width: none !important;
            }
            .cs-print-page {
                border: 1px solid #6b7280 !important;
                padding: 5px 5px 4px !important;
                min-height: 10.42in !important;
                box-sizing: border-box !important;
            }
            .cs-sheet {
                width: 100% !important;
                font-size: 10.35px !important;
                line-height: 1.02 !important;
                --cs-row-height: 15px !important;
            }
            .cs-sheet td,
            .cs-sheet th {
                padding: 0 3px !important;
            }
            .cs-title {
                font-size: 34px !important;
                height: 34px !important;
            }
            .cs-subtitle {
                font-size: 14px !important;
                height: 20px !important;
            }
            .cs-section {
                font-size: 14px !important;
            }
            .cs-section-row td {
                padding-top: 3px !important;
            }
            .cs-input {
                border-bottom-width: 1px !important;
                padding: 0 2px !important;
            }
            .cs-multiline-view {
                height: calc(var(--cs-row-height) - 1px) !important;
                line-height: calc(var(--cs-row-height) - 1px) !important;
                padding: 0 2px !important;
            }
            .cs-total,
            .cs-readonly,
            .cs-amount-highlight {
                background: transparent !important;
            }
            .cs-total .cs-input,
            .cs-readonly,
            .cs-readonly.cs-input,
            #csSalesGrandTotal,
            #csCostAtTotal,
            #csCostBilledTotal,
            #csCostDifferenceTotal,
            #csNetIncome {
                background: transparent !important;
                border-bottom: 0 !important;
                box-shadow: none !important;
            }
            .cs-sales-ref-input,
            .cs-sales-amount-input,
            .cs-sales-exrate-input,
            .cs-sales-total-input,
            .cs-cost-at-input,
            .cs-cost-billed-input {
                border-bottom: 1px solid #667085 !important;
            }
            .cs-total-line {
                border-bottom: 1px solid #667085 !important;
            }
            .cs-cost-billed-input,
            #csCostBilledTotal,
            #csCostDifferenceTotal {
                font-size: 9.5px !important;
            }
        }
    </style>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 no-print">
        <h2 class="mb-0">Create Cost Sheet</h2>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary cs-other-btn" type="button" id="addOtherCostRow">Add Other</button>
            <button class="btn btn-outline-primary" type="button" onclick="window.print()">Print</button>
            <a class="btn btn-outline-secondary" href="{{ route('accounting.cost-sheets.index') }}">Back</a>
            <button class="btn btn-primary" type="button" disabled>Save Cost Sheet</button>
        </div>
    </div>

    <div class="cs-wrap">
        <div class="cs-print-page">
        <table class="cs-sheet">
            <colgroup>
                <col style="width:4%;">
                <col style="width:11%;">
                <col style="width:21%;">
                <col style="width:14%;">
                <col style="width:8%;">
                <col style="width:12%;">
                <col style="width:30%;">
            </colgroup>

            <tr class="cs-title-row"><td colspan="7" class="cs-title">APM CUSTOMS BROKERAGE</td></tr>
            <tr class="cs-subtitle-row"><td colspan="7" class="cs-subtitle">COST &amp; INCOME COMPUTATION SHEET</td></tr>

            <tr>
                <td class="cs-bold">DATE</td>
                <td class="cs-center">:</td>
                <td>
                    @if($isAutoView)
                        <input id="csDateInput" class="cs-input cs-text-view" type="text" value="{{ $defaultCostSheetDateDisplay ?? '' }}" readonly>
                    @else
                        <input id="csDateInput" class="cs-input" type="date" value="{{ $defaultCostSheetDate ?? '' }}">
                    @endif
                </td>
                <td class="cs-bold cs-center">CODE</td>
                <td class="cs-center">:</td>
                <td colspan="2"><input id="csCodeInput" class="cs-input cs-center" type="text" readonly></td>
            </tr>
            <tr>
                <td class="cs-bold">CLIENT'S NAME</td>
                <td class="cs-center">:</td>
                <td>
                    @if($isAutoView)
                        <div id="csClientText" class="cs-multiline-view">{{ $defaultClientName ?? '' }}</div>
                    @endif
                    <select id="csClientSelect" class="cs-input{{ $isAutoView ? ' cs-hidden-control' : '' }}">
                        <option value=""></option>
                        @foreach(($clients ?? collect()) as $clientName)
                            <option value="{{ $clientName }}">{{ $clientName }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="cs-bold cs-center">J.O. NO.</td>
                <td class="cs-center">:</td>
                <td colspan="2">
                    @if($isAutoView)
                        <input id="csJoText" class="cs-input cs-center cs-text-view" type="text" value="{{ $defaultJoNumber ?? '' }}" readonly>
                    @endif
                    <select id="csJoSelect" class="cs-input cs-center{{ $isAutoView ? ' cs-hidden-control' : '' }}">
                        <option value=""></option>
                    </select>
                </td>
            </tr>

            <tr class="cs-section-row"><td class="cs-section">A.</td><td colspan="6" class="cs-section">SALES INFORMATION</td></tr>
            <tr>
                <td></td>
                <td class="cs-center cs-bold"> </td>
                <td class="cs-center cs-bold">REF. NO.</td>
                <td class="cs-center cs-bold">AMOUNT</td>
                <td class="cs-center cs-bold">EX RATE</td>
                <td class="cs-center cs-bold" colspan="2">TOTAL</td>
            </tr>

            @for($i=0;$i<5;$i++)
                <tr>
                    <td></td>
                    <td>
                        @if($isAutoView)
                            <input class="cs-input cs-sales-doc-label cs-text-view" type="text" readonly>
                        @endif
                        <select class="cs-input cs-sales-doc-type{{ $isAutoView ? ' cs-hidden-control' : '' }}">
                            <option value=""></option>
                            <option value="billing_statement">BILLING STATEMENT</option>
                            <option value="service_invoice">SERVICE INVOICE</option>
                            <option value="debit_credit_note">DEBIT / CREDIT NOTE</option>
                        </select>
                    </td>
                    <td><input class="cs-input num cs-sales-ref-input" type="text"></td>
                    <td><input class="cs-input num cs-money-input cs-sales-amount-input" type="text" inputmode="decimal"></td>
                    <td><input class="cs-input num cs-sales-exrate-input" type="number" step="0.0001"></td>
                    <td colspan="2" class="cs-total-line">
                        <input class="cs-input num cs-money-input cs-sales-total-input cs-readonly" type="text" inputmode="decimal" readonly>
                        <input class="cs-sales-vat-hidden" type="hidden" value="0">
                    </td>
                </tr>
            @endfor

            <tr class="cs-rule-top">
                <td></td><td colspan="4"></td>
                <td colspan="2" class="cs-total cs-right">
                    <input id="csSalesGrandTotal" class="cs-input num cs-money-input cs-readonly" type="text" inputmode="decimal" placeholder="0.00" readonly>
                </td>
            </tr>
            <tr id="csAdvanceDeductionRow" style="display:none;">
                <td></td>
                <td colspan="4" class="cs-right">
                    <label class="d-inline-flex align-items-center gap-2">
                        <input id="csDeductAdvances" type="checkbox">
                        <span class="cs-bold">Deduct Advances</span>
                    </label>
                </td>
                <td colspan="2" class="cs-total cs-right">
                    <input id="csAdvanceAmount" class="cs-input num cs-money-input cs-readonly" type="text" inputmode="decimal" placeholder="0.00" readonly>
                </td>
            </tr>

            <tr class="cs-section-row"><td class="cs-section">B.</td><td colspan="6" class="cs-section">COST INFORMATION</td></tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td class="cs-center cs-bold">AT COST</td>
                <td class="cs-center cs-bold">BILLED AMT.</td>
                <td class="cs-center cs-bold">CV NO</td>
                <td class="cs-center cs-bold">REMARKS</td>
            </tr>

            @php
                $costRows = [
                    'AISL' => 'A I S L',
                    'NTC' => 'NTC',
                    'CUSTOMSFORMSSTAMPS' => 'CUSTOMS FORMS & STAMPS',
                    'DOCUMENTATIONANDPHOTOCOPY' => 'DOCUMENTATION AND PHOTOCOPY',
                    'NOTARIALFEEANDINTERCOMMERCECHARGE' => 'NOTARIAL FEE & INTERCOMMERCE CHARGE',
                    'HANDLINGFEE' => 'HANDLING FEE',
                    'ARRASTRECHARGE' => 'ARRASTRE CHARGE',
                    'WHARFAGEDUE' => 'WHARFAGE DUE',
                    'BANKCHARGE' => 'BANK CHARGE',
                    'BREAKBULKFEE' => 'BREAKBULK FEE',
                    'BROKERAGEFEE' => 'BROKERAGE FEE',
                    'LESSWITHHOLDINGTAX' => 'LESS WITHHOLDING TAX',
                    'CFSCHARGES' => 'CFS CHARGES',
                    'CHASSISRENTAL' => 'CHASSIS RENTAL',
                    'CLIENTSCOMMISSION' => 'CLIENT\'S COMMISSION',
                    'CUSTOMSFACILITATION' => 'CUSTOMS FACILITATION',
                    'DUTIESANDTAXES' => 'DUTIES AND TAXES',
                    'DEMURRAGEFEE' => 'DEMURRAGE FEE',
                    'EXTREMEFREIGHTBILL' => 'EXTREME FREIGHT BILL',
                    'FCLCHARGESTHCBLFEEETC' => 'FCL CHARGES (THC, BL FEE, ETC.)',
                    'CONTAINERDEPOSIT' => 'CONTAINER DEPOSIT',
                    'HUSTLING' => 'HUSTLING',
                    'LOLOANDSTORAGE' => 'L O L O & STORAGE',
                    'LCLCHARGES' => 'LCL CHARGES',
                    'NOTARIAL' => 'NOTARIAL',
                    'PROCESSINGEXPENSES' => 'PROCESSING EXPENSES',
                    'PROCESSINGNTC' => 'PROCESSING - NTC',
                    'PROCESSINGIASAOCG' => 'PROCESSING - IAS/AOCG',
                    'PROCESSINGATRIG' => 'PROCESSING - ATRIG',
                    'PROCESSINGWITHDRAWAL' => 'PROCESSING - WITHDRAWAL',
                    'PROCESSING' => 'PROCESSING.',
                    'ROYALTYFEE' => 'ROYALTY FEE',
                    'STORAGEFEE' => 'STORAGE FEE',
                    'SURETYBOND' => 'SURETY BOND',
                    'TABS' => 'T A B S',
                    'TRUCKINGCHARGES' => 'TRUCKING CHARGES',
                    'EMPTYRETURN' => 'EMPTY RETURN',
                    'OTHERS' => 'OTHERS',
                ];
            @endphp

            @foreach($costRows as $rowKey => $rowLabel)
                <tr @if($rowKey === 'OTHERS') class="cs-cost-other-row" data-base-other="true" @endif>
                    <td></td>
                    <td colspan="2" class="cs-bold cs-cost-label">
                        @if($rowKey === 'OTHERS')
                            <input class="cs-input cs-cost-other-desc-input" type="text" value="OTHERS" placeholder="INPUT OTHER DESCRIPTION">
                        @else
                            {{ $rowLabel }}
                        @endif
                    </td>
                    <td><input class="cs-input num cs-money-input cs-cost-at-input" type="text" inputmode="decimal" data-cost-key="{{ $rowKey === 'OTHERS' ? '' : $rowKey }}"></td>
                    <td><input class="cs-input num cs-money-input cs-cost-billed-input" type="text" inputmode="decimal" data-cost-key="{{ $rowKey === 'OTHERS' ? '' : $rowKey }}"></td>
                    <td><input class="cs-input cs-center" type="text"></td>
                    <td><input class="cs-input" type="text"></td>
                </tr>
            @endforeach

            <tr class="cs-rule-top" id="csCostTotalsRow">
                <td></td><td colspan="2"></td>
                <td class="cs-total cs-right">
                    <input id="csCostAtTotal" class="cs-input num cs-money-input cs-readonly" type="text" inputmode="decimal" placeholder="0.00" readonly>
                </td>
                <td class="cs-total cs-right">
                    <input id="csCostBilledTotal" class="cs-input num cs-money-input cs-readonly" type="text" inputmode="decimal" placeholder="0.00" readonly>
                </td>
                <td></td>
                <td class="cs-total cs-right cs-amount-highlight">
                    <input id="csCostDifferenceTotal" class="cs-input num cs-money-input cs-readonly" type="text" inputmode="decimal" placeholder="0.00" readonly>
                </td>
            </tr>
            <tr id="csWithholdingDeductionRow" style="display:none;">
                <td></td>
                <td colspan="4" class="cs-right">
                    <label class="d-inline-flex align-items-center gap-2">
                        <input id="csDeductWithholdingTax" type="checkbox">
                        <span class="cs-bold">Deduct Less Withholding Tax</span>
                    </label>
                </td>
                <td colspan="2" class="cs-total cs-right">
                    <input id="csWithholdingTaxAmount" class="cs-input num cs-money-input cs-readonly" type="text" inputmode="decimal" placeholder="0.00" readonly>
                </td>
            </tr>

            <tr>
                <td class="cs-section">C.</td>
                <td colspan="5" class="cs-section">NET INCOME</td>
                <td class="cs-total cs-right">
                    <input id="csNetIncome" class="cs-input num cs-money-input cs-readonly" type="text" inputmode="decimal" placeholder="0.00" readonly>
                </td>
            </tr>

            <tr>
                <td></td>
                <td colspan="2" class="cs-bold">PREPARED BY:</td>
                <td colspan="2" class="cs-bold">NOTED BY:</td>
                <td colspan="2" class="cs-bold">Approved by:</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2"><input class="cs-input" type="text"></td>
                <td colspan="2"><input class="cs-input" type="text"></td>
                <td colspan="2"><input class="cs-input" type="text"></td>
            </tr>
        </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const jobOrders = @json(($jobOrdersForSelect ?? collect())->values());
        const docRefsByJoNumber = @json($docRefsByJoNumber ?? []);
        const docAmountsByJoNumber = @json($docAmountsByJoNumber ?? []);
        const docVatByJoNumber = @json($docVatByJoNumber ?? []);
        const docNoteIncludedByJoNumber = @json($docNoteIncludedByJoNumber ?? []);
        const docAdvanceAmountsByJoNumber = @json($docAdvanceAmountsByJoNumber ?? []);
        const docEntriesByJoNumber = @json($docEntriesByJoNumber ?? []);
        const costDocAmountsByJoNumber = @json($costDocAmountsByJoNumber ?? []);
        const costAtAmountsByJoNumber = @json($costAtAmountsByJoNumber ?? []);
        const costAtCvNosByJoNumber = @json($costAtCvNosByJoNumber ?? []);
        const costAtRemarksByJoNumber = @json($costAtRemarksByJoNumber ?? []);
        const costDocOtherItemsByJoNumber = @json($costDocOtherItemsByJoNumber ?? []);
        const costAtOtherItemsByJoNumber = @json($costAtOtherItemsByJoNumber ?? []);
        const costAtOtherCvNosByJoNumber = @json($costAtOtherCvNosByJoNumber ?? []);
        const defaultClientName = @json($defaultClientName ?? '');
        const defaultJoNumber = @json($defaultJoNumber ?? '');
        const defaultCostSheetDate = @json($defaultCostSheetDate ?? '');
        const defaultCostSheetDateDisplay = @json($defaultCostSheetDateDisplay ?? '');
        const isAutoView = @json($isAutoView);
        const addOtherCostRowBtn = document.getElementById('addOtherCostRow');
        const clientSelect = document.getElementById('csClientSelect');
        const joSelect = document.getElementById('csJoSelect');
        const codeInput = document.getElementById('csCodeInput');
        const dateInput = document.getElementById('csDateInput');
        const clientText = document.getElementById('csClientText');
        const joText = document.getElementById('csJoText');
        const deductAdvancesCheckbox = document.getElementById('csDeductAdvances');
        const advanceDeductionRow = document.getElementById('csAdvanceDeductionRow');
        const advanceAmountInput = document.getElementById('csAdvanceAmount');
        const deductWithholdingTaxCheckbox = document.getElementById('csDeductWithholdingTax');
        const withholdingDeductionRow = document.getElementById('csWithholdingDeductionRow');
        const withholdingTaxAmountInput = document.getElementById('csWithholdingTaxAmount');

        const parseMoney = (value) => {
            const raw = String(value || '').trim();
            if (raw === '') return 0;
            const isParenNegative = /^\(.*\)$/.test(raw);
            const normalized = raw
                .replace(/[()]/g, '')
                .replace(/,/g, '');
            const parsed = Number.parseFloat(normalized) || 0;
            return isParenNegative ? -Math.abs(parsed) : parsed;
        };

        const formatMoney = (value) => {
            const number = typeof value === 'number' ? value : parseMoney(value);
            if (!Number.isFinite(number)) return '0.00';
            const absolute = Math.abs(number).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            return number < 0 ? `(${absolute})` : absolute;
        };

        const formatMoneyInput = (input) => {
            if (!input) return;

            const rawValue = String(input.value || '').replace(/[^\d.-]/g, '');
            const negative = rawValue.startsWith('-');
            const unsigned = rawValue.replace(/-/g, '');
            const [whole = '', ...decimalParts] = unsigned.split('.');
            const decimals = decimalParts.join('').slice(0, 2);
            const cleanedWhole = whole.replace(/^0+(?=\d)/, '');
            const formattedWhole = cleanedWhole.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

            input.value = (negative ? '-' : '')
                + (decimals.length || rawValue.includes('.') ? `${formattedWhole || '0'}.${decimals}` : formattedWhole);
        };

        const resetJoOptions = () => {
            if (!joSelect) return;
            joSelect.innerHTML = '<option value=""></option>';
        };

        const populateJoOptions = (clientName) => {
            if (!joSelect) return;
            resetJoOptions();
            if (!clientName) return;

            const matches = (Array.isArray(jobOrders) ? jobOrders : []).filter((jobOrder) => {
                return (jobOrder?.consignee || '').toLowerCase() === String(clientName).toLowerCase();
            });

            matches.forEach((jobOrder) => {
                const option = document.createElement('option');
                option.value = jobOrder.number || '';
                option.textContent = jobOrder.jo_display || jobOrder.number || '';
                option.dataset.code = jobOrder.code || '';
                joSelect.appendChild(option);
            });
        };

        const syncCodeFromJo = () => {
            if (!joSelect || !codeInput) return;
            const selected = joSelect.options[joSelect.selectedIndex];
            codeInput.value = selected?.dataset?.code || '';
            if (joText) {
                joText.value = selected?.textContent?.trim() || joSelect.value || defaultJoNumber || '';
            }
        };

        const syncClientText = () => {
            if (!clientText || !clientSelect) return;
            const selected = clientSelect.options[clientSelect.selectedIndex];
            const fullName = selected?.textContent?.trim() || clientSelect.value || defaultClientName || '';
            const compactName = fullName.length > 20 ? (fullName.split(/\s+/)[0] || fullName) : fullName;
            clientText.textContent = compactName;
        };

        const syncSalesDocLabel = (row) => {
            if (!row) return;
            const typeSelect = row.querySelector('.cs-sales-doc-type');
            const labelInput = row.querySelector('.cs-sales-doc-label');
            if (!typeSelect || !labelInput) return;

            const selected = typeSelect.options[typeSelect.selectedIndex];
            labelInput.value = selected?.textContent?.trim() || '';
        };

        clientSelect?.addEventListener('change', () => {
            populateJoOptions(clientSelect.value);
            if (codeInput) codeInput.value = '';
            syncClientText();
        });

        joSelect?.addEventListener('change', syncCodeFromJo);

        const getSalesRows = () => Array.from(document.querySelectorAll('.cs-sales-doc-type'))
            .map((typeSelect) => typeSelect.closest('tr'))
            .filter(Boolean);

        const clearSalesRow = (row) => {
            if (!row) return;
            row.dataset.includeInSales = '1';
            row.dataset.salesSign = '1';

            const typeSelect = row.querySelector('.cs-sales-doc-type');
            const labelInput = row.querySelector('.cs-sales-doc-label');
            const refInput = row.querySelector('.cs-sales-ref-input');
            const amountInput = row.querySelector('.cs-sales-amount-input');
            const exrateInput = row.querySelector('.cs-sales-exrate-input');
            const totalInput = row.querySelector('.cs-sales-total-input');
            const vatHidden = row.querySelector('.cs-sales-vat-hidden');

            if (typeSelect) typeSelect.value = '';
            if (labelInput) labelInput.value = '';
            if (refInput) refInput.value = '';
            if (amountInput) amountInput.value = '';
            if (exrateInput) exrateInput.value = '';
            if (totalInput) totalInput.value = '';
            if (vatHidden) vatHidden.value = '0';
        };

        const ensureSalesRowCount = (count) => {
            const totalRowsNeeded = Math.max(count, 5);
            let rows = getSalesRows();
            const totalsRow = document.getElementById('csSalesGrandTotal')?.closest('tr');

            while (rows.length < totalRowsNeeded && rows.length > 0 && totalsRow?.parentNode) {
                const clone = rows[rows.length - 1].cloneNode(true);
                clearSalesRow(clone);
                totalsRow.parentNode.insertBefore(clone, totalsRow);
                rows = getSalesRows();
            }

            return rows;
        };

        const setSalesRowFromEntry = (row, entry = {}) => {
            if (!row) return;

            clearSalesRow(row);
            row.dataset.includeInSales = entry.include_in_sales === false ? '0' : '1';
            row.dataset.salesSign = String(entry.sales_sign ?? 1);

            const typeSelect = row.querySelector('.cs-sales-doc-type');
            const labelInput = row.querySelector('.cs-sales-doc-label');
            const refInput = row.querySelector('.cs-sales-ref-input');
            const amountInput = row.querySelector('.cs-sales-amount-input');

            if (typeSelect) {
                typeSelect.value = entry.type || '';
            }
            if (labelInput) {
                labelInput.value = entry.label || '';
            }
            if (refInput) {
                refInput.value = entry.ref || '';
            }
            if (amountInput) {
                const amount = parseMoney(entry.amount ?? 0);
                amountInput.value = Number.isFinite(amount) ? formatMoney(amount) : '';
            }
        };

        const autoFillDocDataForRow = (row) => {
            if (!row) return;
            const typeSelect = row.querySelector('.cs-sales-doc-type');
            const refInput = row.querySelector('.cs-sales-ref-input');
            const amountInput = row.querySelector('.cs-sales-amount-input');
            const vatHidden = row.querySelector('.cs-sales-vat-hidden');
            if (!typeSelect || !refInput || !amountInput || !vatHidden || !joSelect) return;

            vatHidden.value = '0';

            const joNumber = joSelect.value || '';
            if (!joNumber) return;

            const selectedType = typeSelect.value || '';
            if (!selectedType) return;

            row.dataset.includeInSales = '1';

            syncSalesDocLabel(row);

            const refs = docRefsByJoNumber[joNumber] || {};
            refInput.value = refs[selectedType] || '';

            const amounts = docAmountsByJoNumber[joNumber] || {};
            const amount = amounts[selectedType];
            if (amount !== undefined && amount !== null && amount !== '') {
                const parsed = parseMoney(amount);
                const computedAmount = Number.isFinite(parsed) ? parsed : 0;
                if (selectedType === 'service_invoice') {
                    const vats = docVatByJoNumber[joNumber] || {};
                    const vatAmount = parseMoney(vats.service_invoice ?? 0);
                    if (Number.isFinite(vatAmount) && vatAmount > 0) {
                        vatHidden.value = vatAmount.toFixed(2);
                    }
                }
                amountInput.value = formatMoney(computedAmount);
                recalc();
            }
        };

        const refreshAllSalesRefs = () => {
            const rows = document.querySelectorAll('.cs-sales-doc-type');
            rows.forEach((typeSelect) => {
                autoFillDocDataForRow(typeSelect.closest('tr'));
            });
        };

        const populateSalesRowsFromAvailableDocs = () => {
            const joNumber = joSelect?.value || '';
            const entries = joNumber ? (docEntriesByJoNumber[joNumber] || []) : [];
            const rows = ensureSalesRowCount(entries.length);

            rows.forEach((row, index) => {
                const entry = entries[index] || null;
                if (entry) {
                    setSalesRowFromEntry(row, entry);
                } else {
                    clearSalesRow(row);
                }
            });

            recalc();
        };

        const fillCostInfoFromDocuments = () => {
            const joNumber = joSelect?.value || '';
            const costMap = (joNumber && costDocAmountsByJoNumber[joNumber]) ? costDocAmountsByJoNumber[joNumber] : {};

            document.querySelectorAll('.cs-cost-billed-input[data-cost-key]').forEach((input) => {
                const key = input.dataset.costKey || '';
                const amount = key ? costMap[key] : null;
                input.value = (amount !== undefined && amount !== null && amount !== '')
                    ? formatMoney(parseMoney(amount))
                    : '';
            });

            recalc();
        };

        const fillAtCostFromLiquidation = () => {
            const joNumber = joSelect?.value || '';
            const costMap = (joNumber && costAtAmountsByJoNumber[joNumber]) ? costAtAmountsByJoNumber[joNumber] : {};
            const cvMap = (joNumber && costAtCvNosByJoNumber[joNumber]) ? costAtCvNosByJoNumber[joNumber] : {};
            const remarksMap = (joNumber && costAtRemarksByJoNumber[joNumber]) ? costAtRemarksByJoNumber[joNumber] : {};

            document.querySelectorAll('.cs-cost-at-input[data-cost-key]').forEach((input) => {
                const key = input.dataset.costKey || '';
                const amount = key ? costMap[key] : null;
                input.value = (amount !== undefined && amount !== null && amount !== '')
                    ? formatMoney(parseMoney(amount))
                    : '';

                const cvInput = input.closest('tr')?.children[4]?.querySelector('input');
                if (cvInput) {
                    cvInput.value = key ? (cvMap[key] || '') : '';
                }

                const remarksInput = input.closest('tr')?.children[5]?.querySelector('input');
                if (remarksInput) {
                    remarksInput.value = key ? (remarksMap[key] || '') : '';
                }
            });

            recalc();
        };

        const syncAdvanceDeduction = () => {
            const joNumber = joSelect?.value || '';
            const amount = parseMoney((joNumber && docAdvanceAmountsByJoNumber[joNumber]) ? docAdvanceAmountsByJoNumber[joNumber] : 0);
            const hasAdvance = Number.isFinite(amount) && Math.abs(amount) > 0.00001;

            if (advanceDeductionRow) {
                advanceDeductionRow.style.display = hasAdvance ? '' : 'none';
            }

            if (advanceAmountInput) {
                advanceAmountInput.value = hasAdvance ? formatMoney(amount) : '0.00';
            }

            if (!hasAdvance && deductAdvancesCheckbox) {
                deductAdvancesCheckbox.checked = false;
            }
        };

        const syncWithholdingDeduction = () => {
            const withholdingInput = document.querySelector('.cs-cost-billed-input[data-cost-key="LESSWITHHOLDINGTAX"]');
            const amount = parseMoney(withholdingInput?.value || '0');
            const hasWithholding = Number.isFinite(amount) && Math.abs(amount) > 0.00001;

            if (withholdingDeductionRow) {
                withholdingDeductionRow.style.display = hasWithholding ? '' : 'none';
            }

            if (withholdingTaxAmountInput) {
                withholdingTaxAmountInput.value = hasWithholding ? formatMoney(amount) : '0.00';
            }

            if (!hasWithholding && deductWithholdingTaxCheckbox) {
                deductWithholdingTaxCheckbox.checked = false;
            }
        };

        const getBaseOtherCostRow = () => document.querySelector('.cs-cost-other-row[data-base-other="true"]');

        const setOtherCostRowValues = (row, values = {}) => {
            if (!row) return;

            const descInput = row.querySelector('.cs-cost-other-desc-input');
            const atInput = row.querySelector('.cs-cost-at-input');
            const billedInput = row.querySelector('.cs-cost-billed-input');
            const cvInput = row.children[4]?.querySelector('input');
            const remarksInput = row.children[5]?.querySelector('input');

            if (descInput) {
                descInput.value = values.description || 'OTHERS';
            }

            if (atInput) {
                atInput.value = values.atCost ?? '';
            }

            if (billedInput) {
                billedInput.value = values.billed ?? '';
            }

            if (cvInput) {
                cvInput.value = values.cvNo ?? '';
            }

            if (remarksInput) {
                remarksInput.value = values.remarks ?? '';
            }
        };

        const removeExtraOtherCostRows = () => {
            document.querySelectorAll('.cs-cost-other-row:not([data-base-other="true"])').forEach((row) => {
                row.remove();
            });
        };

        const createOtherCostRow = (values = {}) => {
            const totalRow = document.getElementById('csCostTotalsRow');
            if (!totalRow) return null;

            const row = document.createElement('tr');
            row.className = 'cs-cost-other-row';
            row.innerHTML = `
                <td></td>
                <td colspan="2" class="cs-bold">
                    <input class="cs-input cs-cost-other-desc-input" type="text" value="OTHERS" placeholder="INPUT OTHER DESCRIPTION">
                </td>
                <td><input class="cs-input num cs-money-input cs-cost-at-input" type="text" inputmode="decimal"></td>
                <td><input class="cs-input num cs-money-input cs-cost-billed-input" type="text" inputmode="decimal"></td>
                <td><input class="cs-input cs-center" type="text"></td>
                <td><input class="cs-input" type="text"></td>
            `;

            totalRow.parentNode.insertBefore(row, totalRow);
            setOtherCostRowValues(row, values);

            return row;
        };

        const mergeOtherCostItems = (billedItems = [], atCostItems = [], atCostCvNos = {}) => {
            const merged = [];
            const indexByKey = new Map();

            const upsert = (item, field) => {
                const description = String(item?.description || 'OTHERS').trim() || 'OTHERS';
                const key = description.toUpperCase();
                let index = indexByKey.get(key);

                if (index === undefined) {
                    index = merged.length;
                    indexByKey.set(key, index);
                    merged.push({
                        description,
                        atCost: '',
                        billed: '',
                        cvNo: '',
                        remarks: '',
                    });
                }

                const amount = parseMoney(item?.amount ?? 0);
                if (Number.isFinite(amount) && Math.abs(amount) > 0.00001) {
                    merged[index][field] = formatMoney(amount);
                }

                const remarks = String(item?.remarks || '').trim();
                if (remarks !== '') {
                    const existingRemarks = String(merged[index].remarks || '')
                        .split(', ')
                        .map((value) => value.trim())
                        .filter(Boolean);

                    if (!existingRemarks.includes(remarks)) {
                        existingRemarks.push(remarks);
                    }

                    merged[index].remarks = existingRemarks.join(', ');
                }

                if (field === 'atCost') {
                    merged[index].cvNo = atCostCvNos[key] || merged[index].cvNo || '';
                }
            };

            billedItems.forEach((item) => upsert(item, 'billed'));
            atCostItems.forEach((item) => upsert(item, 'atCost'));

            return merged;
        };

        const syncOtherCostRows = () => {
            const joNumber = joSelect?.value || '';
            const billedItems = (joNumber && costDocOtherItemsByJoNumber[joNumber]) ? costDocOtherItemsByJoNumber[joNumber] : [];
            const atCostItems = (joNumber && costAtOtherItemsByJoNumber[joNumber]) ? costAtOtherItemsByJoNumber[joNumber] : [];
            const atCostCvNos = (joNumber && costAtOtherCvNosByJoNumber[joNumber]) ? costAtOtherCvNosByJoNumber[joNumber] : {};
            const otherRows = mergeOtherCostItems(billedItems, atCostItems, atCostCvNos);

            removeExtraOtherCostRows();

            const baseRow = getBaseOtherCostRow();
            if (!baseRow) {
                recalc();
                return;
            }

            if (otherRows.length === 0) {
                setOtherCostRowValues(baseRow);
                recalc();
                return;
            }

            otherRows.forEach((rowValues, index) => {
                if (index === 0) {
                    setOtherCostRowValues(baseRow, rowValues);
                    return;
                }

                createOtherCostRow(rowValues);
            });

            recalc();
        };

        joSelect?.addEventListener('change', () => {
            syncCodeFromJo();
            populateSalesRowsFromAvailableDocs();
            fillCostInfoFromDocuments();
            fillAtCostFromLiquidation();
            syncOtherCostRows();
            syncAdvanceDeduction();
        });

        document.addEventListener('change', (event) => {
            if (event.target.classList.contains('cs-sales-doc-type')) {
                const row = event.target.closest('tr');
                syncSalesDocLabel(row);
                autoFillDocDataForRow(row);
            }
        });

        addOtherCostRowBtn?.addEventListener('click', () => {
            createOtherCostRow();
        });

        deductAdvancesCheckbox?.addEventListener('change', () => {
            recalc();
        });

        deductWithholdingTaxCheckbox?.addEventListener('change', () => {
            recalc();
        });

        const costAtInputs = document.querySelectorAll('.cs-cost-at-input');
        const costBilledInputs = document.querySelectorAll('.cs-cost-billed-input');

        const salesGrandTotal = document.getElementById('csSalesGrandTotal');
        const costAtTotal = document.getElementById('csCostAtTotal');
        const costBilledTotal = document.getElementById('csCostBilledTotal');
        const costDifferenceTotal = document.getElementById('csCostDifferenceTotal');
        const netIncome = document.getElementById('csNetIncome');

        const sumInputs = (inputs) => {
            let total = 0;
            inputs.forEach((input) => {
                total += parseMoney(input.value || '0');
            });
            return total;
        };

        const setValue = (el, value) => {
            if (!el) return;
            el.value = Number.isFinite(value) ? formatMoney(value) : '0.00';
        };

        const recalcSalesRows = () => {
            const amountFields = document.querySelectorAll('.cs-sales-amount-input');
            amountFields.forEach((amountInput) => {
                const row = amountInput.closest('tr');
                if (!row) return;
                const exrateInput = row.querySelector('.cs-sales-exrate-input');
                const totalInput = row.querySelector('.cs-sales-total-input');
                if (!totalInput) return;

                const amount = parseMoney(amountInput.value || '0');
                const exrate = parseMoney(exrateInput?.value || '0');
                const rawTotal = exrate > 0 ? (amount * exrate) : amount;
                const salesSign = Number(row.dataset.salesSign || '1') < 0 ? -1 : 1;
                setValue(totalInput, rawTotal * salesSign);
            });
        };

        const recalc = () => {
            recalcSalesRows();
            const sales = Array.from(document.querySelectorAll('.cs-sales-total-input')).reduce((total, input) => {
                const row = input.closest('tr');
                const includeInSales = (row?.dataset?.includeInSales || '1') !== '0';

                if (!includeInSales) {
                    return total;
                }

                return total + parseMoney(input.value || '0');
            }, 0);
            const advanceDeduction = deductAdvancesCheckbox?.checked
                ? parseMoney(advanceAmountInput?.value || '0')
                : 0;
            const salesAfterAdvanceDeduction = sales - advanceDeduction;
            const atCost = sumInputs(document.querySelectorAll('.cs-cost-at-input'));
            const billedBeforeWithholdingDeduction = sumInputs(document.querySelectorAll('.cs-cost-billed-input'));
            const withholdingDeduction = deductWithholdingTaxCheckbox?.checked
                ? parseMoney(withholdingTaxAmountInput?.value || '0')
                : 0;
            const billed = billedBeforeWithholdingDeduction - withholdingDeduction;
            const diff = billed - atCost;

            setValue(salesGrandTotal, salesAfterAdvanceDeduction);
            setValue(costAtTotal, atCost);
            setValue(costBilledTotal, billed);
            setValue(costDifferenceTotal, diff);
            setValue(netIncome, diff);
        };

        document.addEventListener('input', (event) => {
            if (
                event.target.classList.contains('cs-sales-total-input') ||
                event.target.classList.contains('cs-sales-amount-input') ||
                event.target.classList.contains('cs-sales-exrate-input') ||
                event.target.classList.contains('cs-cost-at-input') ||
                event.target.classList.contains('cs-cost-billed-input')
            ) {
                if (event.target.classList.contains('cs-money-input') && !event.target.readOnly) {
                    formatMoneyInput(event.target);
                }

                if (event.target.classList.contains('cs-cost-billed-input')) {
                    syncWithholdingDeduction();
                }
                recalc();
            }
        });

        if (defaultClientName && clientSelect) {
            clientSelect.value = defaultClientName;
            populateJoOptions(defaultClientName);
            syncClientText();
        }

        if (defaultJoNumber && joSelect) {
            joSelect.value = defaultJoNumber;
            syncCodeFromJo();
            populateSalesRowsFromAvailableDocs();
            fillCostInfoFromDocuments();
            fillAtCostFromLiquidation();
            syncOtherCostRows();
            syncAdvanceDeduction();
            syncWithholdingDeduction();
        }

        if (defaultCostSheetDate && dateInput) {
            dateInput.value = isAutoView ? (defaultCostSheetDateDisplay || dateInput.value) : defaultCostSheetDate;
        }

        if (isAutoView) {
            document.querySelectorAll('.cs-input').forEach((input) => {
                if (input.classList.contains('cs-hidden-control')) {
                    return;
                }

                if (input.tagName === 'SELECT') {
                    input.setAttribute('disabled', 'disabled');
                } else {
                    input.setAttribute('readonly', 'readonly');
                }
            });
            addOtherCostRowBtn?.setAttribute('disabled', 'disabled');
        }

        document.querySelectorAll('.cs-money-input').forEach((input) => {
            if (input.value !== '') {
                input.value = formatMoney(input.value);
            }
        });

        recalc();
    })();
</script>
@endpush
