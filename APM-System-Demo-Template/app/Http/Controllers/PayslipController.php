<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use Illuminate\Http\Request;

class PayslipController extends Controller
{
    public function index(Request $request)
    {
        $payslips = Payslip::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('modules.payslips', [
            'payslips' => $payslips,
        ]);
    }
}
