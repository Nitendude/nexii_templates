@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Create Payslip</h2>
        <a class="btn btn-outline-secondary" href="{{ route('admin.payslips.index') }}">Back to list</a>
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
        <form method="POST" action="{{ route('admin.payslips.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Employee</label>
                    <select class="form-select" name="user_id" required>
                        <option value="">Select employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @selected(old('user_id') == $employee->id)>{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Period Start</label>
                    <input type="date" class="form-control" name="period_start" value="{{ old('period_start') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Period End</label>
                    <input type="date" class="form-control" name="period_end" value="{{ old('period_end') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Basic Salary</label>
                    <input type="number" step="0.01" class="form-control" name="basic_salary" value="{{ old('basic_salary') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Allowances Total</label>
                    <input type="number" step="0.01" class="form-control" name="allowances_total" value="{{ old('allowances_total') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Other Deductions</label>
                    <input type="number" step="0.01" class="form-control" name="deductions_total" value="{{ old('deductions_total') }}">
                    <div class="text-muted small">Personal CA deductions are added automatically when the payslip is saved.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pag-IBIG Contribution</label>
                    <input type="number" step="0.01" class="form-control" name="pagibig_contribution" value="{{ old('pagibig_contribution') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">PhilHealth Contribution</label>
                    <input type="number" step="0.01" class="form-control" name="philhealth_contribution" value="{{ old('philhealth_contribution') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">SSS Contribution</label>
                    <input type="number" step="0.01" class="form-control" name="sss_contribution" value="{{ old('sss_contribution') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <input class="form-control" name="notes" value="{{ old('notes') }}">
                </div>
            </div>
            <div class="mt-3 text-end">
                <button class="btn btn-primary">Save Payslip</button>
            </div>
        </form>
    </div>
@endsection
