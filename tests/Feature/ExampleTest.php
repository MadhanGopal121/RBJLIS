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
}