@extends('layouts.employeehub')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">CA Payments</h2>
            <p class="text-muted mb-0">Unpaid and paid cash advances are shown separately to avoid confusion.</p>
        </div>
        <form method="GET" action="{{ route('accounting.cash-advances.payments') }}" class="d-flex gap-2 align-items-center">
            <label class="form-label mb-0">Status</label>
            <select class="form-select" name="payment_status" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="unpaid" @selected($paymentStatus === 'unpaid')>Unpaid</option>
                <option value="paid" @selected($paymentStatus === 'paid')>Paid</option>
            </select>
        </form>
    </div>

    @if(session('status') === 'cash-advance-paid')
        <div class="alert alert-success">Cash advance marked as paid.</div>
    @endif

    @php
        $groupedUnpaidRequests = $unpaidRequests->getCollection()->groupBy(fn ($request) => $request->user->name ?? 'Unknown Employee');
        $groupedPaidRequests = $paidRequests->getCollection()->groupBy(fn ($request) => $request->user->name ?? 'Unknown Employee');
    @endphp

    <div class="eh-card p-3 mb-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h5 class="mb-1">Unpaid Cash Advances</h5>
                <div class="text-muted small">Upload proof here to mark approved cash advances as paid.</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>CA No.</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Paid At</th>
                        <th>Proof</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groupedUnpaidRequests as $employeeName => $requests)
                        <tr class="table-secondary">
                            <td colspan="7" class="fw-semibold">{{ $employeeName }}</td>
                        </tr>
                        @foreach($requests as $request)
                            <tr>
                                <td></td>
                                <td>{{ $request->ca_no ?? '—' }}</td>
                                <td>PHP {{ number_format($request->amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ $request->paid_at ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $request->paid_at ? 'Paid' : 'Unpaid' }}
                                    </span>
                                </td>
                                <td>{{ $request->paid_at ? $request->paid_at->format('M d, Y') : '—' }}</td>
                                <td>
                                    @if($request->paid_proof_path)
                                        <a href="{{ Storage::url($request->paid_proof_path) }}" target="_blank">View</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('accounting.cash-advances.payments.update', $request) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PATCH')
                                        <input type="file" name="paid_proof" class="form-control form-control-sm mb-2" accept=".jpg,.jpeg,.png,.pdf" required>
                                        <button class="btn btn-sm btn-outline-primary">Mark Paid</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No unpaid cash advances yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $unpaidRequests->links() }}
        </div>
    </div>

    <div class="eh-card p-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h5 class="mb-1">Paid Cash Advances</h5>
                <div class="text-muted small">These cash advances already have payment proof uploaded.</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>CA No.</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Paid At</th>
                        <th>Proof</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groupedPaidRequests as $employeeName => $requests)
                        <tr class="table-secondary">
                            <td colspan="7" class="fw-semibold">{{ $employeeName }}</td>
                        </tr>
                        @foreach($requests as $request)
                            <tr>
                                <td></td>
                                <td>{{ $request->ca_no ?? '—' }}</td>
                                <td>PHP {{ number_format($request->amount, 2) }}</td>
                                <td><span class="badge bg-success">Paid</span></td>
                                <td>{{ $request->paid_at ? $request->paid_at->format('M d, Y') : '—' }}</td>
                                <td>
                                    @if($request->paid_proof_path)
                                        <a href="{{ Storage::url($request->paid_proof_path) }}" target="_blank">View</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="text-muted small">Paid</span>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No paid cash advances yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $paidRequests->links() }}
        </div>
    </div>
@endsection
