<?php

namespace App\Http\Controllers;

use App\Models\ProfileCorrectionRequest;
use App\Models\User;
use App\Notifications\ProfileCorrectionRequested;
use Illuminate\Http\Request;

class ProfileCorrectionController extends Controller
{
    public function index(Request $request)
    {
        $requests = ProfileCorrectionRequest::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('profile.corrections', [
            'requests' => $requests,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'section' => ['required', 'in:Personal Information,Emergency Contact'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $correction = ProfileCorrectionRequest::create([
            'user_id' => $request->user()->id,
            'section' => $validated['section'],
            'message' => $validated['message'],
        ]);

        $admins = User::query()->where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new ProfileCorrectionRequested($request->user()->name, $correction->section));
        }

        return back()->with('status', 'profile-correction-requested');
    }
}
