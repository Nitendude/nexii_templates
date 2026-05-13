@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Complete Your Profile</h2>
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
        <form method="POST" action="{{ route('onboarding.update') }}">
            @csrf
            @if($user->created_by_admin)
                <h5 class="mb-3">Change Temporary Password</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Current Password</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>
            @endif

            <div class="row g-3">
                <div class="col-lg-6">
                    <h5 class="mb-3">Personal Information</h5>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input class="form-control" name="profile[contact_number]" value="{{ old('profile.contact_number') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Birthdate</label>
                        <input type="date" class="form-control" name="profile[birthdate]" value="{{ old('profile.birthdate') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <select class="form-select" name="profile[gender]" required>
                            <option value="">Select</option>
                            @foreach(['Male', 'Female', 'Other', 'Prefer not to say'] as $genderOption)
                                <option value="{{ $genderOption }}" @selected(old('profile.gender') === $genderOption)>{{ $genderOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input class="form-control" name="profile[address]" value="{{ old('profile.address') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Civil Status</label>
                        <select class="form-select" name="profile[civil_status]" required>
                            <option value="">Select</option>
                            @foreach(['Single', 'Married', 'Separated', 'Widowed'] as $statusOption)
                                <option value="{{ $statusOption }}" @selected(old('profile.civil_status') === $statusOption)>{{ $statusOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Tax ID No.</label>
                        <input class="form-control" name="profile[tax_ident_no]" value="{{ old('profile.tax_ident_no') }}" required>
                    </div>
                </div>

                <div class="col-lg-6">
                    <h5 class="mb-3">Emergency Contact Info</h5>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="emergency[name]" value="{{ old('emergency.name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Relationship</label>
                        <input class="form-control" name="emergency[relationship]" value="{{ old('emergency.relationship') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input class="form-control" name="emergency[contact_number]" value="{{ old('emergency.contact_number') }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Address</label>
                        <input class="form-control" name="emergency[address]" value="{{ old('emergency.address') }}" required>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                <button class="btn btn-primary">Save and Continue</button>
            </div>
        </form>
    </div>
@endsection
