@extends('user.layouts.app')

@section('title', 'Wallet Transfer & Account Activation - ZIVO PAY')

@section('content')
<div class="space-y-6 font-sans">
    <!-- Header & Wallet Overview -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-2xl space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[11px] font-black uppercase tracking-widest border border-emerald-500/40">
                    WALLET MANAGEMENT & ACTIVATION
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight mt-1">INTERNAL WALLET TRANSFER</h1>
                <p class="text-xs text-neutral-300 mt-1">Transfer funds instantly from Earning Wallet to Fund Wallet or activate your mandatory ₹3,000 subscription.</p>
            </div>
        </div>

        <!-- Wallet Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
            <!-- 1. Fund Wallet -->
            <div class="p-5 rounded-2xl bg-[#02180f] border-2 border-emerald-500/40 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Fund Wallet Balance</span>
                    <i data-lucide="wallet" class="w-5 h-5 text-emerald-400"></i>
                </div>
                <span class="text-3xl font-black text-white font-mono mt-2 block">₹{{ number_format($user->deposit_wallet, 2) }}</span>
                <span class="text-[10px] text-neutral-400 mt-1 block">Used for Package Activation & Capital Investments</span>
            </div>

            <!-- 2. Earning Wallet -->
            <div class="p-5 rounded-2xl bg-[#02180f] border-2 border-emerald-500/40 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-teal-400 uppercase tracking-wider">Earning Wallet Balance</span>
                    <i data-lucide="coins" class="w-5 h-5 text-teal-400"></i>
                </div>
                <span class="text-3xl font-black text-teal-300 font-mono mt-2 block">₹{{ number_format($user->earning_wallet, 2) }}</span>
                <span class="text-[10px] text-neutral-400 mt-1 block">Accumulated ROI & Level Referral Bonus</span>
            </div>

            <!-- 3. Account Activation Status -->
            <div class="p-5 rounded-2xl bg-[#02180f] border-2 {{ $user->is_subscription_active ? 'border-emerald-500/50' : 'border-rose-500/50' }} relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-neutral-300 uppercase tracking-wider">Subscription Status</span>
                    <i data-lucide="{{ $user->is_subscription_active ? 'check-circle-2' : 'alert-circle' }}" class="w-5 h-5 {{ $user->is_subscription_active ? 'text-emerald-400' : 'text-rose-400' }}"></i>
                </div>
                <div class="mt-2">
                    @if($user->is_subscription_active)
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 inline-block">
                            ACTIVE (₹3,000 PAID)
                        </span>
                        <span class="text-[10px] text-neutral-400 mt-1 block">Activated: {{ $user->activated_at ? $user->activated_at->format('d M Y, h:i A') : 'N/A' }}</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/40 inline-block">
                            INACTIVE (₹3,000 REQUIRED)
                        </span>
                        <span class="text-[10px] text-neutral-400 mt-1 block">Mandatory subscription pending</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="p-4 rounded-2xl bg-teal-500/20 border border-teal-500/50 text-teal-300 text-xs font-bold flex items-center gap-2">
            <i data-lucide="info" class="w-4 h-4 text-teal-300"></i>
            {{ session('info') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/20 border border-rose-500/50 text-rose-300 text-xs font-bold space-y-1">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i data-lucide="x-circle" class="w-4 h-4 text-rose-400"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Section A: Mandatory ₹3,000 Subscription Activation Card -->
        <div class="p-6 rounded-3xl bg-[#042718] border-2 border-emerald-500/40 shadow-xl space-y-4">
            <div class="flex items-center gap-3 border-b border-emerald-500/20 pb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black text-white uppercase">MANDATORY ₹3,000 ZIVO FAMILY KIT ACTIVATION</h2>
                    <p class="text-xs text-neutral-400">One-time account activation package fee deducted from Fund Wallet.</p>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-[#02180f] border border-emerald-500/30 text-xs space-y-2">
                <div class="flex items-center justify-between text-neutral-300">
                    <span>Package Price (Zivo Family Kit):</span>
                    <span class="font-bold text-white text-sm">₹3,000.00</span>
                </div>
                <div class="flex items-center justify-between text-neutral-300">
                    <span>Available Fund Wallet Balance:</span>
                    <span class="font-bold text-emerald-400 text-sm">₹{{ number_format($user->deposit_wallet, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-neutral-300">
                    <span>Account Activation Status:</span>
                    <span class="font-bold {{ $user->is_subscription_active ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ $user->is_subscription_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <p class="text-[11px] text-neutral-400">
                <i data-lucide="info" class="w-3.5 h-3.5 inline text-emerald-400"></i>
                Activating your ₹3,000 Zivo Family Kit package unlocks 15-Level Direct & Team Referral Bonuses (5% L1, 0.5% L2-L15) and enables 24-hour Fund Wallet Compound Profit!
            </p>

            @if(!$user->is_subscription_active)
                <form action="{{ route('user.wallet.subscription.activate') }}" method="POST">
                    @csrf
                    <button type="submit" 
                        {{ $user->deposit_wallet < 3000 ? 'disabled' : '' }}
                        class="w-full py-3.5 rounded-xl font-black text-xs uppercase tracking-wider transition flex items-center justify-center gap-2 shadow-lg {{ $user->deposit_wallet >= 3000 ? 'bg-gradient-to-r from-emerald-500 to-teal-400 hover:scale-[1.02] text-black cursor-pointer' : 'bg-neutral-800 text-neutral-500 border border-neutral-700 cursor-not-allowed' }}">
                        <i data-lucide="zap" class="w-4 h-4"></i>
                        ACTIVATE ACCOUNT NOW (₹3,000)
                    </button>
                </form>
                @if($user->deposit_wallet < 3000)
                    <p class="text-[10px] text-rose-400 text-center font-semibold">
                        Insufficient Fund Wallet balance. Please add at least ₹3,000 to your Fund Wallet to activate your account.
                    </p>
                @endif
            @else
                <div class="p-3.5 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold text-center flex items-center justify-center gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i>
                    Your ₹3,000 Zivo Family Kit Package is Active! Account Activated.
                </div>
            @endif
        </div>

        <!-- Section B: Internal Wallet Transfer (Earning -> Fund) -->
        <div class="p-6 rounded-3xl bg-[#042718] border-2 border-emerald-500/40 shadow-xl space-y-4">
            <div class="flex items-center gap-3 border-b border-emerald-500/20 pb-4">
                <div class="w-10 h-10 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center font-bold">
                    <i data-lucide="arrow-right-left" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black text-white uppercase">EARNING TO FUND WALLET TRANSFER</h2>
                    <p class="text-xs text-neutral-400">Convert earned income to Fund Wallet balance for re-investment.</p>
                </div>
            </div>

            <form action="{{ route('user.wallet.transfer.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2">Transfer Amount (₹)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 font-bold">₹</span>
                        <input type="number" step="0.01" min="10" name="amount" placeholder="Minimum ₹10" required class="w-full pl-9 pr-4 py-3 rounded-xl bg-[#01140c] border border-emerald-500/30 text-white font-bold text-sm focus:outline-none focus:border-emerald-400">
                    </div>
                    <span class="text-[10px] text-neutral-400 mt-1 block">Available Earning Balance: ₹{{ number_format($user->earning_wallet, 2) }}</span>
                </div>

                <div class="p-3.5 rounded-xl bg-[#02180f] border border-emerald-500/20 text-[11px] text-neutral-300 space-y-1">
                    <div class="flex justify-between">
                        <span>From:</span>
                        <span class="font-bold text-teal-300">Earning Wallet</span>
                    </div>
                    <div class="flex justify-between">
                        <span>To:</span>
                        <span class="font-bold text-emerald-400">Fund Wallet</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Transfer Fee:</span>
                        <span class="font-bold text-emerald-400">₹0.00 (Instant & Free)</span>
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-black font-black text-xs uppercase tracking-wider shadow-lg hover:scale-[1.02] transition flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    CONFIRM INSTANT TRANSFER
                </button>
            </form>
        </div>
    </div>

    <!-- Section C: Transfer Audit Log -->
    <div class="bg-[#042718] rounded-3xl border border-emerald-500/30 overflow-hidden shadow-2xl p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-emerald-500/20 pb-4">
            <h3 class="text-base font-black text-white uppercase tracking-wider">WALLET TRANSFER HISTORY</h3>
            <span class="text-xs text-emerald-400 font-bold">Instant Transfers Log</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-emerald-400 font-extrabold uppercase tracking-wider border-b border-emerald-500/30">
                    <tr>
                        <th class="px-5 py-4">Transfer ID</th>
                        <th class="px-5 py-4">From Wallet</th>
                        <th class="px-5 py-4">To Wallet</th>
                        <th class="px-5 py-4">Amount</th>
                        <th class="px-5 py-4">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/10 text-neutral-200 font-medium">
                    @forelse($transfers as $transfer)
                        <tr class="hover:bg-emerald-500/5 transition">
                            <td class="px-5 py-4 font-mono text-emerald-300 font-bold">
                                #TRF-{{ $transfer->id }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-teal-500/20 text-teal-300 border border-teal-500/40">
                                    {{ str_replace('_', ' ', $transfer->from_wallet) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                                    {{ str_replace('_', ' ', $transfer->to_wallet) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-bold text-white text-sm">
                                ₹{{ number_format($transfer->amount, 2) }}
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-white font-bold text-xs">{{ $transfer->created_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-neutral-400 font-mono">{{ $transfer->created_at->format('h:i:s A') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-neutral-400">
                                <i data-lucide="arrow-right-left" class="w-8 h-8 mx-auto mb-2 text-emerald-500/40"></i>
                                <p class="font-bold">No wallet transfer history recorded yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transfers->hasPages())
            <div class="pt-2">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
