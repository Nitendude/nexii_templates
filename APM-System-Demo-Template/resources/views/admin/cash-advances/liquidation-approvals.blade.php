@extends('layouts.employeehub')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">Approve Liquidation / Petty Cash</h2>
            <p class="text-muted mb-0">New submissions and approved records are shown separately to avoid confusion.</p>
        </div>
        <form method="GET" action="{{ route('accounting.cash-advances.liquidations.approvals') }}" class="d-flex gap-2 align-items-center">
            <label class="form-label mb-0">Employee</label>
            <select class="form-select" name="user_id" onchange="this.form.submit()">
                <option value="">All</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" @selected(($userId ?? '') == $employee->id)>{{ $employee->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if(session('status') === 'cash-advance-liquidation-reviewed')
        <div class="alert alert-success">Liquidation/Petty Cash request reviewed.</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="eh-card p-3 mb-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h5 class="mb-1">New Liquidation / Petty Cash Requests</h5>
                <div class="text-muted small">These records have not been approved yet.</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>CA No.</th>
                        <th>Date</th>
                        <th>Ref No.</th>
                        <th>J.O No. / Purpose</th>
                        <th>Amount</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingLiquidations as $liquidation)
                        @php
                            $request = $liquidation->cashAdvanceRequest;
                            $employee = $request?->user;
                            $statusClass = match ($liquidation->status) {
                                'Approved' => 'bg-success',
                                'Rejected' => 'bg-danger',
                                default => 'bg-warning text-dark',
                            };
                        @endphp
                        <tr>
                            <td>{{ $employee?->name ?? '-' }}</td>
                            <td>{{ $request?->ca_no ?? '-' }}</td>
                            <td>{{ optional($liquidation->date)->format('M d, Y') ?? '-' }}</td>
                            <td>{{ $liquidation->ref_no ?: '-' }}</td>
                            <td>{{ $liquidation->jo_number ?: '-' }}</td>
                            <td>PHP {{ number_format((float) $liquidation->amount, 2) }}</td>
                            <td>{{ $liquidation->remarks ?: '-' }}</td>
                            <td><span class="badge {{ $statusClass }}">{{ $liquidation->status ?? 'Pending' }}</span></td>
                            <td class="text-end">
                                <form class="d-inline" method="POST" action="{{ route('accounting.cash-advances.liquidations.review', $liquidation) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Approved">
                                    <button class="btn btn-sm btn-outline-success">Approve</button>
                                </form>
                                <form class="d-inline" method="POST" action="{{ route('accounting.cash-advances.liquidations.review', $liquidation) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Rejected">
                                    <button class="btn btn-sm btn-outline-danger">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No new liquidation/petty cash requests yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $pendingLiquidations->links() }}
        </div>
    </div>

    <div class="eh-card p-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h5 class="mb-1">Approved Liquidation / Petty Cash</h5>
                <div class="text-muted small">These records were already approved by an admin.</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>CA No.</th>
                        <th>Date</th>
                        <th>Ref No.</th>
                        <th>J.O No. / Purpose</th>
                        <th>Amount</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($approvedLiquidations as $liquidation)
                        @php
                            $request = $liquidation->cashAdvanceRequest;
                            $employee = $request?->user;
                        @endphp
                        <tr>
                            <td>{{ $employee?->name ?? '-' }}</td>
                            <td>{{ $request?->ca_no ?? '-' }}</td>
                            <td>{{ optional($liquidation->date)->format('M d, Y') ?? '-' }}</td>
                            <td>{{ $liquidation->ref_no ?: '-' }}</td>
                            <td>{{ $liquidation->jo_number ?: '-' }}</td>
                            <td>PHP {{ number_format((float) $liquidation->amount, 2) }}</td>
                            <td>{{ $liquidation->remarks ?: '-' }}</td>
                            <td><span class="badge bg-success">Approved</span></td>
                            <td class="text-end">
                                <span class="text-muted small">Approved</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No approved liquidation/petty cash records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $approvedLiquidations->links() }}
        </div>
    </div>
@endsection
