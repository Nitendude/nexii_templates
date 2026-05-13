@extends('layouts.auth')

@section('content')
    <h5 class="mb-3">Confirm Password</h5>
    <p class="text-muted">This is a secure area. Please confirm your password before continuing.</p>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required autocomplete="current-password">
        </div>
        <button class="btn btn-primary" type="submit">Confirm</button>
    </form>
@endsection
