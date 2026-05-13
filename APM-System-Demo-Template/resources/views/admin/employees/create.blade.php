@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Create Employee</h2>
        <a class="btn btn-outline-secondary" href="{{ route('admin.employees.index') }}">Back to list</a>
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
        <form method="POST" action="{{ route('admin.employees.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Last Name</label>
                    <input class="form-control" name="last_name" value="{{ old('last_name') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">First Name</label>
                    <input class="form-control" name="first_name" value="{{ old('first_name') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Middle Initial</label>
                    <input class="form-control" name="middle_initial" value="{{ old('middle_initial') }}" placeholder="M.I. (optional)">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Employee ID (optional)</label>
                    <input class="form-control" name="employee_id" value="{{ old('employee_id') }}" placeholder="Auto-generate if blank">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                        @foreach(['Active', 'Inactive', 'On Leave', 'Terminated'] as $status)
                            <option value="{{ $status }}" @selected(old('status', 'Active') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Temporary Password</label>
                    <input type="text" class="form-control" value="123456789" readonly>
                    <input type="hidden" name="password" value="123456789">
                    <input type="hidden" name="password_confirmation" value="123456789">
                </div>
            </div>
            <div class="mt-3 text-end">
                <button class="btn btn-primary">Create Employee</button>
            </div>
        </form>
    </div>
@endsection
