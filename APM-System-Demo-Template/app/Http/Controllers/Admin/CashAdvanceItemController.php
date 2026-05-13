<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashAdvanceItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CashAdvanceItemController extends Controller
{
    public function update(Request $request, CashAdvanceItem $cashAdvanceItem): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $updates = [];

        if (array_key_exists('amount', $validated)) {
            $updates['amount'] = $validated['amount'] ?? 0;
        }

        if (array_key_exists('remarks', $validated)) {
            $updates['remarks'] = $validated['remarks'] ?? null;
        }

        $cashAdvanceItem->update($updates);
        $cashAdvanceRequest = $cashAdvanceItem->request;
        if ($cashAdvanceRequest) {
            $cashAdvanceRequest->update([
                'amount' => $cashAdvanceRequest->items()->sum('amount'),
            ]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'ok']);
        }

        return back()->with('status', 'cash-advance-remarks-updated');
    }
}
