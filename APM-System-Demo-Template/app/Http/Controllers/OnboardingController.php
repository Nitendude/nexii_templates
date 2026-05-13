<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OnboardingController extends Controller
{
    public function show(Request $request)
    {
        return view('profile.onboarding', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $rules = [
            'profile' => ['required', 'array'],
            'profile.contact_number' => ['required', 'string', 'max:50'],
            'profile.birthdate' => ['required', 'date'],
            'profile.gender' => ['required', 'string', 'max:50'],
            'profile.address' => ['required', 'string', 'max:255'],
            'profile.civil_status' => ['required', 'string', 'max:50'],
            'profile.tax_ident_no' => ['required', 'string', 'max:50'],
            'emergency' => ['required', 'array'],
            'emergency.name' => ['required', 'string', 'max:255'],
            'emergency.relationship' => ['required', 'string', 'max:50'],
            'emergency.contact_number' => ['required', 'string', 'max:50'],
            'emergency.address' => ['required', 'string', 'max:255'],
        ];

        if ($user->created_by_admin) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules);

        $user->update([
            'must_change_password' => false,
            'profile_completed' => true,
        ]);

        if ($user->created_by_admin && !empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $validated['profile']
        );

        $user->emergencyContact()->updateOrCreate(
            ['user_id' => $user->id],
            $validated['emergency']
        );

        return redirect()->route('dashboard')->with('status', 'onboarding-complete');
    }
}
