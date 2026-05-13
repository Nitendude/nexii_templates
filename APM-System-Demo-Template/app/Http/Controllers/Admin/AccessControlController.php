<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccessControlController extends Controller
{
    private array $permissions = [
        'dashboard' => 'Dashboard',
        'my-profile' => 'My Profile',
        'profile-corrections' => 'Profile Correction',
        'payslips' => 'Payslip',
        'leave-form' => 'Leave Form',
        'documents' => 'Documents',
        'cash-advances' => 'Cash Advance',
        'billing' => 'Billing',
        'job-orders' => 'JO Creation',
        'admin-employees' => 'Admin: Employees',
        'admin-payslips' => 'Admin: Payslips',
        'admin-leave-approvals' => 'Admin: Leave Form Approvals',
        'admin-cash-advance-approvals' => 'Admin: Cash Advance Approvals',
        'admin-liquidation-approvals' => 'Admin: Liquidation/Petty Cash Approvals',
        'admin-ca-summary' => 'Admin: CA Summary Reports / Reimbursable Voucher',
        'admin-ca-payments' => 'Admin: CA Payments',
        'admin-container-deposits' => 'Admin: Container Deposits',
        'admin-cost-sheets' => 'Admin: Cost Sheet / SI Summary',
        'admin-cost-sheet-sales-report' => 'Admin: JO Total Sales Report',
        'admin-record-monitoring' => 'Admin: Record Monitoring',
        'admin-profile-corrections' => 'Admin: Profile Corrections',
        'admin-support-tickets' => 'Admin: Support Tickets',
        'admin-reports' => 'Admin: Reports / User Activity Logs / Audit Logs',
        'admin-clients' => 'Admin: Clients',
    ];

    private array $permissionGroups = [
        'employee' => [
            'label' => 'Employee Sidebar',
            'description' => 'Pages shown under the Employee section of the sidebar.',
            'permissions' => [
                'dashboard',
                'my-profile',
                'cash-advances',
                'payslips',
                'leave-form',
            ],
        ],
        'accounting' => [
            'label' => 'Accounting Sidebar',
            'description' => 'Accounting pages shown in the sidebar, including CA Summary, Reimbursable Voucher, Container Deposits, Cost Sheet, SI Summary, JO Total Sales, Record Monitoring, and CA Payments.',
            'permissions' => [
                'admin-cash-advance-approvals',
                'admin-liquidation-approvals',
                'admin-ca-summary',
                'admin-ca-payments',
                'admin-container-deposits',
                'admin-cost-sheets',
                'admin-cost-sheet-sales-report',
                'admin-record-monitoring',
            ],
            'includes' => [
                'admin-ca-summary' => ['CA Summary Reports', 'Reimbursable Voucher'],
                'admin-container-deposits' => ['Container Deposits'],
                'admin-cost-sheets' => ['Cost Sheet', 'SI Summary'],
                'admin-cost-sheet-sales-report' => ['JO Total Sales'],
            ],
        ],
        'billing' => [
            'label' => 'Billing Sidebar',
            'description' => 'Billing, Service Invoice, Debit/Credit Note, and storage pages.',
            'permissions' => [
                'billing',
            ],
        ],
        'operations' => [
            'label' => 'Operations Sidebar',
            'description' => 'Operations pages including JO Creation.',
            'permissions' => [
                'job-orders',
            ],
        ],
        'documentation' => [
            'label' => 'Documentation / Technical Sidebar',
            'description' => 'Documentation and Technical self-service pages shown in the sidebar.',
            'permissions' => [
                'documents',
                'profile-corrections',
            ],
        ],
        'admin' => [
            'label' => 'Admin Sidebar',
            'description' => 'Admin-only tools and monitoring pages. Access Control itself stays available only to the IT super admin.',
            'permissions' => [
                'admin-employees',
                'admin-payslips',
                'admin-leave-approvals',
                'admin-profile-corrections',
                'admin-support-tickets',
                'admin-reports',
                'admin-clients',
            ],
            'includes' => [
                'admin-reports' => ['Reports', 'User Activity Logs', 'Audit Logs'],
            ],
            'notes' => [
                'Access Control is shown only to the IT super admin and does not use a regular checkbox.',
            ],
        ],
    ];

    public function index(Request $request): View
    {
        $this->authorizeSuperAdmin($request->user());

        $users = User::query()
            ->with('employmentDetail')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.access-control.index', [
            'users' => $users,
        ]);
    }

    public function edit(Request $request, User $user): View
    {
        $this->authorizeSuperAdmin($request->user());

        return view('admin.access-control.edit', [
            'managedUser' => $user,
            'permissions' => $this->permissions,
            'permissionGroups' => $this->permissionGroups,
            'selected' => $this->resolveSelected($user),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeSuperAdmin($request->user());

        $validated = $request->validate([
            'access_permissions' => ['nullable', 'array'],
        ]);

        $selected = array_values(array_filter($validated['access_permissions'] ?? [], function ($permission) {
            return array_key_exists($permission, $this->permissions);
        }));

        $user->update([
            'access_permissions' => $selected,
        ]);

        return redirect()
            ->route('admin.access-control.edit', $user)
            ->with('status', 'access-updated');
    }

    private function authorizeSuperAdmin(User $user): void
    {
        if (!$user->isSuperAdmin()) {
            abort(403);
        }
    }

    private function resolveSelected(User $user): array
    {
        if (is_array($user->access_permissions)) {
            return $user->access_permissions;
        }

        if ($user->role === 'admin') {
            return array_keys($this->permissions);
        }

        return $user->defaultAccessPermissions();
    }
}
