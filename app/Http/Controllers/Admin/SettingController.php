<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display Payment Gateway & System Settings page.
     */
    public function gatewaySettings(): View
    {
        $testMode = Setting::isPaymentTestMode();
        $apiKey = Setting::getPaymentApiKey();
        $usdtAddress = Setting::getUsdtWalletAddress();

        return view('admin.settings.gateway', compact('testMode', 'apiKey', 'usdtAddress'));
    }

    /**
     * Update Payment Gateway & System Settings.
     */
    public function updateGatewaySettings(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_api_key' => 'nullable|string',
            'usdt_wallet_address' => 'nullable|string',
        ]);

        Setting::setValue('payment_test_mode', 'false');
        Setting::setValue('payment_api_key', trim((string) $request->payment_api_key));
        if ($request->filled('usdt_wallet_address')) {
            Setting::setValue('usdt_wallet_address', trim((string) $request->usdt_wallet_address));
        }

        return redirect()->back()->with('success', 'Payment Gateway Settings updated successfully.');
    }
}
