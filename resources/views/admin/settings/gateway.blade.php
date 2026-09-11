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
            <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient font-heading">PAYMENT GATEWAY SETTINGS</h1>
            <p class="text-xs text-neutral-300 mt-1">Configure live API credentials for production payment gateway.</p>
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

            <!-- LIVE MODE STATUS BANNER -->
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/40 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
                    <div>
                        <span class="text-xs font-extrabold text-emerald-400 uppercase tracking-wider block">LIVE PRODUCTION GATEWAY ACTIVE</span>
                        <p class="text-[11px] text-neutral-400">All deposit transactions are processed directly on live blockchain via gateway API.</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-black uppercase border border-emerald-500/40 shrink-0">
                    🚀 LIVE MODE
                </span>
            </div>

            <!-- GATEWAY CREDENTIALS FORM -->
            <div class="space-y-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-amber-400 uppercase mb-1.5">Payment Gateway API Key</label>
                    <input type="text" name="payment_api_key" value="{{ old('payment_api_key', $apiKey) }}" placeholder="pk_Hwho4MCbvOT8j1e6h254lJOkh..."
                        class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-mono text-xs focus:outline-none focus:border-amber-400">
                    <p class="text-[10px] text-neutral-400 mt-1">Provided by your payment gateway provider (e.g. iPaymentWallet / Oxapay / Nowpayments).</p>
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
