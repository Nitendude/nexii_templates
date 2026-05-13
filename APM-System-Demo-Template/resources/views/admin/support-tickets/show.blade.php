@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-1">Support Ticket</h4>
            <div class="text-muted small">Review the request and update the status.</div>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.support-tickets.index') }}">Back</a>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Name</div>
                    <div class="fw-semibold">{{ $ticket->name }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Email</div>
                    <div class="fw-semibold">{{ $ticket->email }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Submitted</div>
                    <div class="fw-semibold">{{ $ticket->created_at?->format('M d, Y g:i A') }}</div>
                </div>
                <div class="col-12">
                    <div class="text-muted small">Subject</div>
                    <div class="fw-semibold">{{ $ticket->subject }}</div>
                </div>
                <div class="col-12">
                    <div class="text-muted small">Message</div>
                    <div class="border rounded p-3 bg-light">{{ $ticket->message }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.support-tickets.update', $ticket) }}">
                @csrf
                @method('PATCH')
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="open" @selected($ticket->status === 'open')>Open</option>
                        <option value="closed" @selected($ticket->status === 'closed')>Closed</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Admin Notes</label>
                    <textarea class="form-control" name="admin_notes" rows="3">{{ old('admin_notes', $ticket->admin_notes) }}</textarea>
                </div>
                <button class="btn btn-primary">Save Updates</button>
            </form>
        </div>
    </div>
@endsection
