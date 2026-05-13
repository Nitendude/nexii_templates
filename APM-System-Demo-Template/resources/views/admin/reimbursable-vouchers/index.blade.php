@extends('layouts.employeehub')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">Reimbursable Voucher Storage</h2>
            <p class="text-muted mb-0">Saved reimbursable vouchers.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('accounting.reimbursable-vouchers.create') }}">Create Voucher</a>
    </div>

    @if(session('status') === 'reimbursable-voucher-saved')
        <div class="alert alert-success">Voucher saved successfully.</div>
    @endif
    @if(session('status') === 'reimbursable-voucher-updated')
        <div class="alert alert-success">Voucher updated successfully.</div>
    @endif
    @if(session('status') === 'reimbursable-voucher-cancelled')
        <div class="alert alert-warning">Voucher cancelled. Its original voucher number can now be reused.</div>
    @endif

    <div class="eh-card p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Voucher No.</th>
                        <th>Date</th>
                        <th>JO No.</th>
                        <th>Client</th>
                        <th>Payee</th>
                        <th>Description</th>
                        <th>Total</th>
                        <th>Created By</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $voucher)
                        <tr>
                            <td>
                                {{ $voucher->cancelled_voucher_no ?: $voucher->voucher_no }}
                                @if(($voucher->status ?? 'active') === 'cancelled')
                                    <span class="badge text-bg-danger ms-1">Cancelled</span>
                                @endif
                            </td>
                            <td>{{ optional($voucher->voucher_date)->format('M d, Y') }}</td>
                            <td>{{ $voucher->items->first()?->jo_no ?: '-' }}</td>
                            <td>{{ $voucher->items->first()?->client_name ?: '-' }}</td>
                            <td>{{ $voucher->items->first()?->payee ?: '-' }}</td>
                            <td>{{ $voucher->items->first()?->description ?: '-' }}</td>
                            <td>PHP {{ number_format((float) $voucher->total_amount, 2) }}</td>
                            <td>{{ $voucher->creator?->name ?: '-' }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    @if(($voucher->status ?? 'active') !== 'cancelled')
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('accounting.reimbursable-vouchers.edit', $voucher) }}">Edit</a>
                                        <form method="POST" action="{{ route('accounting.reimbursable-vouchers.cancel', $voucher) }}" onsubmit="return confirm('Cancel this voucher? This will free the voucher number for reuse.');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                        </form>
                                    @endif
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('accounting.reimbursable-vouchers.show', $voucher) }}">View / Print</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No vouchers saved yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $vouchers->links() }}
        </div>
    </div>
@endsection
