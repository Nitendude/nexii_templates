<?php

namespace App\Http\Controllers;

use App\Models\JobOrderAttachment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = $request->string('q')->toString();

        $attachments = JobOrderAttachment::query()
            ->with('jobOrder')
            ->when(!$user->hasAccess('job-orders'), function ($query) use ($user) {
                $query->whereHas('jobOrder', function ($jobQuery) use ($user) {
                    $jobQuery->where('created_by_user_id', $user->id)
                        ->orWhere('assigned_user_id', $user->id);
                });
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('jobOrder', function ($jobQuery) use ($search) {
                    $jobQuery->where('consignee', 'like', "%{$search}%")
                        ->orWhere('shipper', 'like', "%{$search}%")
                        ->orWhere('number', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('mo', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get()
            ->groupBy('job_order_id');

        return view('modules.documents', [
            'attachments' => $attachments,
            'search' => $search,
        ]);
    }
}
