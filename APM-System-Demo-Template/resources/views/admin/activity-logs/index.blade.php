@extends('layouts.employeehub')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h4 class="mb-1">User Activity Logs</h4>
            <div class="text-muted small">Shows what authenticated users are opening and submitting across the system.</div>
        </div>
        <form method="GET" class="d-flex flex-wrap gap-2">
            <select class="form-select" name="user_id">
                <option value="">All users</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(($userId ?? null) === $user->id)>
                        {{ $user->name }}{{ $user->employee_id ? ' · ' . $user->employee_id : '' }}
                    </option>
                @endforeach
            </select>
            <input class="form-control" type="search" name="q" value="{{ $search ?? '' }}" placeholder="Search user, route, module, IP">
            <select class="form-select" name="method">
                <option value="">All methods</option>
                @foreach(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $httpMethod)
                    <option value="{{ $httpMethod }}" @selected(($method ?? '') === $httpMethod)>{{ $httpMethod }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-primary" type="submit">Filter</button>
        </form>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Activity</th>
                            <th>Method</th>
                            <th>Module</th>
                            <th>Status</th>
                            <th>IP</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="small text-nowrap">{{ optional($log->created_at)->format('M d, Y h:i A') }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $log->user?->name ?? 'Unknown User' }}</div>
                                    <div class="text-muted small">{{ $log->user?->employee_id ?? '—' }}</div>
                                </td>
                                <td style="min-width: 230px;">
                                    <div class="fw-semibold">{{ $log->describeAction() }}</div>
                                    <div class="text-muted small">{{ $log->route_name ?? '-' }}</div>
                                </td>
                                <td><span class="badge text-bg-dark">{{ $log->method }}</span></td>
                                <td>{{ $log->module ?? '-' }}</td>
                                <td>{{ $log->response_status ?? '-' }}</td>
                                <td class="small">{{ $log->ip_address ?? '-' }}</td>
                                <td style="min-width: 340px;">
                                    <div class="small mb-1"><span class="fw-semibold">URL:</span> {{ $log->url ?? '-' }}</div>
                                    <div class="small mb-1"><span class="fw-semibold">Params:</span> {{ json_encode($log->route_parameters ?? [], JSON_UNESCAPED_SLASHES) }}</div>
                                    <div class="small text-muted" style="white-space: pre-wrap;">{{ json_encode($log->request_payload ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No activity logs yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
