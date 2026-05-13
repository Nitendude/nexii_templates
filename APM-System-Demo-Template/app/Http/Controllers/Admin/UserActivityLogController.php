<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $method = strtoupper(trim((string) $request->query('method', '')));
        $userId = $request->integer('user_id');

        $logs = UserActivityLog::query()
            ->with('user')
            ->when($userId > 0, fn ($query) => $query->where('user_id', $userId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('route_name', 'like', "%{$search}%")
                        ->orWhere('module', 'like', "%{$search}%")
                        ->orWhere('url', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('employee_id', 'like', "%{$search}%");
                        });
                });
            })
            ->when(in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], true), function ($query) use ($method) {
                $query->where('method', $method);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'employee_id']);

        return view('admin.activity-logs.index', [
            'logs' => $logs,
            'users' => $users,
            'search' => $search,
            'method' => $method,
            'userId' => $userId > 0 ? $userId : null,
        ]);
    }
}
