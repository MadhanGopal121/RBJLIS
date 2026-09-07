<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RbjlisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Roles
        $roles = [
            ['id' => 1, 'name' => 'Super Admin', 'created_by' => 1, 'created_on' => now()],
            ['id' => 2, 'name' => 'Lab Admin', 'created_by' => 1, 'created_on' => now()],
            ['id' => 3, 'name' => 'Front Office', 'created_by' => 1, 'created_on' => now()],
            ['id' => 4, 'name' => 'Technician', 'created_by' => 1, 'created_on' => now()],
            ['id' => 5, 'name' => 'Pathologist', 'created_by' => 1, 'created_on' => now()],
            ['id' => 6, 'name' => 'Approver', 'created_by' => 1, 'created_on' => now()],
            ['id' => 7, 'name' => 'Sample Collector', 'created_by' => 1, 'created_on' => now()],
            ['id' => 8, 'name' => 'Lab to Lab', 'created_by' => 1, 'created_on' => now()],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(['id' => $role['id']], $role);
        }

        // 2. Default Departments
        $departments = [
            ['id' => 1, 'name' => 'Hematology', 'is_default' => 1, 'price' => 0, 'created_on' => now(), 'status' => 1],
            ['id' => 2, 'name' => 'Biochemistry', 'is_default' => 1, 'price' => 0, 'created_on' => now(), 'status' => 1],
            ['id' => 3, 'name' => 'Microbiology', 'is_default' => 1, 'price' => 0, 'created_on' => now(), 'status' => 1],
            ['id' => 4, 'name' => 'Clinical Pathology', 'is_default' => 1, 'price' => 0, 'created_on' => now(), 'status' => 1],
            ['id' => 5, 'name' => 'Serology', 'is_default' => 1, 'price' => 0, 'created_on' => now(), 'status' => 1],
            ['id' => 6, 'name' => 'Immunology', 'is_default' => 1, 'price' => 0, 'created_on' => now(), 'status' => 1],
            ['id' => 7, 'name' => 'Histopathology', 'is_default' => 1, 'price' => 0, 'created_on' => now(), 'status' => 1],
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->updateOrInsert(['id' => $dept['id']], $dept);
        }

        // 3. Default Lab
        DB::table('labs')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'RBJ Diagnostics & Research Center',
                'contactname' => 'Dr. Admin',
                'shortname' => 'RBJ',
                'email' => 'contact@rbjdiagnostics.com',
                'phone' => '9876543210',
                'address' => 'Main Road, Health City',
                'user_count' => 10,
                'sub_from' => now()->toDateString(),
                'sub_to' => now()->addYear()->toDateString(),
                'defult_notes' => 'Please bring previous reports if available.',
                'created_by' => 1,
                'created_on' => now(),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Lab Departments for Lab 1
        for ($i = 1; $i <= 7; $i++) {
            DB::table('lab_departments')->updateOrInsert(
                ['lab_id' => 1, 'department_id' => $i],
                ['status' => 1, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // 4. Default Users
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@rbjlis.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('admin123'),
                'lab_id' => 0,
                'role_id' => 1,
                'phone' => '9999999999',
                'status' => 1,
                'created_on' => now(),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'labadmin@rbjlis.com'],
            [
                'name' => 'Lab Administrator',
                'password' => Hash::make('admin123'),
                'lab_id' => 1,
                'role_id' => 2,
                'phone' => '9888888888',
                'status' => 1,
                'created_on' => now(),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'reception@rbjlis.com'],
            [
                'name' => 'Front Office Staff',
                'password' => Hash::make('admin123'),
                'lab_id' => 1,
                'role_id' => 3,
                'phone' => '9777777777',
                'status' => 1,
                'created_on' => now(),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'technician@rbjlis.com'],
            [
                'name' => 'Lab Technician',
                'password' => Hash::make('admin123'),
                'lab_id' => 1,
                'role_id' => 4,
                'phone' => '9666666666',
                'status' => 1,
                'created_on' => now(),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
