<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Models\User;
use App\Notifications\EmailOtpNotification;
use App\Providers\RouteServiceProvider;
use App\Support\PersonName;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::min(6)],
        ]);

        $fullName = PersonName::normalize(trim($validated['first_name']) . ' ' . trim($validated['last_name']));

        do {
            $employeeId = 'APM-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (User::where('employee_id', $employeeId)->exists());

        $user = User::create([
            'name' => $fullName,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'employee',
            'employee_id' => $employeeId,
            'status' => 'Active',
            'profile_photo' => 'images/profile-default.svg',
            'must_change_password' => false,
            'profile_completed' => false,
            'created_by_admin' => false,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $otp = EmailOtp::create([
            'user_id' => $user->id,
            'code' => (string) random_int(100000, 999999),
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);
        $user->notify(new EmailOtpNotification($otp->code, 10));

        return redirect()->route('otp.verify.show');
    }
}
