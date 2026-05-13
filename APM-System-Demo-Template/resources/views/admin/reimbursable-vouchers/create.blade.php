@extends('layouts.employeehub')

@section('content')
    <style>
        .rv-paper-wrap {
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            width: 100%;
            max-width: none;
            margin: 0;
        }
        .rv-paper {
            padding: 28px 28px 24px;
            font-family: "Courier New", Courier, monospace;
            color: #111827;
            font-size: 16px;
            line-height: 1.4;
        }
        .rv-paper table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .rv-paper th,
        .rv-paper td {
            border: 1px solid #222;
            padding: 7px 10px;
            vertical-align: top;
        }
        .rv-paper input,
        .rv-paper select {
            width: 100%;
            border: 1px solid #cbd5e1;
            outline: 0;
            background: #ffffff;
            font: inherit;
            padding: 9px 12px;
            border-radius: 10px;
            min-height: 42px;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }
        .rv-paper input:focus,
        .rv-paper select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
            background: #f8fbff;
        }
        .rv-paper .center { text-align: center; }
        .rv-paper .right { text-align: right; }
        .rv-paper .title { font-size: 34px; font-weight: 700; letter-spacing: 0.04em; text-align: center; }
        .rv-paper .subtitle { font-size: 20px; font-weight: 700; letter-spacing: 0.08em; text-align: center; }
        .rv-paper .line-cell { height: 46px; }
        .rv-controls .btn { min-width: 120px; }
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
        .rv-paper .rv-action-col {
            width: 64px;
            min-width: 64px;
            max-width: 64px;
            text-align: center;
            padding-left: 4px;
            padding-right: 4px;
        }
        .rv-paper th.rv-action-col {
            font-size: 11px;
            letter-spacing: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .rv-paper .rv-delete-line {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            line-height: 1;
            margin: 0 auto;
            padding: 0;
        }
        .rv-paper .rv-data-row td {
            min-height: 46px;
        }
        .rv-paper .rv-group-divider td {
            padding: 0;
            border-left: 0;
            border-right: 0;
            background: #eef4ff;
        }
        .rv-paper .rv-group-banner {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px 18px;
            padding: 10px 14px;
            border-top: 1px solid #c7d7fe;
            border-bottom: 1px solid #c7d7fe;
            background: linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%);
        }
        .rv-paper .rv-group-badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 999px;
            background: #1d4ed8;
            color: #fff;
            font-size: 0.84rem;
            font-weight: 700;
            letter-spacing: 0.03em;
        }
        .rv-paper .rv-group-meta {
            color: #334155;
            font-size: 0.9rem;
            line-height: 1.25;
        }
        .rv-paper .rv-group-actions {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
        }
        .rv-paper .rv-add-group-row {
            border: 1px solid #2563eb;
            background: #ffffff;
            color: #1d4ed8;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 0.78rem;
            font-weight: 700;
            line-height: 1;
            cursor: pointer;
        }
        .rv-paper .rv-add-group-row:hover {
            background: #eff6ff;
        }
        .rv-paper .rv-row-under-group td {
            background: #fcfdff;
        }
        .rv-paper .rv-row-under-group .rv-jo-input.rv-jo-inherited,
        .rv-paper .rv-row-under-group .rv-client-input,
        .rv-paper .rv-row-under-group input[name="liq_no[]"] {
            background-color: #f8fafc;
        }
        .rv-paper .rv-head-row td {
            border: 0;
        }
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
        .rv-paper .rv-footer-row td {
            border-left: 0;
            border-right: 0;
        }
        .rv-paper .rv-sign-row td,
        .rv-paper .rv-sign-value-row td {
            border-top: 0;
        }
        .rv-paper .rv-sign-row td {
            border-bottom: 0;
        }
        .rv-paper .rv-footer-row-last td {
            border-left: 0;
            border-right: 0;
            border-bottom: 0;
        }
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
        .rv-paper .rv-no-label,
        .rv-paper .rv-no-value input {
            color: #c62828;
            font-weight: 700;
        }
        .rv-paper .rv-jo-input,
        .rv-paper .rv-description-input {
            color: #0f172a;
        }
        .rv-paper .rv-jo-combobox {
            position: relative;
        }
        .rv-paper .rv-jo-input {
            padding: 8px 42px 8px 14px;
            font-size: 0.95rem;
            line-height: 1.25;
            background-image:
                linear-gradient(45deg, transparent 50%, #475569 50%),
                linear-gradient(135deg, #475569 50%, transparent 50%);
            background-position:
                calc(100% - 18px) calc(50% - 3px),
                calc(100% - 12px) calc(50% - 3px);
            background-size: 6px 6px, 6px 6px;
            background-repeat: no-repeat;
        }
        .rv-paper .rv-jo-input.rv-jo-inherited {
            color: #64748b;
            border-style: dashed;
            background-color: #f8fafc;
        }
        .rv-paper .rv-jo-carry-hint {
            display: none;
            margin-top: 4px;
            color: #64748b;
            font-size: 0.75rem;
            line-height: 1.2;
        }
        .rv-paper .rv-jo-carry-hint.is-visible {
            display: block;
        }
        .rv-paper .rv-payee-carry-hint {
            display: none;
            margin-top: 4px;
            color: #64748b;
            font-size: 0.75rem;
            line-height: 1.2;
        }
        .rv-paper .rv-payee-carry-hint.is-visible {
            display: block;
        }
        .rv-paper .rv-payee-select.rv-payee-inherited {
            color: #475569;
            border-style: dashed;
            background-color: #f8fafc;
        }
        .rv-paper .rv-deduct-ca-wrap {
            margin-top: 4px;
        }
        .rv-paper .rv-deduct-ca {
            min-height: 30px;
            padding: 4px 8px;
            font-size: 0.78rem;
        }
        .rv-paper .rv-jo-menu {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            display: none;
            max-height: 240px;
            overflow-y: auto;
            padding: 6px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.16);
            z-index: 35;
        }
        .rv-paper .rv-jo-menu.is-open {
            display: block;
        }
        .rv-paper .rv-jo-option {
            display: block;
            width: 100%;
            padding: 10px 12px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #0f172a;
            text-align: left;
            font: inherit;
            cursor: pointer;
        }
        .rv-paper .rv-jo-option:hover,
        .rv-paper .rv-jo-option:focus {
            background: #eff6ff;
            color: #1d4ed8;
            outline: none;
        }
        .rv-paper .rv-jo-option-meta {
            display: block;
            margin-top: 2px;
            color: #64748b;
            font-size: 0.84em;
        }
        .rv-paper .rv-jo-empty {
            padding: 10px 12px;
            color: #64748b;
            font-size: 0.92em;
        }
        .rv-paper .rv-description-input {
            padding-right: 34px;
            background-image:
                linear-gradient(45deg, transparent 50%, #94a3b8 50%),
                linear-gradient(135deg, #94a3b8 50%, transparent 50%);
            background-position:
                calc(100% - 18px) calc(50% - 3px),
                calc(100% - 12px) calc(50% - 3px);
            background-size: 6px 6px, 6px 6px;
            background-repeat: no-repeat;
        }
        .rv-paper .rv-description-input[list]::-webkit-calendar-picker-indicator {
            display: none !important;
        }
        .rv-paper .rv-description-input::placeholder {
            color: #94a3b8;
        }
        .rv-paper .rv-description-combobox {
            position: relative;
        }
        .rv-paper .rv-description-input {
            position: relative;
            z-index: 2;
        }
        .rv-paper .rv-description-menu {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            display: none;
            max-height: 220px;
            overflow-y: auto;
            padding: 6px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.16);
            z-index: 30;
        }
        .rv-paper .rv-description-menu.is-open {
            display: block;
        }
        .rv-paper .rv-description-option {
            display: block;
            width: 100%;
            padding: 10px 12px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #0f172a;
            text-align: left;
            font: inherit;
            cursor: pointer;
        }
        .rv-paper .rv-description-option:hover,
        .rv-paper .rv-description-option:focus {
            background: #eff6ff;
            color: #1d4ed8;
            outline: none;
        }
        .rv-paper .rv-description-empty {
            padding: 10px 12px;
            color: #64748b;
            font-size: 0.92em;
        }
        @media print {
            @page {
                size: Letter portrait;
                margin: 0.35in;
            }
            html, body {
                background: #fff !important;
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
            .no-print { display: none !important; }
            .rv-paper-wrap {
                box-shadow: none;
                border: 0;
                border-radius: 0;
                width: 7.8in !important;
                max-width: 7.8in !important;
                margin: 0 auto !important;
            }
            .rv-paper {
                padding: 0;
                font-size: 11.5px;
                line-height: 1.18;
                min-height: 10.5in;
                margin-top: 0.18in;
            }
            .rv-paper .title { font-size: 24px; }
            .rv-paper .subtitle { font-size: 16px; }
            .rv-paper th,
            .rv-paper td { padding: 4px 6px; }
            .rv-paper .line-cell { height: 22px; }
            .rv-paper .rv-header-cell { font-size: 11px; }
            .rv-paper .rv-data-row td { min-height: 22px; }
            .rv-paper .rv-meta-box {
                padding: 7px 10px;
            }
            .rv-paper input,
            .rv-paper select {
                appearance: none !important;
                -webkit-appearance: none !important;
                -moz-appearance: none !important;
                border: 0 !important;
                box-shadow: none !important;
                outline: none !important;
                padding: 0 !important;
                background: transparent !important;
            }
            .rv-paper .rv-jo-input,
            .rv-paper .rv-jo-menu,
            .rv-paper .rv-description-input,
            .rv-paper .rv-description-menu {
                background-image: none !important;
            }
            .rv-paper .rv-group-divider {
                display: none !important;
            }
            .rv-paper .rv-no-label,
            .rv-paper .rv-no-value,
            .rv-paper .rv-no-value input,
            .rv-paper input[name="voucher_no"] {
                color: #c62828 !important;
                -webkit-text-fill-color: #c62828 !important;
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
            }
            .rv-paper input[type="date"]::-webkit-calendar-picker-indicator {
                display: none !important;
                opacity: 0 !important;
            }
        }
    </style>

    @php
        $isEdit = isset($voucher) && $voucher?->exists;
        $voucherItems = $isEdit ? $voucher->items : collect();
        $storedJoNos = $voucherItems->pluck('jo_no')->map(fn ($value) => (string) $value)->all();
        $storedClients = $voucherItems->pluck('client_name')->map(fn ($value) => (string) $value)->all();
        $storedPayees = $voucherItems->pluck('payee')->map(fn ($value) => (string) $value)->all();
        $storedDeductCa = $voucherItems->pluck('deduct_ca')->map(fn ($value) => (string) ((int) ($value ?? 1)))->all();
        $storedDescriptions = $voucherItems->pluck('description')->map(fn ($value) => (string) $value)->all();
        $storedLiqNos = $voucherItems->pluck('liq_no')->map(fn ($value) => (string) $value)->all();
        $storedRemarks = $voucherItems->pluck('remarks')->map(fn ($value) => (string) $value)->all();
        $storedAmounts = $voucherItems->pluck('amount')->map(fn ($value) => $value !== null ? number_format((float) $value, 2) : '')->all();
    @endphp

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 no-print">
        <div>
            <h2 class="mb-1">{{ $isEdit ? 'Edit Reimbursable Voucher' : 'Create Reimbursable Voucher' }}</h2>
            <p class="text-muted mb-0">{{ $isEdit ? 'Update wrong voucher numbers or line information here.' : 'Fill in entries then save. Print is available after save.' }}</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('accounting.reimbursable-vouchers.index') }}">Back</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger no-print">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rv-paper-wrap">
        <div class="rv-paper">
            <form method="POST" action="{{ $isEdit ? route('accounting.reimbursable-vouchers.update', $voucher) : route('accounting.reimbursable-vouchers.store') }}">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif
                <table>
                    <colgroup>
                        <col style="width: 14%;">
                        <col style="width: 14%;">
                        <col style="width: 13%;">
                        <col style="width: 19%;">
                        <col style="width: 10%;">
                        <col style="width: 10%;">
                        <col style="width: 14%;">
                        <col style="width: 6%;">
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
                                <div class="rv-meta-value right rv-no-value">
                                    <div class="input-group input-group-sm">
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="voucherNoInput"
                                            name="voucher_no"
                                            value="{{ old('voucher_no', $isEdit ? $voucher->voucher_no : ($nextVoucherNo ?? '8609')) }}"
                                            placeholder="{{ $nextVoucherNo ?? '8609' }}"
                                        >
                                        <button
                                            class="btn btn-outline-secondary"
                                            type="button"
                                            id="useNextVoucherNo"
                                            data-next-number="{{ $nextVoucherNo ?? '8609' }}">
                                            Use Next
                                        </button>
                                    </div>
                                    <div class="form-text text-start mt-1">{{ $isEdit ? 'You can correct this voucher number as long as it is not used by another voucher.' : 'You can type an older voucher number if it has not been recorded yet, or keep the suggested next number.' }}</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" class="rv-meta-cell">
                            <div class="rv-meta-box rv-meta-box-split">
                                <div class="rv-meta-group">
                                    <div class="rv-meta-label">Payee</div>
                                    <div class="rv-meta-value">
                                        <input type="text" name="voucher_payee" value="{{ old('voucher_payee', $isEdit ? $voucher->payee : '') }}">
                                    </div>
                                </div>
                                <div class="rv-meta-group">
                                    <div class="rv-meta-label">Ref. No.</div>
                                    <div class="rv-meta-value">
                                        <input type="text" name="voucher_ref_no" value="{{ old('voucher_ref_no', $isEdit ? $voucher->ref_no : '') }}">
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" class="rv-meta-cell">
                            <div class="rv-meta-box rv-meta-box-last">
                                <div class="rv-meta-label">Date</div>
                                <div class="rv-meta-value">
                                    <input type="date" name="voucher_date" value="{{ old('voucher_date', $isEdit ? optional($voucher->voucher_date)->format('Y-m-d') : now()->format('Y-m-d')) }}">
                                </div>
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
                        <th class="center rv-header-cell">REMARKS</th>
                        <th class="center rv-header-cell rv-action-col">ACTION</th>
                    </tr>
                    <tbody id="rvLines">
                        @php
                            $oldJoNos = old('jo_no', $storedJoNos);
                            $oldClients = old('client', $storedClients);
                            $oldPayees = old('payee', $storedPayees);
                            $oldDeductCa = old('deduct_ca', $storedDeductCa);
                            $oldDescriptions = old('description', $storedDescriptions);
                            $oldLiqNos = old('liq_no', $storedLiqNos);
                            $oldRemarks = old('remarks', $storedRemarks);
                            $oldAmounts = old('amount', $storedAmounts);
                            $rowCount = max(30, count($oldJoNos), count($oldClients), count($oldPayees), count($oldDeductCa), count($oldDescriptions), count($oldLiqNos), count($oldRemarks), count($oldAmounts));
                            $employeePayeeOptions = collect(['E-Payment', 'Trucking'])
                                ->merge(($employeePayees ?? collect())->pluck('name'))
                                ->unique()
                                ->values()
                                ->all();
                        @endphp
                        @for($i = 0; $i < $rowCount; $i++)
                            <tr class="rv-data-row">
                                <td class="rv-line-cell">
                                    <div class="rv-jo-combobox">
                                        <input type="hidden" name="jo_no[]" class="rv-jo-value" value="{{ $oldJoNos[$i] ?? '' }}">
                                        <input
                                            type="text"
                                            class="rv-jo-input"
                                            value="{{ $oldJoNos[$i] ?? '' }}"
                                            placeholder="Choose JO or leave blank to use row above"
                                            autocomplete="off"
                                        >
                                        <div class="rv-jo-carry-hint"></div>
                                        <div class="rv-jo-menu"></div>
                                    </div>
                                </td>
                                <td class="rv-line-cell"><input type="text" name="client[]" class="rv-client-input" value="{{ $oldClients[$i] ?? '' }}"></td>
                                <td class="rv-line-cell">
                                    <select name="payee[]" class="rv-payee-select">
                                        <option value="">Choose requested by</option>
                                        @foreach($employeePayeeOptions as $employeePayee)
                                            <option value="{{ $employeePayee }}" @selected(($oldPayees[$i] ?? '') === $employeePayee)>{{ $employeePayee }}</option>
                                        @endforeach
                                    </select>
                                    <div class="rv-deduct-ca-wrap">
                                        <select name="deduct_ca[]" class="rv-deduct-ca">
                                            <option value="1" @selected((string) ($oldDeductCa[$i] ?? '1') !== '0')>Deduct CA: Yes</option>
                                            <option value="0" @selected((string) ($oldDeductCa[$i] ?? '1') === '0')>Deduct CA: No</option>
                                        </select>
                                    </div>
                                    <div class="rv-payee-carry-hint"></div>
                                </td>
                                <td class="rv-line-cell">
                                    <div class="rv-description-combobox">
                                        <input
                                            type="text"
                                            name="description[]"
                                            class="rv-description-input"
                                            value="{{ $oldDescriptions[$i] ?? '' }}"
                                            placeholder="Type or choose a description"
                                            autocomplete="off"
                                        >
                                        <div class="rv-description-menu"></div>
                                    </div>
                                </td>
                                <td class="right rv-line-cell"><input type="text" inputmode="decimal" class="right rv-amount" name="amount[]" value="{{ $oldAmounts[$i] ?? '' }}" placeholder="0.00"></td>
                                <td class="rv-line-cell"><input type="text" name="liq_no[]" value="{{ $oldLiqNos[$i] ?? '' }}"></td>
                                <td class="rv-line-cell"><input type="text" name="remarks[]" value="{{ $oldRemarks[$i] ?? '' }}" placeholder="Cost Sheet remarks"></td>
                                <td class="rv-line-cell center rv-action-col">
                                    <button type="button" class="btn btn-outline-danger btn-sm rv-delete-line" title="Delete this row">×</button>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                    <tr>
                        <td colspan="5"><strong>TOTAL</strong></td>
                        <td class="right"><strong id="rvTotal">0.00</strong></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr class="rv-footer-row">
                        <td colspan="2" class="rv-words-label">AMOUNTING IN WORDS</td>
                        <td colspan="6" class="rv-words-value"><input type="text" id="rvAmountWords" name="amount_in_words" readonly value="{{ old('amount_in_words', $isEdit ? $voucher->amount_in_words : '') }}"></td>
                    </tr>
                    <tr class="rv-footer-row rv-sign-row">
                        <td colspan="2" class="rv-sign-label">PREPARED BY</td>
                        <td colspan="2" class="rv-sign-label center">APPROVED BY</td>
                        <td colspan="4" class="rv-sign-label center">RECEIVED PAYMENT:</td>
                    </tr>
                    <tr class="rv-footer-row rv-sign-value-row rv-footer-row-last">
                        <td colspan="2" class="center">M.A.S / D.L.C / K.A.P / R.J.R</td>
                        <td colspan="2" class="center">A.P.M</td>
                        <td colspan="4"></td>
                    </tr>
                </table>

                <div class="d-flex gap-2 mt-3 rv-controls no-print">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="rvAddLine">Add Line</button>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="rvRemoveLine">Remove Last Line</button>
                    <button type="submit" class="btn btn-primary btn-sm">{{ $isEdit ? 'Update Voucher' : 'Save Voucher' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const lines = document.getElementById('rvLines');
        const totalEl = document.getElementById('rvTotal');
        const wordsEl = document.getElementById('rvAmountWords');
        const voucherNoInput = document.getElementById('voucherNoInput');
        const useNextVoucherNoButton = document.getElementById('useNextVoucherNo');
        const voucherJobOrders = @json(($voucherJobOrders ?? collect())->values());
        const employeePayees = @json($employeePayeeOptions ?? []);
        const escapeHtml = (value) => String(value || '').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        }[character]));
        const employeePayeeOptionsHtml = ['<option value="">Choose requested by</option>']
            .concat(employeePayees.map((name) => `<option value="${escapeHtml(name)}">${escapeHtml(name)}</option>`))
            .join('');
        const fixedDescriptionOptions = [
            'LESS: ADVANCES',
            'LESS: PENALTY',
            'A I S L',
            'NTC',
            'CUSTOMS FORMS & STAMPS',
            'DOCUMENTATION AND PHOTOCOPY',
            'NOTARIAL FEE & INTERCOMMERCE CHARGE',
            'HANDLING FEE',
            'ARRASTRE CHARGE',
            'WHARFAGE DUE',
            'BANK CHARGE',
            'BREAKBULK FEE',
            'BROKERAGE FEE',
            'LESS WITHHOLDING TAX',
            'CFS CHARGES',
            'CHASSIS RENTAL',
            'CLIENT\'S COMMISSION',
            'CUSTOMS FACILITATION',
            'DUTIES AND TAXES',
            'DEMURRAGE FEE',
            'EXTREME FREIGHT BILL',
            'FCL CHARGES (THC, BL FEE, ETC.)',
            'CONTAINER DEPOSIT',
            'HUSTLING',
            'L O L O & STORAGE',
            'LCL CHARGES',
            'NOTARIAL',
            'PROCESSING EXPENSES',
            'PROCESSING - NTC',
            'PROCESSING - IAS/AOCG',
            'PROCESSING - ATRIG',
            'PROCESSING - WITHDRAWAL',
            'PROCESSING.',
            'ROYALTY FEE',
            'STORAGE FEE',
            'SURETY BOND',
            'T A B S',
            'TRUCKING CHARGES',
            'TRUCKING - STRIPPING',
            'TRUCKING - BOOMTRUCK',
        ];

        const normalizeDescription = (value) => {
            const raw = String(value || '').trim().toUpperCase();
            if (!raw) return '';

            const stripped = raw.replace(/[^A-Z0-9]+/g, '');
            if (!stripped) return '';

            const aliases = {
                AISL: 'AISL',
                AISLCONTAINERCLEARANCE: 'AISL',
                CONTAINERCLEARANCE: 'AISL',
                NTC: 'NTC',
                CUSTOMSFORMSSTAMPS: 'CUSTOMSFORMSSTAMPS',
                CUSTOMSFORMS: 'CUSTOMSFORMSSTAMPS',
                DOCUMENTATIONPHOTOCOPY: 'DOCUMENTATIONANDPHOTOCOPY',
                DOCUMENTATIONANDPHOTOCOPY: 'DOCUMENTATIONANDPHOTOCOPY',
                NOTARIALFEEINTERCOMMERCECHARGE: 'NOTARIALFEEANDINTERCOMMERCECHARGE',
                NOTARIALFEEANDINTERCOMMERCECHARGE: 'NOTARIALFEEANDINTERCOMMERCECHARGE',
                NOTARIALSTAMP: 'NOTARIAL',
                HANDLINGFEE: 'HANDLINGFEE',
                ARRASTRECHARGE: 'ARRASTRECHARGE',
                ARRASTRECHARGES: 'ARRASTRECHARGE',
                WHARFAGEFEE: 'WHARFAGEDUE',
                WHARFAGEDUE: 'WHARFAGEDUE',
                BANKCHARGE: 'BANKCHARGE',
                BREAKBULKFEE: 'BREAKBULKFEE',
                BROKERAGEFEE: 'BROKERAGEFEE',
                BROKERAGEFEEASPERCAO12001: 'BROKERAGEFEE',
                WITHHOLDINGTAX: 'LESSWITHHOLDINGTAX',
                LESSWITHHOLDINGTAX: 'LESSWITHHOLDINGTAX',
                CFSCHARGES: 'CFSCHARGES',
                CHASSISRENTAL: 'CHASSISRENTAL',
                CLIENTSCOMMISSION: 'CLIENTSCOMMISSION',
                CLIENTCOMMISSION: 'CLIENTSCOMMISSION',
                CUSTOMSFACILITATION: 'PROCESSINGEXPENSES',
                DUTIESANDTAXES: 'DUTIESANDTAXES',
                DEMURRAGEFEE: 'DEMURRAGEFEE',
                DEMURRAGECHARGES: 'DEMURRAGEFEE',
                EXTREMEFREIGHTBILL: 'EXTREMEFREIGHTBILL',
                FCLCHARGES: 'FCLCHARGESTHCBLFEEETC',
                FCLCHARGESTHCBLFEEETC: 'FCLCHARGESTHCBLFEEETC',
                FREIGHTLCLCHARGESTHCBREAKBULKFEE: 'LCLCHARGES',
                LCLTHCBREAKBULKFEE: 'LCLCHARGES',
                CONTAINERDEPOSIT: 'CONTAINERDEPOSIT',
                HUSTLING: 'HUSTLING',
                LOLOSTORAGE: 'LOLOANDSTORAGE',
                LOLOANDSTORAGE: 'LOLOANDSTORAGE',
                LOLOSTORAGEFEE: 'LOLOANDSTORAGE',
                LOLOANDSTORAGEFEE: 'LOLOANDSTORAGE',
                LCLCHARGES: 'LCLCHARGES',
                NOTARIAL: 'NOTARIAL',
                PROCESSINGEXPENSES: 'PROCESSINGEXPENSES',
                PROCESSINGFACILITATIONEXPENSES: 'PROCESSINGEXPENSES',
                PROCESSINGNTC: 'PROCESSINGNTC',
                PROCESSINGIASAOCG: 'PROCESSINGIASAOCG',
                PROCESSINGATRIG: 'PROCESSINGATRIG',
                PROCESSINGWITHDRAWAL: 'PROCESSINGWITHDRAWAL',
                PROCESSING: 'PROCESSING',
                ROYALTYFEE: 'ROYALTYFEE',
                STORAGEFEE: 'STORAGEFEE',
                SURETYBOND: 'SURETYBOND',
                SURETYBONDINSURANCEPREMIUM: 'SURETYBOND',
                TABS: 'TABS',
                TABSTERMINALAPPOINTMENTBOOKINGSYSTEM: 'TABS',
                TRUCKING: 'TRUCKINGCHARGES',
                TRUCKINGCHARGES: 'TRUCKINGCHARGES',
                TRUCKINGDELIVERYCHARGES: 'TRUCKINGCHARGES',
                TRUCKINGSTRIPPING: 'TRUCKINGCHARGES',
                TRUCKINGBOOMTRUCK: 'TRUCKINGCHARGES',
                EMPTYRETURN: 'EMPTYRETURN',
                RETURNOFEMPTYCONTAINERFEE: 'EMPTYRETURN',
                OTHERS: 'OTHERS',
            };

            return aliases[stripped] || stripped;
        };

        const closeAllDescriptionMenus = (exceptRow = null) => {
            document.querySelectorAll('.rv-description-menu').forEach((menu) => {
                if (!exceptRow || !exceptRow.contains(menu)) {
                    menu.classList.remove('is-open');
                }
            });
        };

        const closeAllJoMenus = (exceptRow = null) => {
            document.querySelectorAll('.rv-jo-menu').forEach((menu) => {
                if (!exceptRow || !exceptRow.contains(menu)) {
                    menu.classList.remove('is-open');
                }
            });
        };

        const previousVoucherRow = (row) => {
            let previous = row?.previousElementSibling || null;

            while (previous && !previous.classList.contains('rv-data-row')) {
                previous = previous.previousElementSibling;
            }

            return previous;
        };

        const findVoucherJobOrder = (joNo) => voucherJobOrders.find((option) => option.jo_no === joNo) || null;

        const getEffectiveJoOptionForRow = (row) => {
            let currentRow = row;

            while (currentRow) {
                const joNo = currentRow.querySelector('.rv-jo-value')?.value || '';
                if (joNo) {
                    return findVoucherJobOrder(joNo);
                }
                currentRow = previousVoucherRow(currentRow);
            }

            return null;
        };

        const getOwnPayeeForRow = (row) => row?.querySelector('.rv-payee-select')?.value || '';

        const getEffectivePayeeForRow = (row) => {
            let currentRow = row;

            while (currentRow) {
                const payee = getOwnPayeeForRow(currentRow);
                if (payee) {
                    return payee;
                }
                currentRow = previousVoucherRow(currentRow);
            }

            return '';
        };

        const initializePayeeInheritance = () => {
            document.querySelectorAll('#rvLines .rv-data-row').forEach((row) => {
                const select = row.querySelector('.rv-payee-select');
                if (!select || select.dataset.payeeMode) return;

                const inheritedPayee = getEffectivePayeeForRow(previousVoucherRow(row));
                select.dataset.payeeMode = (!select.value || (inheritedPayee && select.value === inheritedPayee))
                    ? 'inherited'
                    : 'manual';
            });
        };

        const syncInheritedPayees = () => {
            let previousPayee = '';

            document.querySelectorAll('#rvLines .rv-data-row').forEach((row) => {
                const select = row.querySelector('.rv-payee-select');
                const hint = row.querySelector('.rv-payee-carry-hint');
                if (!select) return;

                if (!select.dataset.payeeMode) {
                    select.dataset.payeeMode = 'inherited';
                }

                const shouldInherit = select.dataset.payeeMode === 'inherited' || !select.value;

                if (shouldInherit && previousPayee) {
                    select.value = previousPayee;
                    select.dataset.payeeMode = 'inherited';
                    select.classList.add('rv-payee-inherited');
                    if (hint) {
                        hint.textContent = `Same as above: ${previousPayee}`;
                        hint.classList.add('is-visible');
                    }
                } else {
                    select.classList.toggle('rv-payee-inherited', select.dataset.payeeMode === 'inherited' && !!previousPayee);
                    if (hint) {
                        hint.textContent = '';
                        hint.classList.remove('is-visible');
                    }
                }

                previousPayee = select.value || previousPayee;
            });
        };

        const updateJoCarryHints = () => {
            document.querySelectorAll('#rvLines tr').forEach((row) => {
                const joValueInput = row.querySelector('.rv-jo-value');
                const joInput = row.querySelector('.rv-jo-input');
                const hint = row.querySelector('.rv-jo-carry-hint');
                if (!joInput || !hint) return;

                const ownJoNo = joValueInput?.value || '';
                const inheritedOption = ownJoNo ? null : getEffectiveJoOptionForRow(previousVoucherRow(row));

                if (!ownJoNo && inheritedOption) {
                    joInput.classList.add('rv-jo-inherited');
                    joInput.placeholder = inheritedOption.jo_no || 'Same as above';
                    hint.textContent = `Same as above: ${inheritedOption.jo_no || ''}`;
                    hint.classList.add('is-visible');
                } else {
                    joInput.classList.remove('rv-jo-inherited');
                    joInput.placeholder = 'Choose JO or leave blank to use row above';
                    hint.textContent = '';
                    hint.classList.remove('is-visible');
                }
            });
        };

        const renderJoGroups = () => {
            lines?.querySelectorAll('.rv-group-divider').forEach((divider) => divider.remove());
            lines?.querySelectorAll('.rv-data-row').forEach((row) => {
                row.classList.remove('rv-row-under-group');
            });

            if (!lines) return;

            let previousGroupKey = null;
            Array.from(lines.querySelectorAll('.rv-data-row')).forEach((row) => {
                const joOption = getEffectiveJoOptionForRow(row);
                const joValueInput = row.querySelector('.rv-jo-value');
                const clientInput = row.querySelector('.rv-client-input');
                const liqInput = row.querySelector('input[name="liq_no[]"]');
                const ownJoNo = joValueInput?.value || '';
                const effectiveJoNo = joOption?.jo_no || '';
                const effectiveClient = (clientInput?.value || joOption?.client || '').trim();
                const effectiveLiqNo = (liqInput?.value || joOption?.prefill_liq_no || '').trim();
                const groupKey = `${effectiveJoNo}|${effectiveClient}|${effectiveLiqNo}`;

                row.classList.add('rv-row-under-group');

                if (!effectiveJoNo || groupKey === previousGroupKey) {
                    return;
                }

                const divider = document.createElement('tr');
                divider.className = 'rv-group-divider';
                divider.innerHTML = `
                    <td colspan="8">
                        <div class="rv-group-banner">
                            <span class="rv-group-badge">${effectiveJoNo}</span>
                            <span class="rv-group-meta"><strong>Client:</strong> ${effectiveClient || '-'}</span>
                            <span class="rv-group-meta"><strong>Liq. No.:</strong> ${effectiveLiqNo || '-'}</span>
                            ${ownJoNo ? '' : '<span class="rv-group-meta">Continuing same JO group</span>'}
                            <span class="rv-group-actions">
                                <button type="button" class="rv-add-group-row" data-jo-no="${effectiveJoNo}">+ Row</button>
                            </span>
                        </div>
                    </td>
                `;

                lines.insertBefore(divider, row);
                previousGroupKey = groupKey;
            });
        };

        const syncRowFromJoOption = (row, joOption, { preserveClient = true } = {}) => {
            if (!row) return;

            const joValueInput = row.querySelector('.rv-jo-value');
            const joDisplayInput = row.querySelector('.rv-jo-input');
            const clientInput = row.querySelector('.rv-client-input');
            const descriptionInput = row.querySelector('.rv-description-input');
            const liqInput = row.querySelector('input[name="liq_no[]"]');

            if (joValueInput) {
                joValueInput.value = joOption?.jo_no || '';
            }
            if (joDisplayInput) {
                joDisplayInput.value = joOption?.jo_no || '';
            }

            if (clientInput && joOption && (!preserveClient || !clientInput.value || clientInput.value.trim() === '')) {
                clientInput.value = joOption.client || '';
            }
            if (liqInput && joOption?.prefill_liq_no) {
                liqInput.value = joOption.prefill_liq_no;
            }
            if (descriptionInput && joOption?.prefill_description && (!descriptionInput.value || descriptionInput.value.trim() === '')) {
                descriptionInput.value = joOption.prefill_description;
            }
        };

        const renderJoMenu = (row, query = '') => {
            if (!row) return;

            const menu = row.querySelector('.rv-jo-menu');
            if (!menu) return;

            const normalizedQuery = String(query || '').trim().toUpperCase();
            const options = voucherJobOrders.filter((option) => {
                const haystack = `${option.jo_no || ''} ${option.client || ''} ${option.label || ''}`.toUpperCase();
                return normalizedQuery === '' || haystack.includes(normalizedQuery);
            });

            menu.innerHTML = '';

            if (!options.length) {
                const emptyState = document.createElement('div');
                emptyState.className = 'rv-jo-empty';
                emptyState.textContent = 'No matching job orders.';
                menu.appendChild(emptyState);
                return;
            }

            options.forEach((option) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'rv-jo-option';
                button.dataset.joNo = option.jo_no || '';
                button.innerHTML = `${option.jo_no || ''}<span class="rv-jo-option-meta">${option.client || ''}</span>`;
                menu.appendChild(button);
            });
        };

        const openJoMenu = (row, query = '') => {
            if (!row) return;
            renderJoMenu(row, query);
            closeAllJoMenus(row);
            row.querySelector('.rv-jo-menu')?.classList.add('is-open');
        };

        const getDescriptionOptionsForRow = (row) => {
            return row ? fixedDescriptionOptions : [];
        };

        const validateDescriptionUniqueness = (row) => {
            const input = row?.querySelector('.rv-description-input');
            if (!input) return;
            input.setCustomValidity('');
        };

        const refreshAllDescriptionMenus = () => {
            document.querySelectorAll('#rvLines .rv-data-row').forEach((row) => {
                validateDescriptionUniqueness(row);
                renderDescriptionMenu(row, row.querySelector('.rv-description-input')?.value || '');
            });
        };

        const renderDescriptionMenu = (row, query = '') => {
            if (!row) return;

            const menu = row.querySelector('.rv-description-menu');
            if (!menu) return;

            const normalizedQuery = String(query || '').trim().toUpperCase();
            const options = (getDescriptionOptionsForRow(row) || [])
                .filter((option) => normalizedQuery === '' || option.toUpperCase().includes(normalizedQuery));

            menu.innerHTML = '';

            if (!options.length) {
                const emptyState = document.createElement('div');
                emptyState.className = 'rv-description-empty';
                emptyState.textContent = normalizedQuery === '' ? 'No descriptions available for this JO yet.' : 'No matching descriptions.';
                menu.appendChild(emptyState);
                return;
            }

            options.forEach((optionValue) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'rv-description-option';
                button.textContent = optionValue;
                button.dataset.value = optionValue;
                menu.appendChild(button);
            });
        };

        const openDescriptionMenu = (row, query = '') => {
            if (!row) return;
            renderDescriptionMenu(row, query);
            closeAllDescriptionMenus(row);
            row.querySelector('.rv-description-menu')?.classList.add('is-open');
        };

        const numberToWords = (num) => {
            if (!Number.isFinite(num)) return '';
            const isNegative = num < 0;
            num = Math.abs(num);
            const ones = ['Zero','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
            const tens = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
            const scales = ['','Thousand','Million','Billion'];
            const chunkToWords = (n) => {
                const hundred = Math.floor(n / 100);
                const rest = n % 100;
                const parts = [];
                if (hundred) parts.push(`${ones[hundred]} Hundred`);
                if (rest) {
                    if (rest < 20) parts.push(ones[rest]);
                    else parts.push(tens[Math.floor(rest / 10)] + (rest % 10 ? ` ${ones[rest % 10]}` : ''));
                }
                return parts.join(' ');
            };
            if (num === 0) return 'Zero Pesos Only';
            let whole = Math.floor(num);
            let scaleIndex = 0;
            const words = [];
            while (whole > 0) {
                const chunk = whole % 1000;
                if (chunk) words.unshift(`${chunkToWords(chunk)}${scales[scaleIndex] ? ' ' + scales[scaleIndex] : ''}`.trim());
                whole = Math.floor(whole / 1000);
                scaleIndex++;
            }
            const cents = Math.round((num - Math.floor(num)) * 100);
            const centsText = cents ? ` and ${cents}/100` : '';
            const phrase = `${words.join(' ')}${centsText} Pesos Only`;
            return isNegative ? `Negative ${phrase}` : phrase;
        };

        const refresh = () => {
            let total = 0;
            document.querySelectorAll('#rvLines .rv-data-row').forEach((row) => {
                const amountInput = row.querySelector('.rv-amount');
                const descriptionInput = row.querySelector('input[name="description[]"]');
                const amount = parseCurrency(amountInput?.value || '');
                const description = String(descriptionInput?.value || '').toUpperCase();
                const isPenalty = description.includes('PENALTY');
                total += isPenalty ? -Math.abs(amount) : amount;
            });
            totalEl.textContent = total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            wordsEl.value = numberToWords(total).toUpperCase();
        };

        const parseCurrency = (value) => {
            const normalized = String(value || '').replace(/,/g, '');
            return Number.parseFloat(normalized) || 0;
        };

        const formatCurrencyInput = (input) => {
            const rawValue = String(input.value || '').replace(/[^\d.]/g, '');
            const [whole = '', ...decimalParts] = rawValue.split('.');
            const decimals = decimalParts.join('').slice(0, 2);
            const formattedWhole = whole.replace(/^0+(?=\d)/, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            input.value = decimals.length || rawValue.includes('.')
                ? `${formattedWhole || '0'}.${decimals}`
                : formattedWhole;
        };

        const createVoucherLineRow = () => {
            const tr = document.createElement('tr');
            tr.className = 'rv-data-row';
            tr.innerHTML = `
                <td class="rv-line-cell">
                    <div class="rv-jo-combobox">
                        <input type="hidden" name="jo_no[]" class="rv-jo-value">
                        <input type="text" class="rv-jo-input" placeholder="Choose JO or leave blank to use row above" autocomplete="off">
                        <div class="rv-jo-carry-hint"></div>
                        <div class="rv-jo-menu"></div>
                    </div>
                </td>
                <td class="rv-line-cell"><input type="text" name="client[]" class="rv-client-input"></td>
                <td class="rv-line-cell">
                    <select name="payee[]" class="rv-payee-select" data-payee-mode="inherited">${employeePayeeOptionsHtml}</select>
                    <div class="rv-deduct-ca-wrap">
                        <select name="deduct_ca[]" class="rv-deduct-ca">
                            <option value="1" selected>Deduct CA: Yes</option>
                            <option value="0">Deduct CA: No</option>
                        </select>
                    </div>
                    <div class="rv-payee-carry-hint"></div>
                </td>
                <td class="rv-line-cell">
                    <div class="rv-description-combobox">
                        <input type="text" name="description[]" class="rv-description-input" placeholder="Type or choose a description" autocomplete="off">
                        <div class="rv-description-menu"></div>
                    </div>
                </td>
                <td class="right rv-line-cell"><input type="text" inputmode="decimal" class="right rv-amount" name="amount[]" placeholder="0.00"></td>
                <td class="rv-line-cell"><input type="text" name="liq_no[]"></td>
                <td class="rv-line-cell"><input type="text" name="remarks[]" placeholder="Cost Sheet remarks"></td>
                <td class="rv-line-cell center rv-action-col"><button type="button" class="btn btn-outline-danger btn-sm rv-delete-line" title="Delete this row">×</button></td>
            `;

            return tr;
        };

        const insertVoucherLine = ({ afterRow = null, forceJoOption = null } = {}) => {
            if (!lines) return null;

            const tr = createVoucherLineRow();
            if (afterRow && afterRow.parentNode === lines) {
                afterRow.insertAdjacentElement('afterend', tr);
            } else {
                lines.appendChild(tr);
            }

            const inheritedJo = forceJoOption || getEffectiveJoOptionForRow(previousVoucherRow(tr));
            if (inheritedJo) {
                syncRowFromJoOption(tr, inheritedJo, { preserveClient: false });

                if (!forceJoOption) {
                    const joValueInput = tr.querySelector('.rv-jo-value');
                    const joDisplayInput = tr.querySelector('.rv-jo-input');
                    if (joValueInput) joValueInput.value = '';
                    if (joDisplayInput) joDisplayInput.value = '';
                }
            }

            renderJoMenu(tr);
            syncInheritedPayees();
            refreshAllDescriptionMenus();
            updateJoCarryHints();
            renderJoGroups();

            return tr;
        };

        useNextVoucherNoButton?.addEventListener('click', () => {
            if (!voucherNoInput) return;

            voucherNoInput.value = useNextVoucherNoButton.dataset.nextNumber || '';
            voucherNoInput.focus();
            voucherNoInput.select();
        });

        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('rv-amount')) {
                formatCurrencyInput(e.target);
                refresh();
            }
        });

        document.getElementById('rvAddLine')?.addEventListener('click', () => {
            insertVoucherLine();
        });

        document.getElementById('rvRemoveLine')?.addEventListener('click', () => {
            if (!lines || lines.children.length <= 1) return;
            lines.removeChild(lines.lastElementChild);
            syncInheritedPayees();
            updateJoCarryHints();
            refreshAllDescriptionMenus();
            renderJoGroups();
            refresh();
        });

        document.querySelectorAll('#rvLines .rv-data-row').forEach((row) => {
            const joNo = row.querySelector('.rv-jo-value')?.value || '';
            syncRowFromJoOption(row, findVoucherJobOrder(joNo), { preserveClient: true });
            renderJoMenu(row);
        });
        initializePayeeInheritance();
        syncInheritedPayees();
        updateJoCarryHints();
        refreshAllDescriptionMenus();
        renderJoGroups();

        document.addEventListener('focusin', (event) => {
            const input = event.target.closest('.rv-jo-input');
            if (!input) return;
            const row = input.closest('tr');
            openJoMenu(row, input.value);
        });

        document.addEventListener('focusin', (event) => {
            const input = event.target.closest('.rv-description-input');
            if (!input) return;
            const row = input.closest('tr');
            openDescriptionMenu(row, input.value);
        });

        document.addEventListener('input', (event) => {
            const joInput = event.target.closest('.rv-jo-input');
            if (joInput) {
                const row = joInput.closest('tr');
                const joValueInput = row?.querySelector('.rv-jo-value');
                if (joValueInput) {
                    joValueInput.value = '';
                }
                updateJoCarryHints();
                openJoMenu(row, joInput.value);
                renderDescriptionMenu(row, row?.querySelector('.rv-description-input')?.value || '');
                renderJoGroups();
                return;
            }

            const input = event.target.closest('.rv-description-input');
            if (!input) return;
            const row = input.closest('tr');
            validateDescriptionUniqueness(row);
            openDescriptionMenu(row, input.value);
            refreshAllDescriptionMenus();
        });

        document.addEventListener('click', (event) => {
            const deleteRowButton = event.target.closest('.rv-delete-line');
            if (deleteRowButton) {
                const row = deleteRowButton.closest('.rv-data-row');
                if (!row || !lines) return;

                const allRows = Array.from(lines.querySelectorAll('.rv-data-row'));
                if (allRows.length <= 1) {
                    row.querySelectorAll('input').forEach((input) => {
                        if (input.type !== 'hidden') input.value = '';
                    });
                    row.querySelectorAll('select').forEach((select) => {
                        select.selectedIndex = 0;
                    });
                } else {
                    row.remove();
                }

                syncInheritedPayees();
                updateJoCarryHints();
                refreshAllDescriptionMenus();
                renderJoGroups();
                refresh();
                return;
            }

            const addGroupRowButton = event.target.closest('.rv-add-group-row');
            if (addGroupRowButton) {
                const divider = addGroupRowButton.closest('.rv-group-divider');
                let cursor = divider?.nextElementSibling || null;
                let lastGroupRow = null;

                while (cursor && !cursor.classList.contains('rv-group-divider')) {
                    if (cursor.classList.contains('rv-data-row')) {
                        lastGroupRow = cursor;
                    }
                    cursor = cursor.nextElementSibling;
                }

                const selectedJo = findVoucherJobOrder(addGroupRowButton.dataset.joNo || '');
                const inserted = insertVoucherLine({ afterRow: lastGroupRow, forceJoOption: selectedJo });
                inserted?.querySelector('.rv-description-input')?.focus();
                refresh();
                return;
            }

            const joOption = event.target.closest('.rv-jo-option');
            if (joOption) {
                const row = joOption.closest('tr');
                const selected = findVoucherJobOrder(joOption.dataset.joNo || '');
                syncRowFromJoOption(row, selected, { preserveClient: false });
                renderDescriptionMenu(row, row?.querySelector('.rv-description-input')?.value || '');
                closeAllJoMenus();
                syncInheritedPayees();
                updateJoCarryHints();
                renderJoGroups();
                refresh();
                return;
            }

            const option = event.target.closest('.rv-description-option');
            if (option) {
                const row = option.closest('tr');
                const input = row?.querySelector('.rv-description-input');
                if (input) {
                    input.value = option.dataset.value || option.textContent || '';
                    validateDescriptionUniqueness(row);
                    refreshAllDescriptionMenus();
                    closeAllDescriptionMenus();
                }
                return;
            }

            if (!event.target.closest('.rv-jo-combobox')) {
                closeAllJoMenus();
            }

            if (!event.target.closest('.rv-description-combobox')) {
                closeAllDescriptionMenus();
            }
        });

        document.addEventListener('change', (event) => {
            const payeeSelect = event.target.closest('.rv-payee-select');
            if (!payeeSelect) return;

            payeeSelect.dataset.payeeMode = payeeSelect.value ? 'manual' : 'inherited';
            syncInheritedPayees();
        });
        refresh();
    })();
</script>
@endpush
