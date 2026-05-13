@extends('layouts.employeehub')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">JO Total Sales Report</h2>
            <p class="text-muted mb-0">Gross income summary based on Cost Sheet computation.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('accounting.cost-sheets.index') }}">Back to Cost Sheet</a>
    </div>

    <div class="eh-card p-3 mb-3">
        <form method="GET" action="{{ route('accounting.cost-sheets.sales-report') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">View By</label>
                <select class="form-select" name="period" onchange="this.form.submit()">
                    <option value="week" @selected($period === 'week')>Week</option>
                    <option value="month" @selected($period === 'month')>Month</option>
                    <option value="year" @selected($period === 'year')>Year</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Anchor Date</label>
                <input type="date" class="form-control" name="date" value="{{ $anchorDate }}">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary">Apply Filter</button>
            </div>
            <div class="col-md-3 text-md-end">
                <div class="text-muted small">Period</div>
                <div class="fw-semibold">{{ $periodLabel }}</div>
            </div>
        </form>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="eh-card p-3 h-100">
                <div class="text-muted small">Total Sales</div>
                <div class="fs-4 fw-bold">PHP {{ number_format((float) ($totals['sales_total'] ?? 0), 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="eh-card p-3 h-100">
                <div class="text-muted small">Billed Amount</div>
                <div class="fs-4 fw-bold">PHP {{ number_format((float) ($totals['billed_total'] ?? 0), 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="eh-card p-3 h-100">
                <div class="text-muted small">At Cost</div>
                <div class="fs-4 fw-bold">PHP {{ number_format((float) ($totals['at_cost_total'] ?? 0), 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="eh-card p-3 h-100">
                <div class="text-muted small">Gross Income</div>
                <div class="fs-4 fw-bold">PHP {{ number_format((float) ($totals['gross_income'] ?? 0), 2) }}</div>
            </div>
        </div>
    </div>

    <div class="eh-card p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Client</th>
                        <th>J.O. No.</th>
                        <th class="text-end">Total Sales</th>
                        <th class="text-end">Billed Amount</th>
                        <th class="text-end">At Cost</th>
                        <th class="text-end">Gross Income</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td>{{ optional($row['period_date'])->format('m/d/Y') ?? '-' }}</td>
                            <td>{{ $row['client'] ?: '-' }}</td>
                            <td>{{ $row['jo_display'] ?: '-' }}</td>
                            <td class="text-end">PHP {{ number_format((float) ($row['sales_total'] ?? 0), 2) }}</td>
                            <td class="text-end">PHP {{ number_format((float) ($row['billed_total'] ?? 0), 2) }}</td>
                            <td class="text-end">PHP {{ number_format((float) ($row['at_cost_total'] ?? 0), 2) }}</td>
                            <td class="text-end fw-semibold">PHP {{ number_format((float) ($row['gross_income'] ?? 0), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No JO totals found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
