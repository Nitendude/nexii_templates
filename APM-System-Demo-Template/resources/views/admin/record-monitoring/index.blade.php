@extends('layouts.employeehub')

@section('content')
    @php
        $fmt = fn ($amount) => number_format((float) $amount, 2);
        $quickFilters = [
            'receivables' => 'Existing Receivables',
            'overdue' => 'Overdue',
            'cr' => 'CR',
            'ar' => 'AR',
            'or2' => 'OR 2',
            'or1' => 'OR 1',
            'bi' => 'BI',
            'dn' => 'DN',
        ];
    @endphp

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">Record Monitoring</h2>
            <p class="text-muted mb-0">System version of the Excel billing monitoring workbook, grouped by client for easier tracking.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form method="POST" action="{{ route('accounting.record-monitoring.import') }}">
                @csrf
                <button class="btn btn-outline-primary" type="submit">Import Excel Workbook</button>
            </form>
            <a class="btn btn-primary" href="{{ route('accounting.record-monitoring.create') }}">Add Entry</a>
        </div>
    </div>

    @if(session('status') === 'record-monitoring-imported')
        <div class="alert alert-success">Record Monitoring workbook imported successfully.</div>
    @elseif(session('status') === 'record-monitoring-saved')
        <div class="alert alert-success">Record Monitoring entry saved.</div>
    @elseif(session('status') === 'record-monitoring-updated')
        <div class="alert alert-success">Record Monitoring entry updated.</div>
    @endif

    @if($errors->has('import'))
        <div class="alert alert-danger">{{ $errors->first('import') }}</div>
    @endif

    <div class="d-flex flex-column gap-3 mb-3">
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-sm {{ empty($quickFilter) ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('accounting.record-monitoring.index', array_filter(['q' => $search ?: null, 'status' => $statusFilter ?: null, 'in_charge' => $inChargeFilter ?: null])) }}">
                All
            </a>
            @foreach($quickFilters as $filterKey => $filterLabel)
                <a
                    class="btn btn-sm {{ ($quickFilter ?? '') === $filterKey ? 'btn-primary' : 'btn-outline-primary' }}"
                    href="{{ route('accounting.record-monitoring.index', array_filter(['q' => $search ?: null, 'filter' => $filterKey, 'status' => $statusFilter ?: null, 'in_charge' => $inChargeFilter ?: null])) }}"
                >
                    {{ $filterLabel }}
                </a>
            @endforeach
        </div>

        <div class="d-flex justify-content-end">
            <form method="GET" action="{{ route('accounting.record-monitoring.index') }}" class="d-flex gap-2 align-items-center flex-wrap" id="recordMonitoringSearchForm">
                @if(!empty($quickFilter))
                    <input type="hidden" name="filter" value="{{ $quickFilter }}">
                @endif
                <select class="form-select" name="status" style="min-width: 190px;">
                    <option value="">All Statuses</option>
                    @foreach($statusPresets as $preset)
                        <option value="{{ $preset }}" @selected(($statusFilter ?? '') === $preset)>{{ $preset }}</option>
                    @endforeach
                </select>
                <select class="form-select" name="in_charge" style="min-width: 190px;">
                    <option value="">All In-Charge</option>
                    @foreach($inChargeOptions as $option)
                        <option value="{{ $option }}" @selected(($inChargeFilter ?? '') === $option)>{{ $option }}</option>
                    @endforeach
                </select>
                <input class="form-control" type="text" name="q" id="recordMonitoringSearchInput" value="{{ $search ?? '' }}" placeholder="Search client name, JO no., reference no., BL no., or remarks" style="min-width: 380px;">
                <button type="submit" class="btn btn-outline-primary">Search Records</button>
                <button type="button" class="btn btn-outline-secondary" id="recordMonitoringSearchReset">Reset</button>
            </form>
        </div>
    </div>

    <div class="eh-card p-4">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Clients</div>
                    <div class="fs-4 fw-semibold">{{ number_format((float) ($totals['clients'] ?? 0), 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Entries</div>
                    <div class="fs-4 fw-semibold">{{ number_format((float) ($totals['entries'] ?? 0), 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Active Balance</div>
                    <div class="fs-4 fw-semibold">{{ $fmt($totals['active_balance'] ?? 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Payments Logged</div>
                    <div class="fs-4 fw-semibold">{{ $fmt($totals['payments'] ?? 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Overdue Entries</div>
                    <div class="fs-4 fw-semibold">{{ number_format((float) ($totals['overdue'] ?? 0), 0) }}</div>
                </div>
            </div>
        </div>

        @if(($clientGroups ?? collect())->isEmpty())
            <div class="text-muted">No record monitoring entries found yet.</div>
        @else
            <div class="accordion" id="recordMonitoringAccordion">
                @foreach($clientGroups as $group)
                    @php $collapseId = 'record-monitoring-client-' . $loop->index; @endphp
                    <div class="accordion-item mb-3 border rounded overflow-hidden">
                        <h2 class="accordion-header" id="{{ $collapseId }}-header">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="{{ $collapseId }}">
                                <div class="d-flex flex-wrap justify-content-between align-items-center w-100 gap-3 me-3">
                                    <div>
                                        <div class="fw-semibold">{{ $group['client'] }}</div>
                                        <div class="text-muted small">{{ $group['active_count'] }} active, {{ $group['paid_count'] }} paid</div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-4 text-end">
                                        <div>
                                            <div class="text-muted small">Billings</div>
                                            <div class="fw-semibold">{{ $fmt($group['billing_total']) }}</div>
                                        </div>
                                        <div>
                                            <div class="text-muted small">Advances</div>
                                            <div class="fw-semibold">{{ $fmt($group['advance_total']) }}</div>
                                        </div>
                                        <div>
                                            <div class="text-muted small">Balance</div>
                                            <div class="fw-semibold">{{ $fmt($group['balance_total']) }}</div>
                                        </div>
                                        <div>
                                            <div class="text-muted small">Overdue</div>
                                            <div class="fw-semibold">{{ number_format((float) $group['overdue_count'], 0) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="{{ $collapseId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="{{ $collapseId }}-header" data-bs-parent="#recordMonitoringAccordion">
                            <div class="accordion-body">
                                @if($group['active_rows']->isNotEmpty())
                                    <h5 class="mb-3">Active Monitoring</h5>
                                    <div class="table-responsive mb-4">
                                        <table class="table align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Doc</th>
                                                    <th>J.O. No.</th>
                                                    <th>Ref No.</th>
                                                    <th class="text-end">Billing Amt.</th>
                                                    <th class="text-end">Advances</th>
                                                    <th style="min-width: 110px;">Payment</th>
                                                    <th style="min-width: 110px;">WHT</th>
                                                    <th style="min-width: 110px;">Discount</th>
                                                    <th class="text-end">Balance</th>
                                                    <th>CR No.</th>
                                                    <th>AR No.</th>
                                                    <th>BL No.</th>
                                                    <th>In-Charge</th>
                                                    <th style="min-width: 180px;">Status</th>
                                                    <th style="min-width: 220px;">Remarks</th>
                                                    <th style="min-width: 210px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($group['active_rows'] as $row)
                                                    <tr>
                                                        @php $quickFormId = 'quick-update-' . $row->id; @endphp
                                                        <td>{{ $row->date_text ?: '—' }}</td>
                                                        <td>
                                                            @php
                                                                $docLabel = match($row->source_type) {
                                                                    'billing_statement' => 'BS',
                                                                    'service_invoice' => 'SI',
                                                                    'debit_credit_note' => 'DCN',
                                                                    'workbook' => 'XLS',
                                                                    default => 'MAN',
                                                                };
                                                            @endphp
                                                            <span class="badge text-bg-light border">{{ $docLabel }}</span>
                                                        </td>
                                                        <td>{{ $row->jo_number ?: '—' }}</td>
                                                        <td>
                                                            @php
                                                                $documentUrl = match($row->source_type) {
                                                                    'billing_statement' => $row->source_id ? route('billing.show', $row->source_id) : null,
                                                                    'service_invoice' => $row->source_id ? route('billing.service-invoices.show', $row->source_id) : null,
                                                                    'debit_credit_note' => $row->source_id ? route('billing.notes.show', $row->source_id) : null,
                                                                    default => null,
                                                                };
                                                            @endphp
                                                            @if($documentUrl)
                                                                <a href="{{ $documentUrl }}" class="fw-semibold text-decoration-none">{{ $row->reference_no ?: '—' }}</a>
                                                            @else
                                                                {{ $row->reference_no ?: '—' }}
                                                            @endif
                                                        </td>
                                                        <td class="text-end">{{ $fmt($row->billing_amount) }}</td>
                                                        <td class="text-end">{{ $fmt($row->advances_amount) }}</td>
                                                        <td><input class="form-control form-control-sm text-end" form="{{ $quickFormId }}" type="number" step="0.01" name="payment_amount" value="{{ old('payment_amount', $row->payment_amount) }}"></td>
                                                        <td><input class="form-control form-control-sm text-end" form="{{ $quickFormId }}" type="number" step="0.01" name="wht_amount" value="{{ old('wht_amount', $row->wht_amount) }}"></td>
                                                        <td><input class="form-control form-control-sm text-end" form="{{ $quickFormId }}" type="number" step="0.01" name="discount_amount" value="{{ old('discount_amount', $row->discount_amount) }}"></td>
                                                        <td class="text-end fw-semibold">{{ $fmt($row->balance_amount) }}</td>
                                                        <td><input class="form-control form-control-sm" form="{{ $quickFormId }}" name="cr_no" value="{{ old('cr_no', $row->cr_no) }}"></td>
                                                        <td><input class="form-control form-control-sm" form="{{ $quickFormId }}" name="ar_no" value="{{ old('ar_no', $row->ar_no) }}"></td>
                                                        <td>{{ $row->bl_no ?: '—' }}</td>
                                                        <td>{{ $row->in_charge ?: '—' }}</td>
                                                        <td>
                                                            <select class="form-select form-select-sm" form="{{ $quickFormId }}" name="status_as_of">
                                                                <option value="">No status</option>
                                                                @foreach($statusPresets as $preset)
                                                                    <option value="{{ $preset }}" @selected(($row->status_as_of ?? '') === $preset)>{{ $preset }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input class="form-control form-control-sm" form="{{ $quickFormId }}" name="remarks" value="{{ old('remarks', $row->remarks) }}"></td>
                                                        <td class="text-end">
                                                            <form id="{{ $quickFormId }}" method="POST" action="{{ route('accounting.record-monitoring.quick-update', $row) }}" class="d-none">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="q" value="{{ $search }}">
                                                                <input type="hidden" name="filter" value="{{ $quickFilter }}">
                                                                <input type="hidden" name="status" value="{{ $statusFilter }}">
                                                                <input type="hidden" name="in_charge" value="{{ $inChargeFilter }}">
                                                                <input type="hidden" name="page" value="{{ $clientGroups->currentPage() }}">
                                                            </form>
                                                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                                                @if($documentUrl)
                                                                    <a class="btn btn-sm btn-outline-secondary" href="{{ $documentUrl }}">Open</a>
                                                                @endif
                                                                <button class="btn btn-sm btn-primary" type="submit" form="{{ $quickFormId }}">Save</button>
                                                                <a class="btn btn-sm btn-outline-primary" href="{{ route('accounting.record-monitoring.edit', $row) }}">Edit</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                @if($group['paid_rows']->isNotEmpty())
                                    <h5 class="mb-3">Paid Billings</h5>
                                    <div class="table-responsive">
                                        <table class="table align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Doc</th>
                                                    <th>J.O. No.</th>
                                                    <th>Ref No.</th>
                                                    <th class="text-end">Billing Amt.</th>
                                                    <th class="text-end">Payment</th>
                                                    <th class="text-end">Balance</th>
                                                    <th>CR No.</th>
                                                    <th>AR No.</th>
                                                    <th>Remarks</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($group['paid_rows'] as $row)
                                                    <tr>
                                                        <td>{{ $row->date_text ?: '—' }}</td>
                                                        <td>
                                                            @php
                                                                $docLabel = match($row->source_type) {
                                                                    'billing_statement' => 'BS',
                                                                    'service_invoice' => 'SI',
                                                                    'debit_credit_note' => 'DCN',
                                                                    'workbook' => 'XLS',
                                                                    default => 'MAN',
                                                                };
                                                                $documentUrl = match($row->source_type) {
                                                                    'billing_statement' => $row->source_id ? route('billing.show', $row->source_id) : null,
                                                                    'service_invoice' => $row->source_id ? route('billing.service-invoices.show', $row->source_id) : null,
                                                                    'debit_credit_note' => $row->source_id ? route('billing.notes.show', $row->source_id) : null,
                                                                    default => null,
                                                                };
                                                            @endphp
                                                            <span class="badge text-bg-light border">{{ $docLabel }}</span>
                                                        </td>
                                                        <td>{{ $row->jo_number ?: '—' }}</td>
                                                        <td>
                                                            @if($documentUrl)
                                                                <a href="{{ $documentUrl }}" class="fw-semibold text-decoration-none">{{ $row->reference_no ?: '—' }}</a>
                                                            @else
                                                                {{ $row->reference_no ?: '—' }}
                                                            @endif
                                                        </td>
                                                        <td class="text-end">{{ $fmt($row->billing_amount) }}</td>
                                                        <td class="text-end">{{ $fmt($row->payment_amount) }}</td>
                                                        <td class="text-end fw-semibold">{{ $fmt($row->balance_amount) }}</td>
                                                        <td>{{ $row->cr_no ?: '—' }}</td>
                                                        <td>{{ $row->ar_no ?: '—' }}</td>
                                                        <td>{{ $row->remarks ?: '—' }}</td>
                                                        <td class="text-end">
                                                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                                                @if($documentUrl)
                                                                    <a class="btn btn-sm btn-outline-secondary" href="{{ $documentUrl }}">Open</a>
                                                                @endif
                                                                <a class="btn btn-sm btn-outline-primary" href="{{ route('accounting.record-monitoring.edit', $row) }}">Edit</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4">
                <div class="text-muted small">
                    Showing {{ $clientGroups->firstItem() }} to {{ $clientGroups->lastItem() }} of {{ $clientGroups->total() }} client groups
                    @if(!empty($quickFilter) && isset($quickFilters[$quickFilter]))
                        for {{ $quickFilters[$quickFilter] }}
                    @endif
                </div>
                <div>
                    {{ $clientGroups->onEachSide(1)->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const form = document.getElementById('recordMonitoringSearchForm');
        const input = document.getElementById('recordMonitoringSearchInput');
        const reset = document.getElementById('recordMonitoringSearchReset');
        if (!form || !input) {
            return;
        }

        if (reset) {
            reset.addEventListener('click', function () {
                window.location.href = @json(route('accounting.record-monitoring.index'));
            });
        }
    })();
</script>
@endpush
