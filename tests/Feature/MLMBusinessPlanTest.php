<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserReward;
use App\Services\MLMIncomeService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class MLMBusinessPlanTest extends TestCase
{
    use DatabaseTransactions;

    protected int $roleId;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['slug' => 'member'], ['name' => 'Member']);
        $this->roleId = $role->id;
    }

    public function test_investment_tiers_and_15_level_direct_income(): void
    {
        $service = new MLMIncomeService;

        // Build 15-Level Sponsor Chain
        $sponsors = [];
        $previousCode = null;

        for ($i = 1; $i <= 15; $i++) {
            $user = User::create([
                'role_id' => $this->roleId,
                'name' => "User Level {$i}",
                'email' => "user{$i}@zivopay.com",
                'referral_code' => "ZIVO-00000{$i}",
                'sponsor_code' => $previousCode,
                'deposit_wallet' => 200000.00,
                'earning_wallet' => 0.00,
                'status' => 'inactive',
            ]);

            $sponsors[$i] = $user;
            $previousCode = $user->referral_code;
        }

        // User 15 (bottom user) invests ₹1,00,000
        $investor = $sponsors[15];
        $investment = $service->createInvestment($investor, 100000.00, 'deposit_wallet');

        // 1. Assert investor's investment tier & state
        $this->assertEquals('Gold Tier (0.20% Daily)', $investment->plan_name);
        $this->assertEquals(0.20, $investment->daily_percentage);
        $this->assertEquals(200.00, $investment->daily_amount);
        $this->assertEquals('active', $investor->fresh()->status);
        $this->assertEquals(100000.00, $investor->fresh()->deposit_wallet);

        // 2. Assert Level 1 Direct Sponsor (User 14) gets 5% = ₹5,000
        $this->assertEquals(5000.00, $sponsors[14]->fresh()->earning_wallet);

        // 3. Assert Level 2 to Level 15 Sponsors (Users 1 to 13) get 0.50% = ₹500
        for ($lvl = 1; $lvl <= 13; $lvl++) {
            $this->assertEquals(500.00, $sponsors[$lvl]->fresh()->earning_wallet, "User level {$lvl} failed to receive 0.50% direct level income");
        }

        // 4. Assert 14 level income transactions created for the sponsor chain
        $sponsorIds = array_map(fn ($u) => $u->id, $sponsors);
        $this->assertEquals(14, Transaction::where('type', 'level_income')->whereIn('user_id', $sponsorIds)->count());
    }

    public function test_daily_roi_and_15_level_roi_matching_income(): void
    {
        $service = new MLMIncomeService;

        // Build 15-Level Chain
        $sponsors = [];
        $previousCode = null;

        for ($i = 1; $i <= 15; $i++) {
            $user = User::create([
                'role_id' => $this->roleId,
                'name' => "User Level {$i}",
                'email' => "user{$i}@zivopay.com",
                'referral_code' => "ZIVO-00000{$i}",
                'sponsor_code' => $previousCode,
                'deposit_wallet' => 200000.00,
                'earning_wallet' => 0.00,
                'status' => 'active',
            ]);

            $sponsors[$i] = $user;
            $previousCode = $user->referral_code;
        }

        // User 15 invests ₹1,00,000 (Daily ROI = ₹200)
        $investor = $sponsors[15];
        $investment = $service->createInvestment($investor, 100000.00, 'deposit_wallet');

        // Reset earning wallets in DB to test ROI payout cleanly
        $sponsorIds = array_map(fn ($u) => $u->id, $sponsors);
        User::whereIn('id', $sponsorIds)->update(['earning_wallet' => 0.00]);

        // Run Daily ROI Console Command
        Artisan::call('zivo:process-daily-roi');

        // 1. Investor (User 15) receives ₹200.00 Daily ROI
        $this->assertEquals(200.00, $investor->fresh()->earning_wallet);

        // 2. Level 1 Sponsor (User 14) receives 10% of ₹200 = ₹20.00
        $this->assertEquals(20.00, $sponsors[14]->fresh()->earning_wallet);

        // 3. Level 2 Sponsor (User 13) receives 3% of ₹200 = ₹6.00
        $this->assertEquals(6.00, $sponsors[13]->fresh()->earning_wallet);

        // 4. Level 3 to Level 15 Sponsors (Users 1 to 12) receive 1% of ₹200 = ₹2.00
        for ($lvl = 1; $lvl <= 12; $lvl++) {
            $this->assertEquals(2.00, $sponsors[$lvl]->fresh()->earning_wallet, "User level {$lvl} failed to receive 1% ROI matching income");
        }

        // 5. Assert ROI transaction logs created
        $sponsorIds = array_map(fn ($u) => $u->id, $sponsors);
        $this->assertEquals(1, Transaction::where('type', 'daily_roi')->where('user_id', $investor->id)->count());
        $this->assertEquals(14, Transaction::where('type', 'roi_level_income')->whereIn('user_id', $sponsorIds)->count());
    }

    public function test_reward_offer_achievements(): void
    {
        $service = new MLMIncomeService;

        $sponsor = User::create([
            'role_id' => $this->roleId,
            'name' => 'Top Sponsor',
            'email' => 'sponsor@zivopay.com',
            'referral_code' => 'ZIVO-SPONSOR',
            'deposit_wallet' => 0.00,
            'earning_wallet' => 0.00,
            'status' => 'active',
        ]);

        $downline = User::create([
            'role_id' => $this->roleId,
            'name' => 'Downline Member',
            'email' => 'downline@zivopay.com',
            'referral_code' => 'ZIVO-DOWNLINE',
            'sponsor_code' => 'ZIVO-SPONSOR',
            'deposit_wallet' => 600000.00,
            'earning_wallet' => 0.00,
            'status' => 'inactive',
        ]);

        // Downline invests ₹5,00,000 (qualifies sponsor for Executive Rank - Mobile Phone)
        $service->createInvestment($downline, 500000.00, 'deposit_wallet');

        // Assert reward achieved
        $reward = UserReward::where('user_id', $sponsor->id)->first();
        $this->assertNotNull($reward);
        $this->assertEquals('Executive Rank', $reward->reward_title);
        $this->assertEquals('Mobile Phone (स्मार्टफोन)', $reward->reward_item);
        $this->assertEquals('achieved', $reward->status);
    }
}
