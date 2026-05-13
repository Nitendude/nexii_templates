@extends('layouts.employeehub')

@section('content')
    @php
        $profile = $employee->profile;
        $employment = $employee->employmentDetail;
        $emergency = $employee->emergencyContact;
        $allowances = $employee->allowances;
    @endphp

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Edit Employee</h2>
        <a class="btn btn-outline-secondary" href="{{ route('admin.employees.show', $employee) }}">Back to profile</a>
    </div>

    @if(session('status'))
        <div class="alert alert-success">
            @if(session('status') === 'password-reset-link-sent')
                Password reset link emailed to {{ $employee->email }}.
            @else
                {{ session('status') }}
            @endif
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
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

    <form id="send-reset-form" method="POST" action="{{ route('admin.employees.send-password-reset', $employee) }}" class="d-none">
        @csrf
    </form>

    <form method="POST" action="{{ route('admin.employees.update', $employee) }}">
        @csrf
        @method('PUT')

        <div class="eh-card p-3 mb-3">
            <h5 class="mb-3">Core Details</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input class="form-control" name="name" value="{{ old('name', $employee->name) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                        @foreach(['Active', 'Inactive', 'On Leave', 'Terminated'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $employee->status) === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Employee ID</label>
                    <input class="form-control" value="{{ $employee->employee_id }}" disabled>
                </div>
            </div>
        </div>

        <div class="eh-card p-3 mb-3">
            <h5 class="mb-3">Reset Password</h5>
            <p class="text-muted mb-3">Send the employee a secure reset link so they can choose a new password themselves.</p>
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div>
                    <div class="small text-muted">Reset Email</div>
                    <div class="fw-semibold">{{ $employee->email }}</div>
                </div>
                <button class="btn btn-outline-primary" type="submit" form="send-reset-form">Email Reset Link</button>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="eh-card p-3">
                    <h5 class="mb-3">Personal Information</h5>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input class="form-control" name="profile[contact_number]" value="{{ old('profile.contact_number', $profile?->contact_number) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Birthdate</label>
                        <input type="date" class="form-control" name="profile[birthdate]" value="{{ old('profile.birthdate', optional($profile?->birthdate)->format('Y-m-d')) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <select class="form-select" name="profile[gender]">
                            <option value="">Select</option>
                            @foreach(['Male', 'Female', 'Other', 'Prefer not to say'] as $genderOption)
                                <option value="{{ $genderOption }}" @selected(old('profile.gender', $profile?->gender) === $genderOption)>{{ $genderOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input class="form-control" name="profile[address]" value="{{ old('profile.address', $profile?->address) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Civil Status</label>
                        <select class="form-select" name="profile[civil_status]">
                            <option value="">Select</option>
                            @foreach(['Single', 'Married', 'Separated', 'Widowed'] as $statusOption)
                                <option value="{{ $statusOption }}" @selected(old('profile.civil_status', $profile?->civil_status) === $statusOption)>{{ $statusOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Tax ID No.</label>
                        <input class="form-control" name="profile[tax_ident_no]" value="{{ old('profile.tax_ident_no', $profile?->tax_ident_no) }}">
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="eh-card p-3">
                    <h5 class="mb-3">Employment Details</h5>
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <input class="form-control" name="employment[position]" value="{{ old('employment.position', $employment?->position) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Employment Type</label>
                        <select class="form-select" name="employment[employment_type]">
                            <option value="">Select</option>
                            @foreach(['Full-time', 'Part-time', 'Contract'] as $type)
                                <option value="{{ $type }}" @selected(old('employment.employment_type', $employment?->employment_type) === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select class="form-select" name="employment[department]">
                            <option value="">Select</option>
                            @foreach(['Documentation', 'Operations', 'Admin', 'Accounting', 'Billing', 'Finance', 'Management', 'IT'] as $dept)
                                <option value="{{ $dept }}" @selected(old('employment.department', $employment?->department) === $dept)>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date Joined</label>
                        <input type="date" class="form-control" name="employment[date_joined]" value="{{ old('employment.date_joined', optional($employment?->date_joined)->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-lg-6">
                <div class="eh-card p-3">
                    <h5 class="mb-3">Emergency Contact Info</h5>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="emergency[name]" value="{{ old('emergency.name', $emergency?->name) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Relationship</label>
                        <input class="form-control" name="emergency[relationship]" value="{{ old('emergency.relationship', $emergency?->relationship) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input class="form-control" name="emergency[contact_number]" value="{{ old('emergency.contact_number', $emergency?->contact_number) }}">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Address</label>
                        <input class="form-control" name="emergency[address]" value="{{ old('emergency.address', $emergency?->address) }}">
                    </div>
                </div>
            </div>

        </div>

        <div class="eh-card p-3 mt-3">
            <h5 class="mb-3">Allowances</h5>
            <div class="row g-2 text-muted small mb-2">
                <div class="col-md-5">Type</div>
                <div class="col-md-3">Amount</div>
                <div class="col-md-2">Currency</div>
            </div>

            @php
                $rows = max(3, $allowances->count());
            @endphp

            @for($i = 0; $i < $rows; $i++)
                <div class="row g-2 mb-2">
                    <div class="col-md-5">
                        <input class="form-control" name="allowances[type][]" value="{{ old('allowances.type.' . $i, $allowances[$i]->type ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <input class="form-control" name="allowances[amount][]" value="{{ old('allowances.amount.' . $i, $allowances[$i]->amount ?? '') }}">
                    </div>
                    <div class="col-md-2">
                        <input class="form-control" name="allowances[currency][]" value="{{ old('allowances.currency.' . $i, $allowances[$i]->currency ?? 'PHP') }}">
                    </div>
                </div>
            @endfor
        </div>

        <div class="mt-3 d-flex justify-content-end">
            <button class="btn btn-primary">Save Changes</button>
        </div>
    </form>
@endsection
