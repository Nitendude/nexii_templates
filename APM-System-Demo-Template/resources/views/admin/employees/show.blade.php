@extends('layouts.employeehub')

@section('content')
    @php
        $profile = $employee->profile;
        $employment = $employee->employmentDetail;
        $emergency = $employee->emergencyContact;
    @endphp

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Employee Profile</h2>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('admin.employees.index') }}">Back to list</a>
            <a class="btn btn-primary" href="{{ route('admin.employees.edit', $employee) }}">Edit Profile</a>
        </div>
    </div>

    <div class="eh-card p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <img src="{{ $employee->profile_photo === 'images/profile-default.svg' ? asset('images/profile-default.svg') : ($employee->profile_photo ? Storage::url($employee->profile_photo) : asset('images/profile-default.svg')) }}" alt="Profile photo" class="eh-profile-photo">
            </div>
            <div class="col">
                <h3 class="mb-1">{{ $employee->name }}</h3>
                <div class="text-muted">{{ $employment?->position ?? '—' }} · {{ $employment?->employment_type ?? '—' }}</div>
                <div class="mt-2 d-flex flex-wrap gap-2">
                    @php
                        $displayStatus = $employee->status;
                        if (!in_array($employee->status, ['Terminated', 'Inactive'], true)) {
                            $displayStatus = $hasLeaveToday ? 'On Leave' : ($employee->status === 'On Leave' ? 'Active' : $employee->status);
                        }
                        $statusClass = match ($displayStatus) {
                            'Active' => 'bg-success',
                            'Inactive' => 'bg-warning text-dark',
                            'On Leave' => 'bg-primary',
                            'Terminated' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <span class="badge rounded-pill {{ $statusClass }}">Status: {{ $displayStatus }}</span>
                    <span class="badge rounded-pill bg-light text-dark border">Employee ID: {{ $employee->employee_id }}</span>
                    <span class="badge rounded-pill bg-light text-dark border">Date Joined: {{ optional($employment?->date_joined)->format('M d, Y') ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="adminProfileTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="admin-profile-tab" data-bs-toggle="tab" data-bs-target="#admin-profile-pane" type="button" role="tab">My Profile</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="admin-employment-tab" data-bs-toggle="tab" data-bs-target="#admin-employment-pane" type="button" role="tab">Employment Details</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="admin-emergency-tab" data-bs-toggle="tab" data-bs-target="#admin-emergency-pane" type="button" role="tab">Emergency Contact</button>
        </li>
        <li class="nav-item" role="presentation">
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="admin-contrib-tab" data-bs-toggle="tab" data-bs-target="#admin-contrib-pane" type="button" role="tab">Contributions</button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="admin-profile-pane" role="tabpanel">
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="eh-card p-3">
                        <h5 class="mb-3">Personal Information</h5>
                        <div class="row g-2 text-muted">
                            <div class="col-5">Contact Number</div>
                            <div class="col-7 text-dark">{{ $profile?->contact_number ?? '—' }}</div>
                            <div class="col-5">Birthdate</div>
                            <div class="col-7 text-dark">{{ optional($profile?->birthdate)->format('M d, Y') ?? '—' }}</div>
                            <div class="col-5">Gender</div>
                            <div class="col-7 text-dark">{{ $profile?->gender ?? '—' }}</div>
                            <div class="col-5">Address</div>
                            <div class="col-7 text-dark">{{ $profile?->address ?? '—' }}</div>
                            <div class="col-5">Civil Status</div>
                            <div class="col-7 text-dark">{{ $profile?->civil_status ?? '—' }}</div>
                            <div class="col-5">Tax ID No.</div>
                            <div class="col-7 text-dark">{{ $profile?->tax_ident_no ?? '—' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="eh-card p-3">
                        <h5 class="mb-3">Current Allowances</h5>
                        @if($employee->allowances->isEmpty())
                            <div class="text-muted">No allowances recorded.</div>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($employee->allowances as $allowance)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $allowance->type }}</span>
                                        <span class="fw-semibold">{{ $allowance->currency }} {{ number_format($allowance->amount, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="admin-employment-pane" role="tabpanel">
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="eh-card p-3">
                        <h5 class="mb-3">Employment Details</h5>
                        <div class="row g-2 text-muted">
                            <div class="col-5">Position</div>
                            <div class="col-7 text-dark">{{ $employment?->position ?? '—' }}</div>
                            <div class="col-5">Employment Type</div>
                            <div class="col-7 text-dark">{{ $employment?->employment_type ?? '—' }}</div>
                            <div class="col-5">Department</div>
                            <div class="col-7 text-dark">{{ $employment?->department ?? '—' }}</div>
                            <div class="col-5">Date Joined</div>
                            <div class="col-7 text-dark">{{ optional($employment?->date_joined)->format('M d, Y') ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="admin-emergency-pane" role="tabpanel">
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="eh-card p-3">
                        <h5 class="mb-3">Emergency Contact Info</h5>
                        <div class="row g-2 text-muted">
                            <div class="col-5">Name</div>
                            <div class="col-7 text-dark">{{ $emergency?->name ?? '—' }}</div>
                            <div class="col-5">Relationship</div>
                            <div class="col-7 text-dark">{{ $emergency?->relationship ?? '—' }}</div>
                            <div class="col-5">Contact Number</div>
                            <div class="col-7 text-dark">{{ $emergency?->contact_number ?? '—' }}</div>
                            <div class="col-5">Address</div>
                            <div class="col-7 text-dark">{{ $emergency?->address ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </div>

        <div class="tab-pane fade" id="admin-contrib-pane" role="tabpanel">
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="eh-card p-3">
                        <h5 class="mb-3">Contributions</h5>
                        @if($contributionPayslips->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Pay Period</th>
                                            <th>Pag-IBIG</th>
                                            <th>PhilHealth</th>
                                            <th>SSS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contributionPayslips as $payslip)
                                            <tr>
                                                <td>{{ $payslip->period_start->format('M d, Y') }} - {{ $payslip->period_end->format('M d, Y') }}</td>
                                                <td>
                                                    PHP {{ number_format($payslip->pagibig_contribution, 2) }}
                                                    <span class="badge bg-{{ $payslip->pagibig_contribution > 0 ? 'success' : 'secondary' }} ms-2">
                                                        {{ $payslip->pagibig_contribution > 0 ? 'Deducted' : 'Not deducted' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    PHP {{ number_format($payslip->philhealth_contribution, 2) }}
                                                    <span class="badge bg-{{ $payslip->philhealth_contribution > 0 ? 'success' : 'secondary' }} ms-2">
                                                        {{ $payslip->philhealth_contribution > 0 ? 'Deducted' : 'Not deducted' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    PHP {{ number_format($payslip->sss_contribution, 2) }}
                                                    <span class="badge bg-{{ $payslip->sss_contribution > 0 ? 'success' : 'secondary' }} ms-2">
                                                        {{ $payslip->sss_contribution > 0 ? 'Deducted' : 'Not deducted' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-muted">No payslip data available yet.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
