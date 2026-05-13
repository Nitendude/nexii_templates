@php
    $user = Auth::user();
    $needsOnboarding = $user && ($user->must_change_password || !$user->profile_completed);
@endphp

<nav class="navbar navbar-expand-lg navbar-dark eh-navbar">
    <div class="container-fluid">
        @unless($needsOnboarding)
            <button class="btn btn-outline-light d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                <i class="bi bi-list"></i>
            </button>
            <button class="btn btn-outline-light d-none d-lg-inline-flex me-2" type="button" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="bi bi-layout-sidebar-inset"></i>
            </button>
        @endunless
        <a class="navbar-brand" href="{{ $needsOnboarding ? route('onboarding.show') : route('dashboard') }}">APM</a>
        @unless($needsOnboarding)
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-3">
                    @if($user?->hasAccess('dashboard'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                    @endif
                    @if($user?->hasAccess('admin-employees'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.employees.index') }}">Employees</a></li>
                    @endif
                    @if($user?->hasAccess('leave-form'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('timeoff') }}">Leave Form</a></li>
                    @endif
                    @if($user?->hasAccess('payslips'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('payslips') }}">Payslip</a></li>
                    @endif
                    @if($user?->hasAccess('admin-reports'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports') }}">Reports</a></li>
                    @endif
                </ul>
            </div>
        @endunless
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item dropdown me-3">
                <a class="nav-link position-relative dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell"></i>
                    @if($user && $user->unreadNotifications->count() > 0)
                        <span id="notifUnreadBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $user->unreadNotifications->count() }}
                        </span>
                    @else
                        <span id="notifUnreadBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 360px;">
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                        <div>
                            <div class="fw-semibold">Notifications</div>
                            <div id="notifUnreadText" class="text-muted small">{{ $user?->unreadNotifications->count() ?? 0 }} unread</div>
                        </div>
                        <form id="notifReadAllForm" method="POST" action="{{ route('notifications.readAll') }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary">Mark all read</button>
                        </form>
                    </div>
                    <div id="notifList" class="list-group list-group-flush" style="max-height: 360px; overflow-y: auto;">
                        @forelse($user->notifications->take(10) as $notification)
                            @php
                                $isUnread = is_null($notification->read_at);
                                $title = $notification->data['title'] ?? 'Update';
                                $message = $notification->data['message'] ?? '';
                                $timestamp = $notification->data['timestamp'] ?? null;
                                $url = $notification->data['url'] ?? null;
                            @endphp
                            <div class="list-group-item {{ $isUnread ? 'bg-light' : '' }}" data-notification-id="{{ $notification->id }}">
                                <div class="d-flex gap-2">
                                    <div class="pt-1">
                                        <span class="badge rounded-pill {{ $isUnread ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ $isUnread ? 'New' : 'Seen' }}
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">{{ $title }}</div>
                                        <div class="text-muted small">{{ $message }}</div>
                                        @if($timestamp)
                                            <div class="text-muted small">{{ $timestamp }}</div>
                                        @endif
                                        <div class="d-flex gap-2 mt-2">
                                            @if($url)
                                                <a class="btn btn-sm btn-outline-primary" href="{{ $url }}">View</a>
                                            @endif
                                            @if($isUnread)
                                                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-secondary">Mark read</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-muted small" data-notif-empty>No notifications yet.</div>
                        @endforelse
                    </div>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ $user?->profile_photo === 'images/profile-default.svg' ? asset('images/profile-default.svg') : ($user?->profile_photo ? Storage::url($user->profile_photo) : asset('images/profile-default.svg')) }}" alt="Avatar" class="rounded-circle" width="32" height="32">
                    <span class="d-none d-md-inline">{{ $user?->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('my-profile') }}">My Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('change-password.edit') }}">Change Password</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">Logout</button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<div id="notifToastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1200;"></div>

<script>
    (function () {
        const listEl = document.getElementById('notifList');
        const unreadBadge = document.getElementById('notifUnreadBadge');
        const unreadText = document.getElementById('notifUnreadText');
        const toastContainer = document.getElementById('notifToastContainer');
        const readAllForm = document.getElementById('notifReadAllForm');
        if (!listEl || !unreadBadge || !unreadText || !toastContainer || !readAllForm) {
            return;
        }

        const readAllAction = readAllForm.getAttribute('action') || '';
        const readActionTemplate = @json(route('notifications.read', ['notification' => '__ID__']));
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const feedUrl = @json(route('notifications.feed'));
        const seenIds = new Set([...listEl.querySelectorAll('[data-notification-id]')].map((el) => el.dataset.notificationId));

        const esc = (value) => String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const updateUnreadUI = (count) => {
            unreadText.textContent = `${count} unread`;
            unreadBadge.textContent = count;
            unreadBadge.classList.toggle('d-none', count <= 0);
        };

        const showToast = (notification) => {
            const toastWrapper = document.createElement('div');
            toastWrapper.className = 'toast text-bg-dark border-0';
            toastWrapper.setAttribute('role', 'alert');
            toastWrapper.setAttribute('aria-live', 'assertive');
            toastWrapper.setAttribute('aria-atomic', 'true');
            toastWrapper.innerHTML = `
                <div class="toast-header">
                    <strong class="me-auto">${esc(notification.title)}</strong>
                    <small>New</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <div>${esc(notification.message)}</div>
                    ${notification.url ? `<a class="btn btn-sm btn-light mt-2" href="${esc(notification.url)}">View</a>` : ''}
                </div>
            `;
            toastContainer.appendChild(toastWrapper);
            const toast = new bootstrap.Toast(toastWrapper, { delay: 4500 });
            toastWrapper.addEventListener('hidden.bs.toast', () => toastWrapper.remove());
            toast.show();
        };

        const renderList = (notifications) => {
            if (!Array.isArray(notifications) || notifications.length === 0) {
                listEl.innerHTML = '<div class="p-3 text-muted small" data-notif-empty>No notifications yet.</div>';
                return;
            }

            listEl.innerHTML = notifications.map((notification) => {
                const isUnread = !!notification.is_unread;
                const badgeClass = isUnread ? 'bg-primary' : 'bg-secondary';
                const badgeText = isUnread ? 'New' : 'Seen';
                const rowClass = isUnread ? 'bg-light' : '';
                const readAction = readActionTemplate.replace('__ID__', notification.id);
                return `
                    <div class="list-group-item ${rowClass}" data-notification-id="${esc(notification.id)}">
                        <div class="d-flex gap-2">
                            <div class="pt-1">
                                <span class="badge rounded-pill ${badgeClass}">${badgeText}</span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">${esc(notification.title)}</div>
                                <div class="text-muted small">${esc(notification.message)}</div>
                                ${notification.timestamp ? `<div class="text-muted small">${esc(notification.timestamp)}</div>` : ''}
                                <div class="d-flex gap-2 mt-2">
                                    ${notification.url ? `<a class="btn btn-sm btn-outline-primary" href="${esc(notification.url)}">View</a>` : ''}
                                    ${isUnread ? `
                                        <form method="POST" action="${esc(readAction)}">
                                            <input type="hidden" name="_token" value="${esc(csrf)}">
                                            <button class="btn btn-sm btn-outline-secondary">Mark read</button>
                                        </form>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        };

        const poll = async () => {
            try {
                const response = await fetch(feedUrl, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    cache: 'no-store',
                });
                if (!response.ok) {
                    return;
                }
                const payload = await response.json();
                const notifications = payload.notifications || [];
                const unreadCount = Number(payload.unread_count || 0);

                for (const notification of notifications) {
                    if (notification.is_unread && !seenIds.has(notification.id)) {
                        showToast(notification);
                    }
                    seenIds.add(notification.id);
                }

                renderList(notifications);
                updateUnreadUI(unreadCount);
            } catch (error) {
                // Silent fail: polling should never break page interaction
            }
        };

        setInterval(poll, 8000);
        poll();

        readAllForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            try {
                const response = await fetch(readAllAction, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                if (response.ok) {
                    await poll();
                } else {
                    readAllForm.submit();
                }
            } catch (error) {
                readAllForm.submit();
            }
        });
    })();
</script>
