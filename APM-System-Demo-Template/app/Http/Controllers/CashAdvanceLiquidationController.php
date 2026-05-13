<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CashAdvanceLiquidationController extends Controller
{
    public function store(Request $request)
    {
        return redirect()
            ->to(route('cash-advances', [], false))
            ->withErrors(['cash_advance' => 'Liquidation and petty cash records can only be created by Admin or Accounting.']);
    }
}
