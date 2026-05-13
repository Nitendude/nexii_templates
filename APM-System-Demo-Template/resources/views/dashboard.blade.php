@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="mb-1">Dashboard</h2>
            <p class="text-muted mb-0">Welcome back, {{ $user->name }}.</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="eh-card p-3">
                <div class="text-muted small">My Profile</div>
                <div class="h4 mb-2">Profile Overview</div>
                <a href="{{ route('my-profile') }}" class="text-decoration-none">View profile</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="eh-card p-3">
                <div class="text-muted small">Leave Form</div>
                <div class="h4 mb-2">{{ $remainingLeaveDays }} Days</div>
                <span class="text-muted">Available balance</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="eh-card p-3">
                <div class="text-muted small">Cash Advance</div>
                <div class="h4 mb-2">Request</div>
                <a href="{{ route('cash-advances') }}" class="text-decoration-none">Submit request</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="eh-card p-3">
                <div class="text-muted small">Payslip</div>
                <div class="h4 mb-2">Latest Payslip</div>
                <span class="text-muted">View your pay period summary</span>
            </div>
        </div>
    </div>
@endsection
