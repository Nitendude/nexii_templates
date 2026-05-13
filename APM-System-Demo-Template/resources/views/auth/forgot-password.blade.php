@extends('layouts.auth')

@section('content')
    <h5 class="mb-3">Forgot Password</h5>

    <p class="text-muted">Enter your email and we will send you a reset link.</p>

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

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
        </div>
        <button class="btn btn-primary" type="submit">Email Password Reset Link</button>
    </form>
@endsection
