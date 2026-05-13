@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-1">Add Client</h4>
            <div class="text-muted small">Create a new client record.</div>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.clients.index') }}">Back</a>
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

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.clients.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Client Name</label>
                        <input class="form-control" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Business Style</label>
                        <input class="form-control" name="business_style" value="{{ old('business_style') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">TIN Number</label>
                        <input class="form-control" name="tin_number" value="{{ old('tin_number') }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <input class="form-control" name="address" value="{{ old('address') }}">
                    </div>
                </div>
                <div class="mt-3 d-flex justify-content-end">
                    <button class="btn btn-primary">Save Client</button>
                </div>
            </form>
        </div>
    </div>
@endsection
