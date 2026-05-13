@extends('layouts.employeehub')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">Cost Sheet</h2>
            <p class="text-muted mb-0">Available cost sheets are generated automatically from existing billing statements, service invoices, debit/credit notes, or reimbursable vouchers.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <form method="GET" action="{{ route('accounting.cost-sheets.index') }}" class="d-flex gap-2 align-items-center" id="costSheetSearchForm">
                <input class="form-control" type="text" name="q" id="costSheetSearchInput" value="{{ $search ?? '' }}" placeholder="Search client, JO no., reference" style="min-width: 280px;">
                <button type="button" class="btn btn-outline-secondary" id="costSheetSearchReset">Reset</button>
            </form>
            <a class="btn btn-outline-secondary" href="{{ route('accounting.cost-sheets.create') }}">Open Blank View</a>
        </div>
    </div>

    <div class="eh-card p-4">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Clients</div>
                    <div class="fs-4 fw-semibold">{{ number_format((float) (($clientGroups ?? collect())->count()), 0) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Cost Sheets</div>
                    <div class="fs-4 fw-semibold">{{ number_format((float) (($availableCostSheets ?? collect())->count()), 0) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Latest Source Date</div>
                    <div class="fs-5 fw-semibold">
                        @php $latestSheet = ($availableCostSheets ?? collect())->first(); @endphp
                        {{ optional($latestSheet['created_at'] ?? null)->format('m/d/Y') ?: '—' }}
                    </div>
                </div>
            </div>
        </div>

        @if(($clientGroups ?? collect())->isEmpty())
            <div class="text-muted">No cost sheets are available yet. A cost sheet will appear here once a billing statement, service invoice, debit/credit note, or reimbursable voucher exists for a JO.</div>
        @else
            <div class="accordion" id="costSheetAccordion">
                @foreach($clientGroups as $group)
                    @php $collapseId = 'cost-sheet-client-' . $loop->index; @endphp
                    <div class="accordion-item mb-3 border rounded overflow-hidden">
                        <h2 class="accordion-header" id="{{ $collapseId }}-header">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="{{ $collapseId }}">
                                <div class="d-flex flex-wrap justify-content-between align-items-center w-100 gap-3 me-3">
                                    <div>
                                        <div class="fw-semibold">{{ $group['client'] }}</div>
                                        <div class="text-muted small">{{ number_format((float) $group['sheet_count'], 0) }} Cost Sheet{{ $group['sheet_count'] === 1 ? '' : 's' }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-muted small">Latest Source Date</div>
                                        <div class="fw-semibold">{{ optional($group['latest_created_at'])->format('m/d/Y') ?: '—' }}</div>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="{{ $collapseId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="{{ $collapseId }}-header" data-bs-parent="#costSheetAccordion">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>J.O. No.</th>
                                                <th>Reference</th>
                                                <th>Source</th>
                                                <th>Date</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($group['rows'] as $sheet)
                                                <tr>
                                                    <td>{{ $sheet['jo_display'] }}</td>
                                                    <td>{{ $sheet['reference_no'] ?: '-' }}</td>
                                                    <td>
                                                        {{
                                                            match ($sheet['document_source']) {
                                                                'service_invoice' => 'Service Invoice',
                                                                'debit_credit_note' => 'Debit / Credit Note',
                                                                'reimbursable_voucher' => 'Reimbursable Voucher',
                                                                default => 'Billing Statement',
                                                            }
                                                        }}
                                                    </td>
                                                    <td>{{ optional($sheet['created_at'])->format('m/d/Y') }}</td>
                                                    <td class="text-end">
                                                        <a
                                                            class="btn btn-sm btn-primary"
                                                            href="{{ route('accounting.cost-sheets.create', ['client' => $sheet['client'], 'jo' => $sheet['jo_number']]) }}"
                                                        >
                                                            View Cost Sheet
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const form = document.getElementById('costSheetSearchForm');
        const input = document.getElementById('costSheetSearchInput');
        const reset = document.getElementById('costSheetSearchReset');
        const groups = Array.from(document.querySelectorAll('#costSheetAccordion .accordion-item'));
        if (!form || !input || groups.length === 0) {
            return;
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
        });

        const filterGroups = function () {
            const query = input.value.trim().toLowerCase();

            groups.forEach(function (group) {
                const header = group.querySelector('.accordion-button');
                const rows = Array.from(group.querySelectorAll('tbody tr'));
                const headerText = (header ? header.textContent : group.textContent).toLowerCase();
                const groupMatches = query === '' || headerText.includes(query);

                let visibleRows = 0;
                rows.forEach(function (row) {
                    const rowMatches = groupMatches || row.textContent.toLowerCase().includes(query);
                    row.style.display = rowMatches ? '' : 'none';
                    if (rowMatches) {
                        visibleRows += 1;
                    }
                });

                group.style.display = visibleRows > 0 ? '' : 'none';
            });
        };

        input.addEventListener('input', filterGroups);

        if (reset) {
            reset.addEventListener('click', function () {
                input.value = '';
                filterGroups();
                input.focus();
            });
        }

        filterGroups();
    })();
</script>
@endpush
