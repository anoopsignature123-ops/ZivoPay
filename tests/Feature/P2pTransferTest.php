<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class P2pTransferTest extends TestCase
{
    use DatabaseTransactions;

    protected int $userRoleId;

    protected int $adminRoleId;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['id' => 1], ['name' => 'Admin', 'slug' => 'admin']);
        $userRole = Role::firstOrCreate(['id' => 2], ['name' => 'User', 'slug' => 'user']);

        $this->adminRoleId = $adminRole->id;
        $this->userRoleId = $userRole->id;
    }

    public function test_p2p_member_verification_ajax(): void
    {
        $sender = User::create([
            'role_id' => $this->userRoleId,
            'name' => 'Sender User',
            'email' => 'sender_p2p@zivopay.com',
            'referral_code' => 'ZIVO-SND01',
        ]);

        $receiver = User::create([
            'role_id' => $this->userRoleId,
            'name' => 'Receiver User',
            'email' => 'receiver_p2p@zivopay.com',
            'referral_code' => 'ZIVO-RCV01',
            'is_subscription_active' => true,
        ]);

        $response = $this->actingAs($sender)->get(route('user.p2p.check-member', ['member_code' => 'ZIVO-RCV01']));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'name' => 'Receiver User',
            'referral_code' => 'ZIVO-RCV01',
            'is_active' => true,
        ]);
    }

    public function test_p2p_self_transfer_is_blocked(): void
    {
        $user = User::create([
            'role_id' => $this->userRoleId,
            'name' => 'Self User',
            'email' => 'self_p2p@zivopay.com',
            'referral_code' => 'ZIVO-SLF01',
            'earning_wallet' => 1000.00,
        ]);

        $response = $this->actingAs($user)->post(route('user.p2p.store'), [
            'receiver_code' => 'ZIVO-SLF01',
            'from_wallet' => 'earning_wallet',
            'amount' => 500,
        ]);

        $response->assertSessionHasErrors(['receiver_code']);
        $this->assertDatabaseMissing('p2p_transfers', [
            'sender_id' => $user->id,
            'receiver_id' => $user->id,
        ]);
    }

    public function test_successful_p2p_transfer_deducts_sender_and_credits_receiver(): void
    {
        $sender = User::create([
            'role_id' => $this->userRoleId,
            'name' => 'Alice Sender',
            'email' => 'alice@zivopay.com',
            'referral_code' => 'ZIVO-ALC01',
            'earning_wallet' => 2000.00,
            'deposit_wallet' => 0.00,
        ]);

        $receiver = User::create([
            'role_id' => $this->userRoleId,
            'name' => 'Bob Receiver',
            'email' => 'bob@zivopay.com',
            'referral_code' => 'ZIVO-BOB01',
            'deposit_wallet' => 500.00,
        ]);

        $response = $this->actingAs($sender)->post(route('user.p2p.store'), [
            'receiver_code' => 'ZIVO-BOB01',
            'from_wallet' => 'earning_wallet',
            'amount' => 1000,
            'remarks' => 'Test P2P Transfer for Package',
        ]);

        $response->assertRedirect(route('user.p2p.index'));
        $response->assertSessionHas('success');

        // Sender Earning Wallet: 2000 - 1000 = 1000
        $this->assertEquals(1000.00, (float) $sender->fresh()->earning_wallet);

        // Receiver Fund Wallet: 500 + 1000 = 1500
        $this->assertEquals(1500.00, (float) $receiver->fresh()->deposit_wallet);

        // Check P2P Log
        $this->assertDatabaseHas('p2p_transfers', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'amount' => 1000.00,
            'from_wallet' => 'earning_wallet',
            'remarks' => 'Test P2P Transfer for Package',
        ]);

        // Check Sender Debit Transaction
        $this->assertDatabaseHas('transactions', [
            'user_id' => $sender->id,
            'amount' => 1000.00,
            'trx_type' => '-',
            'type' => 'p2p_transfer',
        ]);

        // Check Receiver Credit Transaction
        $this->assertDatabaseHas('transactions', [
            'user_id' => $receiver->id,
            'amount' => 1000.00,
            'trx_type' => '+',
            'type' => 'p2p_transfer',
        ]);
    }

    public function test_admin_can_view_p2p_reports(): void
    {
        $admin = User::create([
            'role_id' => $this->adminRoleId,
            'name' => 'Admin User',
            'email' => 'admin_p2p@zivopay.com',
            'referral_code' => 'ZIVO-ADM99',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.p2p'));

        $response->assertOk();
        $response->assertViewIs('admin.reports.p2p');
    }
}
