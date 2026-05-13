@extends('layouts.auth')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <div class="text-uppercase text-muted small">APM Customs Brokerage</div>
            <h5 class="mb-1">Verify your email</h5>
            <div class="text-muted small">Enter the 6-digit code sent to your email.</div>
        </div>
        <span class="badge rounded-pill text-bg-light border">OTP</span>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
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

    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Verification Code</label>
            <input type="text" class="form-control" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required autofocus>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <button class="btn btn-primary px-4" type="submit">Verify</button>
        </div>
    </form>

    <form method="POST" action="{{ route('otp.resend') }}" class="mt-3">
        @csrf
        <button class="btn btn-link p-0 text-decoration-none" type="submit">Resend code</button>
    </form>
@endsection
