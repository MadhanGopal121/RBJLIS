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
        if (Schema::hasTable('labs')) {
            Schema::table('labs', function (Blueprint $table) {
                if (!Schema::hasColumn('labs', 'upi_id')) {
                    $table->string('upi_id', 100)->nullable()->after('address');
                }
                if (!Schema::hasColumn('labs', 'upi_name')) {
                    $table->string('upi_name', 100)->nullable()->after('upi_id');
                }
                if (!Schema::hasColumn('labs', 'enable_upi')) {
                    $table->boolean('enable_upi')->default(true)->after('upi_name');
                }
                if (!Schema::hasColumn('labs', 'stripe_key')) {
                    $table->string('stripe_key', 255)->nullable()->after('enable_upi');
                }
                if (!Schema::hasColumn('labs', 'stripe_secret')) {
                    $table->string('stripe_secret', 255)->nullable()->after('stripe_key');
                }
                if (!Schema::hasColumn('labs', 'stripe_webhook_secret')) {
                    $table->string('stripe_webhook_secret', 255)->nullable()->after('stripe_secret');
                }
                if (!Schema::hasColumn('labs', 'enable_stripe')) {
                    $table->boolean('enable_stripe')->default(false)->after('stripe_webhook_secret');
                }
                if (!Schema::hasColumn('labs', 'sms_provider')) {
                    $table->string('sms_provider', 50)->default('bulksmsgateway')->after('enable_stripe');
                }
                if (!Schema::hasColumn('labs', 'sms_api_key')) {
                    $table->string('sms_api_key', 255)->nullable()->after('sms_provider');
                }
                if (!Schema::hasColumn('labs', 'sms_sender_id')) {
                    $table->string('sms_sender_id', 50)->nullable()->after('sms_api_key');
                }
                if (!Schema::hasColumn('labs', 'enable_sms')) {
                    $table->boolean('enable_sms')->default(false)->after('sms_sender_id');
                }
                if (!Schema::hasColumn('labs', 'enable_email')) {
                    $table->boolean('enable_email')->default(true)->after('enable_sms');
                }
                if (!Schema::hasColumn('labs', 'smtp_host')) {
                    $table->string('smtp_host', 100)->nullable()->after('enable_email');
                }
                if (!Schema::hasColumn('labs', 'smtp_port')) {
                    $table->string('smtp_port', 10)->nullable()->after('smtp_host');
                }
                if (!Schema::hasColumn('labs', 'smtp_user')) {
                    $table->string('smtp_user', 100)->nullable()->after('smtp_port');
                }
                if (!Schema::hasColumn('labs', 'smtp_pass')) {
                    $table->string('smtp_pass', 255)->nullable()->after('smtp_user');
                }
                if (!Schema::hasColumn('labs', 'smtp_encryption')) {
                    $table->string('smtp_encryption', 10)->nullable()->after('smtp_pass');
                }
            });
        }

        if (Schema::hasTable('lab_payments')) {
            Schema::table('lab_payments', function (Blueprint $table) {
                if (!Schema::hasColumn('lab_payments', 'payment_gateway')) {
                    $table->string('payment_gateway', 50)->default('cash')->after('paymenttype');
                }
                if (!Schema::hasColumn('lab_payments', 'gateway_order_id')) {
                    $table->string('gateway_order_id', 255)->nullable()->after('trans_number');
                }
                if (!Schema::hasColumn('lab_payments', 'gateway_payment_id')) {
                    $table->string('gateway_payment_id', 255)->nullable()->after('gateway_order_id');
                }
                if (!Schema::hasColumn('lab_payments', 'gateway_status')) {
                    $table->string('gateway_status', 50)->default('completed')->after('gateway_payment_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('labs')) {
            Schema::table('labs', function (Blueprint $table) {
                $table->dropColumn([
                    'upi_id', 'upi_name', 'enable_upi',
                    'stripe_key', 'stripe_secret', 'stripe_webhook_secret', 'enable_stripe',
                    'sms_provider', 'sms_api_key', 'sms_sender_id', 'enable_sms',
                    'enable_email', 'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_encryption'
                ]);
            });
        }

        if (Schema::hasTable('lab_payments')) {
            Schema::table('lab_payments', function (Blueprint $table) {
                $table->dropColumn(['payment_gateway', 'gateway_order_id', 'gateway_payment_id', 'gateway_status']);
            });
        }
    }
};
