@extends('admin.layouts.app')

@section('title', 'Admin Dashboard Overview - ZIVO PAY')

@section('content')
    <style>
        .admin-card-glow {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.95) 0%, rgba(3, 28, 18, 0.95) 100%) !important;
            border: 2px solid rgba(16, 185, 129, 0.4) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.8), 0 0 20px rgba(16, 185, 129, 0.15) !important;
            transition: all 0.3s ease !important;
        }

        .admin-card-glow:hover {
            border-color: rgba(16, 185, 129, 0.8) !important;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.9), 0 0 30px rgba(16, 185, 129, 0.25) !important;
            transform: translateY(-2px);
        }
    </style>

    <div class="w-full space-y-6 font-sans relative">

        <!-- Ambient Emerald Radial Glow Decorator -->
        <div
            class="absolute -top-24 left-1/2 -translate-x-1/2 w-[900px] h-[450px] bg-emerald-500/10 blur-[130px] pointer-events-none rounded-full">
        </div>

        <!-- Top Header Banner -->
        <div
            class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 p-6 sm:p-8 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl relative z-10">
            <div>
                <div class="text-[11px] font-black text-emerald-400 uppercase tracking-widest mb-1 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    SUPER ADMIN CONTROL CENTER • ZIVO PAY
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white font-heading uppercase tracking-tight">PLATFORM
                    FINANCIAL & NETWORK OVERVIEW</h1>
                <p class="text-xs text-neutral-300 mt-1">Real-time statistics for ₹3,000 package activations, Fund Wallet
                    idle returns, capital investments, and member downlines.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">


                <a href="{{ route('admin.users') }}"
                    class="px-4 py-3 rounded-xl bg-[#02180f] border border-emerald-500/40 text-emerald-300 font-bold text-xs uppercase hover:bg-emerald-500/20 transition flex items-center gap-1.5">
                    <i data-lucide="users" class="w-4 h-4"></i> Users
                </a>
            </div>
        </div>

        @if(session('success'))
            <div
                class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2 relative z-10">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- STATS ROW 1: Financial & Wallet Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 relative z-10">

            <!-- Total Fund Wallets Sum -->
            <div class="p-5 rounded-3xl admin-card-glow space-y-2">
                <div class="flex items-center justify-between text-neutral-400 text-xs font-extrabold uppercase">
                    <span>TOTAL FUND WALLETS</span>
                    <div
                        class="w-9 h-9 rounded-xl bg-teal-500/20 text-teal-300 flex items-center justify-center border border-teal-500/30">
                        <i data-lucide="wallet" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-teal-300 font-mono">₹{{ number_format($totalDepositWalletSum, 2) }}
                </div>
                <p class="text-[11px] text-neutral-400">Total member capital available for activation & investments</p>
            </div>

            <!-- Total Earning Wallets Sum -->
            <div class="p-5 rounded-3xl admin-card-glow space-y-2">
                <div class="flex items-center justify-between text-neutral-400 text-xs font-extrabold uppercase">
                    <span>TOTAL EARNING WALLETS</span>
                    <div
                        class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                        <i data-lucide="coins" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-emerald-400 font-mono">₹{{ number_format($totalEarningWalletSum, 2) }}
                </div>
                <p class="text-[11px] text-neutral-400">Total member accumulated earnings</p>
            </div>

            <!-- ₹3,000 Package Activation Revenue -->
            <div class="p-5 rounded-3xl admin-card-glow space-y-2">
                <div class="flex items-center justify-between text-neutral-400 text-xs font-extrabold uppercase">
                    <span>₹3,000 PACKAGE REVENUE</span>
                    <div
                        class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                        <i data-lucide="package-check" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-white font-mono">₹{{ number_format($subscriptionRevenue, 2) }}</div>
                <p class="text-[11px] text-emerald-400 font-semibold">{{ number_format($subscribedMembers) }} Members
                    Activated (₹3,000 Package)</p>
            </div>

            <!-- Active Capital Invested -->
            <div class="p-5 rounded-3xl admin-card-glow space-y-2">
                <div class="flex items-center justify-between text-neutral-400 text-xs font-extrabold uppercase">
                    <span>ACTIVE CAPITAL INVESTED</span>
                    <div
                        class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                        <i data-lucide="trending-up" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-emerald-300 font-mono">₹{{ number_format($totalInvestmentsSum, 2) }}
                </div>
                <p class="text-[11px] text-neutral-400">Capital packages earning 0.15% to 0.30% daily ROI</p>
            </div>
        </div>

        <!-- STATS ROW 2: Network & Payouts Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 relative z-10">

            <!-- Total Registered Members -->
            <div class="p-5 rounded-3xl admin-card-glow space-y-2">
                <div class="flex items-center justify-between text-neutral-400 text-xs font-extrabold uppercase">
                    <span>TOTAL MEMBERS</span>
                    <div
                        class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-white font-heading">{{ number_format($totalMembers) }}</div>
                <p class="text-[11px] text-emerald-400 font-semibold">{{ number_format($activeMembers) }} Active •
                    {{ number_format($inactiveMembers) }} Inactive</p>
            </div>

            <!-- Daily ROI Distributed -->
            <div class="p-5 rounded-3xl admin-card-glow space-y-2">
                <div class="flex items-center justify-between text-neutral-400 text-xs font-extrabold uppercase">
                    <span>DAILY ROI PAID OUT</span>
                    <div
                        class="w-9 h-9 rounded-xl bg-teal-500/20 text-teal-300 flex items-center justify-center border border-teal-500/30">
                        <i data-lucide="award" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-teal-300 font-mono">₹{{ number_format($totalRoiDistributed, 2) }}</div>
                <p class="text-[11px] text-neutral-400">Cumulative daily ROI returns credited to members</p>
            </div>

            <!-- Total Commissions Paid -->
            <div class="p-5 rounded-3xl admin-card-glow space-y-2">
                <div class="flex items-center justify-between text-neutral-400 text-xs font-extrabold uppercase">
                    <span>TOTAL COMMISSIONS PAID</span>
                    <div
                        class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                        <i data-lucide="share-2" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-emerald-400 font-mono">₹{{ number_format($totalCommissionsPaid, 2) }}
                </div>
                <p class="text-[11px] text-neutral-400">15-Level Referral & 26% ROI Matching Level Income</p>
            </div>

            <!-- Subscribed Members Count -->
            <div class="p-5 rounded-3xl admin-card-glow space-y-2">
                <div class="flex items-center justify-between text-neutral-400 text-xs font-extrabold uppercase">
                    <span>ACTIVE PACKAGE SUBSCRIBERS</span>
                    <div
                        class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                        <i data-lucide="user-check" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-emerald-300 font-mono">{{ number_format($subscribedMembers) }}</div>
                <p class="text-[11px] text-emerald-400 font-semibold">₹3,000 Activated Accounts</p>
            </div>
        </div>

        <!-- ROW 1 (col-sm-6 & col-sm-6 side-by-side): Recent Registrations + Recent Package Activations -->
        <div class="grid grid-cols-2 gap-6 relative z-10 grid-2-col">

            <!-- CARD 1 (col-sm-6): Recent Member Registrations -->
            <div class="p-5 sm:p-6 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-emerald-500/20 pb-3.5">
                    <div>
                        <h3 class="text-sm font-black text-white uppercase tracking-wide flex items-center gap-2">
                            <i data-lucide="user-plus" class="w-4 h-4 text-emerald-400"></i>
                            Recent Users
                        </h3>
                        <p class="text-[11px] text-neutral-400">Newly registered platform members</p>
                    </div>
                    <a href="{{ route('admin.users') }}"
                        class="text-[11px] text-emerald-400 font-bold hover:underline flex items-center gap-1">
                        View All &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead
                            class="bg-[#02180f] text-emerald-400 uppercase font-black text-[10px] tracking-wider border-b border-emerald-500/20">
                            <tr>
                                <th class="p-2.5">Member</th>
                                <th class="p-2.5">Code</th>
                                <th class="p-2.5">Package</th>
                                <th class="p-2.5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-emerald-500/10 font-medium">
                            @forelse($recentUsers as $member)
                                <tr class="hover:bg-emerald-500/5 transition">
                                    <td class="p-2.5">
                                        <span
                                            class="font-bold text-white block text-xs truncate max-w-[120px] sm:max-w-[150px]">{{ $member->name }}</span>
                                        <span
                                            class="text-[10px] text-neutral-400 font-mono block truncate max-w-[120px] sm:max-w-[150px]">{{ $member->email }}</span>
                                    </td>
                                    <td class="p-2.5 font-mono font-bold text-emerald-400 text-xs">{{ $member->referral_code }}
                                    </td>
                                    <td class="p-2.5">
                                        @if($member->is_subscription_active)
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[9px] font-black border border-emerald-500/50">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                                ₹3,000 ACTIVE
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 text-[9px] font-black border border-rose-500/50">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                INACTIVE
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-2.5 text-right">
                                        <a href="{{ route('admin.users.show', $member) }}"
                                            class="px-2 py-1 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[10px] font-bold hover:bg-emerald-500 hover:text-black transition">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-neutral-400 font-bold text-xs">No member
                                        registrations found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CARD 2 (col-sm-6): Recent Package Activations (₹3,000) -->
            <div class="p-5 sm:p-6 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-emerald-500/20 pb-3.5">
                    <div>
                        <h3 class="text-sm font-black text-white uppercase tracking-wide flex items-center gap-2">
                            <i data-lucide="zap" class="w-4 h-4 text-emerald-400"></i>
                            Recent Package Activations
                        </h3>
                        <p class="text-[11px] text-neutral-400">Members with active ₹3,000 package</p>
                    </div>
                    <span class="text-[11px] text-emerald-400 font-bold flex items-center gap-1">
                        Active ({{ $subscribedMembers }})
                    </span>
                </div>

                <div class="space-y-2.5">
                    @forelse($recentSubscriptions as $subMember)
                        <div
                            class="p-3 rounded-2xl bg-[#01140c] border border-emerald-500/20 flex items-center justify-between gap-3 hover:border-emerald-500/40 transition">
                            <div>
                                <span class="font-bold text-white text-xs block">{{ $subMember->name }}</span>
                                <span
                                    class="text-[10px] text-emerald-400 font-mono block">{{ $subMember->referral_code }}</span>
                                <span
                                    class="text-[10px] text-neutral-400 block">{{ $subMember->activated_at ? $subMember->activated_at->format('d M Y, h:i A') : 'Active' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs sm:text-sm font-mono font-black text-emerald-400 block">₹3,000.00</span>
                                <span
                                    class="inline-block mt-1 px-2.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[10px] font-bold">
                                    Package Active
                                </span>
                            </div>
                        </div>
                    @empty
                        <div
                            class="p-6 text-center text-neutral-400 text-xs font-bold bg-[#01140c] rounded-2xl border border-emerald-500/20">
                            No package activations recorded yet.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
@endsection