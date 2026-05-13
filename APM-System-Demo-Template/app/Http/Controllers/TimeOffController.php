<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveRequestSubmitted;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TimeOffController extends Controller
{
    public function index(Request $request)
    {
        $requests = LeaveRequest::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $totalLeaveDays = 12;
        $approvedLeaves = LeaveRequest::query()
            ->where('user_id', $request->user()->id)
            ->where('status', 'Approved')
            ->get();

        $usedLeaveDays = 0;
        foreach ($approvedLeaves as $leave) {
            $usedLeaveDays += $leave->workingDays();
        }

        $remainingLeaveDays = max(0, $totalLeaveDays - $usedLeaveDays);

        return view('modules.timeoff', [
            'requests' => $requests,
            'totalLeaveDays' => $totalLeaveDays,
            'usedLeaveDays' => $usedLeaveDays,
            'remainingLeaveDays' => $remainingLeaveDays,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'type' => ['required', 'string', 'max:100'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        if (LeaveRequest::workingDaysBetween($validated['start_date'], $validated['end_date']) === 0) {
            throw ValidationException::withMessages([
                'end_date' => 'Leave request must include at least one working day. Saturdays and Sundays are not counted.',
            ]);
        }

        $request->user()->leaveRequests()->create($validated);

        $admins = User::query()->where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new LeaveRequestSubmitted($request->user()->name));
        }

        return back()->with('status', 'leave-requested');
    }
}
