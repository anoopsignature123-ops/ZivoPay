<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_login_page_renders_with_secret_key(): void
    {
        $response = $this->get(route('admin.login', ['key' => 'ngt-2026']));
        $response->assertStatus(200);
    }

    public function test_authenticated_admin_can_access_control_center(): void
    {
        $admin = User::create([
            'role_id' => 1,
            'name' => 'Super Admin',
            'email' => 'admin@zivopay.com',
            'referral_code' => 'ZIVO-ADMIN01',
            'password' => bcrypt('admin123'),
        ]);

        $this->actingAs($admin);

        $dashRes = $this->get(route('admin.dashboard'));
        $dashRes->assertStatus(200);

        $usersRes = $this->get(route('admin.users'));
        $usersRes->assertStatus(200);

        $treeRes = $this->get(route('admin.network.tree'));
        $treeRes->assertStatus(200);

        $profileRes = $this->get(route('admin.profile'));
        $profileRes->assertStatus(200);
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $admin = User::create([
            'role_id' => 1,
            'name' => 'Super Admin',
            'email' => 'admin@zivopay.com',
            'referral_code' => 'ZIVO-ADMIN01',
            'password' => bcrypt('admin123'),
        ]);

        $member = User::create([
            'role_id' => 2,
            'name' => 'Member Test',
            'email' => 'member@example.com',
            'status' => 'inactive',
            'referral_code' => 'ZIVO-4000001',
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.users.toggle-status', $member));
        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'status' => 'active',
        ]);
    }
}
