@extends('layouts.employeehub')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">JO Creation</h2>
            <p class="text-muted mb-0">Manage job orders across departments.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form method="GET" class="d-flex gap-2 js-live-search">
                <input class="form-control" type="search" name="q" value="{{ $search ?? '' }}" placeholder="Search client, consignee, JO no." data-live-search>
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </form>
            @if($canCreate)
                <a class="btn btn-primary" href="{{ route('operations.job-orders.create') }}">Create JO</a>
            @endif
        </div>
    </div>

    @if(session('status') === 'job-order-created')
        <div class="alert alert-success">Job order created.</div>
    @endif
    @if(session('status') === 'job-order-updated')
        <div class="alert alert-success">Job order updated.</div>
    @endif

    <div class="eh-card p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>JO No.</th>
                        <th>Code</th>
                        <th>JO Date</th>
                        <th>Consignee</th>
                        <th>Shipper</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobOrders as $jobOrder)
                        <tr>
                            <td>{{ $jobOrder->number ?? '-' }}</td>
                            <td>{{ $jobOrder->code ?? '-' }}</td>
                            <td>{{ optional($jobOrder->jo_date)->format('M d, Y') ?? '-' }}</td>
                            <td>{{ $jobOrder->consignee ?? '-' }}</td>
                            <td>{{ $jobOrder->shipper ?? '-' }}</td>
                            <td>{{ $jobOrder->status ?? '-' }}</td>
                            <td>{{ $jobOrder->createdBy?->name ?? '-' }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('operations.job-orders.show', $jobOrder) }}">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No job orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $jobOrders->links() }}
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const form = document.querySelector('.js-live-search');
        if (!form) {
            return;
        }
        const input = form.querySelector('[data-live-search]');
        if (!input) {
            return;
        }
        let timer;
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => form.requestSubmit(), 400);
        });
    })();
</script>
@endpush
