<?php

namespace Tests\Feature;

use App\Models\AppService;
use App\Models\User;
use Database\Seeders\RechargeMasterSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(RechargeMasterSeeder::class);
    }

    public function test_admin_can_view_and_toggle_app_service_status(): void
    {
        $admin = User::create([
            'role_id' => 1,
            'name' => 'Super Admin Service',
            'email' => 'admin_svc_'.uniqid().'@zivopay.com',
            'referral_code' => 'ZIVO-ADM'.rand(100000, 999999),
            'password' => bcrypt('admin123'),
        ]);

        $this->actingAs($admin);

        // 1. View Admin Services Management Page
        $resView = $this->get(route('admin.services.index'));
        $resView->assertStatus(200);

        // 2. Toggle 'gaming_topup' from coming_soon to active
        $service = AppService::where('key', 'gaming_topup')->first();
        $this->assertNotNull($service);
        $this->assertEquals('coming_soon', $service->status);

        $resToggle = $this->post(route('admin.services.toggle-status', $service));
        $resToggle->assertRedirect();

        $this->assertEquals('active', $service->fresh()->status);
        $this->assertTrue((bool) $service->fresh()->is_active);

        // 3. Verify Dashboard API reflects updated status dynamically
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $dashRes = $this->getJson('/api/dashboard');
        $dashRes->assertStatus(200);

        $rechargeServices = $dashRes->json('data.services.recharge_and_topup');
        $gamingItem = collect($rechargeServices)->firstWhere('key', 'gaming_topup');

        $this->assertNotNull($gamingItem);
        $this->assertEquals('active', $gamingItem['status']);
        $this->assertTrue($gamingItem['is_active']);
        $this->assertNull($gamingItem['badge']);
    }
}
