<?php

namespace Database\Seeders;

use App\Models\Allowance;
use App\Models\BankAccount;
use App\Models\EmergencyContact;
use App\Models\EmployeeProfile;
use App\Models\EmploymentDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@employeehub.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'employee_id' => 'EMP-0001',
            'status' => 'Active',
            'profile_photo' => 'images/profile-default.svg',
        ]);

        $employee = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@employeehub.test',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'employee_id' => 'EMP-1001',
            'status' => 'Active',
            'profile_photo' => 'images/profile-default.svg',
        ]);

        EmployeeProfile::create([
            'user_id' => $employee->id,
            'contact_number' => '+63 912 345 6789',
            'birthdate' => '1995-04-15',
            'gender' => 'Male',
            'address' => 'Makati City, Philippines',
            'civil_status' => 'Single',
            'tax_ident_no' => '123-456-789',
        ]);

        EmploymentDetail::create([
            'user_id' => $employee->id,
            'position' => 'Software Engineer',
            'employment_type' => 'Full-time',
            'department' => 'Engineering',
            'date_joined' => '2022-06-15',
            'supervisor_name' => 'Jane Smith',
            'shift_start' => '09:00',
            'shift_end' => '18:00',
        ]);

        EmergencyContact::create([
            'user_id' => $employee->id,
            'name' => 'Mary Doe',
            'relationship' => 'Mother',
            'contact_number' => '+63 917 555 1234',
            'address' => 'Quezon City, Philippines',
        ]);

        BankAccount::create([
            'user_id' => $employee->id,
            'bank_name' => 'BDO',
            'branch' => 'Makati',
            'account_name' => 'John Doe',
            'account_number' => '1234567890',
        ]);

        Allowance::create([
            'user_id' => $employee->id,
            'type' => 'Transport',
            'amount' => 2500,
            'currency' => 'PHP',
        ]);

        Allowance::create([
            'user_id' => $employee->id,
            'type' => 'Meal',
            'amount' => 1800,
            'currency' => 'PHP',
        ]);
    }
}
