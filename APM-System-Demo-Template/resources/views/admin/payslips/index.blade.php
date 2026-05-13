@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="mb-1">Payslips</h2>
            <p class="text-muted mb-0">Create and manage employee payslips.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.payslips.create') }}">Create Payslip</a>
    </div>

    @if(session('status') === 'payslip-created')
        <div class="alert alert-success">Payslip created.</div>
    @endif
    @if(session('status') === 'payslip-updated')
        <div class="alert alert-success">Payslip updated.</div>
    @endif

    <div class="eh-card p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Employee ID</th>
                        <th>Payslips</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->employee_id }}</td>
                            <td>{{ $employee->payslips_count }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.payslips.user', $employee) }}">View Payslips</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
