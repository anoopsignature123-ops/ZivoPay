<?php

namespace Tests\Feature;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SupportTicketTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test fetching support contact info API.
     */
    public function test_can_fetch_support_contact_info(): void
    {
        $response = $this->getJson('/api/support/contact');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'support_email',
                    'support_phone',
                    'whatsapp',
                    'working_hours',
                    'faqs',
                ],
            ]);
    }

    /**
     * Test user ticket creation, listing, and fetching ticket details.
     */
    public function test_user_can_create_and_view_support_tickets(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // 1. Create ticket
        $createResponse = $this->postJson('/api/support/tickets', [
            'category' => 'recharge',
            'subject' => 'Mobile Recharge Pending Issue',
            'message' => 'My recharge of Rs. 239 is pending since 10 minutes.',
            'priority' => 'high',
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ticket.subject', 'Mobile Recharge Pending Issue');

        $ticketId = $createResponse->json('data.ticket.id');

        // 2. Fetch ticket list
        $listResponse = $this->getJson('/api/support/tickets');

        $listResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data.tickets');

        // 3. Fetch single ticket detail
        $showResponse = $this->getJson("/api/support/tickets/{$ticketId}");

        $showResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ticket.id', $ticketId);
    }

    /**
     * Test admin viewing and replying to support ticket.
     */
    public function test_admin_can_view_and_reply_to_support_ticket(): void
    {
        $adminUser = User::factory()->create([
            'role_id' => 1, // Admin role
        ]);

        $user = User::factory()->create();

        $ticket = SupportTicket::create([
            'ticket_number' => 'TKT999999',
            'user_id' => $user->id,
            'category' => 'deposit',
            'subject' => 'Payment deducted but fund not added',
            'message' => 'UPI transaction ref #123456',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        // Login as admin
        $this->actingAs($adminUser);

        // View index
        $indexRes = $this->get(route('admin.support.index'));
        $indexRes->assertStatus(200);

        // View show page
        $showRes = $this->get(route('admin.support.show', $ticket));
        $showRes->assertStatus(200);

        // Submit reply
        $replyRes = $this->post(route('admin.support.reply', $ticket), [
            'admin_reply' => 'Fund has been credited to your wallet manually.',
            'status' => 'resolved',
        ]);

        $replyRes->assertRedirect(route('admin.support.show', $ticket));

        $this->assertDatabaseHas('support_tickets', [
            'id' => $ticket->id,
            'status' => 'resolved',
            'admin_reply' => 'Fund has been credited to your wallet manually.',
        ]);
    }
}
