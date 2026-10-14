<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGatewaySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentGatewayController extends Controller
{
    public function index(): View
    {
        return view('admin.payment-gateway.index', ['settings' => PaymentGatewaySetting::razorpay()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => ['required', 'in:test,live'],
            'test_key_id' => ['nullable', 'string', 'max:255', 'regex:/^rzp_test_[A-Za-z0-9]+$/'],
            'test_key_secret' => ['nullable', 'string', 'min:8', 'max:2000'],
            'test_webhook_secret' => ['nullable', 'string', 'min:8', 'max:2000'],
            'live_key_id' => ['nullable', 'string', 'max:255', 'regex:/^rzp_live_[A-Za-z0-9]+$/'],
            'live_key_secret' => ['nullable', 'string', 'min:8', 'max:2000'],
            'live_webhook_secret' => ['nullable', 'string', 'min:8', 'max:2000'],
        ], [
            'test_key_id.regex' => 'Enter a valid Razorpay Test Key ID starting with rzp_test_.',
            'live_key_id.regex' => 'Enter a valid Razorpay Live Key ID starting with rzp_live_.',
            'test_key_secret.min' => 'The Test Key Secret must contain at least 8 characters.',
            'test_webhook_secret.min' => 'The Test Webhook Secret must contain at least 8 characters.',
            'live_key_secret.min' => 'The Live Key Secret must contain at least 8 characters.',
            'live_webhook_secret.min' => 'The Live Webhook Secret must contain at least 8 characters.',
        ]);
        $settings = PaymentGatewaySetting::razorpay();

        if ($request->boolean('enabled')) {
            $mode = $validated['mode'];
            $keyId = trim((string) ($validated[$mode.'_key_id'] ?? '')) ?: $settings->keyIdFor($mode);
            $keySecret = trim((string) ($validated[$mode.'_key_secret'] ?? '')) ?: $settings->keySecretFor($mode);

            if (blank($keyId) || blank($keySecret)) {
                return back()
                    ->withErrors([$mode.'_key_id' => ucfirst($mode).' mode needs a Key ID and Key Secret before the gateway can be enabled.'])
                    ->withInput();
            }
        }

        $settings->mode = $validated['mode'];
        $settings->enabled = $request->boolean('enabled');
        foreach (['test_key_id', 'test_key_secret', 'test_webhook_secret', 'live_key_id', 'live_key_secret', 'live_webhook_secret'] as $field) {
            $value = trim((string) ($validated[$field] ?? ''));
            if ($value !== '') {
                $settings->{$field} = $value;
            }
        }
        $settings->save();

        return back()->with('success', 'Razorpay settings saved successfully.');
    }
}
