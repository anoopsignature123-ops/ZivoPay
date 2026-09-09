@extends('admin.layouts.app')

@section('content')
<div class="w-full space-y-6 font-sans">
    <!-- Header Banner -->
    <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="pdf-num-badge">GW</span>
                <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">SYSTEM CONFIGURATION</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient font-heading">PAYMENT GATEWAY & SIMULATION SETTINGS</h1>
            <p class="text-xs text-neutral-300 mt-1">Configure live API credentials, USDT wallet addresses, or enable sandbox testing simulation mode.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-3xl mx-auto bg-panel p-6 sm:p-8 space-y-6 border border-amber-500/30 rounded-2xl shadow-2xl">
        <form action="{{ route('admin.settings.gateway.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- MODE SELECTOR CARD -->
            <div class="p-5 rounded-2xl bg-black/60 border-2 {{ $testMode ? 'border-amber-400' : 'border-emerald-500/50' }} space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold text-amber-400 uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="sliders" class="w-4 h-4 text-amber-400"></i>
                        PAYMENT GATEWAY OPERATION MODE
                    </span>
                    @if($testMode)
                        <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-[10px] font-black uppercase border border-amber-500/40">
                            🧪 TEST / SIMULATION MODE ACTIVE
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-black uppercase border border-emerald-500/40">
                            🚀 LIVE PRODUCTION GATEWAY ACTIVE
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <label class="p-4 rounded-xl border border-amber-500/40 bg-bg cursor-pointer hover:border-amber-400 transition flex items-start gap-3 group">
                        <input type="radio" name="payment_test_mode" value="true" {{ $testMode ? 'checked' : '' }} class="mt-0.5 accent-amber-500">
                        <div class="space-y-1">
                            <span class="text-xs font-extrabold text-amber-300 uppercase block">Testing / Simulation Mode</span>
                            <p class="text-[11px] text-neutral-400 leading-snug">
                                Allows member add funds to be instantly verified & credited in sandbox mode without requiring live gateway API key.
                            </p>
                        </div>
                    </label>

                    <label class="p-4 rounded-xl border border-amber-500/40 bg-bg cursor-pointer hover:border-amber-400 transition flex items-start gap-3 group">
                        <input type="radio" name="payment_test_mode" value="false" {{ !$testMode ? 'checked' : '' }} class="mt-0.5 accent-amber-500">
                        <div class="space-y-1">
                            <span class="text-xs font-extrabold text-emerald-400 uppercase block">Live Production Gateway</span>
                            <p class="text-[11px] text-neutral-400 leading-snug">
                                Connects to iPaymentWallet API gateway using production API Key and verifies real blockchain transactions.
                            </p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- GATEWAY CREDENTIALS FORM -->
            <div class="space-y-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-amber-400 uppercase mb-1.5">Payment Gateway API Key</label>
                    <input type="text" name="payment_api_key" value="{{ old('payment_api_key', $apiKey) }}" placeholder="pk_Hwho4MCbvOT8j1e6h254lJOkh..."
                        class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-mono text-xs focus:outline-none focus:border-amber-400">
                    <p class="text-[10px] text-neutral-400 mt-1">Provided by your payment gateway provider (e.g. iPaymentWallet / Oxapay / Nowpayments).</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-amber-400 uppercase mb-1.5">USDT BEP-20 Receiver Wallet Address *</label>
                    <input type="text" name="usdt_wallet_address" value="{{ old('usdt_wallet_address', $usdtAddress) }}" required placeholder="0x71C7656EC7ab88b098defB751B7401B5f6d8976F"
                        class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-amber-300 font-mono text-xs focus:outline-none focus:border-amber-400">
                    <p class="text-[10px] text-neutral-400 mt-1">Official Binance Smart Chain (BEP20) wallet address displayed to members during deposit checkout.</p>
                </div>
            </div>

            <div class="pt-4 border-t border-amber-500/20 flex items-center justify-end">
                <button type="submit" class="px-8 py-3.5 rounded-xl bg-amber-500 text-black font-black text-xs uppercase tracking-wider shadow-lg hover:bg-amber-400 hover:scale-102 transition flex items-center gap-2 cursor-pointer">
                    <i data-lucide="save" class="w-4 h-4 text-black"></i>
                    SAVE GATEWAY SETTINGS
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
