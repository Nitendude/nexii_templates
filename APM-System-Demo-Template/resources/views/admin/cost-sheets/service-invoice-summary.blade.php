@extends('layouts.employeehub')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">Service Invoice Summary</h2>
            <p class="text-muted mb-0">Separate view of Service Invoice VAT and withholding figures for Accounting.</p>
        </div>
        <form method="GET" action="{{ route('accounting.cost-sheets.service-invoice-summary') }}" class="d-flex gap-2 align-items-center" id="siSummarySearchForm">
            <input class="form-control" type="text" name="q" id="siSummarySearchInput" value="{{ $search ?? '' }}" placeholder="Search client, JO no., SI no." style="min-width: 280px;">
            <button type="button" class="btn btn-outline-secondary" id="siSummarySearchReset">Reset</button>
        </form>
    </div>

    <div class="eh-card p-4">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Clients</div>
                    <div class="fs-4 fw-semibold">{{ number_format((float) (($clientGroups ?? collect())->count()), 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Service Invoices</div>
                    <div class="fs-4 fw-semibold">{{ number_format((float) ($totals['si_count'] ?? 0), 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Total Less: VAT</div>
                    <div class="fs-4 fw-semibold">{{ number_format((float) ($totals['less_vat'] ?? 0), 2) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Total Less: Withholding Tax</div>
                    <div class="fs-4 fw-semibold">{{ number_format((float) ($totals['less_withholding_tax'] ?? 0), 2) }}</div>
                </div>
            </div>
        </div>

        @if(($clientGroups ?? collect())->isEmpty())
            <div class="text-muted">No service invoices found.</div>
        @else
            <div class="accordion" id="siSummaryAccordion">
                @foreach($clientGroups as $group)
                    @php $collapseId = 'si-client-' . $loop->index; @endphp
                    <div class="accordion-item mb-3 border rounded overflow-hidden">
                        <h2 class="accordion-header" id="{{ $collapseId }}-header">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="{{ $collapseId }}">
                                <div class="d-flex flex-wrap justify-content-between align-items-center w-100 gap-3 me-3">
                                    <div>
                                        <div class="fw-semibold">{{ $group['client'] }}</div>
                                        <div class="text-muted small">{{ number_format((float) $group['si_count'], 0) }} Service Invoice{{ $group['si_count'] === 1 ? '' : 's' }}</div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-4 text-end">
                                        <div>
                                            <div class="text-muted small">Less: VAT</div>
                                            <div class="fw-semibold">{{ number_format((float) $group['less_vat'], 2) }}</div>
                                        </div>
                                        <div>
                                            <div class="text-muted small">Less: Withholding Tax</div>
                                            <div class="fw-semibold">{{ number_format((float) $group['less_withholding_tax'], 2) }}</div>
                                        </div>
                                        <div>
                                            <div class="text-muted small">Total Amount Due</div>
                                            <div class="fw-semibold">{{ number_format((float) $group['total_amount_due'], 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="{{ $collapseId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="{{ $collapseId }}-header" data-bs-parent="#siSummaryAccordion">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>J.O. No.</th>
                                                <th>SI No.</th>
                                                <th class="text-end">Total Sales</th>
                                                <th class="text-end">Less: VAT</th>
                                                <th class="text-end">Amount: Net of VAT</th>
                                                <th class="text-end">Less: Withholding Tax</th>
                                                <th class="text-end">Add: VAT</th>
                                                <th class="text-end">Total Amount Due</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($group['rows'] as $row)
                                                <tr>
                                                    <td>{{ optional($row['created_at'])->format('m/d/Y') }}</td>
                                                    <td>{{ $row['jo_display'] }}</td>
                                                    <td>{{ $row['statement_no'] ?: '—' }}</td>
                                                    <td class="text-end">{{ number_format((float) $row['total_sales'], 2) }}</td>
                                                    <td class="text-end">{{ number_format((float) $row['less_vat'], 2) }}</td>
                                                    <td class="text-end">{{ number_format((float) $row['amount_net_vat'], 2) }}</td>
                                                    <td class="text-end">{{ number_format((float) $row['less_withholding_tax'], 2) }}</td>
                                                    <td class="text-end">{{ number_format((float) $row['add_vat'], 2) }}</td>
                                                    <td class="text-end fw-semibold">{{ number_format((float) $row['total_amount_due'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="3" class="text-end">Client Total</th>
                                                <th class="text-end">{{ number_format((float) $group['total_sales'], 2) }}</th>
                                                <th class="text-end">{{ number_format((float) $group['less_vat'], 2) }}</th>
                                                <th class="text-end">{{ number_format((float) $group['amount_net_vat'], 2) }}</th>
                                                <th class="text-end">{{ number_format((float) $group['less_withholding_tax'], 2) }}</th>
                                                <th class="text-end">{{ number_format((float) $group['add_vat'], 2) }}</th>
                                                <th class="text-end">{{ number_format((float) $group['total_amount_due'], 2) }}</th>
                                            </tr>
                                        </tfoot>
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
        const form = document.getElementById('siSummarySearchForm');
        const input = document.getElementById('siSummarySearchInput');
        const reset = document.getElementById('siSummarySearchReset');
        const groups = Array.from(document.querySelectorAll('#siSummaryAccordion .accordion-item'));
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
