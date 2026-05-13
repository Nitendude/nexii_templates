@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Edit JO</h2>
        <a class="btn btn-outline-secondary" href="{{ route('operations.job-orders.show', $jobOrder) }}">Back to JO</a>
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

    <div class="eh-card p-3">
        <form method="POST" action="{{ route('operations.job-orders.update', $jobOrder) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('job-orders._form', ['showAssignee' => true, 'canAssign' => $canAssign ?? false])
            <div class="mt-3 d-flex justify-content-end">
                <button class="btn btn-primary">Update JO</button>
            </div>
        </form>
    </div>
@endsection
