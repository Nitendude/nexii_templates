@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="mb-1">Leave Form Approvals</h2>
            <p class="text-muted mb-0">Review and approve employee leave requests.</p>
        </div>
    </div>

    @if(session('status') === 'leave-updated')
        <div class="alert alert-success">Leave request updated.</div>
    @endif

    <div class="eh-card p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Dates</th>
                        <th>Working Days</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td>{{ $request->user->name }}</td>
                            <td>{{ $request->start_date->format('M d, Y') }} - {{ $request->end_date->format('M d, Y') }}</td>
                            <td>{{ $request->workingDays() }}</td>
                            <td>{{ $request->type }}</td>
                            <td>{{ $request->reason ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $request->status === 'Approved' ? 'bg-success' : ($request->status === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ $request->status }}
                                </span>
                            </td>
                            <td class="text-end">
                                <form class="d-inline" method="POST" action="{{ route('admin.timeoff.update', $request) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Approved">
                                    <button class="btn btn-sm btn-outline-success" @disabled($request->status === 'Approved')>Approve</button>
                                </form>
                                <form class="d-inline" method="POST" action="{{ route('admin.timeoff.update', $request) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Rejected">
                                    <button class="btn btn-sm btn-outline-danger" @disabled($request->status === 'Rejected')>Reject</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No leave requests yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $requests->links() }}
        </div>
    </div>
@endsection
