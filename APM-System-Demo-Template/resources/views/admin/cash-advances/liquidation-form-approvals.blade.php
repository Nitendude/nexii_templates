@extends('layouts.employeehub')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">Liquidation Form Approvals</h2>
            <p class="text-muted mb-0">Review and approve Liquidation Form submissions.</p>
        </div>
    </div>

    @if(session('status') === 'liquidation-form-reviewed')
        <div class="alert alert-success">Liquidation form status updated.</div>
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
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1">Employee</label>
                <select name="user_id" class="form-select">
                    <option value="">All employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" @selected((string) $userId === (string) $employee->id)>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.cash-advances.liquidation-form-approvals') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="eh-card p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Form No.</th>
                        <th>Date</th>
                        <th>Employee</th>
                        <th>J.O No.</th>
                        <th>Client</th>
                        <th>LIQ. No.</th>
                        <th class="text-end">Amount</th>
                        <th>Remarks</th>
                        <th>Receipts</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($forms as $form)
                        @php
                            $statusClass = match($form->status) {
                                'Approved' => 'bg-success',
                                'Rejected' => 'bg-danger',
                                default => 'bg-warning text-dark',
                            };
                        @endphp
                        <tr>
                            <td>{{ $form->liq_no ?? $form->form_no ?? ('LF-' . $form->id) }}</td>
                            <td>{{ optional($form->date)->format('M d, Y') }}</td>
                            <td>{{ $form->user?->name ?? '-' }}</td>
                            <td>{{ $form->jo_number }}</td>
                            <td>{{ $form->client_name ?: '-' }}</td>
                            <td>{{ $form->liq_no ?: '-' }}</td>
                            <td class="text-end">PHP {{ number_format((float) $form->amount, 2) }}</td>
                            <td>{{ $form->remarks ?: '-' }}</td>
                            <td>
                                @if(is_array($form->receipt_paths) && count($form->receipt_paths))
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($form->receipt_paths as $i => $path)
                                            <a href="{{ asset('storage/' . $path) }}" target="_blank" rel="noopener">
                                                Receipt {{ $i + 1 }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $statusClass }}">{{ $form->status }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.cash-advances.liquidation-form-approvals.show', $form) }}" target="_blank">View/Print</a>
                                @if($form->status === 'Pending')
                                        <form method="POST" action="{{ route('admin.cash-advances.liquidation-form-approvals.review', $form) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="Approved">
                                            <button class="btn btn-sm btn-success">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.cash-advances.liquidation-form-approvals.review', $form) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="Rejected">
                                            <button class="btn btn-sm btn-outline-danger">Reject</button>
                                        </form>
                                @else
                                    <span class="text-muted">Reviewed</span>
                                @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">No liquidation form submissions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-2">{{ $forms->links() }}</div>
    </div>
@endsection
