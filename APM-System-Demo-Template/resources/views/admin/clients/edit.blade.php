@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-1">Edit Client</h4>
            <div class="text-muted small">Update client information.</div>
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
            <form method="POST" action="{{ route('admin.clients.update', $client) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Client Name</label>
                        <input class="form-control" name="name" value="{{ old('name', $client->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Business Style</label>
                        <input class="form-control" name="business_style" value="{{ old('business_style', $client->business_style) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">TIN Number</label>
                        <input class="form-control" name="tin_number" value="{{ old('tin_number', $client->tin_number) }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <input class="form-control" name="address" value="{{ old('address', $client->address) }}">
                    </div>
                </div>
                <div class="mt-3 d-flex justify-content-end">
                    <button class="btn btn-primary">Update Client</button>
                </div>
            </form>
        </div>
    </div>
@endsection
