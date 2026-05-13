@extends('layouts.auth')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <div class="text-uppercase text-muted small"></div>
            <h5 class="mb-1">Sign in to your account</h5>
            <div class="text-muted small">Use your email or employee ID to continue.</div>
        </div>
        <span class="badge rounded-pill text-bg-light border">Encrypted</span>
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

    <form method="POST" action="{{ route('login') }}" class="mt-3">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email or Employee ID</label>
            <input type="text" class="form-control" name="login" value="{{ old('login') }}" required autofocus autocomplete="username" placeholder="e.g. APM-0001 or name@apm.ph">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="loginPassword" name="password" required autocomplete="current-password" placeholder="Enter your password">
                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="loginPassword" aria-label="Show password">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                <label class="form-check-label" for="remember_me">Remember me</label>
            </div>
            @if (Route::has('password.request'))
                <a class="text-decoration-none small" href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <a class="text-decoration-none small" href="{{ route('support.create') }}">Need help? Contact Admin.</a>
            <button class="btn btn-primary px-4" type="submit">Log in</button>
        </div>
    </form>

    <div class="border-top pt-3 mt-4 text-center">
        <span class="text-muted small">New to APM?</span>
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
