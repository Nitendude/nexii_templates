<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'employee_id',
        'status',
        'profile_photo',
        'must_change_password',
        'profile_completed',
        'created_by_admin',
        'access_permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'must_change_password' => 'boolean',
        'profile_completed' => 'boolean',
        'created_by_admin' => 'boolean',
        'access_permissions' => 'array',
    ];

    public function profile()
    {
        return $this->hasOne(EmployeeProfile::class);
    }

    public function employmentDetail()
    {
        return $this->hasOne(EmploymentDetail::class);
    }

    public function emergencyContact()
    {
        return $this->hasOne(EmergencyContact::class);
    }

    public function bankAccount()
    {
        return $this->hasOne(BankAccount::class);
    }

    public function allowances()
    {
        return $this->hasMany(Allowance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payslips()
    {
        return $this->hasMany(Payslip::class);
    }

    public function cashAdvanceRequests()
    {
        return $this->hasMany(CashAdvanceRequest::class);
    }

    public function department(): ?string
    {
        return $this->employmentDetail?->department;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin' && $this->department() === 'IT';
    }

    public function defaultAccessPermissions(): array
    {
        $permissions = [
            'dashboard',
            'my-profile',
            'profile-corrections',
            'payslips',
            'leave-form',
            'documents',
            'cash-advances',
        ];

        if ($this->department() === 'Documentation') {
            $permissions[] = 'job-orders';
        }

        return $permissions;
    }

    public function adminAccessPermissions(): array
    {
        return [
            'admin-employees',
            'admin-payslips',
            'admin-leave-approvals',
            'admin-profile-corrections',
            'admin-support-tickets',
            'admin-reports',
        ];
    }

    public function accountingAccessPermissions(): array
    {
        return [
            'admin-cash-advance-approvals',
            'admin-liquidation-approvals',
            'admin-ca-summary',
            'admin-ca-payments',
            'admin-container-deposits',
            'admin-cost-sheets',
            'admin-cost-sheet-sales-report',
            'admin-record-monitoring',
        ];
    }

    public function hasAnyAdminAccess(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $permissions = is_array($this->access_permissions)
            ? $this->access_permissions
            : ($this->role === 'admin' ? $this->adminAccessPermissions() : []);

        return !empty(array_intersect($permissions, $this->adminAccessPermissions()));
    }

    public function hasAnyAccountingAccess(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $permissions = is_array($this->access_permissions)
            ? $this->access_permissions
            : ($this->role === 'admin' ? $this->accountingAccessPermissions() : []);

        return !empty(array_intersect($permissions, $this->accountingAccessPermissions()));
    }

    public function hasAccess(string $key): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (is_array($this->access_permissions)) {
            return in_array($key, $this->access_permissions, true);
        }

        if ($this->role === 'admin') {
            return true;
        }

        return in_array($key, $this->defaultAccessPermissions(), true);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
