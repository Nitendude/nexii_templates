<?php

namespace App\Http\Controllers;

use App\Models\CashAdvanceRequest;
use App\Models\LeaveRequest;
use App\Models\ProfileCorrectionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiveUpdateController extends Controller
{
    public function approvalStamp(Request $request): JsonResponse
    {
        $user = $request->user();
        $isAdminApprover = $user && (
            $user->hasAccess('admin-cash-advance-approvals') ||
            $user->hasAccess('admin-leave-approvals') ||
            $user->hasAccess('admin-profile-corrections')
        );

        $cashQuery = CashAdvanceRequest::query();
        $leaveQuery = LeaveRequest::query();
        $profileCorrectionQuery = ProfileCorrectionRequest::query();

        if (!$isAdminApprover && $user) {
            $cashQuery->where('user_id', $user->id);
            $leaveQuery->where('user_id', $user->id);
            $profileCorrectionQuery->where('user_id', $user->id);
        }

        $cashPending = (clone $cashQuery)->where('status', 'Pending')->count();
        $leavePending = (clone $leaveQuery)->where('status', 'Pending')->count();
        $profilePending = (clone $profileCorrectionQuery)->where('status', 'Pending')->count();

        $cashUpdated = (string) ((clone $cashQuery)->max('updated_at') ?? '');
        $leaveUpdated = (string) ((clone $leaveQuery)->max('updated_at') ?? '');
        $profileUpdated = (string) ((clone $profileCorrectionQuery)->max('updated_at') ?? '');

        $stampData = [
            'cash_pending' => $cashPending,
            'leave_pending' => $leavePending,
            'profile_pending' => $profilePending,
            'cash_updated' => $cashUpdated,
            'leave_updated' => $leaveUpdated,
            'profile_updated' => $profileUpdated,
        ];

        return response()->json([
            'stamp' => sha1(json_encode($stampData)),
            'data' => $stampData,
        ]);
    }
}

