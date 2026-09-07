<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->unsignedBigInteger('created_by')->default(1);
            $table->dateTime('created_on')->useCurrent();
            $table->timestamps();
        });

        // 2. Labs
        Schema::create('labs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('contactname', 50)->nullable();
            $table->string('shortname', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('logo', 255)->nullable();
            $table->string('letter_head', 255)->nullable();
            $table->string('lab_seal', 255)->nullable();
            $table->text('address')->nullable();
            $table->integer('user_count')->default(1);
            $table->date('sub_from')->nullable();
            $table->date('sub_to')->nullable();
            $table->text('defult_notes')->nullable();
            $table->unsignedBigInteger('created_by')->default(1);
            $table->dateTime('created_on')->useCurrent();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 3. Departments
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->boolean('is_default')->default(false);
            $table->decimal('price', 10, 2)->default(0);
            $table->dateTime('created_on')->useCurrent();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 4. Update Users table or add custom columns
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'lab_id')) {
                    $table->unsignedBigInteger('lab_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('users', 'role_id')) {
                    $table->unsignedBigInteger('role_id')->default(2)->after('lab_id');
                }
                if (!Schema::hasColumn('users', 'phone')) {
                    $table->string('phone', 30)->nullable()->after('email');
                }
                if (!Schema::hasColumn('users', 'signature')) {
                    $table->string('signature', 255)->nullable();
                }
                if (!Schema::hasColumn('users', 'designation')) {
                    $table->string('designation', 100)->nullable();
                }
                if (!Schema::hasColumn('users', 'permissions')) {
                    $table->string('permissions', 255)->nullable();
                }
                if (!Schema::hasColumn('users', 'last_login')) {
                    $table->dateTime('last_login')->nullable();
                }
                if (!Schema::hasColumn('users', 'created_on')) {
                    $table->dateTime('created_on')->useCurrent();
                }
                if (!Schema::hasColumn('users', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->default(1);
                }
                if (!Schema::hasColumn('users', 'status')) {
                    $table->boolean('status')->default(true);
                }
            });
        }

        // 5. User Departments
        Schema::create('user_departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('department_id');
            $table->timestamps();
        });

        // 6. Lab Departments
        Schema::create('lab_departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_id');
            $table->unsignedBigInteger('department_id');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 7. Doctors
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_id')->default(0);
            $table->string('doctor_name', 200);
            $table->string('clinic_name', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->decimal('discount', 10, 2)->default(0);
            $table->unsignedBigInteger('created_by')->default(1);
            $table->dateTime('created_on')->useCurrent();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 8. Lab to Lab
        Schema::create('labtolab', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_id');
            $table->string('lab_name', 200);
            $table->string('contact_name', 200)->nullable();
            $table->string('contact_email', 255)->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('created_by')->default(1);
            $table->dateTime('created_on')->useCurrent();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 9. Master Tests
        Schema::create('mastertests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('name', 100);
            $table->string('reference_val', 250)->nullable();
            $table->string('units', 100)->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('method', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('specimen_time', 50)->nullable();
            $table->unsignedBigInteger('created_by')->default(1);
            $table->dateTime('created_on')->useCurrent();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 10. Diagnostics Tests
        Schema::create('diagnosticstests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_id')->default(0);
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('m_testid')->nullable();
            $table->unsignedBigInteger('test_id')->nullable();
            $table->string('test_code', 20)->nullable();
            $table->string('name', 100);
            $table->string('reference_val', 250)->nullable();
            $table->string('units', 100)->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('lab_price', 10, 2)->default(0);
            $table->string('method', 100)->nullable();
            $table->text('notes')->nullable();
            $table->text('note')->nullable();
            $table->text('default_notes')->nullable();
            $table->text('default_note')->nullable();
            $table->text('interpretation')->nullable();
            $table->string('interpretation_img', 255)->nullable();
            $table->string('sample_type', 100)->nullable();
            $table->string('specimen_time', 50)->nullable();
            $table->unsignedBigInteger('created_by')->default(1);
            $table->dateTime('created_on')->useCurrent();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 11. Parameters
        Schema::create('parameters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diagnosticstests_id');
            $table->string('type', 20)->nullable();
            $table->string('name', 100);
            $table->integer('sort')->default(0);
            $table->text('default_value')->nullable();
            $table->string('units', 50)->nullable();
            $table->string('method', 255)->nullable();
            $table->unsignedBigInteger('created_by')->default(1);
            $table->dateTime('created_on')->useCurrent();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 12. Profiles
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_id')->default(0);
            $table->string('name', 150);
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('lab_price', 10, 2)->default(0);
            $table->unsignedBigInteger('created_by')->default(1);
            $table->dateTime('created_on')->useCurrent();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 13. Profile Tests
        Schema::create('profile_tests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('diagnosticstest_id');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 14. Patients
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_id')->default(0);
            $table->string('unique_id', 50)->nullable();
            $table->string('title', 10)->default('Mr');
            $table->string('name', 200);
            $table->string('age', 20)->nullable();
            $table->string('gender', 30)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('aadhar', 50)->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('created_by')->default(1);
            $table->dateTime('created_on')->useCurrent();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('updated_on')->nullable();
            $table->timestamps();
        });

        // 15. Investigations
        Schema::create('investigations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_id')->default(0);
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->unsignedBigInteger('ltol_id')->nullable();
            $table->string('refered_by', 100)->nullable();
            $table->string('clinicname', 100)->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('prescription', 255)->nullable();
            $table->boolean('status')->default(true);
            $table->integer('test_status')->default(1);
            $table->text('delete_notes')->nullable();
            $table->unsignedBigInteger('created_by')->default(1);
            $table->dateTime('created_on')->useCurrent();
            $table->timestamps();
        });

        // 16. Investigation Test
        Schema::create('investigation_test', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investigation_id');
            $table->unsignedBigInteger('test_id');
            $table->unsignedBigInteger('package_id')->nullable();
            $table->boolean('is_emergency')->default(false);
            $table->dateTime('specimen_time')->nullable();
            $table->unsignedBigInteger('specimen_by')->nullable();
            $table->dateTime('test_time')->nullable();
            $table->unsignedBigInteger('test_by')->nullable();
            $table->string('result', 200)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('authenticated_by')->nullable();
            $table->dateTime('authenticated_time')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_time')->nullable();
            $table->boolean('is_reportedgenerated')->default(false);
            $table->unsignedBigInteger('print_by')->nullable();
            $table->dateTime('print_time')->nullable();
            $table->boolean('is_declined')->default(false);
            $table->string('declined_media', 200)->nullable();
            $table->text('declined_reason')->nullable();
            $table->unsignedBigInteger('declined_by')->nullable();
            $table->integer('status')->default(1);
            $table->unsignedBigInteger('created_by')->default(1);
            $table->dateTime('created_on')->useCurrent();
            $table->timestamps();
        });

        // 17. Investigation Test Results
        Schema::create('investigation_test_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investigation_id');
            $table->unsignedBigInteger('investigation_test_id');
            $table->unsignedBigInteger('parameter_id')->nullable();
            $table->string('result', 255)->nullable();
            $table->boolean('is_bold')->default(false);
            $table->integer('iscritical')->default(0);
            $table->integer('sort')->default(0);
            $table->string('type', 20)->nullable();
            $table->unsignedBigInteger('created_by')->default(1);
            $table->dateTime('created_on')->useCurrent();
            $table->timestamps();
        });

        // 18. Investigation Decline
        Schema::create('investigation_decline', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investigation_id');
            $table->unsignedBigInteger('investigation_test_id')->nullable();
            $table->unsignedBigInteger('lab_id')->default(0);
            $table->text('reason')->nullable();
            $table->string('filepath', 255)->nullable();
            $table->integer('declined_role')->default(0);
            $table->unsignedBigInteger('created_by')->default(1);
            $table->dateTime('created_on')->useCurrent();
            $table->timestamps();
        });

        // 19. Lab Payments
        Schema::create('lab_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_id')->default(0);
            $table->unsignedBigInteger('investigation_id');
            $table->unsignedBigInteger('labtolab_id')->nullable();
            $table->string('paymenttype', 50)->default('Cash');
            $table->decimal('payment_amount', 10, 2)->default(0);
            $table->string('trans_number', 100)->nullable();
            $table->dateTime('received_date')->useCurrent();
            $table->unsignedBigInteger('received_by')->default(1);
            $table->timestamps();
        });

        // 20. Lab Special Prices
        Schema::create('lab_specialprices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_id');
            $table->unsignedBigInteger('child_lab_id')->default(0);
            $table->unsignedBigInteger('diagnosticestest_id')->nullable();
            $table->unsignedBigInteger('mastertest_id')->nullable();
            $table->decimal('sp_price', 10, 2)->default(0);
            $table->unsignedBigInteger('created_by')->default(1);
            $table->dateTime('created_on')->useCurrent();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 21. IT Para Values
        Schema::create('it_para_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parameter_id');
            $table->unsignedBigInteger('investigationtest_id');
            $table->string('value', 255)->nullable();
            $table->unsignedBigInteger('created_by')->default(1);
            $table->dateTime('created_on')->useCurrent();
            $table->timestamps();
        });

        // 22. Report Last Day
        Schema::create('reportlastday', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id')->nullable();
            $table->string('unique_id', 50)->nullable();
            $table->string('name', 200)->nullable();
            $table->integer('age')->nullable();
            $table->string('gender', 30)->nullable();
            $table->string('phone', 50)->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('payment_amount', 10, 2)->default(0);
            $table->string('paymenttype', 50)->nullable();
            $table->mediumText('testnames')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportlastday');
        Schema::dropIfExists('it_para_values');
        Schema::dropIfExists('lab_specialprices');
        Schema::dropIfExists('lab_payments');
        Schema::dropIfExists('investigation_decline');
        Schema::dropIfExists('investigation_test_results');
        Schema::dropIfExists('investigation_test');
        Schema::dropIfExists('investigations');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('profile_tests');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('parameters');
        Schema::dropIfExists('diagnosticstests');
        Schema::dropIfExists('mastertests');
        Schema::dropIfExists('labtolab');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('lab_departments');
        Schema::dropIfExists('user_departments');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('labs');
        Schema::dropIfExists('roles');
    }
};
