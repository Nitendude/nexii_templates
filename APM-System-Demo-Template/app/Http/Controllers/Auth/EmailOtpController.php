<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Notifications\EmailOtpNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EmailOtpController extends Controller
{
    private int $ttlMinutes = 10;

    public function show(): View
    {
        return view('auth.verify-otp');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if ($user->email_verified_at) {
            return redirect()->route('dashboard');
        }

        $otp = EmailOtp::query()
            ->where('user_id', $user->id)
            ->where('code', $request->code)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$otp || $otp->expires_at->isPast()) {
            return back()->withErrors(['code' => 'The verification code is invalid or expired.']);
        }

        DB::transaction(function () use ($user, $otp) {
            $otp->update(['used_at' => now()]);
            $user->forceFill(['email_verified_at' => now()])->save();
        });

        return redirect()->route('dashboard');
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return redirect()->route('dashboard');
        }

        $otp = $this->createOtp($user->id);
        $user->notify(new EmailOtpNotification($otp->code, $this->ttlMinutes));

        return back()->with('status', 'A new verification code has been sent.');
    }

    public function sendOnRegister(int $userId): void
    {
        $otp = $this->createOtp($userId);
        $user = Auth::user();

        if ($user && $user->id === $userId) {
            $user->notify(new EmailOtpNotification($otp->code, $this->ttlMinutes));
        }
    }

    private function createOtp(int $userId): EmailOtp
    {
        $code = (string) random_int(100000, 999999);

        return EmailOtp::create([
            'user_id' => $userId,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes($this->ttlMinutes),
        ]);
    }
}
