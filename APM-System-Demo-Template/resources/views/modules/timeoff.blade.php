@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Leave Form</h2>
    </div>

    @if(session('status') === 'leave-requested')
        <div class="alert alert-success">Leave request submitted for approval.</div>
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

    <div class="row g-3 mb-3">
        <div class="col-lg-4">
            <div class="eh-card p-3">
                <div class="text-muted small">Total Leave Days</div>
                <div class="h4 mb-0">{{ $totalLeaveDays }}</div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="eh-card p-3">
                <div class="text-muted small">Used Leave Days</div>
                <div class="h4 mb-0">{{ $usedLeaveDays }}</div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="eh-card p-3">
                <div class="text-muted small">Remaining Leave Days</div>
                <div class="h4 mb-0">{{ $remainingLeaveDays }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="eh-card p-3">
                <h5 class="mb-3">Request Leave</h5>
                <form method="POST" action="{{ route('timeoff.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" value="{{ old('start_date') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" value="{{ old('end_date') }}" required>
                        <div class="form-text">Saturdays and Sundays are excluded from the leave count.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Leave Type</label>
                        <select class="form-select" name="type" required>
                            @foreach(['Vacation', 'Sick', 'Emergency', 'Unpaid', 'Other'] as $type)
                                <option value="{{ $type }}" @selected(old('type') === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea class="form-control" name="reason" rows="3">{{ old('reason') }}</textarea>
                    </div>
                    <button class="btn btn-primary">Submit Request</button>
                </form>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="eh-card p-3">
                <h5 class="mb-3">My Requests</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Dates</th>
                                <th>Working Days</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Requested</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                                <tr>
                                    <td>{{ $request->start_date->format('M d, Y') }} - {{ $request->end_date->format('M d, Y') }}</td>
                                    <td>{{ $request->workingDays() }}</td>
                                    <td>{{ $request->type }}</td>
                                    <td>
                                        <span class="badge {{ $request->status === 'Approved' ? 'bg-success' : ($request->status === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                            {{ $request->status }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No leave requests yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
