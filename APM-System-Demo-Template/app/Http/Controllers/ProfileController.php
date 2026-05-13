<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function dashboard(Request $request)
    {
        $totalLeaveDays = 12;
        $approvedLeaves = $request->user()->leaveRequests()
            ->where('status', 'Approved')
            ->get();

        $usedLeaveDays = 0;
        foreach ($approvedLeaves as $leave) {
            $usedLeaveDays += $leave->workingDays();
        }

        $remainingLeaveDays = max(0, $totalLeaveDays - $usedLeaveDays);

        return view('dashboard', [
            'user' => $request->user(),
            'remainingLeaveDays' => $remainingLeaveDays,
        ]);
    }

    public function show(Request $request)
    {
        $user = $request->user()->load([
            'profile',
            'employmentDetail',
            'emergencyContact',
            'allowances',
        ]);

        Gate::authorize('view-employee', $user);

        $today = now()->toDateString();
        $latestPayslip = $user->payslips()->latest()->first();
        $contributionPayslips = $user->payslips()->latest()->take(5)->get();
        $hasLeaveToday = !now()->isWeekend() && $user->leaveRequests()
            ->where('status', 'Approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->exists();

        return view('profile.show', [
            'employee' => $user,
            'latestPayslip' => $latestPayslip,
            'contributionPayslips' => $contributionPayslips,
            'hasLeaveToday' => $hasLeaveToday,
        ]);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'max:2048'],
        ]);

        $user = $request->user();
        Gate::authorize('view-employee', $user);

        if ($user->profile_photo && $user->profile_photo !== 'images/profile-default.svg') {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('profile_photo')->store('profile-photos', 'public');

        $user->update([
            'profile_photo' => $path,
        ]);

        return back()->with('status', 'photo-updated');
    }
}
