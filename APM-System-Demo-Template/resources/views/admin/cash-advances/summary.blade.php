@extends('layouts.employeehub')

@section('content')
    <style>
        .jo-table-wrap {
            max-height: 72vh;
            overflow: auto;
        }
        .jo-summary-wrap {
            position: relative;
        }
        .table-side-action {
            position: absolute;
            top: 12px;
            z-index: 4;
        }
        .table-side-action.left {
            left: 10px;
        }
        .table-side-action.right {
            right: 10px;
        }
        .employee-filter-wrap {
            min-width: 260px;
        }
        .jo-table-wrap thead th {
            position: sticky;
            top: 0;
            z-index: 2;
        }
        .jo-table-wrap thead tr:nth-child(2) th {
            top: 38px;
            z-index: 3;
        }
        .jo-table-wrap .form-control-sm {
            min-height: 30px;
            padding-top: 0.2rem;
            padding-bottom: 0.2rem;
        }
        @media (max-width: 768px) {
            .jo-table-wrap {
                max-height: 64vh;
            }
            .table-side-action {
                position: static;
            }
        }
    </style>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">CA Summary Reports</h2>
            <p class="text-muted mb-0">Cash Advance vs Liquidation summary per employee.</p>
        </div>
        <form method="GET" action="{{ route('accounting.cash-advances.summary', [], false) }}" class="d-flex flex-wrap gap-2 align-items-end" id="ca-filter-form">
            <div class="employee-filter-wrap">
                <label class="form-label mb-0">Employee</label>
                <input type="hidden" name="user_id" id="employee-id-hidden" value="{{ $userId ?? '' }}">
                <input
                    type="text"
                    id="employee-combobox"
                    name="employee_name"
                    list="employee-options"
                    class="form-control"
                    placeholder="Type or select employee..."
                    value="{{ $selectedEmployee?->name ?? '' }}"
                    autocomplete="off"
                >
                <datalist id="employee-options">
                    @foreach($employees as $employee)
                        <option value="{{ $employee->name }}" data-id="{{ $employee->id }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div>
                <label class="form-label mb-0">From</label>
                <input type="date" name="from_date" class="form-control" value="{{ $fromDate ?? '' }}">
            </div>
            <div>
                <label class="form-label mb-0">To</label>
                <input type="date" name="to_date" class="form-control" value="{{ $toDate ?? '' }}">
            </div>
            <div>
                <label class="form-label mb-0">Month</label>
                <select class="form-select" name="month">
                    <option value="">Any</option>
                    @foreach(range(1, 12) as $monthValue)
                        <option value="{{ $monthValue }}" @selected((string) $monthValue === (string) ($month ?? ''))>{{ date('F', mktime(0, 0, 0, $monthValue, 1)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label mb-0">Year</label>
                <input type="number" name="year" class="form-control" min="2000" max="2100" value="{{ $year ?? '' }}" placeholder="YYYY">
            </div>
            <div>
                <a class="btn btn-outline-secondary" href="{{ route('accounting.cash-advances.summary', [], false) }}">Reset</a>
            </div>
        </form>
    </div>

    @if(!$selectedEmployee)
        <div class="eh-card p-4">
            <div class="text-muted">Select an employee to view the CA summary.</div>
        </div>
    @else
        <div class="eh-card p-3 mb-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h6 class="mb-0">CA Actions</h6>
                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#addCaPanel" aria-expanded="false" aria-controls="addCaPanel">
                    Add New CA
                </button>
            </div>
            <div class="collapse mt-3" id="addCaPanel">
                <form method="POST" action="{{ route('accounting.cash-advances.store', [], false) }}">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $selectedEmployee->id }}">
                    <input type="hidden" name="ca_type" value="jo">
                    <input type="hidden" name="mark_as_paid" value="1">
                    <div class="row g-2 mb-2">
                        <div class="col-md-2">
                            <label class="form-label mb-1">CA No.</label>
                            <input class="form-control form-control-sm" name="ca_no" placeholder="Optional">
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label mb-0">JO Entries</label>
                        <button class="btn btn-sm btn-outline-primary" type="button" id="summary-add-ca-item">Add Another JO</button>
                    </div>
                    <div id="summary-ca-items">
                        <div class="row g-2 mb-2 summary-ca-item">
                            <div class="col-md-3">
                                <label class="form-label mb-1">J.O No.</label>
                                <input class="form-control form-control-sm" name="items[0][jo_number]" placeholder="e.g. 12250">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label mb-1">Reason</label>
                                <input class="form-control form-control-sm" name="items[0][reason]" placeholder="If no J.O No.">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label mb-1">Amount</label>
                                <input class="form-control form-control-sm text-end money-input" type="text" inputmode="decimal" name="items[0][amount]" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-sm btn-outline-danger w-100 remove-summary-ca-item" type="button">Remove</button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <button class="btn btn-sm btn-primary" type="submit">Save New CA for {{ $selectedEmployee->name }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-6">
                <div class="eh-card p-3 h-100">
                    <div class="d-flex flex-wrap gap-3">
                        <div>
                            <div class="text-muted small">Employee</div>
                            <div class="fw-semibold">{{ $selectedEmployee->name }}</div>
                        </div>
                        <div>
                            <div class="text-muted small">JO CA Total</div>
                            <div class="fw-semibold">PHP <span id="total-ca">{{ number_format($totalsJo['ca_total'], 2) }}</span></div>
                        </div>
                        <div>
                            <div class="text-muted small">JO Liquidation</div>
                            <div class="fw-semibold">PHP <span id="total-liquidation">{{ number_format($totalsJo['liquidation_total'], 2) }}</span></div>
                        </div>
                        <div>
                            <div class="text-muted small">JO Balance</div>
                            <div class="fw-semibold">PHP <span id="total-balance">{{ number_format($totalsJo['balance'], 2) }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="eh-card p-3 h-100">
                    <div class="d-flex flex-wrap gap-3">
                        <div>
                            <div class="text-muted small">Personal CA Total</div>
                            <div class="fw-semibold">PHP <span id="personal-balance-total">{{ number_format($totalsPersonal['balance'], 2) }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="eh-card p-3 mb-3" id="jo-summary">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="mb-0">JO Cash Advances</h5>
            </div>
            <div class="collapse mb-3" id="addLiquidationPanel">
                <form method="POST" action="{{ route('accounting.cash-advances.liquidations.store', [], false) }}" class="border rounded p-3 bg-light-subtle">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label mb-1">Apply To CA</label>
                            <select class="form-select form-select-sm" name="cash_advance_request_id" required>
                                <option value="">Select CA</option>
                                @foreach($joGroups as $group)
                                    @php
                                        $caLabel = trim(($group['request']->ca_no ?? '') . ' ' . ($group['request']->created_at?->format('M d, Y') ?? ''));
                                    @endphp
                                    <option value="{{ $group['request']->id }}">{{ $caLabel !== '' ? $caLabel : ('CA #' . $group['request']->id) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1">Date</label>
                            <input type="date" class="form-control form-control-sm" name="date" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1">Ref No.</label>
                            <input class="form-control form-control-sm" name="ref_no">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1">J.O No. / Purpose</label>
                            <input class="form-control form-control-sm" name="jo_number" placeholder="e.g. 14120 or GAS/PARKING">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1">Amount</label>
                            <input type="text" inputmode="decimal" class="form-control form-control-sm text-end money-input" name="amount" required>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button class="btn btn-sm btn-primary w-100" type="submit">Save</button>
                        </div>
                        <div class="col-12">
                            <input class="form-control form-control-sm" name="remarks" placeholder="Remarks (optional)">
                        </div>
                    </div>
                </form>
            </div>
            <div class="jo-summary-wrap">
                <div class="table-side-action left">
                    <button type="button" class="btn btn-sm btn-outline-success" id="sideAddCaBtn">+ CA</button>
                </div>
                <div class="table-side-action right">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="sideAddLiqBtn">+ Liquidation</button>
                </div>
                <div class="table-responsive jo-table-wrap">
                <table class="table table-bordered align-middle text-center">
                    <thead>
                        <tr>
                            <th colspan="4" style="background:#d9ead3;">Cash Advance</th>
                            <th colspan="4" style="background:#ead1dc;">Liquidation / Petty Cash</th>
                            <th colspan="2" style="background:#cfe2f3;">For Return</th>
                            <th rowspan="2">Remarks</th>
                        </tr>
                        <tr>
                            <th style="background:#d9ead3;">Date</th>
                            <th style="background:#d9ead3;">C.A No.</th>
                            <th style="background:#d9ead3;">J.O No.</th>
                            <th style="background:#d9ead3;">Amount</th>
                            <th style="background:#ead1dc;">Date</th>
                            <th style="background:#ead1dc;">Ref No.</th>
                            <th style="background:#ead1dc;">J.O No. / Purpose</th>
                            <th style="background:#ead1dc;">Amount</th>
                            <th style="background:#cfe2f3;">Difference</th>
                            <th style="background:#cfe2f3;">Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($joGroups as $group)
                            @php
                                $groupIndex = $loop->index;
                                $items = $group['items']->values();
                                $liquidations = $group['liquidations']
                                    ->filter(function ($liq) {
                                        $hasAmount = (float) ($liq->amount ?? 0) > 0;
                                        $hasRef = trim((string) ($liq->ref_no ?? '')) !== '';
                                        $hasJo = trim((string) ($liq->jo_number ?? '')) !== '';
                                        $hasRemarks = trim((string) ($liq->remarks ?? '')) !== '';
                                        $hasReceipts = !empty($liq->receipt_paths ?? []) || !empty($liq->receipt_path ?? null);
                                        $isRvLinked = str_contains((string) ($liq->remarks ?? ''), 'Reimbursable Voucher #');

                                        // Hide accidental date-only/empty liquidation rows from rendering.
                                        // Keep rows that are RV-linked even if amount is currently zero.
                                        return $hasAmount || $hasRef || $hasJo || $hasRemarks || $hasReceipts || $isRvLinked;
                                    })
                                    ->values();
                                $usedLiquidationIds = [];
                                $pairedRows = collect();

                                foreach ($items as $item) {
                                    $itemJo = strtoupper(trim((string) ($item->jo_number ?? '')));
                                    $itemJoNormalized = preg_match('/(\d{3,})$/', $itemJo, $m) ? $m[1] : $itemJo;
                                    $itemAmount = (float) ($item->amount ?? 0);
                                    $matchedLiquidation = null;

                                    if ($itemJo !== '') {
                                        $matchedLiquidation = $liquidations->first(function ($liq) use ($itemJo, $itemJoNormalized, $itemAmount, $usedLiquidationIds) {
                                            if (in_array($liq->id, $usedLiquidationIds, true)) {
                                                return false;
                                            }

                                            $liqJo = strtoupper(trim((string) ($liq->jo_number ?? '')));
                                            $liqJoNormalized = preg_match('/(\d{3,})$/', $liqJo, $m) ? $m[1] : $liqJo;
                                            $liqAmount = (float) ($liq->amount ?? 0);

                                            return $liqJo !== ''
                                                && $liqJoNormalized === $itemJoNormalized
                                                && abs($liqAmount - $itemAmount) < 0.01;
                                        });

                                        if (!$matchedLiquidation) {
                                            $matchedLiquidation = $liquidations->first(function ($liq) use ($itemJoNormalized, $usedLiquidationIds) {
                                                if (in_array($liq->id, $usedLiquidationIds, true)) {
                                                    return false;
                                                }

                                                $liqJo = strtoupper(trim((string) ($liq->jo_number ?? '')));
                                                $liqJoNormalized = preg_match('/(\d{3,})$/', $liqJo, $m) ? $m[1] : $liqJo;

                                                return $liqJo !== '' && $liqJoNormalized === $itemJoNormalized;
                                            });
                                        }
                                    }

                                    if ($matchedLiquidation) {
                                        $usedLiquidationIds[] = $matchedLiquidation->id;
                                    }

                                    $pairedRows->push([
                                        'item' => $item,
                                        'liq' => $matchedLiquidation,
                                    ]);
                                }

                                foreach ($liquidations as $liq) {
                                    if (in_array($liq->id, $usedLiquidationIds, true)) {
                                        continue;
                                    }

                                    $pairedRows->push([
                                        'item' => null,
                                        'liq' => $liq,
                                    ]);
                                }

                                if ($pairedRows->isEmpty()) {
                                    $pairedRows->push([
                                        'item' => null,
                                        'liq' => null,
                                    ]);
                                }
                            @endphp
                            @foreach($pairedRows as $i => $rowData)
                                @php
                                    $item = $rowData['item'];
                                    $liq = $rowData['liq'];
                                @endphp
                                <tr data-group="{{ $group['request']->id }}" data-group-index="{{ $groupIndex }}">
                                    <td style="background:#d9ead3;">{{ $i === 0 ? $group['request']->created_at->format('M d, Y') : '' }}</td>
                                    <td style="background:#d9ead3;">{{ $i === 0 ? ($group['request']->ca_no ?? '') : '' }}</td>
                                    <td style="background:#d9ead3;">{{ $item?->jo_number ?: ($item?->reason ?? '') }}</td>
                                    <td style="background:#d9ead3;" class="text-end ca-amount">
                                        @if($item)
                                            <input form="ca-item-form-{{ $item->id }}" type="text" inputmode="decimal" name="amount" class="form-control form-control-sm text-end money-input ca-amount-input" value="{{ number_format((float) $item->amount, 2) }}">
                                            <form id="ca-item-form-{{ $item->id }}" class="mt-1 ca-item-auto-save" method="POST" action="{{ route('accounting.cash-advances.items.update', $item->id, false) }}">
                                                @csrf
                                                @method('PATCH')
                                                <div class="text-muted small auto-save-status" aria-live="polite"></div>
                                            </form>
                                        @endif
                                    </td>
                                <td style="background:#ead1dc;">
                                    @if($liq && $liq->id)
                                        <input form="liq-form-{{ $liq->id }}" type="date" name="date" class="form-control form-control-sm" value="{{ $liq->date->format('Y-m-d') }}" required>
                                    @elseif($liq)
                                        <span class="small">{{ optional($liq->date)->format('m/d/Y') }}</span>
                                    @elseif($item && $i === 0)
                                        <input form="liq-create-{{ $group['request']->id }}-{{ $i }}" type="date" name="date" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}" required>
                                    @endif
                                </td>
                                <td style="background:#ead1dc;">
                                    @if($liq && $liq->id)
                                        <input form="liq-form-{{ $liq->id }}" name="ref_no" class="form-control form-control-sm" value="{{ $liq->ref_no }}">
                                    @elseif($liq)
                                        <span class="small">{{ $liq->ref_no }}</span>
                                    @elseif($item && $i === 0)
                                        <input form="liq-create-{{ $group['request']->id }}-{{ $i }}" name="ref_no" class="form-control form-control-sm" placeholder="Ref No.">
                                    @endif
                                </td>
                                <td style="background:#ead1dc;">
                                    @if($liq && $liq->id)
                                        <input form="liq-form-{{ $liq->id }}" name="jo_number" class="form-control form-control-sm" value="{{ $liq->jo_number }}">
                                    @elseif($liq)
                                        <span class="small">{{ $liq->jo_number }}</span>
                                    @elseif($item && $i === 0)
                                        <input form="liq-create-{{ $group['request']->id }}-{{ $i }}" name="jo_number" class="form-control form-control-sm" placeholder="J.O No. or purpose">
                                    @endif
                                </td>
                                <td style="background:#ead1dc;" class="text-end">
                                    @if($liq && $liq->id)
                                        <input form="liq-form-{{ $liq->id }}" type="text" inputmode="decimal" name="amount" class="form-control form-control-sm text-end money-input liq-amount" value="{{ number_format((float) $liq->amount, 2) }}">
                                    @elseif($liq)
                                        <span class="small">{{ number_format((float) $liq->amount, 2) }}</span>
                                    @elseif($item && $i === 0)
                                        <input form="liq-create-{{ $group['request']->id }}-{{ $i }}" type="text" inputmode="decimal" name="amount" class="form-control form-control-sm text-end money-input liq-amount" placeholder="Amount">
                                    @endif
                                </td>
                                    <td style="background:#cfe2f3;" class="text-end" data-role="difference">
                                        0.00
                                    </td>
                                    <td style="background:#cfe2f3;" class="text-end" data-role="balance">
                                        0.00
                                    </td>
                                <td class="text-start">
                                    @php
                                        $receiptList = $liq?->receipt_paths ?? [];
                                        if (empty($receiptList) && !empty($liq?->receipt_path)) {
                                            $receiptList = [$liq->receipt_path];
                                        }
                                    @endphp
                                    @if(!empty($receiptList))
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#receiptsModal"
                                            data-receipts='@json($receiptList)'>
                                            View Receipts ({{ count($receiptList) }})
                                        </button>
                                    @endif
                                    @if($liq && $liq->id)
                                        <form id="liq-form-{{ $liq->id }}" class="mt-2 liq-auto-save" method="POST" action="{{ route('accounting.cash-advances.liquidations.update', $liq->id, false) }}" data-mode="update" data-liquidation-id="{{ $liq->id }}" data-update-url-base="{{ route('accounting.cash-advances.liquidations.store', [], false) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input class="form-control form-control-sm mb-2" name="remarks" value="{{ $liq->remarks ?? '' }}" placeholder="Add remarks">
                                            <div class="text-muted small auto-save-status" aria-live="polite"></div>
                                        </form>
                                    @elseif($liq)
                                        <div class="small text-muted">
                                            {{ $liq->remarks ?? '' }}
                                        </div>
                                    @elseif($item && $i === 0)
                                        <form id="liq-create-{{ $group['request']->id }}-{{ $i }}" class="mt-2 liq-auto-save" method="POST" action="{{ route('accounting.cash-advances.liquidations.store', [], false) }}" data-mode="create" data-update-url-base="{{ route('accounting.cash-advances.liquidations.store', [], false) }}">
                                            @csrf
                                            <input type="hidden" name="cash_advance_request_id" value="{{ $group['request']->id }}">
                                            <input class="form-control form-control-sm mb-2" name="remarks" placeholder="Add remarks">
                                            <div class="text-muted small auto-save-status" aria-live="polite"></div>
                                        </form>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column gap-1 align-items-center">
                                    @if($liq && $liq->id)
                                        <form method="POST" action="{{ route('accounting.cash-advances.liquidations.destroy', $liq->id, false) }}" onsubmit="return confirm('Delete this liquidation from CA Monitoring only? Cost Sheet and Reimbursable Voucher will stay.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-warning">Delete Liq (CA only)</button>
                                        </form>
                                    @elseif($liq && !empty($liq->grouped_ref_no))
                                        <form method="POST" action="{{ route('accounting.cash-advances.liquidations.destroy-grouped', $group['request'], false) }}" onsubmit="return confirm('Delete all CA Monitoring liquidation entries for Reimbursable Voucher #{{ $liq->grouped_ref_no }}? Cost Sheet and Reimbursable Voucher will stay.');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="ref_no" value="{{ $liq->grouped_ref_no }}">
                                            <button type="submit" class="btn btn-sm btn-outline-warning">Delete Liq (CA only)</button>
                                        </form>
                                    @endif
                                    @if($i === 0)
                                        <form method="POST" action="{{ route('accounting.cash-advances.destroy', $group['request'], false) }}" onsubmit="return confirm('Delete this CA and all related liquidations? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete CA</button>
                                        </form>
                                    @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted">No cash advance records.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>
        </div>

        <div class="eh-card p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="mb-0">Personal Cash Advances</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>CA No.</th>
                            <th>Reason</th>
                            <th class="text-end">Amount</th>
                            <th>Terms</th>
                            <th class="text-end">Per Payslip</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($personalGroups as $group)
                            <tr>
                                <td>{{ $group['request']->created_at->format('M d, Y') }}</td>
                                <td>{{ $group['request']->ca_no ?? '-' }}</td>
                                <td class="text-start">
                                    @foreach($group['items'] as $item)
                                        <div>{{ $item->reason ?? '-' }}</div>
                                    @endforeach
                                </td>
                                <td class="text-end">{{ number_format($group['items']->sum('amount'), 2) }}</td>
                                @php
                                    $personalTerms = max((int) ($group['request']->salary_deduction_terms ?: 1), 1);
                                    $personalBalance = max((float) $group['difference'], 0);
                                    $personalInstallment = min($personalBalance, round((float) $group['items']->sum('amount') / $personalTerms, 2));
                                @endphp
                                <td>{{ $personalTerms }}</td>
                                <td class="text-end">{{ number_format($personalInstallment, 2) }}</td>
                                <td class="text-end">
                                    <input type="text"
                                           inputmode="decimal"
                                           class="form-control form-control-sm text-end money-input personal-paid-input"
                                           value="{{ number_format($group['paid'], 2) }}"
                                           data-url="{{ route('accounting.cash-advances.personal-paid', $group['request'], false) }}">
                                    <div class="text-muted small personal-save-status" aria-live="polite"></div>
                                </td>
                                <td class="text-end personal-balance">{{ number_format($group['difference'], 2) }}</td>
                                <td class="text-center">
                                    <form method="POST" action="{{ route('accounting.cash-advances.destroy', $group['request'], false) }}" onsubmit="return confirm('Delete this CA and all related records? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete CA</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No personal cash advances.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    (function () {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const parseMoney = (value) => {
            if (typeof value !== 'string') {
                value = String(value ?? '');
            }
            const cleaned = value.replace(/,/g, '').trim();
            const number = Number(cleaned);
            return Number.isFinite(number) ? number : 0;
        };
        const formatMoney = (value) => {
            return value.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        };
        const stripMoney = (value) => String(value || '').replace(/,/g, '');
        const formatMoneyInput = (input) => {
            if (!input) {
                return;
            }

            const rawValue = String(input.value || '').replace(/[^\d.]/g, '');
            const [whole = '', ...decimalParts] = rawValue.split('.');
            const decimals = decimalParts.join('').slice(0, 2);
            const formattedWhole = whole
                .replace(/^0+(?=\d)/, '')
                .replace(/\B(?=(\d{3})+(?!\d))/g, ',');

            input.value = decimals.length || rawValue.includes('.')
                ? `${formattedWhole || '0'}.${decimals}`
                : formattedWhole;
        };
        const recalculateSummary = () => {
            const summary = document.getElementById('jo-summary');
            if (!summary) {
                return;
            }
            const rows = Array.from(summary.querySelectorAll('tr[data-group]'));

            let totalCa = 0;
            let totalLiquidation = 0;
            const entries = [];

            rows.forEach((row) => {
                const caCell = row.querySelector('.ca-amount');
                let caAmount = 0;
                if (caCell) {
                    const caInput = caCell.querySelector('.ca-amount-input');
                    caAmount = caInput ? parseMoney(caInput.value) : parseMoney(caCell.textContent);
                    totalCa += caAmount;
                }
                const liqInput = row.querySelector('.liq-amount');
                const liqAmount = liqInput && liqInput.value ? parseMoney(liqInput.value) : 0;
                totalLiquidation += liqAmount;

                entries.push({
                    difference: caAmount - liqAmount,
                    diffCell: row.querySelector('[data-role="difference"]'),
                    balanceCell: row.querySelector('[data-role="balance"]'),
                });
            });

            const totalDifference = entries.reduce((sum, entry) => sum + entry.difference, 0);
            let runningBalanceDesc = totalDifference;

            entries.forEach((entry) => {
                if (entry.diffCell) {
                    entry.diffCell.textContent = formatMoney(entry.difference);
                }
                if (entry.balanceCell) {
                    entry.balanceCell.textContent = formatMoney(runningBalanceDesc);
                }
                runningBalanceDesc -= entry.difference;
            });

            const totalCaEl = document.getElementById('total-ca');
            if (totalCaEl) {
                totalCaEl.textContent = formatMoney(totalCa);
            }
            const totalLiquidationEl = document.getElementById('total-liquidation');
            if (totalLiquidationEl) {
                totalLiquidationEl.textContent = formatMoney(totalLiquidation);
            }
            const totalBalanceEl = document.getElementById('total-balance');
            if (totalBalanceEl) {
                totalBalanceEl.textContent = formatMoney(totalDifference);
            }
        };

        const queueSave = (form) => {
            if (form.dataset.saving === 'true') {
                form.dataset.pendingSave = 'true';
                return;
            }
            const status = form.querySelector('.auto-save-status');
            const controls = new Set([
                ...form.querySelectorAll('input, select, textarea'),
                ...document.querySelectorAll(`[form="${form.id}"]`)
            ]);
            for (const input of controls) {
                if (input.hasAttribute('required') && !String(input.value || '').trim()) {
                    if (status) {
                        status.textContent = 'Complete required fields';
                    }
                    return;
                }
            }

            if (form.dataset.mode === 'create') {
                const dateInput = form.querySelector('input[name="date"]');
                const refInput = form.querySelector('input[name="ref_no"]');
                const joInput = form.querySelector('input[name="jo_number"]');
                const amountInput = form.querySelector('input[name="amount"]');
                const remarksInput = form.querySelector('input[name="remarks"]');
                const hasDetails =
                    String(refInput?.value || '').trim() !== '' ||
                    String(joInput?.value || '').trim() !== '' ||
                    String(remarksInput?.value || '').trim() !== '' ||
                    parseMoney(String(amountInput?.value || '0')) > 0;

                // Avoid creating liquidation rows from date-only autosave.
                if (!hasDetails) {
                    if (status) {
                        status.textContent = '';
                    }
                    return;
                }

                if (dateInput && !String(dateInput.value || '').trim()) {
                    if (status) {
                        status.textContent = 'Date is required';
                    }
                    return;
                }
            }
            if (status) {
                status.textContent = 'Saving...';
            }
            form.dataset.saving = 'true';
            const saveToken = String(Number(form.dataset.saveToken || '0') + 1);
            form.dataset.saveToken = saveToken;
            const body = new FormData(form);
            controls.forEach((input) => {
                if (!(input instanceof HTMLElement) || !('name' in input)) {
                    return;
                }
                const name = input.getAttribute('name');
                if (!name) {
                    return;
                }
                if (input instanceof HTMLInputElement) {
                    if (input.type === 'checkbox') {
                        if (input.checked) {
                            body.set(name, input.value || '1');
                        } else {
                            body.delete(name);
                        }
                        return;
                    }
                    if (input.type === 'radio') {
                        if (input.checked) {
                            body.set(name, input.value || '');
                        }
                        return;
                    }
                    if (input.type === 'file') {
                        return;
                    }
                    if (input.classList.contains('money-input')) {
                        body.set(name, stripMoney(input.value));
                    } else {
                        body.set(name, input.value ?? '');
                    }
                    return;
                }
                if (input instanceof HTMLSelectElement || input instanceof HTMLTextAreaElement) {
                    body.set(name, input.value ?? '');
                }
            });
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body
            }).then(async (response) => {
                if (response.redirected) {
                    const redirectedTo = response.url || '';
                    if (redirectedTo.includes('/login')) {
                        throw new Error('Session expired. Refresh and login again.');
                    }
                    // Some endpoints may redirect back even after successful save.
                    return { status: 'ok' };
                }
                const contentType = response.headers.get('content-type') || '';
                const data = contentType.includes('application/json')
                    ? await response.json().catch(() => ({}))
                    : {};
                if (response.status >= 300 && response.status < 400) {
                    throw new Error(`Save failed (redirect ${response.status})`);
                }
                if (response.status === 419) {
                    throw new Error('csrf-expired');
                }
                if (response.status === 422) {
                    const errors = data?.errors ? Object.values(data.errors).flat() : [];
                    const message = errors.length ? errors[0] : 'Validation failed';
                    throw new Error(message);
                }
                if (!response.ok) {
                    throw new Error(`Save failed (HTTP ${response.status})`);
                }
                return data;
            }).then((data) => {
                  if (form.dataset.saveToken !== saveToken) {
                      return;
                  }
                  if (form.dataset.mode === 'create' && data?.liquidation_id) {
                      form.dataset.mode = 'update';
                      form.dataset.liquidationId = data.liquidation_id;
                      const updateBase = form.dataset.updateUrlBase || '';
                      if (updateBase) {
                          form.action = `${updateBase}/${data.liquidation_id}`;
                      }
                      if (!form.querySelector('input[name="_method"]')) {
                          const methodInput = document.createElement('input');
                          methodInput.type = 'hidden';
                          methodInput.name = '_method';
                          methodInput.value = 'PATCH';
                          form.appendChild(methodInput);
                      }
                  }
                  recalculateSummary();
                  if (status) {
                      status.textContent = 'Saved';
                      setTimeout(() => {
                          if (form.dataset.saveToken === saveToken) {
                              status.textContent = '';
                          }
                      }, 1500);
                  }
              })
              .catch((err) => {
                  if (form.dataset.saveToken !== saveToken) {
                      return;
                  }
                  if (status) {
                      status.textContent = err?.message === 'csrf-expired'
                          ? 'Session expired. Refresh the page.'
                          : (err?.message || 'Save failed');
                  }
              })
              .finally(() => {
                  form.dataset.saving = 'false';
                  if (form.dataset.pendingSave === 'true') {
                      form.dataset.pendingSave = 'false';
                      queueSave(form);
                  }
              });
        };

        const saveTimers = new Map();
        const getBoundAutosaveForm = (element) => {
            if (!(element instanceof HTMLElement)) {
                return null;
            }
            const nativeForm = element.form;
            if (nativeForm && (nativeForm.classList.contains('liq-auto-save') || nativeForm.classList.contains('ca-item-auto-save'))) {
                return nativeForm;
            }
            const formId = element.getAttribute('form');
            if (!formId) {
                return null;
            }
            const form = document.getElementById(formId);
            if (!form) {
                return null;
            }
            if (!form.classList.contains('liq-auto-save') && !form.classList.contains('ca-item-auto-save')) {
                return null;
            }
            return form;
        };
        const scheduleFormSave = (form) => {
            if (!form || !form.id) {
                return;
            }
            const existingTimer = saveTimers.get(form.id);
            if (existingTimer) {
                clearTimeout(existingTimer);
            }
            const timer = setTimeout(() => queueSave(form), 600);
            saveTimers.set(form.id, timer);
        };

        document.querySelectorAll('.liq-auto-save, .ca-item-auto-save').forEach((form) => {
            const schedule = () => scheduleFormSave(form);
            const controls = new Set([
                ...form.querySelectorAll('input, select, textarea'),
                ...document.querySelectorAll(`[form="${form.id}"]`)
            ]);
            controls.forEach((input) => {
                input.addEventListener('input', schedule);
                input.addEventListener('change', schedule);
                input.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        schedule();
                    }
                });
            });
        });

        // Fallback delegated listener: ensures external [form="..."] inputs
        // (like CA/Liquidation amount fields in table cells) always trigger autosave.
        const delegatedSchedule = (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }
            const form = getBoundAutosaveForm(target);
            if (!form) {
                return;
            }
            scheduleFormSave(form);
        };
        document.addEventListener('input', delegatedSchedule);
        document.addEventListener('change', delegatedSchedule);

        // Force amount fields to autosave even without touching remarks.
        const amountFieldImmediateSave = (event) => {
            const target = event.target;
            if (!(target instanceof HTMLInputElement)) {
                return;
            }
            if (!target.classList.contains('ca-amount-input') && !target.classList.contains('liq-amount')) {
                return;
            }
            const form = getBoundAutosaveForm(target);
            if (!form) {
                return;
            }
            scheduleFormSave(form);
        };
        document.addEventListener('blur', amountFieldImmediateSave, true);
        document.addEventListener('change', amountFieldImmediateSave);

        const getAutosaveFormForInput = (input) => {
            if (!(input instanceof HTMLInputElement)) {
                return null;
            }
            const formId = input.getAttribute('form');
            if (!formId) {
                return null;
            }
            const form = document.getElementById(formId);
            if (!form) {
                return null;
            }
            if (!form.classList.contains('liq-auto-save') && !form.classList.contains('ca-item-auto-save')) {
                return null;
            }
            return form;
        };

        // Direct path for liquidation amount fields: save immediately on change/input.
        const liquidationAmountDirectSave = (event) => {
            const target = event.target;
            if (!(target instanceof HTMLInputElement)) {
                return;
            }
            if (!target.classList.contains('liq-amount')) {
                return;
            }
            const form = getAutosaveFormForInput(target) || getBoundAutosaveForm(target);
            if (!form) {
                return;
            }
            const status = form.querySelector('.auto-save-status');
            if (status) {
                status.textContent = 'Saving...';
            }
            queueSave(form);
        };
        const caAmountDirectSave = (event) => {
            const target = event.target;
            if (!(target instanceof HTMLInputElement)) {
                return;
            }
            if (!target.classList.contains('ca-amount-input')) {
                return;
            }
            const form = getAutosaveFormForInput(target) || getBoundAutosaveForm(target);
            if (!form) {
                return;
            }
            const status = form.querySelector('.auto-save-status');
            if (status) {
                status.textContent = 'Saving...';
            }
            queueSave(form);
        };
        document.addEventListener('change', liquidationAmountDirectSave);
        document.addEventListener('blur', liquidationAmountDirectSave, true);
        document.addEventListener('change', caAmountDirectSave);
        document.addEventListener('blur', caAmountDirectSave, true);

        // Hard bind autosave for ALL liquidation row fields (date/ref/jo/amount).
        const liquidationFieldAutosave = (event) => {
            const target = event.target;
            if (!(target instanceof HTMLInputElement)) {
                return;
            }
            const formId = target.getAttribute('form') || '';
            if (!formId.startsWith('liq-')) {
                return;
            }
            const form = document.getElementById(formId);
            if (!form || !form.classList.contains('liq-auto-save')) {
                return;
            }
            const status = form.querySelector('.auto-save-status');
            if (status) {
                status.textContent = 'Saving...';
            }
            scheduleFormSave(form);
        };
        document.addEventListener('input', liquidationFieldAutosave);
        document.addEventListener('change', liquidationFieldAutosave);
        document.addEventListener('blur', liquidationFieldAutosave, true);

        document.addEventListener('input', (event) => {
            const input = event.target;
            if (!(input instanceof HTMLInputElement) || !input.classList.contains('money-input')) {
                return;
            }

            formatMoneyInput(input);
            recalculateSummary();
        });

        document.addEventListener('submit', () => {
            document.querySelectorAll('input.money-input').forEach((input) => {
                input.value = stripMoney(input.value);
            });
        });

        document.querySelectorAll('input.money-input').forEach((input) => {
            if (input.value !== '') {
                input.value = formatMoney(parseMoney(input.value));
            }
        });

        recalculateSummary();

        const filterForm = document.getElementById('ca-filter-form');
        if (filterForm) {
            const triggerSubmit = () => {
                filterForm.submit();
            };

            ['from_date', 'to_date', 'month', 'year'].forEach((fieldName) => {
                const input = filterForm.querySelector(`[name="${fieldName}"]`);
                if (input) {
                    input.addEventListener('change', triggerSubmit);
                }
            });

            const employeeInput = document.getElementById('employee-combobox');
            const employeeIdHidden = document.getElementById('employee-id-hidden');
            const employeeOptions = Array.from(document.querySelectorAll('#employee-options option'));
            const syncEmployeeId = () => {
                if (!employeeInput || !employeeIdHidden) {
                    return false;
                }
                const typed = String(employeeInput.value || '').trim().toLowerCase();
                const matched = employeeOptions.find((option) => option.value.trim().toLowerCase() === typed);
                employeeIdHidden.value = matched ? String(matched.dataset.id || '') : '';
                return Boolean(matched || typed === '');
            };

            if (employeeInput && employeeIdHidden) {
                employeeInput.addEventListener('input', () => {
                    syncEmployeeId();
                });
                employeeInput.addEventListener('change', () => {
                    syncEmployeeId();
                    triggerSubmit();
                });
                employeeInput.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        syncEmployeeId();
                        triggerSubmit();
                    }
                });
                if (!syncEmployeeId()) {
                    employeeIdHidden.value = '';
                };
            }
        }

        const sideAddCaBtn = document.getElementById('sideAddCaBtn');
        const sideAddLiqBtn = document.getElementById('sideAddLiqBtn');
        const addCaPanel = document.getElementById('addCaPanel');
        const addLiqPanel = document.getElementById('addLiquidationPanel');
        const summaryCaItems = document.getElementById('summary-ca-items');
        const summaryAddCaItemBtn = document.getElementById('summary-add-ca-item');
        const showPanel = (panel) => {
            if (!panel) {
                return;
            }
            if (window.bootstrap?.Collapse) {
                window.bootstrap.Collapse.getOrCreateInstance(panel, { toggle: false }).show();
            } else {
                panel.classList.add('show');
            }
            panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        };
        sideAddCaBtn?.addEventListener('click', () => showPanel(addCaPanel));
        sideAddLiqBtn?.addEventListener('click', () => showPanel(addLiqPanel));

        const updateSummaryCaItemIndexes = () => {
            if (!summaryCaItems) {
                return;
            }

            summaryCaItems.querySelectorAll('.summary-ca-item').forEach((item, index) => {
                const joInput = item.querySelector('input[name*="[jo_number]"]');
                const reasonInput = item.querySelector('input[name*="[reason]"]');
                const amountInput = item.querySelector('input[name*="[amount]"]');
                if (joInput) {
                    joInput.name = `items[${index}][jo_number]`;
                }
                if (reasonInput) {
                    reasonInput.name = `items[${index}][reason]`;
                }
                if (amountInput) {
                    amountInput.name = `items[${index}][amount]`;
                }
            });
        };

        summaryAddCaItemBtn?.addEventListener('click', () => {
            if (!summaryCaItems) {
                return;
            }

            const item = document.createElement('div');
            item.className = 'row g-2 mb-2 summary-ca-item';
            item.innerHTML = `
                <div class="col-md-3">
                    <label class="form-label mb-1">J.O No.</label>
                    <input class="form-control form-control-sm" name="items[][jo_number]" placeholder="e.g. 12250">
                </div>
                <div class="col-md-4">
                    <label class="form-label mb-1">Reason</label>
                    <input class="form-control form-control-sm" name="items[][reason]" placeholder="If no J.O No.">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Amount</label>
                    <input class="form-control form-control-sm text-end money-input" type="text" inputmode="decimal" name="items[][amount]" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-sm btn-outline-danger w-100 remove-summary-ca-item" type="button">Remove</button>
                </div>
            `;
            summaryCaItems.appendChild(item);
            updateSummaryCaItemIndexes();
        });

        summaryCaItems?.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement) || !target.classList.contains('remove-summary-ca-item')) {
                return;
            }

            const items = summaryCaItems.querySelectorAll('.summary-ca-item');
            if (items.length <= 1) {
                target.closest('.summary-ca-item')?.querySelectorAll('input').forEach((input) => {
                    input.value = '';
                });
                return;
            }

            target.closest('.summary-ca-item')?.remove();
            updateSummaryCaItemIndexes();
        });

        updateSummaryCaItemIndexes();

        const modal = document.getElementById('receiptsModal');
        if (!modal) {
            return;
        }
        modal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            const raw = trigger?.getAttribute('data-receipts') || '[]';
            let receipts = [];
            try {
                receipts = JSON.parse(raw);
            } catch (err) {
                receipts = [];
            }
            const list = modal.querySelector('.receipt-list');
            list.innerHTML = '';
            if (!receipts.length) {
                list.innerHTML = '<div class="text-muted">No receipts found.</div>';
                return;
            }
            const baseUrl = "{{ rtrim(Storage::url(''), '/') }}/";
            receipts.forEach((path) => {
                const item = document.createElement('div');
                const link = document.createElement('a');
                link.href = baseUrl + path;
                link.target = '_blank';
                link.textContent = path.split('/').pop();
                item.appendChild(link);
                list.appendChild(item);
            });
        });
    })();
</script>
@endpush

@push('scripts')
<script>
    (function () {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const formatMoney = (value) => {
            return value.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        };
        const recalcPersonalTotal = () => {
            const totalEl = document.getElementById('personal-balance-total');
            if (!totalEl) {
                return;
            }
            let sum = 0;
            document.querySelectorAll('.personal-balance').forEach((cell) => {
                const value = Number(String(cell.textContent || '0').replace(/,/g, ''));
                if (Number.isFinite(value)) {
                    sum += value;
                }
            });
            totalEl.textContent = formatMoney(sum);
        };

        document.querySelectorAll('.personal-paid-input').forEach((input) => {
            let timer;
            const status = input.parentElement.querySelector('.personal-save-status');
            const balanceCell = input.closest('tr')?.querySelector('.personal-balance');
            const amountCell = input.closest('tr')?.querySelector('td:nth-child(4)');
            const submit = () => {
                clearTimeout(timer);
                timer = setTimeout(async () => {
                    const url = input.getAttribute('data-url');
                    if (!url) {
                        return;
                    }
                    const rawValue = String(input.value ?? '').trim();
                    if (!rawValue) {
                        if (status) {
                            status.textContent = 'Enter amount';
                        }
                        return;
                    }
                    if (status) {
                        status.textContent = 'Saving...';
                    }
                    const paidValue = rawValue.replace(/,/g, '');
                    const body = new FormData();
                    body.append('personal_paid_amount', paidValue);
                    body.append('_method', 'PATCH');
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token || '',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body
                    }).then(async (response) => {
                        const data = await response.json().catch(() => ({}));
                        if (!response.ok) {
                            throw new Error(data?.message || `Save failed (HTTP ${response.status})`);
                        }
                        return data;
                    }).then(() => {
                        if (amountCell && balanceCell) {
                            const amountValue = Number((amountCell.textContent || '0').replace(/,/g, ''));
                            const paidNumber = Number(String(paidValue).replace(/,/g, ''));
                            if (Number.isFinite(amountValue) && Number.isFinite(paidNumber)) {
                                const balanceValue = amountValue - paidNumber;
                                balanceCell.textContent = formatMoney(balanceValue);
                                recalcPersonalTotal();
                            }
                        }
                        if (status) {
                            status.textContent = 'Saved';
                            setTimeout(() => {
                                status.textContent = '';
                            }, 1500);
                        }
                    }).catch((err) => {
                        if (status) {
                            status.textContent = err?.message || 'Save failed';
                        }
                    });
                }, 500);
            };

            input.addEventListener('input', submit);
            input.addEventListener('change', submit);
        });

        recalcPersonalTotal();
    })();
</script>
@endpush

<div class="modal fade" id="receiptsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Receipts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="receipt-list d-flex flex-column gap-2"></div>
            </div>
        </div>
    </div>
</div>
