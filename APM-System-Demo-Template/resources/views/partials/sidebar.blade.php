@php
    $user = Auth::user();
@endphp

<div class="d-flex flex-column gap-2">
    <button class="section-title section-title-employee d-flex justify-content-between align-items-center btn w-100" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarEmployee" data-sidebar-toggle="employee">
        <span>Employee</span>
        <i class="bi bi-chevron-down nav-text"></i>
    </button>
    <div class="collapse show sidebar-section sidebar-section-employee" id="sidebarEmployee" data-sidebar-section="employee">
        @if($user?->hasAccess('dashboard'))
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                <i class="bi bi-speedometer2 me-2"></i><span class="nav-text">Dashboard</span>
            </a>
        @endif
        @if($user?->hasAccess('my-profile'))
            <a class="nav-link {{ request()->routeIs('my-profile') ? 'active' : '' }}" href="{{ route('my-profile') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="My Profile">
                <i class="bi bi-person me-2"></i><span class="nav-text">My Profile</span>
            </a>
        @endif
        @if($user?->hasAccess('cash-advances'))
            <a class="nav-link {{ request()->routeIs('cash-advances') ? 'active' : '' }}" href="{{ route('cash-advances') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Cash Advance">
                <i class="bi bi-cash-stack me-2"></i><span class="nav-text">Cash Advance</span>
            </a>
        @endif
        @if($user?->hasAccess('payslips'))
            <a class="nav-link {{ request()->routeIs('payslips') ? 'active' : '' }}" href="{{ route('payslips') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Payslip">
                <i class="bi bi-receipt-cutoff me-2"></i><span class="nav-text">Payslip</span>
            </a>
        @endif
        @if($user?->hasAccess('leave-form'))
            <a class="nav-link {{ request()->routeIs('timeoff') ? 'active' : '' }}" href="{{ route('timeoff') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Leave Form">
                <i class="bi bi-calendar-event me-2"></i><span class="nav-text">Leave Form</span>
            </a>
        @endif
    </div>

    @if($user?->hasAnyAccountingAccess())
        <button class="section-title section-title-accounting d-flex justify-content-between align-items-center btn w-100" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarAccounting" data-sidebar-toggle="accounting">
            <span>Accounting</span>
            <i class="bi bi-chevron-down nav-text"></i>
        </button>
        <div class="collapse show sidebar-section sidebar-section-accounting" id="sidebarAccounting" data-sidebar-section="accounting">
            @if($user?->hasAccess('admin-cash-advance-approvals'))
                <a class="nav-link {{ request()->routeIs('accounting.cash-advances.index') ? 'active' : '' }}" href="{{ route('accounting.cash-advances.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Cash Advance Approvals">
                    <i class="bi bi-cash-stack me-2"></i><span class="nav-text">Cash Advance Approvals</span>
                </a>
            @endif
            @if($user?->hasAccess('admin-cash-advance-approvals') || $user?->hasAccess('admin-liquidation-approvals'))
                <a class="nav-link {{ request()->routeIs('accounting.cash-advances.liquidations.approvals') ? 'active' : '' }}" href="{{ route('accounting.cash-advances.liquidations.approvals') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Liquidation/Petty Cash Approvals">
                    <i class="bi bi-list-check me-2"></i><span class="nav-text">Liquidation/Petty Cash Approvals</span>
                </a>
            @endif
            @if($user?->hasAccess('admin-ca-summary'))
                <a class="nav-link {{ request()->routeIs('accounting.cash-advances.summary') ? 'active' : '' }}" href="{{ route('accounting.cash-advances.summary') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="CA Summary Reports">
                    <i class="bi bi-clipboard-data me-2"></i><span class="nav-text">CA Summary Reports</span>
                </a>
                <a class="nav-link {{ request()->routeIs('accounting.reimbursable-vouchers.*') ? 'active' : '' }}" href="{{ route('accounting.reimbursable-vouchers.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Reimbursable Voucher">
                    <i class="bi bi-journal-check me-2"></i><span class="nav-text">Reimbursable Voucher</span>
                </a>
            @endif
            @if($user?->hasAccess('admin-cost-sheets'))
                <a class="nav-link {{ request()->routeIs('accounting.cost-sheets.*') ? 'active' : '' }}" href="{{ route('accounting.cost-sheets.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Cost Sheet">
                    <i class="bi bi-table me-2"></i><span class="nav-text">Cost Sheet</span>
                </a>
                <a class="nav-link {{ request()->routeIs('accounting.cost-sheets.service-invoice-summary') ? 'active' : '' }}" href="{{ route('accounting.cost-sheets.service-invoice-summary') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Service Invoice Summary">
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i><span class="nav-text">SI Summary</span>
                </a>
            @endif
            @if($user?->hasAccess('admin-cost-sheet-sales-report'))
                <a class="nav-link {{ request()->routeIs('accounting.cost-sheets.sales-report') ? 'active' : '' }}" href="{{ route('accounting.cost-sheets.sales-report') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="JO Total Sales Report">
                    <i class="bi bi-graph-up-arrow me-2"></i><span class="nav-text">JO Total Sales</span>
                </a>
            @endif
            @if($user?->hasAccess('admin-record-monitoring'))
                <a class="nav-link {{ request()->routeIs('accounting.record-monitoring.*') ? 'active' : '' }}" href="{{ route('accounting.record-monitoring.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Record Monitoring">
                    <i class="bi bi-folder-check me-2"></i><span class="nav-text">Record Monitoring</span>
                </a>
            @endif
            @if($user?->hasAccess('admin-ca-payments'))
                <a class="nav-link {{ request()->routeIs('accounting.cash-advances.payments') ? 'active' : '' }}" href="{{ route('accounting.cash-advances.payments') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="CA Payments">
                    <i class="bi bi-cash-coin me-2"></i><span class="nav-text">CA Payments</span>
                </a>
            @endif
            @if($user?->hasAccess('admin-container-deposits'))
                <a class="nav-link {{ request()->routeIs('accounting.container-deposits') ? 'active' : '' }}" href="{{ route('accounting.container-deposits') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Container Deposits">
                    <i class="bi bi-box-seam me-2"></i><span class="nav-text">Container Deposits</span>
                </a>
            @endif
        </div>
    @endif

    @if($user?->hasAccess('billing'))
        <button class="section-title section-title-billing d-flex justify-content-between align-items-center btn w-100" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarBilling" data-sidebar-toggle="billing">
            <span>Billing</span>
            <i class="bi bi-chevron-down nav-text"></i>
        </button>
        <div class="collapse show sidebar-section sidebar-section-billing" id="sidebarBilling" data-sidebar-section="billing">
            <a class="nav-link {{ request()->routeIs('billing.storage') ? 'active' : '' }}" href="{{ route('billing.storage') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Master Billing Storage">
                <i class="bi bi-folder-symlink me-2"></i><span class="nav-text">Master Storage</span>
            </a>
            <a class="nav-link {{ request()->routeIs('billing') ? 'active' : '' }}" href="{{ route('billing') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Billing">
                <i class="bi bi-receipt me-2"></i><span class="nav-text">Billing</span>
            </a>
            <a class="nav-link {{ request()->routeIs('billing.documents') ? 'active' : '' }}" href="{{ route('billing.documents') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Billing Documents">
                <i class="bi bi-archive me-2"></i><span class="nav-text">Billing Documents</span>
            </a>
            <a class="nav-link {{ request()->routeIs('billing.service-invoices') ? 'active' : '' }}" href="{{ route('billing.service-invoices') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Service Invoice">
                <i class="bi bi-file-earmark-text me-2"></i><span class="nav-text">Service Invoice</span>
            </a>
            <a class="nav-link {{ request()->routeIs('billing.service-invoices.documents') ? 'active' : '' }}" href="{{ route('billing.service-invoices.documents') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Service Invoice Documents">
                <i class="bi bi-folder2 me-2"></i><span class="nav-text">SI Documents</span>
            </a>
            <a class="nav-link {{ request()->routeIs('billing.notes') || request()->routeIs('billing.notes.create') || request()->routeIs('billing.notes.show') ? 'active' : '' }}" href="{{ route('billing.notes') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Debit Credit Notes">
                <i class="bi bi-journal-plus me-2"></i><span class="nav-text">Debit/Credit Notes</span>
            </a>
            <a class="nav-link {{ request()->routeIs('billing.notes.documents') ? 'active' : '' }}" href="{{ route('billing.notes.documents') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Debit Credit Note Documents">
                <i class="bi bi-folder2 me-2"></i><span class="nav-text">DCN Documents</span>
            </a>
        </div>
    @endif

    <button class="section-title section-title-technical d-flex justify-content-between align-items-center btn w-100" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarTechnical" data-sidebar-toggle="technical">
        <span>Technical</span>
        <i class="bi bi-chevron-down nav-text"></i>
    </button>
    <div class="collapse show sidebar-section sidebar-section-technical" id="sidebarTechnical" data-sidebar-section="technical">
        @if($user?->hasAccess('profile-corrections'))
            <a class="nav-link {{ request()->routeIs('profile-corrections.*') ? 'active' : '' }}" href="{{ route('profile-corrections.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Profile Correction">
                <i class="bi bi-pencil-square me-2"></i><span class="nav-text">Profile Correction</span>
            </a>
        @endif
        <a class="nav-link {{ request()->routeIs('support.*') ? 'active' : '' }}" href="{{ route('support.employee') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Submit Ticket">
            <i class="bi bi-life-preserver me-2"></i><span class="nav-text">Submit Ticket</span>
        </a>
    </div>

    @if($user?->hasAccess('job-orders'))
        <button class="section-title section-title-operations d-flex justify-content-between align-items-center btn w-100" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarOperations" data-sidebar-toggle="operations">
            <span>Operations</span>
            <i class="bi bi-chevron-down nav-text"></i>
        </button>
        <div class="collapse show sidebar-section sidebar-section-operations" id="sidebarOperations" data-sidebar-section="operations">
            <a class="nav-link {{ request()->routeIs('operations.job-orders.*') ? 'active' : '' }}" href="{{ route('operations.job-orders.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="JO Creation">
                <i class="bi bi-journal-text me-2"></i><span class="nav-text">JO Creation</span>
            </a>
            @if($user?->hasAccess('documents'))
                <a class="nav-link {{ request()->routeIs('documentation.documents') ? 'active' : '' }}" href="{{ route('documentation.documents') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Documents">
                    <i class="bi bi-folder2-open me-2"></i><span class="nav-text">Documents</span>
                </a>
            @endif
        </div>
    @endif

    @if($user?->isAdmin() || $user?->hasAnyAdminAccess())
        <button class="section-title section-title-admin d-flex justify-content-between align-items-center btn w-100" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarAdmin" data-sidebar-toggle="admin">
            <span>Admin</span>
            <i class="bi bi-chevron-down nav-text"></i>
        </button>
        <div class="collapse show sidebar-section sidebar-section-admin" id="sidebarAdmin" data-sidebar-section="admin">
            @if($user?->hasAccess('admin-employees'))
                <a class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}" href="{{ route('admin.employees.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Employees">
                    <i class="bi bi-people me-2"></i><span class="nav-text">Employees</span>
                </a>
            @endif
            @if($user?->hasAccess('admin-payslips'))
                <a class="nav-link {{ request()->routeIs('admin.payslips.*') ? 'active' : '' }}" href="{{ route('admin.payslips.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Payslips">
                    <i class="bi bi-receipt me-2"></i><span class="nav-text">Payslips</span>
                </a>
            @endif
            @if($user?->hasAccess('admin-leave-approvals'))
                <a class="nav-link {{ request()->routeIs('admin.timeoff.*') ? 'active' : '' }}" href="{{ route('admin.timeoff.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Leave Form Approvals">
                    <i class="bi bi-clipboard-check me-2"></i><span class="nav-text">Leave Form Approvals</span>
                </a>
            @endif
            @if($user?->hasAccess('admin-profile-corrections'))
                <a class="nav-link {{ request()->routeIs('admin.profile-corrections.*') ? 'active' : '' }}" href="{{ route('admin.profile-corrections.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Profile Corrections">
                    <i class="bi bi-person-check me-2"></i><span class="nav-text">Profile Corrections</span>
                </a>
            @endif
            @if($user?->hasAccess('admin-support-tickets'))
                <a class="nav-link {{ request()->routeIs('admin.support-tickets.*') ? 'active' : '' }}" href="{{ route('admin.support-tickets.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Support Tickets">
                    <i class="bi bi-headset me-2"></i><span class="nav-text">Support Tickets</span>
                </a>
            @endif
            @if($user?->hasAccess('admin-clients'))
                <a class="nav-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}" href="{{ route('admin.clients.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Clients">
                    <i class="bi bi-building me-2"></i><span class="nav-text">Clients</span>
                </a>
            @endif
            @if($user?->hasAccess('admin-reports'))
                <a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{ route('admin.reports') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Reports">
                    <i class="bi bi-graph-up me-2"></i><span class="nav-text">Reports</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}" href="{{ route('admin.activity-logs.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="User Activity Logs">
                    <i class="bi bi-clock-history me-2"></i><span class="nav-text">User Activity Logs</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}" href="{{ route('admin.audit-logs.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Audit Logs">
                    <i class="bi bi-journal-text me-2"></i><span class="nav-text">Audit Logs</span>
                </a>
            @endif
            @if($user?->isSuperAdmin())
                <a class="nav-link {{ request()->routeIs('admin.access-control.*') ? 'active' : '' }}" href="{{ route('admin.access-control.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Access Control">
                    <i class="bi bi-shield-lock me-2"></i><span class="nav-text">Access Control</span>
                </a>
            @endif
        </div>
    @endif
</div>
