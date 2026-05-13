@extends('layouts.auth')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <div class="text-uppercase text-muted small">APM Customs Brokerage</div>
            <h5 class="mb-1">Contact Admin</h5>
            <div class="text-muted small">Tell us what you need help with and we will respond soon.</div>
        </div>
        <span class="badge rounded-pill text-bg-light border">Support</span>
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

    <form method="POST" action="{{ route('support.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" name="name" value="{{ old('name', auth()->user()?->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" class="form-control" name="email" value="{{ old('email', auth()->user()?->email) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Subject</label>
            <input type="text" class="form-control" name="subject" value="{{ old('subject') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea class="form-control" name="message" rows="4" required>{{ old('message') }}</textarea>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <a class="text-decoration-none" href="{{ route('login') }}">Back to Login</a>
            <button class="btn btn-primary px-4" type="submit">Send Request</button>
        </div>
    </form>
@endsection
