@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="mb-1">Payslips for {{ $employee->name }}</h2>
            <p class="text-muted mb-0">Employee ID: {{ $employee->employee_id }}</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.payslips.index') }}">Back to list</a>
    </div>

    <div class="eh-card p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Pay Period</th>
                        <th>Basic</th>
                        <th>Allowances</th>
                        <th>Other Deductions</th>
                        <th>Personal CA Deduction</th>
                        <th>Pag-IBIG</th>
                        <th>PhilHealth</th>
                        <th>SSS</th>
                        <th>Net Pay</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payslips as $payslip)
                        <tr>
                            <td>{{ $payslip->period_start->format('M d, Y') }} - {{ $payslip->period_end->format('M d, Y') }}</td>
                            <td>PHP {{ number_format($payslip->basic_salary, 2) }}</td>
                            <td>PHP {{ number_format($payslip->allowances_total, 2) }}</td>
                            <td>PHP {{ number_format($payslip->deductions_total, 2) }}</td>
                            <td>PHP {{ number_format($payslip->cash_advance_deduction, 2) }}</td>
                            <td>PHP {{ number_format($payslip->pagibig_contribution, 2) }}</td>
                            <td>PHP {{ number_format($payslip->philhealth_contribution, 2) }}</td>
                            <td>PHP {{ number_format($payslip->sss_contribution, 2) }}</td>
                            <td class="fw-semibold">PHP {{ number_format($payslip->net_pay, 2) }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.payslips.edit', $payslip) }}">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No payslips found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $payslips->links() }}
        </div>
    </div>
@endsection
