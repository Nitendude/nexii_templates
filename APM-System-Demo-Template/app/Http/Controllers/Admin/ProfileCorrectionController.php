<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileCorrectionRequest;
use Illuminate\Http\Request;

class ProfileCorrectionController extends Controller
{
    public function index()
    {
        $requests = ProfileCorrectionRequest::query()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('admin.profile-corrections.index', [
            'requests' => $requests,
        ]);
    }

    public function update(Request $request, ProfileCorrectionRequest $profileCorrectionRequest)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Pending,Reviewed'],
        ]);

        $profileCorrectionRequest->update([
            'status' => $validated['status'],
        ]);

        return back()->with('status', 'profile-correction-updated');
    }
}
