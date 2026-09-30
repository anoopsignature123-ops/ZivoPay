<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test fetching profile via API.
     */
    public function test_authenticated_user_can_view_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Profile User',
            'mobile' => '9988776655',
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.name', 'Profile User')
            ->assertJsonPath('data.user.mobile', '9988776655');
    }

    /**
     * Test updating profile details & profile avatar image via API.
     */
    public function test_authenticated_user_can_update_profile_and_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'name' => 'Old Name',
            'mobile' => '9111111111',
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        $avatarFile = UploadedFile::fake()->image('avatar.jpg', 400, 400);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/profile', [
                'name' => 'New Updated Name',
                'mobile' => '9222222222',
                'wallet_address' => '0x1234567890abcdef',
                'image' => $avatarFile,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.name', 'New Updated Name')
            ->assertJsonPath('data.user.mobile', '9222222222')
            ->assertJsonPath('data.user.wallet_address', '0x1234567890abcdef');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Updated Name',
            'mobile' => '9222222222',
            'wallet_address' => '0x1234567890abcdef',
        ]);

        $updatedUser = $user->fresh();
        $this->assertNotNull($updatedUser->image);
        Storage::disk('public')->assertExists($updatedUser->image);
    }

    /**
     * Test changing password via API.
     */
    public function test_authenticated_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/change-password', [
                'current_password' => 'oldpassword123',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));

        // Clear auth guard memory state
        Auth::forgetGuards();

        // Verify that the old Bearer token is now revoked and returns 401 Unauthorized
        $profileResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/profile');

        $profileResponse->assertStatus(401);
    }
}
