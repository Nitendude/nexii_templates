@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Profile Correction Request</h2>
    </div>

    @if(session('status') === 'profile-correction-requested')
        <div class="alert alert-success">Your request has been sent to Admin.</div>
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

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="eh-card p-3">
                <h5 class="mb-3">Request a Correction</h5>
                <form method="POST" action="{{ route('profile-corrections.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Section</label>
                        <select class="form-select" name="section" required>
                            <option value="">Select section</option>
                            @foreach(['Personal Information', 'Emergency Contact'] as $section)
                                <option value="{{ $section }}" @selected(old('section') === $section)>{{ $section }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correction Details</label>
                        <textarea class="form-control" name="message" rows="5" required>{{ old('message') }}</textarea>
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
                                <th>Date</th>
                                <th>Section</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                                <tr>
                                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                                    <td>{{ $request->section }}</td>
                                    <td>
                                        <span class="badge {{ $request->status === 'Reviewed' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $request->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No requests yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
