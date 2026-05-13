@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Payslip</h2>
    </div>

    <div class="eh-card p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Pay Period</th>
                        <th>Basic Salary</th>
                        <th>Allowances</th>
                        <th>Other Deductions</th>
                        <th>Personal CA Deduction</th>
                        <th>Pag-IBIG</th>
                        <th>PhilHealth</th>
                        <th>SSS</th>
                        <th>Net Pay</th>
                        <th>Notes</th>
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
                            <td>{{ $payslip->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No payslips available yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
