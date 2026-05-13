<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashAdvanceRequest;
use App\Notifications\CashAdvancePaid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CashAdvancePaymentController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = CashAdvanceRequest::query()
            ->with('user')
            ->where('status', 'Approved')
            ->orderByDesc('created_at');

        $paymentStatus = $request->query('payment_status');
        $unpaidRequests = (clone $baseQuery)
            ->when($paymentStatus === 'paid', fn ($query) => $query->whereRaw('1 = 0'))
            ->whereNull('paid_at')
            ->paginate(10, ['*'], 'unpaid_page')
            ->withQueryString();

        $paidRequests = (clone $baseQuery)
            ->when($paymentStatus === 'unpaid', fn ($query) => $query->whereRaw('1 = 0'))
            ->whereNotNull('paid_at')
            ->paginate(10, ['*'], 'paid_page')
            ->withQueryString();

        return view('admin.cash-advances.payments', [
            'unpaidRequests' => $unpaidRequests,
            'paidRequests' => $paidRequests,
            'paymentStatus' => $paymentStatus,
        ]);
    }

    public function update(Request $request, CashAdvanceRequest $cashAdvanceRequest): RedirectResponse
    {
        $validated = $request->validate([
            'paid_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $path = $request->file('paid_proof')->store('cash-advance-payments', 'public');

        $cashAdvanceRequest->update([
            'paid_at' => now(),
            'paid_proof_path' => $path,
            'paid_by' => $request->user()->id,
        ]);

        $cashAdvanceRequest->user->notify(new CashAdvancePaid($cashAdvanceRequest->ca_no ?? '—'));

        return back()->with('status', 'cash-advance-paid');
    }
}
