@extends('layouts.auth')

@section('content')
    <h5 class="mb-3">Verify Email</h5>
    <p class="text-muted">
        Thanks for signing up! Please verify your email address by clicking the link we just emailed to you.
    </p>

    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-success">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn btn-primary" type="submit">Resend Verification Email</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-secondary" type="submit">Logout</button>
        </form>
    </div>
@endsection
