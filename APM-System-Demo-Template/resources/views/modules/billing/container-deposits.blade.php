@extends('layouts.employeehub')

@section('content')
    @php
        $canOpenBillingDocuments = auth()->user()?->hasAccess('billing');
    @endphp
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h2 class="mb-1">Container Deposit Records</h2>
            <p class="text-muted mb-0">All billing records with a Container Deposit line.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if($canOpenBillingDocuments)
                <a class="btn btn-outline-primary" href="{{ route('billing.storage') }}">Master Billing Storage</a>
                <a class="btn btn-outline-secondary" href="{{ route('billing') }}">Back to Billing</a>
            @endif
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6 col-xl-3">
            <div class="eh-card p-3 h-100">
                <div class="text-muted small">Records Found</div>
                <div class="fs-4 fw-semibold">{{ number_format($totalRecords ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="eh-card p-3 h-100">
                <div class="text-muted small">Container Deposit Total</div>
                <div class="fs-4 fw-semibold">PHP {{ number_format((float) ($totalAmount ?? 0), 2) }}</div>
            </div>
        </div>
    </div>

    <div class="eh-card p-3 mb-3">
        <form method="GET" class="d-flex flex-wrap gap-2">
            <input class="form-control" type="search" name="q" value="{{ $search ?? '' }}" placeholder="Search JO no., code, consignee, document no., description" style="min-width: min(100%, 420px);">
            <button class="btn btn-outline-primary" type="submit">Search</button>
            @if(!empty($search))
                <a class="btn btn-outline-secondary" href="{{ route('accounting.container-deposits') }}">Reset</a>
            @endif
        </form>
    </div>

    <div class="eh-card p-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div class="fw-semibold">Container Deposit List</div>
            <div class="text-muted small">
                Showing {{ $containerDeposits->firstItem() ?? 0 }}-{{ $containerDeposits->lastItem() ?? 0 }} of {{ $containerDeposits->total() }}
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Document</th>
                        <th>JO No.</th>
                        <th>Code</th>
                        <th>Consignee</th>
                        <th>Container Deposit Lines</th>
                        <th class="text-end">Amount</th>
                        <th>Date</th>
                        <th>Created By</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($containerDeposits as $entry)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $entry['document_type'] }}</div>
                                <div class="text-muted small">#{{ $entry['document_no'] ?: '-' }}</div>
                            </td>
                            <td>{{ $entry['jo_no'] }}</td>
                            <td>{{ $entry['jo_code'] }}</td>
                            <td>{{ $entry['consignee'] }}</td>
                            <td>
                                @foreach($entry['rows'] as $row)
                                    <div class="mb-1">
                                        <span class="badge text-bg-light border me-1">{{ $row['section'] }}</span>
                                        <span>{{ $row['description'] }}</span>
                                        <span class="text-muted small">- PHP {{ number_format((float) $row['amount'], 2) }}</span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="text-end fw-semibold">PHP {{ number_format((float) $entry['total_amount'], 2) }}</td>
                            <td>{{ optional($entry['date'])->format('M d, Y') ?? '-' }}</td>
                            <td>{{ $entry['created_by'] }}</td>
                            <td class="text-end">
                                @if($canOpenBillingDocuments)
                                    <a class="btn btn-sm btn-outline-primary" href="{{ $entry['url'] }}">Open</a>
                                @else
                                    <span class="text-muted small">No billing access</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No container deposit billing records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $containerDeposits->links() }}
        </div>
    </div>
@endsection
