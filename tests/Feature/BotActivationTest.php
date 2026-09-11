<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BotActivationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test inactive user cannot activate bot and is redirected to packages page with error.
     */
    public function test_inactive_user_cannot_activate_bot(): void
    {
        $user = User::factory()->create([
            'status' => 'inactive',
            'is_bot_active' => false,
        ]);

        $response = $this->actingAs($user)->post(route('user.bot.activate'));

        $response->assertRedirect(route('user.bot.trading'));
        $response->assertSessionHas('error');
        $this->assertFalse((bool) $user->fresh()->is_bot_active);
    }

    /**
     * Test active user can activate bot successfully.
     */
    public function test_active_user_can_activate_bot(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'is_bot_active' => false,
        ]);

        $response = $this->actingAs($user)->post(route('user.bot.activate'));

        $response->assertRedirect(route('user.bot.trading'));
        $response->assertSessionHas('success');
        $this->assertTrue((bool) $user->fresh()->is_bot_active);
        $this->assertNotNull($user->fresh()->bot_activated_at);
    }

    /**
     * Test already active bot user receives info notification.
     */
    public function test_already_active_bot_user_returns_info_message(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'is_bot_active' => true,
            'bot_activated_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->post(route('user.bot.activate'));

        $response->assertRedirect(route('user.bot.trading'));
        $response->assertSessionHas('info');
    }
}
