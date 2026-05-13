@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="mb-1">Profile Correction Requests</h2>
            <p class="text-muted mb-0">Review employee correction requests.</p>
        </div>
    </div>

    @if(session('status') === 'profile-correction-updated')
        <div class="alert alert-success">Request updated.</div>
    @endif

    <div class="eh-card p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Section</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td>{{ $request->user->name }}</td>
                            <td>{{ $request->section }}</td>
                            <td class="text-muted small">{{ $request->message }}</td>
                            <td>
                                <span class="badge {{ $request->status === 'Reviewed' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $request->status }}
                                </span>
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.profile-corrections.update', $request) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Reviewed">
                                    <button class="btn btn-sm btn-outline-success" @disabled($request->status === 'Reviewed')>
                                        Mark Reviewed
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No correction requests yet.</td>
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
