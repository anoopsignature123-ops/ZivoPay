@extends('admin.layouts.app')

@section('title', 'Member Details & Profile Overview - ZIVO PAY')

@section('content')
<div class="w-full space-y-6 font-sans">
    
    <!-- Top Header Banner -->
    <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.users') }}" class="text-xs text-emerald-400 font-extrabold tracking-[3px] uppercase hover:underline flex items-center gap-1">
                    &larr; BACK TO MEMBER DIRECTORY
                </a>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white font-heading uppercase">MEMBER DETAILS: {{ $user->name }}</h1>
            <p class="text-xs text-neutral-300 mt-1">Full profile information, sponsor credentials, and referral link management.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.impersonate', $user) }}" target="_blank" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-black text-xs uppercase tracking-wider shadow-lg flex items-center gap-2">
                <i data-lucide="user-check" class="w-4 h-4 text-white"></i> LOGIN AS MEMBER
            </a>

            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider shadow-lg flex items-center gap-2 {{ $user->status === 'active' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500/30' }}">
                    <i data-lucide="power" class="w-4 h-4"></i> {{ $user->status === 'active' ? 'DEACTIVATE ACCOUNT' : 'ACTIVATE ACCOUNT' }}
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Profile Overview Card -->
    <div class="p-6 rounded-3xl bg-panel border border-emerald-500/30 shadow-2xl space-y-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-emerald-500/20">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 via-emerald-600 to-teal-800 text-white font-black text-2xl flex items-center justify-center shadow-xl border-2 border-emerald-300 shrink-0">
                    <span class="text-white font-black">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <div>
                    <h2 class="text-xl font-black text-white font-heading">{{ $user->name }}</h2>
                    <p class="text-xs text-neutral-300 font-mono">{{ $user->email }} • {{ $user->mobile }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                        <span class="px-2.5 py-0.5 rounded bg-black/80 border border-emerald-500/40 text-emerald-400 font-mono font-bold text-[10px]">REFERRAL CODE: {{ $user->referral_code }}</span>
                        <span class="px-2.5 py-0.5 rounded bg-black/80 border border-teal-500/40 text-teal-300 font-mono font-bold text-[10px]">SPONSOR: {{ $user->sponsor_code ?? 'None' }}</span>
                        @if($user->is_subscription_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-black border border-emerald-500/50 shadow-[0_0_15px_rgba(16,185,129,0.35)]">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                                </span>
                                ₹3,000 PACKAGE ACTIVE
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-300 text-[10px] font-black border border-rose-500/50 shadow-[0_0_15px_rgba(244,63,94,0.35)]">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-500 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                </span>
                                NO PACKAGE TAKEN
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2.5 rounded-xl bg-black/80 border border-emerald-500/40 text-emerald-300 font-bold text-xs uppercase tracking-wider hover:bg-emerald-500/20 transition flex items-center gap-1.5">
                    <i data-lucide="edit-3" class="w-4 h-4 text-emerald-400"></i> Edit Profile
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 font-mono text-xs">
            <div class="p-3.5 rounded-2xl bg-black/80 border border-emerald-500/30">
                <span class="text-neutral-400 block text-[10px] uppercase font-sans font-bold">Deposit Wallet:</span>
                <strong class="text-teal-300 font-black text-sm block">${{ number_format((float)$user->deposit_wallet, 2) }}</strong>
            </div>

            <div class="p-3.5 rounded-2xl bg-black/80 border border-emerald-500/30">
                <span class="text-neutral-400 block text-[10px] uppercase font-sans font-bold">Earning Wallet:</span>
                <strong class="text-emerald-400 font-black text-sm block">${{ number_format((float)$user->earning_wallet, 2) }}</strong>
            </div>

            <div class="p-3.5 rounded-2xl bg-black/80 border border-emerald-500/30">
                <span class="text-neutral-400 block text-[10px] uppercase font-sans font-bold">Direct Members:</span>
                <strong class="text-white font-black text-sm block">{{ $directCount }} Referrals</strong>
            </div>

            <div class="p-3.5 rounded-2xl bg-black/80 border border-emerald-500/30">
                <span class="text-neutral-400 block text-[10px] uppercase font-sans font-bold">USDT Withdrawal Address:</span>
                <strong class="text-emerald-400 font-black text-xs block truncate" title="{{ $user->wallet_address ?? 'Not Set' }}">{{ $user->wallet_address ?? 'NOT SET YET' }}</strong>
            </div>
        </div>
    </div>

    <!-- Admin Add/Deduct Wallet Direct Controls -->
    <div class="p-6 rounded-3xl bg-[#042718] border border-emerald-500/30 space-y-4 shadow-xl">
        <div class="flex items-center gap-2 border-b border-emerald-500/20 pb-3">
            <i data-lucide="wallet-cards" class="w-5 h-5 text-emerald-400"></i>
            <h2 class="text-base font-black text-white uppercase tracking-tight font-heading">ADMIN DIRECT WALLET ADJUSTMENT</h2>
        </div>
        <p class="text-xs text-neutral-300">Credit Fund Wallet or Earning Wallet directly for member {{ $user->name }}</p>

        <form action="{{ route('admin.users.add-fund', $user) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Select Target Wallet *</label>
                <select name="wallet_type" required class="w-full px-4 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400" style="background-color: #01140c !important;">
                    <option value="deposit_wallet">Fund Wallet</option>
                    <option value="earning_wallet">Earning Wallet</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-emerald-400 uppercase mb-1">Amount ($) *</label>
                <input type="number" step="0.01" name="amount" required placeholder="100.00" class="w-full px-4 py-2.5 rounded-xl bg-black border border-emerald-500/40 text-white font-mono text-xs focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-emerald-400 uppercase mb-1">Admin Remark</label>
                <input type="text" name="remark" placeholder="e.g. Special Fund Addition" class="w-full px-4 py-2.5 rounded-xl bg-black border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none">
            </div>

            <div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-black text-xs uppercase tracking-wider shadow-lg transition">
                    Credit Fund
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
