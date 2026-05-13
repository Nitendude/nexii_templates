<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashAdvanceRequest;
use App\Models\Payslip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayslipController extends Controller
{
    public function index()
    {
        $employees = User::query()
            ->withCount('payslips')
            ->orderBy('name')
            ->get();

        return view('admin.payslips.index', [
            'employees' => $employees,
        ]);
    }

    public function showUser(User $user)
    {
        $payslips = Payslip::query()
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('admin.payslips.user', [
            'employee' => $user,
            'payslips' => $payslips,
        ]);
    }

    public function create()
    {
        $employees = User::query()
            ->orderBy('name')
            ->get();

        return view('admin.payslips.create', [
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'allowances_total' => ['nullable', 'numeric', 'min:0'],
            'deductions_total' => ['nullable', 'numeric', 'min:0'],
            'pagibig_contribution' => ['nullable', 'numeric', 'min:0'],
            'philhealth_contribution' => ['nullable', 'numeric', 'min:0'],
            'sss_contribution' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        if ((int) $validated['user_id'] !== (int) $payslip->user_id && (float) $payslip->cash_advance_deduction > 0) {
            return back()
                ->withErrors(['user_id' => 'This payslip has a Personal CA deduction, so the employee cannot be changed.'])
                ->withInput();
        }

        $allowances = $validated['allowances_total'] ?? 0;
        $deductions = $validated['deductions_total'] ?? 0;
        $pagibig = $validated['pagibig_contribution'] ?? 0;
        $philhealth = $validated['philhealth_contribution'] ?? 0;
        $sss = $validated['sss_contribution'] ?? 0;

        DB::transaction(function () use ($validated, $allowances, $deductions, $pagibig, $philhealth, $sss) {
            $cashAdvanceDeduction = $this->applyPersonalCashAdvanceDeductions((int) $validated['user_id']);
            $netPay = $validated['basic_salary'] + $allowances - ($deductions + $cashAdvanceDeduction + $pagibig + $philhealth + $sss);
            $notes = $validated['notes'] ?? null;

            if ($cashAdvanceDeduction > 0) {
                $caNote = 'Personal CA salary deduction: PHP ' . number_format($cashAdvanceDeduction, 2);
                $notes = trim(implode(' ', array_filter([$notes, $caNote])));
            }

            Payslip::create([
                'user_id' => $validated['user_id'],
                'period_start' => $validated['period_start'],
                'period_end' => $validated['period_end'],
                'basic_salary' => $validated['basic_salary'],
                'allowances_total' => $allowances,
                'deductions_total' => $deductions,
                'cash_advance_deduction' => $cashAdvanceDeduction,
                'pagibig_contribution' => $pagibig,
                'philhealth_contribution' => $philhealth,
                'sss_contribution' => $sss,
                'net_pay' => $netPay,
                'notes' => $notes ?: null,
            ]);
        });

        return redirect()
            ->route('admin.payslips.index')
            ->with('status', 'payslip-created');
    }

    public function edit(Payslip $payslip)
    {
        $employees = User::query()
            ->orderBy('name')
            ->get();

        return view('admin.payslips.edit', [
            'payslip' => $payslip,
            'employees' => $employees,
        ]);
    }

    public function update(Request $request, Payslip $payslip)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'allowances_total' => ['nullable', 'numeric', 'min:0'],
            'deductions_total' => ['nullable', 'numeric', 'min:0'],
            'pagibig_contribution' => ['nullable', 'numeric', 'min:0'],
            'philhealth_contribution' => ['nullable', 'numeric', 'min:0'],
            'sss_contribution' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $allowances = $validated['allowances_total'] ?? 0;
        $deductions = $validated['deductions_total'] ?? 0;
        $pagibig = $validated['pagibig_contribution'] ?? 0;
        $philhealth = $validated['philhealth_contribution'] ?? 0;
        $sss = $validated['sss_contribution'] ?? 0;
        $cashAdvanceDeduction = (float) ($payslip->cash_advance_deduction ?? 0);
        $netPay = $validated['basic_salary'] + $allowances - ($deductions + $cashAdvanceDeduction + $pagibig + $philhealth + $sss);

        $payslip->update([
            'user_id' => $validated['user_id'],
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'basic_salary' => $validated['basic_salary'],
            'allowances_total' => $allowances,
            'deductions_total' => $deductions,
            'cash_advance_deduction' => $cashAdvanceDeduction,
            'pagibig_contribution' => $pagibig,
            'philhealth_contribution' => $philhealth,
            'sss_contribution' => $sss,
            'net_pay' => $netPay,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.payslips.index')
            ->with('status', 'payslip-updated');
    }

    private function applyPersonalCashAdvanceDeductions(int $userId): float
    {
        $cashAdvances = CashAdvanceRequest::query()
            ->where('user_id', $userId)
            ->where('status', 'Approved')
            ->where('is_personal', true)
            ->orderBy('created_at')
            ->lockForUpdate()
            ->get();

        $totalDeduction = 0.0;

        foreach ($cashAdvances as $cashAdvance) {
            $amount = (float) $cashAdvance->amount;
            $paid = (float) ($cashAdvance->personal_paid_amount ?? 0);
            $balance = max($amount - $paid, 0);

            if ($balance <= 0) {
                continue;
            }

            $terms = max((int) ($cashAdvance->salary_deduction_terms ?: 1), 1);
            $installmentAmount = round($amount / $terms, 2);
            $deduction = min($balance, $installmentAmount);

            $cashAdvance->update([
                'personal_paid_amount' => round($paid + $deduction, 2),
            ]);

            $totalDeduction += $deduction;
        }

        return round($totalDeduction, 2);
    }
}
