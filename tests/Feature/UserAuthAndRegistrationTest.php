<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAuthAndRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_user_can_view_login_and_registration_pages(): void
    {
        $loginRes = $this->get(route('user.login'));
        $loginRes->assertStatus(200);

        $regRes = $this->get(route('user.register'));
        $regRes->assertStatus(200);
    }

    public function test_user_can_check_sponsor_via_ajax_api(): void
    {
        $sponsor = User::create([
            'role_id' => 2,
            'name' => 'Test Sponsor',
            'email' => 'sponsor@example.com',
            'referral_code' => 'ZIVO-1000001',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->getJson(route('user.check-sponsor', ['code' => 'ZIVO-1000001']));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'name' => 'Test Sponsor',
                'referral_code' => 'ZIVO-1000001',
            ]);
    }

    public function test_user_can_register_new_account(): void
    {
        $sponsor = User::create([
            'role_id' => 2,
            'name' => 'Sponsor One',
            'email' => 'sponsor1@example.com',
            'referral_code' => 'ZIVO-2000001',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('user.register'), [
            'sponsor_id' => 'ZIVO-2000001',
            'name' => 'New Member',
            'email' => 'newmember@example.com',
            'mobile' => '+1234567890',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'email' => 'newmember@example.com',
            'sponsor_code' => 'ZIVO-2000001',
        ]);
    }

    public function test_authenticated_user_can_access_dashboard_network_and_profile(): void
    {
        $user = User::create([
            'role_id' => 2,
            'name' => 'John Member',
            'email' => 'john@example.com',
            'referral_code' => 'ZIVO-3000001',
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($user);

        $dashRes = $this->get(route('user.dashboard'));
        $dashRes->assertStatus(200);

        $directRes = $this->get(route('user.network.direct'));
        $directRes->assertStatus(200);

        $treeRes = $this->get(route('user.network.tree'));
        $treeRes->assertStatus(200);

        $profileRes = $this->get(route('user.profile'));
        $profileRes->assertStatus(200);
    }
}
