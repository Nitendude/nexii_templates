<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ProfileUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class EmployeeController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $isWeekend = now()->isWeekend();
        $search = trim((string) request('q', ''));
        $employeesQuery = User::query()
            ->with('employmentDetail')
            ->withExists(['leaveRequests as on_leave_today' => function ($query) use ($today) {
                $query->where('status', 'Approved')
                    ->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today);
            }])
            ->orderBy('name');

        if ($search !== '') {
            $employeesQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('employmentDetail', function ($employmentQuery) use ($search) {
                        $employmentQuery->where('department', 'like', "%{$search}%");
                    });
            });
        }

        $status = request('status');
        if ($status === 'On Leave') {
            if ($isWeekend) {
                $employeesQuery->whereRaw('1 = 0');
            } else {
                $employeesQuery->whereHas('leaveRequests', function ($query) use ($today) {
                    $query->where('status', 'Approved')
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today);
                });
            }
        } elseif ($status) {
            $employeesQuery->where('status', $status);
        }

        $employees = $employeesQuery->paginate(10)->withQueryString();

        return view('admin.employees.index', [
            'employees' => $employees,
            'status' => $status,
            'search' => $search,
        ]);
    }

    public function show(User $user)
    {
        Gate::authorize('view-employee', $user);

        $user->load([
            'profile',
            'employmentDetail',
            'emergencyContact',
            'allowances',
        ]);

        $today = now()->toDateString();
        $latestPayslip = $user->payslips()->latest()->first();
        $contributionPayslips = $user->payslips()->latest()->take(5)->get();
        $hasLeaveToday = !now()->isWeekend() && $user->leaveRequests()
            ->where('status', 'Approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->exists();

        return view('admin.employees.show', [
            'employee' => $user,
            'latestPayslip' => $latestPayslip,
            'contributionPayslips' => $contributionPayslips,
            'hasLeaveToday' => $hasLeaveToday,
        ]);
    }

    public function edit(User $user)
    {
        Gate::authorize('edit-employee', $user);

        $user->load([
            'profile',
            'employmentDetail',
            'emergencyContact',
            'allowances',
        ]);

        return view('admin.employees.edit', [
            'employee' => $user,
        ]);
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:5'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'employee_id' => ['nullable', 'string', 'max:50', 'unique:users,employee_id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'status' => ['required', 'in:Active,Inactive,On Leave,Terminated'],
        ]);

        $nameParts = [
            trim($validated['last_name']),
            trim($validated['first_name']),
        ];
        if (!empty($validated['middle_initial'])) {
            $nameParts[] = strtoupper(trim($validated['middle_initial']));
        }
        $fullName = implode(', ', array_slice($nameParts, 0, 2));
        if (count($nameParts) > 2) {
            $fullName .= ' ' . $nameParts[2];
        }

        $employeeId = $validated['employee_id'] ?? $this->generateEmployeeId();

        $tempPassword = $validated['password'] ?? '123456789';

        $user = User::create([
            'name' => $fullName,
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
            'role' => 'employee',
            'employee_id' => $employeeId,
            'status' => $validated['status'],
            'profile_photo' => 'images/profile-default.svg',
            'must_change_password' => true,
            'profile_completed' => false,
            'created_by_admin' => true,
        ]);

        return redirect()
            ->route('admin.employees.edit', $user)
            ->with('status', 'employee-created');
    }

    private function generateEmployeeId(): string
    {
        do {
            $employeeId = 'APM-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (User::where('employee_id', $employeeId)->exists());

        return $employeeId;
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('edit-employee', $user);

        $user->load(['profile', 'employmentDetail', 'emergencyContact']);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:Active,Inactive,On Leave,Terminated'],
            'profile' => ['nullable', 'array'],
            'profile.contact_number' => ['nullable', 'string', 'max:50'],
            'profile.birthdate' => ['nullable', 'date'],
            'profile.gender' => ['nullable', 'string', 'max:50'],
            'profile.address' => ['nullable', 'string', 'max:255'],
            'profile.civil_status' => ['nullable', 'string', 'max:50'],
            'profile.tax_ident_no' => ['nullable', 'string', 'max:50'],
            'employment' => ['nullable', 'array'],
            'employment.position' => ['nullable', 'string', 'max:255'],
            'employment.employment_type' => ['nullable', 'in:Full-time,Part-time,Contract'],
            'employment.department' => ['nullable', 'string', 'max:255'],
            'employment.date_joined' => ['nullable', 'date'],
            'emergency' => ['nullable', 'array'],
            'emergency.name' => ['nullable', 'string', 'max:255'],
            'emergency.relationship' => ['nullable', 'string', 'max:50'],
            'emergency.contact_number' => ['nullable', 'string', 'max:50'],
            'emergency.address' => ['nullable', 'string', 'max:255'],
            'allowances' => ['nullable', 'array'],
            'allowances.type' => ['nullable', 'array'],
            'allowances.amount' => ['nullable', 'array'],
            'allowances.currency' => ['nullable', 'array'],
            'allowances.type.*' => ['nullable', 'string', 'max:100'],
            'allowances.amount.*' => ['nullable', 'numeric', 'min:0'],
            'allowances.currency.*' => ['nullable', 'string', 'size:3'],
        ]);

        $changes = [];
        $userMap = [
            'name' => 'Name',
            'status' => 'Status',
        ];
        foreach ($userMap as $key => $label) {
            $current = (string) ($user->$key ?? '');
            $incoming = (string) ($validated[$key] ?? '');
            if ($current !== $incoming) {
                $changes[] = $label;
            }
        }

        $profileMap = [
            'contact_number' => 'Contact Number',
            'birthdate' => 'Birthdate',
            'gender' => 'Gender',
            'address' => 'Address',
            'civil_status' => 'Civil Status',
            'tax_ident_no' => 'Tax ID No.',
        ];
        foreach ($profileMap as $key => $label) {
            if (array_key_exists($key, $validated['profile'] ?? [])) {
                $current = (string) ($user->profile?->$key ?? '');
                $incoming = (string) ($validated['profile'][$key] ?? '');
                if ($current !== $incoming) {
                    $changes[] = $label;
                }
            }
        }

        $employmentMap = [
            'position' => 'Position',
            'employment_type' => 'Employment Type',
            'department' => 'Department',
            'date_joined' => 'Date Joined',
        ];
        foreach ($employmentMap as $key => $label) {
            if (array_key_exists($key, $validated['employment'] ?? [])) {
                $current = (string) ($user->employmentDetail?->$key ?? '');
                $incoming = (string) ($validated['employment'][$key] ?? '');
                if ($current !== $incoming) {
                    $changes[] = $label;
                }
            }
        }

        $emergencyMap = [
            'name' => 'Emergency Name',
            'relationship' => 'Emergency Relationship',
            'contact_number' => 'Emergency Contact Number',
            'address' => 'Emergency Address',
        ];
        foreach ($emergencyMap as $key => $label) {
            if (array_key_exists($key, $validated['emergency'] ?? [])) {
                $current = (string) ($user->emergencyContact?->$key ?? '');
                $incoming = (string) ($validated['emergency'][$key] ?? '');
                if ($current !== $incoming) {
                    $changes[] = $label;
                }
            }
        }


        if (!empty($validated['allowances'])) {
            $changes[] = 'Allowances';
        }

        $user->update([
            'name' => $validated['name'],
            'status' => $validated['status'],
        ]);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $validated['profile'] ?? []
        );

        $user->employmentDetail()->updateOrCreate(
            ['user_id' => $user->id],
            $validated['employment'] ?? []
        );

        $user->emergencyContact()->updateOrCreate(
            ['user_id' => $user->id],
            $validated['emergency'] ?? []
        );

        if (!empty($validated['allowances'])) {
            $user->allowances()->delete();

            $types = $validated['allowances']['type'] ?? [];
            $amounts = $validated['allowances']['amount'] ?? [];
            $currencies = $validated['allowances']['currency'] ?? [];

            foreach ($types as $index => $type) {
                if (!$type) {
                    continue;
                }

                $user->allowances()->create([
                    'type' => $type,
                    'amount' => $amounts[$index] ?? 0,
                    'currency' => $currencies[$index] ?? 'PHP',
                ]);
            }
        }

        $uniqueChanges = array_values(array_unique($changes));
        $user->notify(new ProfileUpdated($request->user()->name, $uniqueChanges));

        return redirect()
            ->route('admin.employees.edit', $user)
            ->with('status', 'employee-updated');
    }

    public function sendPasswordResetLink(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('edit-employee', $user);

        $status = Password::sendResetLink([
            'email' => $user->email,
        ]);

        return redirect()
            ->route('admin.employees.edit', $user)
            ->with(
                $status === Password::RESET_LINK_SENT ? 'status' : 'error',
                $status === Password::RESET_LINK_SENT ? 'password-reset-link-sent' : __($status)
            );
    }
}
