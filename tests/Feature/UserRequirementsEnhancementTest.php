<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserRequirementsEnhancementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['id' => 1], ['name' => 'Admin', 'slug' => 'admin']);
        Role::firstOrCreate(['id' => 2], ['name' => 'User', 'slug' => 'user']);
    }

    public function test_registration_rejects_mobile_with_country_code_plus_91(): void
    {
        $response = $this->post('/user/register', [
            'name' => 'Test Mobile Rejection',
            'email' => 'reject_'.uniqid().'@test.com',
            'mobile' => '+919876543210',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'sponsor_id' => 'ZIVO-0000001',
        ]);

        $response->assertSessionHasErrors(['mobile']);
    }

    public function test_registration_accepts_valid_10_digit_mobile(): void
    {
        $uniquePhone = '98'.rand(10000000, 99999999);
        $uniqueEmail = 'valid_'.uniqid().'@test.com';

        $response = $this->post('/user/register', [
            'name' => 'Valid Member',
            'email' => $uniqueEmail,
            'mobile' => $uniquePhone,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'sponsor_id' => 'ZIVO-0000001',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $uniqueEmail,
            'mobile' => $uniquePhone,
        ]);
    }

    public function test_profile_update_saves_usdt_address_and_reflects_in_withdrawal(): void
    {
        $user = User::create([
            'role_id' => 2,
            'name' => 'Profile Test Member',
            'email' => 'profile_'.uniqid().'@test.com',
            'mobile' => '98'.rand(10000000, 99999999),
            'referral_code' => User::generateReferralCode(),
            'kyc_status' => 'approved',
            'earning_wallet' => 5000,
        ]);

        $bep20Address = '0x1234567890abcdef1234567890abcdef12345678';

        $updateResponse = $this->actingAs($user)->put(route('user.profile.update'), [
            'name' => 'Updated Profile Member',
            'mobile' => $user->mobile,
            'usdt_address' => $bep20Address,
        ]);

        $updateResponse->assertSessionHas('success');
        $user->refresh();
        $this->assertEquals($bep20Address, $user->usdt_address);

        $viewResponse = $this->actingAs($user)->get(route('user.withdrawal.index'));
        $viewResponse->assertOk();
        $viewResponse->assertSee($bep20Address);
    }
}
