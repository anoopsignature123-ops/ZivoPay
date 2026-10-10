<?php

namespace Database\Seeders;

use App\Models\AppService;
use App\Models\Operator;
use App\Models\RechargePlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RechargeMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $operators = config('a1topup.operators', []);
        $plans = config('a1topup.plans', []);

        foreach ($operators as $category => $items) {
            foreach ($items as $item) {
                Operator::updateOrCreate(
                    ['code' => $item['code']],
                    [
                        'name' => $item['name'],
                        'category' => strtolower((string) ($item['type'] ?? $category)),
                        'icon' => $item['icon'] ?? null,
                        'is_active' => true,
                    ]
                );
            }
        }

        DB::transaction(function () use ($plans): void {
            $mobileOperatorCodes = ['RC', 'A', 'V', 'BT', 'BR'];

            RechargePlan::whereIn('operator_code', $mobileOperatorCodes)
                ->update(['is_active' => false]);

            foreach ($plans as $opCode => $planList) {
                foreach ($planList as $plan) {
                    RechargePlan::updateOrCreate(
                        [
                            'operator_code' => $opCode,
                            'amount' => $plan['amount'],
                        ],
                        [
                            'validity' => $plan['validity'] ?? '28 Days',
                            'description' => $plan['description'] ?? '',
                            'category' => $plan['category'] ?? 'Recommended Plans',
                            'is_active' => true,
                        ]
                    );
                }
            }
        });

        $defaultServices = [
            'recharge_and_topup' => [
                ['title' => 'Mobile Recharge', 'key' => 'mobile_recharge', 'icon' => 'smartphone', 'status' => 'active', 'is_active' => true, 'sort_order' => 1],
                ['title' => 'DTH Recharge', 'key' => 'dth_recharge', 'icon' => 'tv', 'status' => 'active', 'is_active' => true, 'sort_order' => 2],
                ['title' => 'FASTag Recharge', 'key' => 'fastag_recharge', 'icon' => 'car', 'status' => 'active', 'is_active' => true, 'sort_order' => 3],
                ['title' => 'Gaming Top-Up', 'key' => 'gaming_topup', 'icon' => 'gamepad', 'status' => 'coming_soon', 'is_active' => false, 'sort_order' => 4],
            ],
            'bills_and_utilities' => [
                ['title' => 'Credit Card Bill', 'key' => 'credit_card_bill', 'icon' => 'credit-card', 'status' => 'coming_soon', 'is_active' => false, 'sort_order' => 5],
                ['title' => 'Electricity Bill', 'key' => 'electricity_bill', 'icon' => 'zap', 'status' => 'active', 'is_active' => true, 'sort_order' => 6],
                ['title' => 'Gas Bill', 'key' => 'gas_bill', 'icon' => 'flame', 'status' => 'active', 'is_active' => true, 'sort_order' => 7],
                ['title' => 'Broadband', 'key' => 'broadband_bill', 'icon' => 'wifi', 'status' => 'coming_soon', 'is_active' => false, 'sort_order' => 8],
            ],
            'travel_and_tickets' => [
                ['title' => 'Train Booking', 'key' => 'train_booking', 'icon' => 'train', 'status' => 'coming_soon', 'is_active' => false, 'sort_order' => 9],
                ['title' => 'Flight Booking', 'key' => 'flight_booking', 'icon' => 'plane', 'status' => 'coming_soon', 'is_active' => false, 'sort_order' => 10],
                ['title' => 'Bus Ticket', 'key' => 'bus_ticket', 'icon' => 'bus', 'status' => 'coming_soon', 'is_active' => false, 'sort_order' => 11],
            ],
            'entertainment' => [
                ['title' => 'OTT Subscriptions', 'key' => 'ott_subscriptions', 'icon' => 'film', 'status' => 'coming_soon', 'is_active' => false, 'sort_order' => 12],
            ],
            'financial_services' => [
                ['title' => 'Insurance', 'key' => 'insurance', 'icon' => 'shield', 'status' => 'active', 'is_active' => true, 'sort_order' => 13],
                ['title' => 'Money Transfer', 'key' => 'money_transfer', 'icon' => 'send', 'status' => 'coming_soon', 'is_active' => false, 'sort_order' => 14],
            ],
        ];

        foreach ($defaultServices as $catKey => $items) {
            foreach ($items as $item) {
                AppService::updateOrCreate(
                    ['key' => $item['key']],
                    [
                        'category' => $catKey,
                        'title' => $item['title'],
                        'icon' => $item['icon'],
                        'status' => $item['status'],
                        'is_active' => $item['is_active'],
                        'sort_order' => $item['sort_order'],
                    ]
                );
            }
        }
    }
}
