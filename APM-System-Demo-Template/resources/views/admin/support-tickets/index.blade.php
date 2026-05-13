@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-1">Support Tickets</h4>
            <div class="text-muted small">Review and respond to help requests.</div>
        </div>
        <form method="GET" class="d-flex gap-2">
            <select class="form-select" name="status" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="open" @selected($status === 'open')>Open</option>
                <option value="closed" @selected($status === 'closed')>Closed</option>
            </select>
        </form>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Submitted By</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $ticket->name }}</div>
                                    <div class="text-muted small">{{ $ticket->email }}</div>
                                </td>
                                <td>{{ $ticket->subject }}</td>
                                <td>
                                    <span class="badge {{ $ticket->status === 'open' ? 'bg-warning text-dark' : 'bg-success' }}">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </td>
                                <td>{{ $ticket->created_at?->format('M d, Y g:i A') }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.support-tickets.show', $ticket) }}">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No support tickets yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tickets->hasPages())
            <div class="card-footer">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
@endsection
