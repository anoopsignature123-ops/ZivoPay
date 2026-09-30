<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful user registration via API without auto-login.
     */
    public function test_user_can_register_via_api(): void
    {
        $sponsor = User::factory()->create([
            'referral_code' => 'ZIVO-1000000',
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'John API User',
            'email' => 'john.api@example.com',
            'mobile' => '9876543210',
            'sponsor_code' => $sponsor->referral_code,
            'position' => 'direct',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'mobile',
                        'referral_code',
                        'sponsor_code',
                        'wallets' => ['fund_wallet', 'earning_wallet'],
                    ],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john.api@example.com',
            'mobile' => '9876543210',
            'sponsor_code' => $sponsor->referral_code,
        ]);
    }

    /**
     * Test user registration followed by explicit login using User ID / Email / Mobile + Password.
     */
    public function test_user_can_login_via_id_email_or_mobile(): void
    {
        $user = User::factory()->create([
            'referral_code' => 'ZIVO-7777777',
            'email' => 'user777@example.com',
            'mobile' => '9988776655',
            'status' => 'inactive',
            'password' => Hash::make('password123'),
        ]);

        // Login using Referral Code (User ID)
        $responseId = $this->postJson('/api/login', [
            'login_id' => 'ZIVO-7777777',
            'password' => 'password123',
        ]);

        $responseId->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', 'user777@example.com')
            ->assertJsonStructure(['data' => ['token', 'token_type']]);

        // Login using Mobile number
        $responseMobile = $this->postJson('/api/login', [
            'login_id' => '9988776655',
            'password' => 'password123',
        ]);

        $responseMobile->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    /**
     * Test fetching authenticated profile via Bearer token.
     */
    public function test_authenticated_user_can_fetch_profile(): void
    {
        $user = User::factory()->create([
            'status' => 'inactive',
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.id', $user->id);
    }
}
