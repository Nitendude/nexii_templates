<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'APM' }}</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

        <style>
            :root {
                --eh-bg: #f4f6f9;
                --eh-card: #ffffff;
                --eh-border: #e3e7ee;
                --eh-primary: #0d6efd;
            }
            body {
                background: var(--eh-bg);
            }
            .eh-navbar {
                background: #0f1f3d;
            }
            .eh-navbar .navbar-brand {
                font-weight: 700;
                letter-spacing: 0.06em;
            }
            .eh-sidebar {
                min-height: calc(100vh - 64px);
                background: #0f1f3d;
            }
            .eh-sidebar-col {
                transition: all 0.2s ease;
            }
            .eh-main-col {
                transition: all 0.2s ease;
            }
            .eh-sidebar .nav-link {
                color: #e5e7eb;
                padding: 0.6rem 0.75rem;
                border-radius: 0.5rem;
                transition: background 0.15s ease, color 0.15s ease, transform 0.15s ease;
                display: flex;
                align-items: center;
                gap: 0.6rem;
            }
            .eh-sidebar .nav-link.active,
            .eh-sidebar .nav-link:hover {
                background: rgba(255, 255, 255, 0.18);
                color: #ffffff;
                transform: translateX(2px);
            }
            .eh-sidebar .section-title {
                font-size: 0.78rem;
                letter-spacing: 0.08em;
                color: #e2e8f0;
                padding: 0.45rem 0.75rem;
                margin-top: 0.75rem;
                border-top: 1px solid rgba(255, 255, 255, 0.08);
                font-weight: 700;
                background: #f8fafc;
                border-radius: 0.5rem;
            }
            .eh-sidebar .section-title:first-child {
                border-top: none;
                margin-top: 0;
            }
            .eh-sidebar .sidebar-section {
                border-left: 3px solid rgba(255, 255, 255, 0.18);
                padding-left: 0.4rem;
                margin-left: 0.2rem;
            }
            .eh-sidebar .section-title-employee {
                background: #eef2ff;
                color: #1e3a8a;
                border: 1px solid #dbeafe;
            }
            .eh-sidebar .sidebar-section-employee {
                border-color: #2563eb;
            }
            .eh-sidebar .sidebar-section-employee .nav-link.active,
            .eh-sidebar .sidebar-section-employee .nav-link:hover {
                background: #e8f0ff;
                color: #1e3a8a;
            }
            .eh-sidebar .section-title-accounting {
                background: #fdf2f8;
                color: #9d174d;
                border: 1px solid #fbcfe8;
            }
            .eh-sidebar .sidebar-section-accounting {
                border-color: #db2777;
            }
            .eh-sidebar .sidebar-section-accounting .nav-link.active,
            .eh-sidebar .sidebar-section-accounting .nav-link:hover {
                background: #fce7f3;
                color: #9d174d;
            }
            .eh-sidebar .section-title-billing {
                background: #f0fdf4;
                color: #166534;
                border: 1px solid #bbf7d0;
            }
            .eh-sidebar .sidebar-section-billing {
                border-color: #16a34a;
            }
            .eh-sidebar .sidebar-section-billing .nav-link.active,
            .eh-sidebar .sidebar-section-billing .nav-link:hover {
                background: #dcfce7;
                color: #166534;
            }
            .eh-sidebar .section-title-technical {
                background: #fff7ed;
                color: #9a3412;
                border: 1px solid #fed7aa;
            }
            .eh-sidebar .sidebar-section-technical {
                border-color: #ea580c;
            }
            .eh-sidebar .sidebar-section-technical .nav-link.active,
            .eh-sidebar .sidebar-section-technical .nav-link:hover {
                background: #ffedd5;
                color: #9a3412;
            }
            .eh-sidebar .section-title-operations {
                background: #ecfeff;
                color: #155e75;
                border: 1px solid #a5f3fc;
            }
            .eh-sidebar .sidebar-section-operations {
                border-color: #06b6d4;
            }
            .eh-sidebar .sidebar-section-operations .nav-link.active,
            .eh-sidebar .sidebar-section-operations .nav-link:hover {
                background: #cffafe;
                color: #155e75;
            }
            .eh-sidebar .section-title-admin {
                background: #f8fafc;
                color: #334155;
                border: 1px solid #e2e8f0;
            }
            .eh-sidebar .sidebar-section-admin {
                border-color: #64748b;
            }
            .eh-sidebar .sidebar-section-admin .nav-link.active,
            .eh-sidebar .sidebar-section-admin .nav-link:hover {
                background: #e2e8f0;
                color: #334155;
            }
            .eh-sidebar .nav-link i {
                width: 18px;
                color: inherit;
                opacity: 0.95;
            }
            @media (max-width: 991.98px) {
                #sidebarOffcanvas {
                    width: min(88vw, 360px);
                }
                #sidebarOffcanvas .offcanvas-header {
                    padding: 1rem 1rem 0.75rem;
                    border-bottom: 1px solid var(--eh-border);
                    background: #0f1f3d;
                    color: #fff;
                }
                #sidebarOffcanvas .offcanvas-title {
                    font-weight: 800;
                    letter-spacing: 0.06em;
                }
                #sidebarOffcanvas .btn-close {
                    filter: invert(1);
                }
                #sidebarOffcanvas .offcanvas-body {
                    padding: 1rem 0.875rem 1.5rem;
                    background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
                }
                #sidebarOffcanvas .section-title {
                    padding: 0.85rem 1rem;
                    margin-top: 0.95rem;
                    border-radius: 0.85rem;
                    font-size: 0.92rem;
                    letter-spacing: 0.04em;
                }
                #sidebarOffcanvas .sidebar-section {
                    margin-left: 0;
                    padding-left: 0.7rem;
                    border-left-width: 4px;
                }
                #sidebarOffcanvas .nav-link {
                    min-height: 52px;
                    padding: 0.8rem 1rem;
                    margin: 0.2rem 0;
                    border-radius: 0.85rem;
                    font-size: 0.98rem;
                    line-height: 1.25;
                    gap: 0.85rem;
                    align-items: center;
                }
                #sidebarOffcanvas .nav-link i {
                    width: 22px;
                    font-size: 1.05rem;
                    flex: 0 0 22px;
                }
                #sidebarOffcanvas .nav-text {
                    display: block;
                    white-space: normal;
                }
            }
            .sidebar-collapsed .eh-sidebar-col {
                flex: 0 0 72px;
                max-width: 72px;
            }
            .sidebar-collapsed .eh-main-col {
                flex: 0 0 calc(100% - 72px);
                max-width: calc(100% - 72px);
            }
            .sidebar-collapsed .eh-sidebar .nav-text {
                display: none;
            }
            .sidebar-collapsed .eh-sidebar .section-title {
                display: none !important;
            }
            .sidebar-collapsed .eh-sidebar .collapse {
                display: block !important;
            }
            .sidebar-collapsed .eh-sidebar .nav-link {
                justify-content: center;
            }
            .sidebar-collapsed .eh-sidebar .nav-link i {
                margin-right: 0 !important;
            }
            .eh-card {
                background: var(--eh-card);
                border: 1px solid var(--eh-border);
                border-radius: 0.75rem;
            }
            .eh-profile-photo {
                width: 96px;
                height: 96px;
                object-fit: cover;
                border-radius: 16px;
                border: 2px solid #fff;
                box-shadow: 0 6px 18px rgba(15, 31, 61, 0.15);
            }
            .eh-badge {
                background: #f0f4ff;
                color: #2b4ea2;
                border: 1px solid #d6e0ff;
            }
            .chatbot-fab {
                position: fixed;
                right: 24px;
                bottom: 24px;
                z-index: 1040;
                touch-action: none;
            }
            .chatbot-window {
                position: fixed;
                right: 24px;
                bottom: 90px;
                width: 420px;
                max-height: 560px;
                background: #ffffff;
                border-radius: 0.75rem;
                border: 1px solid var(--eh-border);
                display: none;
                flex-direction: column;
                z-index: 1040;
            }
            .chatbot-window.open {
                display: flex;
            }
            .chatbot-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem 1rem;
                border-bottom: 1px solid var(--eh-border);
                background: #f7f9fc;
                border-top-left-radius: 0.75rem;
                border-top-right-radius: 0.75rem;
            }
            .chatbot-messages {
                padding: 0.75rem 1rem;
                overflow-y: auto;
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
                flex: 1;
            }
            .chatbot-message {
                padding: 0.5rem 0.75rem;
                border-radius: 0.6rem;
                font-size: 0.875rem;
                line-height: 1.3;
                max-width: 90%;
            }
            .chatbot-message.user {
                align-self: flex-end;
                background: #0d6efd;
                color: #fff;
            }
            .chatbot-message.bot {
                align-self: flex-start;
                background: #f1f5f9;
                color: #1f2937;
            }
            .chatbot-input {
                display: flex;
                gap: 0.5rem;
                padding: 0.75rem;
                border-top: 1px solid var(--eh-border);
            }
        </style>
        @stack('styles')
    </head>
    <body>
        @include('partials.navbar')

        <div class="container-fluid">
            <div class="row">
                @php
                    $needsOnboarding = auth()->check() && (auth()->user()->must_change_password || !auth()->user()->profile_completed);
                @endphp
                @unless($needsOnboarding)
                    <nav class="col-lg-2 d-none d-lg-block border-end eh-sidebar eh-sidebar-col px-3 py-4">
                        @include('partials.sidebar')
                    </nav>
                @endunless

                <main class="col-12 {{ $needsOnboarding ? '' : 'col-lg-10' }} eh-main-col px-4 py-4">
                    @if(session('inactive_notice'))
                        <div class="alert alert-warning">
                            {{ session('inactive_notice') }}
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>

        @unless($needsOnboarding)
            <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="sidebarOffcanvas">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title">APM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    @include('partials.sidebar')
                </div>
            </div>
        @endunless

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        @stack('scripts')

        @include('partials.chatbot')
        <script>
            (function () {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach((triggerEl) => {
                    new bootstrap.Tooltip(triggerEl);
                });

                const sidebarSections = document.querySelectorAll('[data-sidebar-section]');
                sidebarSections.forEach((section) => {
                    const key = section.getAttribute('data-sidebar-section');
                    if (!key) {
                        return;
                    }
                    const stored = localStorage.getItem(`sidebarSection:${key}`);
                    if (stored === 'collapsed') {
                        section.classList.remove('show');
                    }
                });

                sidebarSections.forEach((section) => {
                    const key = section.getAttribute('data-sidebar-section');
                    if (!key) {
                        return;
                    }
                    section.addEventListener('shown.bs.collapse', () => {
                        localStorage.setItem(`sidebarSection:${key}`, 'open');
                    });
                    section.addEventListener('hidden.bs.collapse', () => {
                        localStorage.setItem(`sidebarSection:${key}`, 'collapsed');
                    });
                });

                const sidebarToggle = document.getElementById('sidebarToggle');
                const body = document.body;
                if (sidebarToggle && body) {
                    const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                    if (collapsed) {
                        body.classList.add('sidebar-collapsed');
                    }
                    sidebarToggle.addEventListener('click', () => {
                        body.classList.toggle('sidebar-collapsed');
                        localStorage.setItem('sidebarCollapsed', body.classList.contains('sidebar-collapsed'));
                    });
                }

                const sidebarOffcanvas = document.getElementById('sidebarOffcanvas');
                if (sidebarOffcanvas) {
                    const offcanvasInstance = bootstrap.Offcanvas.getOrCreateInstance(sidebarOffcanvas);
                    sidebarOffcanvas.querySelectorAll('.nav-link').forEach((link) => {
                        link.addEventListener('click', () => {
                            offcanvasInstance.hide();
                        });
                    });
                }

                const toggle = document.getElementById('chatbotToggle');
                const windowEl = document.getElementById('chatbotWindow');
                const closeBtn = document.getElementById('chatbotClose');
                const form = document.getElementById('chatbotForm');
                const input = document.getElementById('chatbotInput');
                const messages = document.getElementById('chatbotMessages');
                const fab = document.querySelector('.chatbot-fab');
                const fabButton = document.getElementById('chatbotToggle');
                if (!toggle || !windowEl || !closeBtn || !form || !input || !messages) {
                    return;
                }

                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const appendMessage = (text, type) => {
                    const div = document.createElement('div');
                    div.className = `chatbot-message ${type}`;
                    div.textContent = text;
                    messages.appendChild(div);
                    messages.scrollTop = messages.scrollHeight;
                };

                const positionWindowFromFab = () => {
                    if (!fab) {
                        return;
                    }
                    const rect = fab.getBoundingClientRect();
                    const left = Math.min(rect.left, window.innerWidth - windowEl.offsetWidth);
                    const top = Math.max(0, rect.top - windowEl.offsetHeight - 12);
                    windowEl.style.left = `${left}px`;
                    windowEl.style.top = `${top}px`;
                    windowEl.style.right = 'auto';
                    windowEl.style.bottom = 'auto';
                };

                let suppressClick = false;
                toggle.addEventListener('click', () => {
                    if (suppressClick) {
                        suppressClick = false;
                        return;
                    }
                    windowEl.classList.add('open');
                    windowEl.setAttribute('aria-hidden', 'false');
                    positionWindowFromFab();
                    input.focus();
                });
                closeBtn.addEventListener('click', () => {
                    windowEl.classList.remove('open');
                    windowEl.setAttribute('aria-hidden', 'true');
                });

                form.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    const text = input.value.trim();
                    if (!text) {
                        return;
                    }
                    appendMessage(text, 'user');
                    input.value = '';
                    appendMessage('Thinking...', 'bot');
                    const pending = messages.lastElementChild;
                    try {
                        const response = await fetch("{{ route('chatbot.message') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf || ''
                            },
                            body: JSON.stringify({ message: text })
                        });
                        const data = await response.json();
                        pending.textContent = data.message || 'Sorry, I could not respond.';
                    } catch (err) {
                        pending.textContent = 'Chatbot is unavailable right now.';
                    }
                });

                const stored = localStorage.getItem('chatbotFabPosition');
                if (stored) {
                    try {
                        const { left, top } = JSON.parse(stored);
                        if (typeof left === 'number' && typeof top === 'number') {
                            if (fab) {
                                fab.style.left = `${left}px`;
                                fab.style.top = `${top}px`;
                                fab.style.right = 'auto';
                                fab.style.bottom = 'auto';
                            }
                        }
                    } catch (err) {
                    }
                }

                if (fab && fabButton) {
                    let dragOffsetX = 0;
                    let dragOffsetY = 0;
                    let dragging = false;
                    let isMouseDown = false;
                    let startX = 0;
                    let startY = 0;

                    const onMouseMove = (event) => {
                        if (!isMouseDown) {
                            return;
                        }
                        if (!dragging) {
                            const deltaX = Math.abs(event.clientX - startX);
                            const deltaY = Math.abs(event.clientY - startY);
                            if (deltaX < 5 && deltaY < 5) {
                                return;
                            }
                            dragging = true;
                            suppressClick = true;
                        }
                        const maxLeft = window.innerWidth - fab.offsetWidth;
                        const maxTop = window.innerHeight - fab.offsetHeight;
                        const left = Math.min(Math.max(0, event.clientX - dragOffsetX), Math.max(0, maxLeft));
                        const top = Math.min(Math.max(0, event.clientY - dragOffsetY), Math.max(0, maxTop));
                        fab.style.left = `${left}px`;
                        fab.style.top = `${top}px`;
                        fab.style.right = 'auto';
                        fab.style.bottom = 'auto';
                        if (windowEl.classList.contains('open')) {
                            positionWindowFromFab();
                        }
                    };

                    const onMouseUp = () => {
                        if (!isMouseDown) {
                            return;
                        }
                        isMouseDown = false;
                        if (dragging) {
                            dragging = false;
                            const left = parseFloat(fab.style.left || '0');
                            const top = parseFloat(fab.style.top || '0');
                            localStorage.setItem('chatbotFabPosition', JSON.stringify({ left, top }));
                        }
                    };

                    fabButton.addEventListener('mousedown', (event) => {
                        if (event.button !== 0) {
                            return;
                        }
                        isMouseDown = true;
                        dragging = false;
                        startX = event.clientX;
                        startY = event.clientY;
                        const rect = fab.getBoundingClientRect();
                        dragOffsetX = event.clientX - rect.left;
                        dragOffsetY = event.clientY - rect.top;
                    });
                    window.addEventListener('mousemove', onMouseMove);
                    window.addEventListener('mouseup', onMouseUp);
                }
            })();
        </script>
        <script>
            (function () {
                const currentRoute = @json(optional(request()->route())->getName());
                const liveApprovalRoutes = new Set([
                    'admin.cash-advances.index',
                    'accounting.cash-advances.index',
                    'admin.timeoff.index',
                    'admin.profile-corrections.index',
                    'cash-advances',
                    'timeoff',
                    'profile-corrections.index',
                ]);

                if (!liveApprovalRoutes.has(currentRoute)) {
                    return;
                }

                const stampUrl = @json(route('live.approvals-stamp'));
                let lastStamp = null;
                let polling = false;

                const fetchStamp = async () => {
                    if (polling || document.hidden) {
                        return;
                    }
                    polling = true;
                    try {
                        const response = await fetch(`${stampUrl}?_=${Date.now()}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            cache: 'no-store',
                        });
                        if (!response.ok) {
                            return;
                        }
                        const payload = await response.json();
                        const nextStamp = payload?.stamp || null;
                        if (!nextStamp) {
                            return;
                        }
                        if (lastStamp === null) {
                            lastStamp = nextStamp;
                            return;
                        }
                        if (nextStamp !== lastStamp) {
                            window.location.reload();
                        }
                    } catch (error) {
                        // Silent fail; next poll will retry.
                    } finally {
                        polling = false;
                    }
                };

                fetchStamp();
                setInterval(fetchStamp, 10000);
                document.addEventListener('visibilitychange', fetchStamp);
            })();
        </script>
    </body>
</html>
