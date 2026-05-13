<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Notifications\LeaveRequestApproved;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $requests = LeaveRequest::query()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('admin.timeoff.index', [
            'requests' => $requests,
        ]);
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Approved,Rejected'],
        ]);

        $leaveRequest->update([
            'status' => $validated['status'],
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        $leaveRequest->user->notify(new LeaveRequestApproved($validated['status']));

        return back()->with('status', 'leave-updated');
    }
}
