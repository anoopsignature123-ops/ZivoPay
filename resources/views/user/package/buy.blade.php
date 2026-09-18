@extends('user.layouts.app')

@section('title', 'Buy Package ₹3,000 - ZIVO PAY')

@section('content')
<div class="space-y-6 font-sans max-w-4xl mx-auto">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[11px] font-black uppercase tracking-widest border border-emerald-500/40">
                MANDATORY ACCOUNT ACTIVATION
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight mt-1">BUY PACKAGE ₹3,000 (ZIVO FAMILY KIT)</h1>
            <p class="text-xs text-neutral-300 mt-1">Activate your account with mandatory ₹3,000 Zivo Family Kit package to unlock 15-level referral bonuses and 24H Fund Wallet profits.</p>
        </div>

        <div class="px-5 py-3 rounded-2xl bg-[#02180f] border border-emerald-500/40 text-right">
            <span class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-wider block">Fund Wallet Balance</span>
            <span class="text-2xl font-black text-white font-mono">₹{{ number_format($user->deposit_wallet, 2) }}</span>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="p-4 rounded-xl bg-teal-500/20 border border-teal-500/50 text-teal-300 text-xs font-bold flex items-center gap-2">
            <i data-lucide="info" class="w-4 h-4 text-teal-300"></i>
            {{ session('info') }}
        </div>
    @endif

    @if($errors->has('subscription'))
        <div class="p-4 rounded-xl bg-rose-500/20 border border-rose-500/50 text-rose-400 text-xs font-bold flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400"></i>
            {{ $errors->first('subscription') }}
        </div>
    @endif

    <!-- Package Card Container -->
    <div class="bg-[#042718] rounded-3xl border-2 border-emerald-500/40 p-6 sm:p-8 shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 pb-6 border-b border-emerald-500/20">
            <div>
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-wider border border-emerald-500/30">
                    STARTER SUBSCRIPTION
                </span>
                <h2 class="text-2xl font-black text-white uppercase tracking-tight mt-2">ZIVO FAMILY KIT PACKAGE</h2>
                <p class="text-xs text-neutral-300 mt-1">One-time account activation package for all new members.</p>
            </div>

            <div class="text-left md:text-right">
                <span class="text-[10px] text-neutral-400 uppercase font-bold tracking-wider block">Package Price</span>
                <span class="text-3xl font-black text-emerald-400 font-mono">₹3,000.00</span>
            </div>
        </div>

        <!-- Package Features List -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-6 text-xs text-neutral-200">
            <div class="flex items-center gap-3 p-3 rounded-2xl bg-[#02180f] border border-emerald-500/20">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                    <i data-lucide="check" class="w-4 h-4"></i>
                </div>
                <div>
                    <span class="font-bold text-white">15-Level Referral Income</span>
                    <p class="text-[10px] text-neutral-400">5% Level 1 (₹150) + 0.50% Level 2-15 (₹15 / level)</p>
                </div>
            </div>

            <div class="flex items-center gap-3 p-3 rounded-2xl bg-[#02180f] border border-emerald-500/20">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                    <i data-lucide="check" class="w-4 h-4"></i>
                </div>
                <div>
                    <span class="font-bold text-white">24-Hour Idle Wallet Profit</span>
                    <p class="text-[10px] text-neutral-400">0.15% - 0.30% daily return on Fund Wallet balance >= ₹1,000</p>
                </div>
            </div>

            <div class="flex items-center gap-3 p-3 rounded-2xl bg-[#02180f] border border-emerald-500/20">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                    <i data-lucide="check" class="w-4 h-4"></i>
                </div>
                <div>
                    <span class="font-bold text-white">Full Platform Features</span>
                    <p class="text-[10px] text-neutral-400">Unlocks capital investment packages & 24x7 payouts</p>
                </div>
            </div>

            <div class="flex items-center gap-3 p-3 rounded-2xl bg-[#02180f] border border-emerald-500/20">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                    <i data-lucide="check" class="w-4 h-4"></i>
                </div>
                <div>
                    <span class="font-bold text-white">Instant Account Activation</span>
                    <p class="text-[10px] text-neutral-400">Deducted instantly from Fund Wallet balance</p>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div class="pt-4 border-t border-emerald-500/20">
            @if(!$user->is_subscription_active)
                <form action="{{ route('user.wallet.subscription.activate') }}" method="POST">
                    @csrf
                    <button type="submit"
                        {{ (float) ($user->deposit_wallet ?? 0) < 3000 ? 'disabled' : '' }}
                        class="w-full py-4 rounded-2xl font-black text-sm uppercase tracking-wider transition flex items-center justify-center gap-2 shadow-xl {{ (float) ($user->deposit_wallet ?? 0) >= 3000 ? 'bg-gradient-to-r from-emerald-500 to-teal-400 hover:scale-[1.01] text-black cursor-pointer' : 'bg-neutral-800 text-neutral-500 border border-neutral-700 cursor-not-allowed' }}">
                        <i data-lucide="zap" class="w-5 h-5"></i>
                        BUY PACKAGE & ACTIVATE NOW (₹3,000)
                    </button>
                </form>
                @if((float) ($user->deposit_wallet ?? 0) < 3000)
                    <p class="text-xs text-rose-400 text-center font-semibold mt-3">
                        Insufficient Fund Wallet balance (Available: ₹{{ number_format($user->deposit_wallet, 2) }}). Please <a href="{{ route('user.wallet.transfer') }}" class="underline font-bold text-emerald-400">Add Fund</a> first.
                    </p>
                @endif
            @else
                <div class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/50 text-center">
                    <span class="text-emerald-400 font-black uppercase text-sm tracking-wider block">ACCOUNT FULLY ACTIVATED</span>
                    <p class="text-xs text-neutral-300 mt-1">Your Zivo Family Kit package (₹3,000) was activated on {{ $user->activated_at ? $user->activated_at->format('d M Y, h:i A') : 'active' }}.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
