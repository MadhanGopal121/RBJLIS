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
            ['email' => 'frontoffice@rbjlis.com'],
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
            ['email' => 'reception@rbjlis.com'],
            [
                'name' => 'Reception Staff',
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

        // 5. Demo Doctor
        DB::table('doctors')->updateOrInsert(
            ['id' => 1],
            [
                'lab_id' => 1,
                'doctor_name' => 'Sharma, MD',
                'clinic_name' => 'City Heart & Diagnostic Clinic',
                'email' => 'drsharma@example.com',
                'phone' => '9822334455',
                'discount' => 10.00,
                'status' => 1,
                'created_by' => 1,
                'created_on' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 6. Demo Diagnostic Tests
        DB::table('diagnosticstests')->updateOrInsert(
            ['id' => 1],
            [
                'lab_id' => 1,
                'department_id' => 1,
                'name' => 'Complete Blood Count (CBC)',
                'test_code' => 'CBC',
                'sample_type' => 'EDTA Whole Blood',
                'method' => 'Automated Cell Counter',
                'price' => 350.00,
                'lab_price' => 200.00,
                'status' => 1,
                'created_by' => 1,
                'created_on' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('diagnosticstests')->updateOrInsert(
            ['id' => 2],
            [
                'lab_id' => 1,
                'department_id' => 2,
                'name' => 'Lipid Profile',
                'test_code' => 'LIPID',
                'sample_type' => 'Plain Serum (Fasting)',
                'method' => 'Enzymatic Colorimetric',
                'price' => 650.00,
                'lab_price' => 400.00,
                'status' => 1,
                'created_by' => 1,
                'created_on' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 7. Demo Parameters for CBC
        DB::table('parameters')->updateOrInsert(
            ['id' => 1],
            [
                'diagnosticstests_id' => 1,
                'name' => 'Hemoglobin (Hb)',
                'units' => 'g/dL',
                'default_value' => '13.0 - 17.0',
                'type' => 'value',
                'sort' => 1,
                'status' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('parameters')->updateOrInsert(
            ['id' => 2],
            [
                'diagnosticstests_id' => 1,
                'name' => 'Total Leucocyte Count (WBC)',
                'units' => '/cumm',
                'default_value' => '4000 - 11000',
                'type' => 'value',
                'sort' => 2,
                'status' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('parameters')->updateOrInsert(
            ['id' => 3],
            [
                'diagnosticstests_id' => 1,
                'name' => 'Platelet Count',
                'units' => '/cumm',
                'default_value' => '150000 - 450000',
                'type' => 'value',
                'sort' => 3,
                'status' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 8. Demo Parameters for Lipid Profile
        DB::table('parameters')->updateOrInsert(
            ['id' => 4],
            [
                'diagnosticstests_id' => 2,
                'name' => 'Total Cholesterol',
                'units' => 'mg/dL',
                'default_value' => '< 200',
                'type' => 'value',
                'sort' => 1,
                'status' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('parameters')->updateOrInsert(
            ['id' => 5],
            [
                'diagnosticstests_id' => 2,
                'name' => 'Triglycerides',
                'units' => 'mg/dL',
                'default_value' => '< 150',
                'type' => 'value',
                'sort' => 2,
                'status' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('parameters')->updateOrInsert(
            ['id' => 6],
            [
                'diagnosticstests_id' => 2,
                'name' => 'HDL (Good) Cholesterol',
                'units' => 'mg/dL',
                'default_value' => '> 40',
                'type' => 'value',
                'sort' => 3,
                'status' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 9. Demo Patient
        DB::table('patients')->updateOrInsert(
            ['id' => 1],
            [
                'lab_id' => 1,
                'unique_id' => 'RBJ00001',
                'title' => 'Mr',
                'name' => 'Rajesh Kumar',
                'age' => '45',
                'gender' => 'Male',
                'phone' => '9876543210',
                'email' => 'rajesh.kumar@example.com',
                'address' => '12, MG Road, Health City',
                'created_by' => 1,
                'created_on' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 10. Demo Investigation Order
        DB::table('investigations')->updateOrInsert(
            ['id' => 1],
            [
                'lab_id' => 1,
                'patient_id' => 1,
                'doctor_id' => 1,
                'total_amount' => 1000.00,
                'discount' => 100.00,
                'balance_amount' => 0.00,
                'notes' => 'Fasting sample. Routine health checkup.',
                'status' => 1,
                'created_by' => 1,
                'created_on' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Investigation Test 1 (CBC)
        DB::table('investigation_test')->updateOrInsert(
            ['id' => 1],
            [
                'investigation_id' => 1,
                'test_id' => 1,
                'specimen_by' => 0,
                'test_by' => 0,
                'authenticated_by' => 0,
                'approved_by' => 0,
                'is_emergency' => 0,
                'is_declined' => 0,
                'created_on' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Investigation Test 2 (Lipid Profile)
        DB::table('investigation_test')->updateOrInsert(
            ['id' => 2],
            [
                'investigation_id' => 1,
                'test_id' => 2,
                'specimen_by' => 0,
                'test_by' => 0,
                'authenticated_by' => 0,
                'approved_by' => 0,
                'is_emergency' => 0,
                'is_declined' => 0,
                'created_on' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
