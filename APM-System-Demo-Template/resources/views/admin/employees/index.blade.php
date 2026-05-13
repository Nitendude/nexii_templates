@extends('layouts.employeehub')

@section('content')
    @php
        $registrationInviteUrl = route('register');
    @endphp

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">Employees</h2>
            <p class="text-muted mb-0">Manage employee profiles and status.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <form method="GET" action="{{ route('admin.employees.index') }}" class="d-flex flex-wrap gap-2 align-items-center" id="employeeSearchForm">
                <input class="form-control" type="text" name="q" id="employeeSearchInput" placeholder="Search name, email, employee ID, department" value="{{ $search ?? '' }}" style="min-width: 280px;">
                <label class="form-label mb-0">Status</label>
                <select class="form-select" name="status" onchange="this.form.submit()">
                    <option value="">All</option>
                    @foreach(['Active', 'Inactive', 'On Leave', 'Terminated'] as $filterStatus)
                        <option value="{{ $filterStatus }}" @selected(($status ?? '') === $filterStatus)>{{ $filterStatus }}</option>
                    @endforeach
                </select>
                <button class="btn btn-outline-secondary" type="submit">Search</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.employees.index') }}">Reset</a>
            </form>
            <button class="btn btn-outline-success" type="button" onclick="navigator.clipboard.writeText({{ \Illuminate\Support\Js::from($registrationInviteUrl) }}).then(() => alert('Permanent employee registration link copied.'))">
                Copy Employee Invite Link
            </button>
            <a class="btn btn-primary" href="{{ route('admin.employees.create') }}">Create Employee</a>
        </div>
    </div>

    <div class="eh-card p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Employee ID</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Department</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->employee_id }}</td>
                            <td class="text-capitalize">{{ $employee->role }}</td>
                            <td>
                                @php
                                    $displayStatus = $employee->status;
                                    if (!in_array($employee->status, ['Terminated', 'Inactive'], true)) {
                                        $displayStatus = $employee->on_leave_today ? 'On Leave' : ($employee->status === 'On Leave' ? 'Active' : $employee->status);
                                    }
                                    $statusClass = match ($displayStatus) {
                                        'Active' => 'bg-success',
                                        'Inactive' => 'bg-warning text-dark',
                                        'On Leave' => 'bg-primary',
                                        'Terminated' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $displayStatus }}</span>
                            </td>
                            <td>{{ $employee->employmentDetail?->department ?? '—' }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.employees.show', $employee) }}">View</a>
                                <a class="btn btn-sm btn-primary" href="{{ route('admin.employees.edit', $employee) }}">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $employees->links() }}
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const form = document.getElementById('employeeSearchForm');
        const input = document.getElementById('employeeSearchInput');
        if (!form || !input) {
            return;
        }

        let timer = null;
        input.addEventListener('input', function () {
            window.clearTimeout(timer);
            timer = window.setTimeout(function () {
                form.submit();
            }, 350);
        });
    })();
</script>
@endpush
