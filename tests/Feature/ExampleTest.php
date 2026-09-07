<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RbjlisSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbjlisSeeder::class);
    }

    public function test_login_page_renders(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('RBJLIS');
    }

    public function test_root_redirects_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_superadmin_dashboard_renders(): void
    {
        $user = User::where('role_id', 1)->first();
        $response = $this->actingAs($user)->get('/superadmin');
        $response->assertStatus(200);
    }

    public function test_labadmin_dashboard_renders(): void
    {
        $user = User::where('role_id', 2)->first();
        $response = $this->actingAs($user)->get('/labadmin');
        $response->assertStatus(200);
    }

    public function test_frontoffice_dashboard_renders(): void
    {
        $user = User::where('role_id', 3)->first();
        $response = $this->actingAs($user)->get('/frontoffice');
        $response->assertStatus(200);
    }

    public function test_labinvestigation_dashboard_renders(): void
    {
        $user = User::where('role_id', 4)->first();
        $response = $this->actingAs($user)->get('/labinvestigation');
        $response->assertStatus(200);
    }

    public function test_ll_dashboard_renders(): void
    {
        $user = User::where('role_id', 8)->first() ?? User::where('role_id', 2)->first();
        $response = $this->actingAs($user)->get('/ll');
        $response->assertStatus(200);
    }

    public function test_change_password_page_renders(): void
    {
        $user = User::where('role_id', 1)->first();
        $response = $this->actingAs($user)->get('/password/changepassword');
        $response->assertStatus(200);
        $response->assertSee('Change Account Password');
    }

    public function test_profile_page_renders(): void
    {
        $user = User::where('role_id', 1)->first();
        $response = $this->actingAs($user)->get('/profile');
        $response->assertStatus(200);
    }

    public function test_print_bill_renders(): void
    {
        $user = User::where('role_id', 2)->first();
        $response = $this->actingAs($user)->get('/print/bill?id=1');
        $response->assertStatus(200);
    }

    public function test_print_report_renders(): void
    {
        $user = User::where('role_id', 2)->first();
        $response = $this->actingAs($user)->get('/print/report?id=1');
        $response->assertStatus(200);
    }

    public function test_save_test_results(): void
    {
        $user = User::where('role_id', 4)->first();
        $response = $this->actingAs($user)->post('/labinvestigation/updateresult', [
            'invtestid' => 1,
            'resultid' => 1,
            'diagnostictestid' => 1,
            'parresultval_1_1' => '14.5',
            'parresultval_2_1' => '7800',
            'parresultval_3_1' => '250000',
            'reportnotes' => 'CBC test values within normal limits.',
        ]);

        $response->assertSessionHas('success');
    }

    public function test_payment_checkout_portal_renders(): void
    {
        $response = $this->get('/pay/1');
        $response->assertStatus(200);
        $response->assertSee('Secure Diagnostic Payment');
        $response->assertSee('UPI QR');
    }

    public function test_stripe_checkout_success_flow(): void
    {
        $response = $this->get('/payment/stripe/success?session_id=mock_123&inv_id=1&mock=1&amount=500');
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_upi_utr_submission(): void
    {
        $response = $this->post('/pay/1/upi-submit', [
            'trans_number' => '423987123456',
            'payment_amount' => 500,
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_labadmin_settings_save_integrations(): void
    {
        $user = User::where('role_id', 2)->first();
        $response = $this->actingAs($user)->post('/labadmin/settings', [
            'address' => '789 Medical Boulevard, Chennai',
            'upi_id' => 'customlab@upi',
            'upi_name' => 'Custom Diagnostic Center',
            'enable_upi' => 'on',
            'enable_email' => 'on',
        ]);

        $response->assertRedirect('/labadmin/settings');
        $response->assertSessionHas('success');
    }
}