<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppService;
use App\Models\Recharge;
use App\Models\Transaction;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponse;

    /**
     * Get Mobile App Home Dashboard Overview API.
     * Matches Mobile App UI Dashboard (User Profile Image URL, Wallet Card, Services Grid, Recent Transactions Feed).
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Fetch Recent Transactions & Recharges merged and sorted by date
            $transactions = Transaction::where('user_id', $user->id)
                ->orderBy('id', 'desc')
                ->take(8)
                ->get()
                ->map(function ($t) {
                    $isCredit = in_array(strtolower($t->type), ['deposit', 'recharge_refund', 'p2p_receive', 'daily_roi', 'direct_bonus', 'level_income']);
                    $amountPrefix = $isCredit ? '+ ₹' : '- ₹';

                    return [
                        'id' => $t->id,
                        'trx_id' => $t->trx_id,
                        'type' => $t->type,
                        'title' => $this->formatTransactionTitle($t),
                        'subtitle' => $t->description ?? ($t->created_at->format('d M Y, h:i A')),
                        'amount' => $amountPrefix.number_format((float) $t->amount, 2),
                        'is_credit' => $isCredit,
                        'status' => 'Success',
                        'status_color' => $isCredit ? 'green' : 'black',
                        'created_at' => $t->created_at->toIso8601String(),
                        'date_formatted' => $t->created_at->format('d M Y, h:i A'),
                    ];
                });

            // Profile Image URL
            $imageUrl = $user->image ? asset('storage/'.$user->image) : asset('images/default-avatar.png');

            $specialOffers = [
                [
                    'id' => 1,
                    'badge' => 'LIMITED TIME',
                    'title' => 'Get 20% Cashback',
                    'subtitle' => 'On your first Mobile Recharge',
                    'background_color' => '#0088FF',
                ],
                [
                    'id' => 2,
                    'badge' => 'HOT DEAL',
                    'title' => 'Zero Fee DTH Recharge',
                    'subtitle' => 'Instant cashback on Tata Play & Airtel TV',
                    'background_color' => '#FF9900',
                ],
            ];

            $dbServices = AppService::orderBy('sort_order')->get();

            if ($dbServices->isNotEmpty()) {
                $grouped = $dbServices->groupBy('category');
                $services = [];
                foreach ($grouped as $catKey => $itemList) {
                    $services[$catKey] = $itemList->map(function ($item) {
                        $status = strtolower((string) $item->status);
                        $isActive = ($status === 'active' && (bool) $item->is_active);

                        return [
                            'title' => $item->title,
                            'key' => $item->key,
                            'icon' => $item->icon,
                            'status' => $status,
                            'is_active' => $isActive,
                            'badge' => $isActive ? null : 'Coming Soon',
                        ];
                    })->values()->toArray();
                }
            } else {
                $serviceStatuses = config('a1topup.service_statuses', []);

                $rawServices = [
                    'recharge_and_topup' => [
                        ['title' => 'Mobile Recharge', 'key' => 'mobile_recharge', 'icon' => 'smartphone'],
                        ['title' => 'DTH Recharge', 'key' => 'dth_recharge', 'icon' => 'tv'],
                        ['title' => 'FASTag Recharge', 'key' => 'fastag_recharge', 'icon' => 'car'],
                        ['title' => 'Gaming Top-Up', 'key' => 'gaming_topup', 'icon' => 'gamepad'],
                    ],
                    'bills_and_utilities' => [
                        ['title' => 'Credit Card Bill', 'key' => 'credit_card_bill', 'icon' => 'credit-card'],
                        ['title' => 'Electricity Bill', 'key' => 'electricity_bill', 'icon' => 'zap'],
                        ['title' => 'Gas Bill', 'key' => 'gas_bill', 'icon' => 'flame'],
                        ['title' => 'Broadband', 'key' => 'broadband_bill', 'icon' => 'wifi'],
                    ],
                    'travel_and_tickets' => [
                        ['title' => 'Train Booking', 'key' => 'train_booking', 'icon' => 'train'],
                        ['title' => 'Flight Booking', 'key' => 'flight_booking', 'icon' => 'plane'],
                        ['title' => 'Bus Ticket', 'key' => 'bus_ticket', 'icon' => 'bus'],
                    ],
                    'entertainment' => [
                        ['title' => 'OTT Subscriptions', 'key' => 'ott_subscriptions', 'icon' => 'film'],
                    ],
                    'financial_services' => [
                        ['title' => 'Insurance', 'key' => 'insurance', 'icon' => 'shield'],
                        ['title' => 'Money Transfer', 'key' => 'money_transfer', 'icon' => 'send'],
                    ],
                ];

                $services = [];
                foreach ($rawServices as $catKey => $itemList) {
                    $services[$catKey] = array_map(function ($item) use ($serviceStatuses) {
                        $status = $serviceStatuses[$item['key']] ?? 'coming_soon';
                        $isActive = ($status === 'active');

                        return [
                            'title' => $item['title'],
                            'key' => $item['key'],
                            'icon' => $item['icon'],
                            'status' => $status,
                            'is_active' => $isActive,
                            'badge' => $isActive ? null : 'Coming Soon',
                        ];
                    }, $itemList);
                }
            }

            return $this->successResponse([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'referral_code' => $user->referral_code,
                    'image' => $user->image,
                    'image_url' => $imageUrl,
                    'deposit_wallet' => (float) $user->deposit_wallet,
                    'earning_wallet' => (float) $user->earning_wallet,
                    'formatted_balance' => '₹'.number_format((float) $user->deposit_wallet, 2),
                ],
                'special_offers' => $specialOffers,
                'services' => $services,
                'recent_transactions' => $transactions,
            ], 'Home dashboard overview retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Dashboard fetch failed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Helper to format readable transaction titles.
     */
    protected function formatTransactionTitle(Transaction $t): string
    {
        return match (strtolower($t->type)) {
            'deposit' => 'Add Fund / Deposit',
            'recharge' => 'Mobile / Utility Recharge',
            'recharge_refund' => 'Recharge Auto-Refund',
            'p2p_transfer', 'p2p_send' => 'P2P Transfer Sent',
            'p2p_receive' => 'P2P Transfer Received',
            'withdrawal' => 'Wallet Withdrawal',
            'daily_roi' => 'Daily ROI Income',
            'direct_bonus' => 'Direct Referral Income',
            default => ucfirst(str_replace('_', ' ', $t->type)),
        };
    }

    /**
     * Get Transaction Details API for Recent Transactions Click View.
     */
    public function transactionDetails(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $id = $request->id;
            $t = Transaction::where('user_id', $user->id)
                ->where(function ($q) use ($id) {
                    if (is_numeric($id)) {
                        $q->where('id', $id)->orWhere('trx_id', $id);
                    } else {
                        $q->where('trx_id', $id);
                    }
                })
                ->first();

            if (!$t) {
                return $this->errorResponse('Transaction record not found.', 404);
            }

            $isCredit = in_array(strtolower($t->type), ['deposit', 'recharge_refund', 'p2p_receive', 'daily_roi', 'direct_bonus', 'level_income']);
            $amountPrefix = $isCredit ? '+ ₹' : '- ₹';

            $rechargeDetails = null;
            if (in_array(strtolower($t->type), ['recharge', 'mobile', 'dth', 'electricity', 'fastag', 'postpaid', 'gas', 'insurance', 'voucher'])) {
                $recharge = Recharge::where('user_id', $user->id)
                    ->where(function ($q) use ($t) {
                        $q->where('order_id', $t->trx_id)
                            ->orWhere('txid', $t->trx_id);
                    })
                    ->first();

                if ($recharge) {
                    $rechargeDetails = [
                        'order_id' => $recharge->order_id,
                        'service_type' => $recharge->service_type,
                        'operator_code' => $recharge->operator_code,
                        'operator_name' => $recharge->operator_name,
                        'number' => $recharge->number,
                        'circle_code' => $recharge->circle_code,
                        'value1' => $recharge->value1,
                        'value2' => $recharge->value2,
                        'txid' => $recharge->txid,
                        'opid' => $recharge->opid,
                        'status' => ucfirst($recharge->status),
                    ];
                }
            }

            $formattedData = [
                'id' => $t->id,
                'trx_id' => $t->trx_id ?? 'TXN' . $t->id,
                'type' => $t->type,
                'type_formatted' => ucfirst(str_replace('_', ' ', $t->type)),
                'title' => $rechargeDetails['operator_name'] ?? $this->formatTransactionTitle($t),
                'subtitle' => $t->description ?? $t->created_at->format('d M Y, h:i A'),
                'amount' => (float) $t->amount,
                'amount_formatted' => $amountPrefix . number_format((float) $t->amount, 2),
                'is_credit' => $isCredit,
                'wallet_type' => $t->wallet_type ?? 'deposit_wallet',
                'wallet_type_formatted' => ($t->wallet_type === 'earning_wallet') ? 'Earning Wallet' : 'Fund Wallet',
                'post_balance' => (float) $t->post_balance,
                'post_balance_formatted' => '₹' . number_format((float) $t->post_balance, 2),
                'status' => 'Success',
                'status_color' => $isCredit ? 'green' : 'black',
                'created_at' => $t->created_at->toIso8601String(),
                'date_formatted' => $t->created_at->format('d M Y, h:i A'),
                'description' => $t->description,
                'recharge_details' => $rechargeDetails,
            ];

            return $this->successResponse($formattedData, 'Transaction details retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch transaction details: ' . $e->getMessage(), 500);
        }
    }
}