<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'APM' }}</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
        <style>
            :root {
                --apm-ink: #0f172a;
                --apm-muted: #64748b;
                --apm-ice: #f8fafc;
                --apm-sea: #0ea5a4;
                --apm-sky: #38bdf8;
                --apm-cloud: rgba(255, 255, 255, 0.6);
            }
            body {
                background:
                    radial-gradient(circle at 15% 15%, rgba(56, 189, 248, 0.18), transparent 55%),
                    radial-gradient(circle at 85% 10%, rgba(14, 165, 164, 0.16), transparent 50%),
                    linear-gradient(135deg, #f1f5f9, #e2e8f0);
                color: var(--apm-ink);
                font-family: "Poppins", "Segoe UI", Tahoma, sans-serif;
            }
            .auth-shell {
                background: var(--apm-cloud);
                border: 1px solid rgba(226, 232, 240, 0.9);
                border-radius: 1.25rem;
                box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
                backdrop-filter: blur(8px);
                padding: 2rem;
            }
            .auth-card {
                border: 1px solid #e3e7ee;
                border-radius: 1rem;
                box-shadow: 0 18px 40px rgba(15, 31, 61, 0.12);
                position: relative;
                overflow: hidden;
            }
            .auth-card::before {
                content: "";
                position: absolute;
                inset: 0;
                border-top: 4px solid var(--apm-sea);
                pointer-events: none;
            }
            .auth-title {
                letter-spacing: 0.08em;
                text-transform: uppercase;
                font-size: 0.75rem;
                color: var(--apm-muted);
            }
            .auth-brand {
                font-weight: 700;
                font-size: 1.5rem;
            }
            .auth-helper {
                color: var(--apm-muted);
                font-size: 0.9rem;
            }
            .auth-card .form-control:focus {
                border-color: var(--apm-sea);
                box-shadow: 0 0 0 0.2rem rgba(14, 165, 164, 0.2);
            }
            .auth-card .btn-primary {
                background: linear-gradient(135deg, var(--apm-sea), var(--apm-sky));
                border: none;
                box-shadow: 0 12px 18px rgba(14, 165, 164, 0.25);
            }
            .auth-card .btn-primary:hover {
                filter: brightness(0.98);
            }
        </style>
    </head>
    <body>
        <div class="container d-flex align-items-center justify-content-center min-vh-100">
            <div class="col-lg-5 col-md-7">
                <div class="auth-shell">
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/apm-logo.png') }}" alt="APM Customs Brokerage" class="mb-3" style="height: 68px; width: auto;">
                        <div class="auth-title"></div>
                        <div class="auth-brand">Employee Portal</div>
                        <div class="auth-helper">Secure access to your profile, payslip, and requests.</div>
                    </div>
                    <div class="auth-card bg-white p-4">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
