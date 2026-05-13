@extends('layouts.auth')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <div class="text-uppercase text-muted small">APM Customs Brokerage</div>
            <h5 class="mb-1">Create your account</h5>
            <div class="text-muted small">We will send a 6-digit OTP to verify your email.</div>
        </div>
        <span class="badge rounded-pill text-bg-light border">Register</span>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">First Name</label>
                <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name">
            </div>
            <div class="col-md-6">
                <label class="form-label">Last Name</label>
                <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="username">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="registerPassword" name="password" required autocomplete="new-password">
                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="registerPassword" aria-label="Show password">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="registerPasswordConfirmation" name="password_confirmation" required autocomplete="new-password">
                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="registerPasswordConfirmation" aria-label="Show confirm password">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <a class="text-decoration-none" href="{{ route('login') }}">Already registered?</a>
            <button class="btn btn-primary" type="submit">Register</button>
        </div>
    </form>

    <div class="border-top pt-3 mt-4 text-center">
        <span class="text-muted small">Registration is invite-only.</span>
        <a class="text-decoration-none small ms-1" href="{{ route('support.create') }}">Request a registration link from admin.</a>
    </div>

    <script>
        (function () {
            document.querySelectorAll('.toggle-password').forEach((button) => {
                button.addEventListener('click', function () {
                    const targetId = button.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    const icon = button.querySelector('i');
                    if (!input || !icon) {
                        return;
                    }

                    const isHidden = input.type === 'password';
                    input.type = isHidden ? 'text' : 'password';
                    icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
                });
            });
        })();
    </script>
@endsection
