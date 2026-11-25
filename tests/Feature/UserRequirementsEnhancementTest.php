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

    public function test_upi_withdrawal_succeeds_even_when_user_has_non_bep20_wallet_address(): void
    {
        $user = User::create([
            'role_id' => 2,
            'name' => 'UPI Withdraw Member',
            'email' => 'upi_'.uniqid().'@test.com',
            'mobile' => '98'.rand(10000000, 99999999),
            'referral_code' => User::generateReferralCode(),
            'kyc_status' => 'approved',
            'earning_wallet' => 3084.72,
            'wallet_address' => '9919323203-6@axl',
        ]);

        $viewResponse = $this->actingAs($user)->get(route('user.withdrawal.index'));
        $viewResponse->assertOk();
        $viewResponse->assertDontSee('value="9919323203-6@axl"', false);

        $postResponse = $this->actingAs($user)->from(route('user.withdrawal.index'))->post(route('user.withdrawal.store'), [
            'from_wallet' => 'earning_wallet',
            'payout_method' => 'upi',
            'amount' => 3000,
            'upi_id' => '9919323203-6@axl',
            'usdt_address' => '9919323203-6@axl',
        ]);

        $postResponse->assertSessionHas('success');
        $this->assertDatabaseHas('withdrawals', [
            'user_id' => $user->id,
            'payment_method' => 'INR UPI',
            'amount' => 3000.00,
            'status' => 'pending',
        ]);
    }
}
