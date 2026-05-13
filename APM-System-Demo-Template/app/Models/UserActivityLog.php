<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'method',
        'route_name',
        'module',
        'url',
        'ip_address',
        'user_agent',
        'response_status',
        'request_payload',
        'route_parameters',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'route_parameters' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function describeAction(): string
    {
        $routeName = (string) $this->route_name;
        $method = strtoupper((string) $this->method);
        $payload = is_array($this->request_payload) ? $this->request_payload : [];

        return match ($routeName) {
            'dashboard' => 'Opened Dashboard',
            'my-profile' => 'Opened My Profile',
            'my-profile.photo' => 'Updated profile photo',
            'change-password.edit' => 'Opened Change Password page',
            'change-password.update', 'password.update' => 'Changed password',
            'timeoff' => 'Opened Leave Form',
            'timeoff.store' => 'Submitted leave request',
            'cash-advances' => 'Opened Cash Advance page',
            'cash-advances.store' => 'Submitted cash advance request',
            'cash-advances.liquidations.store' => 'Submitted liquidation or petty cash',
            'billing' => 'Opened Billing page',
            'billing.create' => 'Opened create billing page',
            'billing.store' => 'Created billing statement',
            'billing.edit' => 'Opened billing edit page',
            'billing.update' => 'Updated billing statement',
            'billing.documents' => 'Opened billing documents',
            'billing.show' => 'Viewed billing statement',
            'service-invoices' => 'Opened Service Invoice page',
            'service-invoices.create' => 'Opened create service invoice page',
            'service-invoices.store' => 'Created service invoice',
            'service-invoices.documents' => 'Opened service invoice documents',
            'service-invoices.show' => 'Viewed service invoice',
            'service-invoices.edit' => 'Opened service invoice edit page',
            'service-invoices.update' => 'Updated service invoice',
            'billing.notes' => 'Opened Debit/Credit Note page',
            'billing.notes.create' => 'Opened create debit/credit note page',
            'billing.notes.store' => 'Created debit/credit note',
            'billing.notes.documents' => 'Opened debit/credit note documents',
            'billing.notes.show' => 'Viewed debit/credit note',
            'documents' => 'Opened Documents page',
            'profile-corrections.index' => 'Opened Profile Correction page',
            'profile-corrections.store' => 'Submitted profile correction request',
            'support.employee', 'support.create' => 'Opened Support page',
            'support.store' => 'Submitted support ticket',
            'job-orders.index' => 'Opened JO list',
            'job-orders.create' => 'Opened create JO page',
            'job-orders.store' => 'Created JO ' . $this->resolveJoNumber($payload),
            'job-orders.show' => 'Viewed JO details',
            'job-orders.edit' => 'Opened edit JO page',
            'job-orders.update' => 'Updated JO ' . $this->resolveJoNumber($payload),
            'admin.employees.index' => 'Opened Employees page',
            'admin.employees.create' => 'Opened create employee page',
            'admin.employees.store' => 'Added new user ' . $this->resolveUserLabel($payload),
            'admin.employees.show' => 'Viewed employee record',
            'admin.employees.edit' => 'Opened edit employee page',
            'admin.employees.update' => 'Updated user ' . $this->resolveUserLabel($payload),
            'admin.clients.store' => 'Created client ' . $this->resolveSimpleLabel($payload, ['name', 'client_name']),
            'admin.clients.update' => 'Updated client ' . $this->resolveSimpleLabel($payload, ['name', 'client_name']),
            'admin.clients.destroy' => 'Archived client',
            'admin.clients.restore' => 'Restored client',
            'admin.timeoff.index' => 'Opened leave approvals',
            'admin.timeoff.update' => 'Updated leave request status',
            'admin.cash-advances.index' => 'Opened cash advance approvals',
            'accounting.cash-advances.index' => 'Opened cash advance approvals',
            'admin.cash-advances.store' => 'Created cash advance request for employee',
            'accounting.cash-advances.store' => 'Created cash advance request for employee',
            'admin.cash-advances.update' => 'Updated cash advance status',
            'accounting.cash-advances.update' => 'Updated cash advance status',
            'admin.cash-advances.personal-paid' => 'Marked personal paid status',
            'accounting.cash-advances.personal-paid' => 'Marked personal paid status',
            'admin.cash-advances.liquidations.approvals' => 'Opened liquidation approvals',
            'accounting.cash-advances.liquidations.approvals' => 'Opened liquidation approvals',
            'admin.cash-advances.liquidations.review' => 'Reviewed liquidation',
            'accounting.cash-advances.liquidations.review' => 'Reviewed liquidation',
            'admin.cash-advances.summary' => 'Opened CA summary report',
            'accounting.cash-advances.summary' => 'Opened CA summary report',
            'admin.cash-advances.summary.import' => 'Imported CA summary ledger',
            'accounting.cash-advances.summary.import' => 'Imported CA summary ledger',
            'admin.cash-advances.liquidations.store' => 'Created liquidation record',
            'accounting.cash-advances.liquidations.store' => 'Created liquidation record',
            'admin.cash-advances.liquidations.update' => 'Updated liquidation record',
            'accounting.cash-advances.liquidations.update' => 'Updated liquidation record',
            'admin.cash-advances.items.update' => 'Updated cash advance item remarks',
            'accounting.cash-advances.items.update' => 'Updated cash advance item remarks',
            'admin.cash-advances.payments' => 'Opened CA payments page',
            'accounting.cash-advances.payments' => 'Opened CA payments page',
            'admin.cash-advances.payments.update' => 'Uploaded cash advance payment proof',
            'accounting.cash-advances.payments.update' => 'Uploaded cash advance payment proof',
            'admin.reimbursable-vouchers.index' => 'Opened reimbursable vouchers',
            'accounting.reimbursable-vouchers.index' => 'Opened reimbursable vouchers',
            'admin.reimbursable-vouchers.create' => 'Opened create reimbursable voucher page',
            'accounting.reimbursable-vouchers.create' => 'Opened create reimbursable voucher page',
            'admin.reimbursable-vouchers.store' => 'Created reimbursable voucher',
            'accounting.reimbursable-vouchers.store' => 'Created reimbursable voucher',
            'admin.reimbursable-vouchers.show' => 'Viewed reimbursable voucher',
            'accounting.reimbursable-vouchers.show' => 'Viewed reimbursable voucher',
            'admin.cost-sheets.index' => 'Opened cost sheets',
            'accounting.cost-sheets.index' => 'Opened cost sheets',
            'admin.cost-sheets.create' => 'Opened cost sheet',
            'accounting.cost-sheets.create' => 'Opened cost sheet',
            'admin.cost-sheets.service-invoice-summary' => 'Opened service invoice summary',
            'accounting.cost-sheets.service-invoice-summary' => 'Opened service invoice summary',
            'admin.cost-sheets.sales-report' => 'Opened JO total sales report',
            'accounting.cost-sheets.sales-report' => 'Opened JO total sales report',
            'admin.record-monitoring.index' => 'Opened record monitoring',
            'accounting.record-monitoring.index' => 'Opened record monitoring',
            'admin.record-monitoring.create' => 'Opened create record monitoring entry page',
            'accounting.record-monitoring.create' => 'Opened create record monitoring entry page',
            'admin.record-monitoring.store' => 'Created record monitoring entry',
            'accounting.record-monitoring.store' => 'Created record monitoring entry',
            'admin.record-monitoring.edit' => 'Opened edit record monitoring entry page',
            'accounting.record-monitoring.edit' => 'Opened edit record monitoring entry page',
            'admin.record-monitoring.update' => 'Updated record monitoring entry',
            'accounting.record-monitoring.update' => 'Updated record monitoring entry',
            'admin.record-monitoring.import' => 'Imported record monitoring workbook',
            'accounting.record-monitoring.import' => 'Imported record monitoring workbook',
            'admin.support-tickets.index' => 'Opened support tickets',
            'admin.support-tickets.show' => 'Viewed support ticket',
            'admin.support-tickets.update' => 'Updated support ticket status',
            'admin.profile-corrections.index' => 'Opened profile corrections',
            'admin.profile-corrections.update' => 'Reviewed profile correction request',
            'admin.payslips.index' => 'Opened payslips admin page',
            'admin.payslips.user' => 'Viewed employee payslips',
            'admin.payslips.create' => 'Opened create payslip page',
            'admin.payslips.store' => 'Created payslip',
            'admin.payslips.edit' => 'Opened edit payslip page',
            'admin.payslips.update' => 'Updated payslip',
            'admin.access-control.index' => 'Opened access control',
            'admin.access-control.edit' => 'Opened user access settings',
            'admin.access-control.update' => 'Updated user access permissions',
            'admin.reports' => 'Opened Reports page',
            'admin.activity-logs.index' => 'Opened User Activity Logs',
            'admin.audit-logs.index' => 'Opened Audit Logs',
            'notifications.read' => 'Opened a notification',
            'notifications.readAll' => 'Marked all notifications as read',
            'logout' => 'Logged out',
            default => $this->fallbackDescription($routeName, $method),
        };
    }

    private function fallbackDescription(string $routeName, string $method): string
    {
        if ($routeName === '') {
            return $method === 'GET' ? 'Opened a page' : 'Performed an action';
        }

        $label = str_replace(['.', '-'], ' ', $routeName);
        $label = preg_replace('/\s+/', ' ', (string) $label);
        $label = ucwords(trim((string) $label));

        if ($method === 'GET') {
            return 'Opened ' . $label;
        }

        return $method . ' ' . $label;
    }

    private function resolveJoNumber(array $payload): string
    {
        $number = $payload['number']
            ?? $payload['jo_number']
            ?? $payload['items'][0]['jo_number']
            ?? null;

        return $this->normalizeLabel($number, 'Unknown JO');
    }

    private function resolveUserLabel(array $payload): string
    {
        $name = $payload['name'] ?? null;
        $employeeId = $payload['employee_id'] ?? null;

        if ($name && $employeeId) {
            return trim($name . ' (' . $employeeId . ')');
        }

        return $this->normalizeLabel($name ?: $employeeId, 'Unknown user');
    }

    private function resolveSimpleLabel(array $payload, array $keys): string
    {
        foreach ($keys as $key) {
            if (!empty($payload[$key])) {
                return $this->normalizeLabel($payload[$key], 'record');
            }
        }

        return 'record';
    }

    private function normalizeLabel(mixed $value, string $fallback): string
    {
        $string = trim((string) $value);

        return $string !== '' ? $string : $fallback;
    }
}
