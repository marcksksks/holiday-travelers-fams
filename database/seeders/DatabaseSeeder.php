<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\RetentionPolicy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // One account per role so every dashboard/permission path can be tested immediately.
        $accounts = [
            ['full_name' => 'System Administrator', 'email' => 'sysadmin@fams.local', 'app_role' => User::ROLE_SYS_ADMIN],
            ['full_name' => 'General Manager', 'email' => 'manager@fams.local', 'app_role' => User::ROLE_MANAGER],
            ['full_name' => 'Admin Officer', 'email' => 'admin.officer@fams.local', 'app_role' => User::ROLE_ADMIN_OFFICER],
            ['full_name' => 'Legal Officer', 'email' => 'legal@fams.local', 'app_role' => User::ROLE_LEGAL_OFFICER],
            ['full_name' => 'Front Desk Receptionist', 'email' => 'reception@fams.local', 'app_role' => User::ROLE_RECEPTIONIST],
            ['full_name' => 'Staff Employee', 'email' => 'employee@fams.local', 'app_role' => User::ROLE_EMPLOYEE],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                $account + ['password' => Hash::make('password'), 'is_active' => true, 'force_password_change' => false]
            );
        }

        $facilities = [
            ['name' => 'Boardroom A', 'facility_type' => 'conference_room', 'capacity' => 12, 'location' => '3rd Floor'],
            ['name' => 'Training Hall', 'facility_type' => 'training_room', 'capacity' => 40, 'location' => '2nd Floor'],
            ['name' => 'Meeting Room 1', 'facility_type' => 'meeting_room', 'capacity' => 6, 'location' => '1st Floor'],
            ['name' => 'Function Hall', 'facility_type' => 'function_room', 'capacity' => 100, 'location' => 'Ground Floor'],
        ];
        foreach ($facilities as $facility) {
            Facility::updateOrCreate(['name' => $facility['name']], $facility + ['status' => 'available']);
        }

        $policies = [
            ['name' => 'Administrative records — 5 years', 'record_category' => 'administrative', 'retention_years' => 5, 'legal_basis' => 'Internal records management policy'],
            ['name' => 'Contracts — 10 years post-expiry', 'record_category' => 'contract', 'retention_years' => 10, 'legal_basis' => 'Statute of limitations for written contracts'],
            ['name' => 'Legal/compliance — 7 years', 'record_category' => 'legal', 'retention_years' => 7, 'legal_basis' => 'Regulatory compliance requirement'],
            ['name' => 'Financial records — 10 years', 'record_category' => 'financial', 'retention_years' => 10, 'legal_basis' => 'Tax and audit requirements'],
        ];
        foreach ($policies as $policy) {
            RetentionPolicy::updateOrCreate(['name' => $policy['name']], $policy + ['is_active' => true]);
        }
    }
}
