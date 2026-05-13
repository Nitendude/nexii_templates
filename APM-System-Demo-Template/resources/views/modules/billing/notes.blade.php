@extends('layouts.employeehub')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h2 class="mb-1">Debit / Credit Note</h2>
            <p class="text-muted mb-0">Find a JO before creating a Debit/Credit Note.</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('billing.notes.documents') }}">Debit/Credit Note Documents</a>
        </div>
    </div>

    @if(session('status') === 'dcn-select-jo-first')
        <div class="alert alert-info">Select a Job Order below first to create a Debit/Credit Note.</div>
    @endif

    <div class="eh-card p-3 mb-4">
        <form method="GET" class="d-flex gap-2">
            <input class="form-control" type="search" name="q" value="{{ $search ?? '' }}" placeholder="Search consignee, shipper, JO no.">
            <button class="btn btn-outline-primary" type="submit">Search</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header fw-semibold">Job Orders</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>JO No.</th>
                            <th>Code</th>
                            <th>Consignee</th>
                            <th>Shipper</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobOrders as $jobOrder)
                            <tr class="dcn-jo-row" style="cursor:pointer;" data-href="{{ route('billing.notes.create', ['job_order_id' => $jobOrder->id]) }}">
                                <td>{{ $jobOrder->number ?? '-' }}</td>
                                <td>{{ $jobOrder->code ?? '-' }}</td>
                                <td>{{ $jobOrder->consignee ?? '-' }}</td>
                                <td>{{ $jobOrder->shipper ?? '-' }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary"
                                       href="{{ route('billing.notes.create', ['job_order_id' => $jobOrder->id]) }}"
                                       onclick="event.stopPropagation();">
                                        Create Debit/Credit Note
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No job orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.dcn-jo-row').forEach((row) => {
            row.addEventListener('click', () => {
                const href = row.getAttribute('data-href');
                if (href) {
                    window.location.href = href;
                }
            });
        });
    });
</script>
@endpush
