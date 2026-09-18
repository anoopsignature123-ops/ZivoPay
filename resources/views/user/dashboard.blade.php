@extends('user.layouts.app')

@section('title', 'User Dashboard Overview - ZIVO PAY')

@section('content')
    <style>
        /* High-Tech Emerald Glow & Entrance Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }

        .zivo-emerald-card {
            background: linear-gradient(180deg, #042718 0%, #021a10 60%, #01120b 100%) !important;
            border: 1.5px solid rgba(16, 185, 129, 0.65) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.85), inset 0 1px 1px rgba(255, 255, 255, 0.1) !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        .zivo-emerald-card:hover {
            transform: translateY(-3px) scale(1.01) !important;
            border-color: #34d399 !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.95), 0 0 25px rgba(52, 211, 153, 0.4) !important;
        }

        .zivo-pill-emerald {
            background: linear-gradient(90deg, #059669 0%, #10b981 50%, #047857 100%) !important;
            color: #ffffff !important;
            font-weight: 900 !important;
            letter-spacing: 0.5px !important;
            box-shadow: 0 3px 10px rgba(16, 185, 129, 0.4) !important;
            transition: all 0.2s ease !important;
        }

        .zivo-pill-emerald:hover {
            background: linear-gradient(90deg, #10b981 0%, #34d399 50%, #059669 100%) !important;
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.6) !important;
        }

        .zivo-pill-outline {
            background: rgba(0, 0, 0, 0.7) !important;
            border: 1.2px solid rgba(16, 185, 129, 0.5) !important;
            color: #6ee7b7 !important;
            font-weight: 800 !important;
        }

        .zivo-pill-outline:hover {
            border-color: #34d399 !important;
            background: rgba(16, 185, 129, 0.15) !important;
            color: #ffffff !important;
        }

        .zivo-icon-box {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #34d399;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
        }

        @keyframes growthCardFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        .growth-hub {
            position: relative;
            overflow: hidden;
            padding: 1.25rem;
            border: 1px solid rgba(52, 211, 153, 0.24);
            border-radius: 1.5rem;
            background: linear-gradient(135deg, rgba(1, 30, 18, 0.86), rgba(0, 12, 8, 0.92));
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.28);
        }

        .growth-hub::before {
            content: '';
            position: absolute;
            top: -70px;
            left: 50%;
            width: 380px;
            height: 150px;
            transform: translateX(-50%);
            border-radius: 50%;
            background: radial-gradient(ellipse, rgba(52, 211, 153, 0.28), transparent 68%);
            filter: blur(14px);
            pointer-events: none;
        }

        .growth-card {
            position: relative;
            min-height: 155px;
            overflow: hidden;
            border: 1px solid rgba(110, 231, 183, 0.16);
            border-radius: 1.1rem;
            background: linear-gradient(145deg, rgba(8, 42, 27, 0.68), rgba(1, 16, 10, 0.9));
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05), 0 12px 24px rgba(0, 0, 0, 0.28);
            transition: transform .35s cubic-bezier(.16, 1, .3, 1), border-color .35s ease, box-shadow .35s ease;
        }

        .growth-card::after {
            content: '';
            position: absolute;
            right: -22px;
            bottom: -34px;
            width: 100px;
            height: 76px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(52, 211, 153, 0.8), rgba(16, 185, 129, 0.12) 46%, transparent 70%);
            filter: blur(8px);
            transition: transform .35s ease;
        }

        .growth-card:hover {
            transform: translateY(-6px);
            border-color: rgba(110, 231, 183, 0.48);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1), 0 18px 35px rgba(0, 0, 0, 0.38), 0 0 25px rgba(16, 185, 129, 0.16);
        }

        .growth-card:hover::after { transform: scale(1.25); }
        .growth-card:nth-child(2) { animation: growthCardFloat 5s ease-in-out infinite 0.5s; }

        @media (prefers-reduced-motion: reduce) {
            .growth-card:nth-child(2) { animation: none; }
            .growth-card:hover { transform: none; }
        }
    </style>

    <div class="w-full space-y-5 font-sans relative select-none animate-fade-in-up">

        <!-- Ambient Glow Background Decorator -->
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-[950px] h-[400px] bg-emerald-500/10 blur-[130px] pointer-events-none rounded-full"></div>

        <!-- 1. TOP HEADER BANNER (WELCOME BACK) -->
        <div class="p-4 sm:p-5 rounded-3xl zivo-emerald-card flex flex-col md:flex-row items-start md:items-center justify-between gap-4 relative z-10">
            <div>
                <div class="flex items-center gap-2 text-emerald-400 text-xs font-bold uppercase tracking-widest mb-1">
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
                    <span class="text-neutral-400">• ZIVO PAY FINANCIAL PORTAL</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white font-heading tracking-tight">
                    Welcome Back, <span class="text-emerald-400">{{ $user->name }}</span>
                </h1>
                <p class="text-xs text-neutral-300 mt-1 font-mono">
                    Referral Code: <span class="text-emerald-300 font-bold">{{ $user->referral_code }}</span> 
                    &bull; Sponsor: <span class="text-teal-300 font-bold">{{ $user->sponsor_code ?? 'None' }}</span>
                    &bull; Status: 
                    @if($user->is_subscription_active)
                        <span class="text-emerald-400 font-black uppercase">ACTIVE MEMBER</span>
                    @else
                        <span class="text-rose-400 font-black uppercase">INACTIVE MEMBER</span>
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <span class="px-3 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/60 text-emerald-300 font-black text-xs uppercase flex items-center gap-1.5 shadow">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>ZIVO PAY LIVE ⚡</span>
                </span>
                <span class="px-3.5 py-1.5 rounded-xl bg-[#01140c] border border-emerald-500/40 text-emerald-300 text-xs font-mono font-bold flex items-center gap-1.5 shadow">
                    <i data-lucide="calendar" class="w-4 h-4 text-emerald-400"></i>
                    <span>{{ now()->format('l, d M Y') }}</span>
                </span>
            </div>
        </div>

        <!-- 2. SMART GROWTH HUB -->
        <section class="growth-hub relative z-10">
            <div class="relative mb-4 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.22em] text-emerald-400">Your growth space</p>
                    <h2 class="mt-1 text-lg font-black text-white sm:text-xl">Build. Share. Earn rewards.</h2>
                </div>
                <p class="text-xs text-emerald-100/60">Everything you need, in one place.</p>
            </div>
            <div class="relative grid grid-cols-1 gap-3 sm:grid-cols-3">
                <a href="{{ route('user.investment.index') }}" class="growth-card block p-4">
                    <div class="relative z-10 flex h-full flex-col justify-between">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl border border-emerald-300/25 bg-emerald-400/10 text-emerald-300"><i data-lucide="rocket" class="h-4 w-4"></i></div>
                        <div><h3 class="text-sm font-black text-white">Activate & Grow</h3><p class="mt-1 max-w-[13rem] text-[11px] leading-4 text-emerald-50/65">Choose your plan and start your growth journey.</p></div>
                    </div>
                </a>
                <a href="{{ route('user.network.direct') }}" class="growth-card block p-4">
                    <div class="relative z-10 flex h-full flex-col justify-between">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl border border-emerald-300/25 bg-emerald-400/10 text-emerald-300"><i data-lucide="share-2" class="h-4 w-4"></i></div>
                        <div><h3 class="text-sm font-black text-white">Grow Your Network</h3><p class="mt-1 max-w-[13rem] text-[11px] leading-4 text-emerald-50/65">Share your referral link and build your team.</p></div>
                    </div>
                </a>
                <a href="{{ route('user.rewards.index') }}" class="growth-card block p-4">
                    <div class="relative z-10 flex h-full flex-col justify-between">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl border border-emerald-300/25 bg-emerald-400/10 text-emerald-300"><i data-lucide="trophy" class="h-4 w-4"></i></div>
                        <div><h3 class="text-sm font-black text-white">Unlock Rewards</h3><p class="mt-1 max-w-[13rem] text-[11px] leading-4 text-emerald-50/65">Track milestones and unlock your next reward.</p></div>
                    </div>
                </a>
            </div>
        </section>

        <!-- 2. TOP 2 ACTION CARDS (PACKAGE ACTIVATION STATUS + OFFICIAL REFERRAL LINK) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 relative z-10 grid-2-col">
            
            <!-- Package Status Card -->
            <div class="p-4 rounded-2xl zivo-emerald-card flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="zivo-icon-box">
                        @if($user->is_subscription_active)
                            <i data-lucide="package-check" class="w-5 h-5 text-emerald-400"></i>
                        @else
                            <i data-lucide="package-x" class="w-5 h-5 text-rose-400"></i>
                        @endif
                    </div>
                    <div class="min-w-0">
                        @if($user->is_subscription_active)
                            <h4 class="text-xs sm:text-sm font-black text-emerald-300 uppercase tracking-wide font-heading truncate">
                                ⚡ ₹3,000 PACKAGE ACTIVE & WORKING
                            </h4>
                            <p class="text-[11px] text-neutral-300 truncate">
                                Activated on {{ $user->created_at ? $user->created_at->format('M d, Y H:i') : 'N/A' }}. 15-Level Referral & ROI Active.
                            </p>
                        @else
                            <h4 class="text-xs sm:text-sm font-black text-rose-300 uppercase tracking-wide font-heading truncate">
                                ⚠️ NO PACKAGE ACTIVATED YET
                            </h4>
                            <p class="text-[11px] text-neutral-300 truncate">
                                Activate ₹3,000 package to unlock 15-level referral & daily ROI incomes.
                            </p>
                        @endif
                    </div>
                </div>
                @if($user->is_subscription_active)
                    <a href="{{ route('user.investment.index') }}" class="px-4 py-2 rounded-full border border-emerald-400/60 bg-emerald-500/10 hover:bg-emerald-500/30 text-emerald-300 font-extrabold text-xs uppercase tracking-wider transition shrink-0 shadow">
                        MY PACKAGES &rarr;
                    </a>
                @else
                    <a href="{{ route('user.package.buy') }}" class="px-4 py-2 rounded-full zivo-pill-emerald text-white font-extrabold text-xs uppercase tracking-wider transition shrink-0 shadow">
                        BUY PACKAGE &rarr;
                    </a>
                @endif
            </div>

            <!-- Official Referral Link Card -->
            <div class="p-4 rounded-2xl zivo-emerald-card flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0 w-full">
                    <div class="zivo-icon-box">
                        <i data-lucide="link" class="w-5 h-5 text-emerald-300"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[10px] font-black text-emerald-400 uppercase tracking-wider block mb-0.5">OFFICIAL REFERRAL LINK</span>
                        <input type="text" id="dashSingleRefLink" readonly value="{{ url('/user/register?sponsor='.$user->referral_code) }}" class="w-full px-2.5 py-1 rounded-lg bg-[#01140c] border border-emerald-500/40 text-emerald-300 font-mono text-[11px] truncate focus:outline-none">
                    </div>
                </div>
                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('dashSingleRefLink').value); alert('Official referral link copied!');" class="px-4 py-2.5 rounded-full zivo-pill-emerald text-white font-black text-xs uppercase tracking-wider transition shrink-0 flex items-center justify-center gap-1.5 shadow cursor-pointer">
                    <i data-lucide="copy" class="w-4 h-4"></i> COPY LINK
                </button>
            </div>

        </div>

        <!-- 3. CORE 8 METRIC SMALL CARDS (4 CARDS PER ROW IN 2 ROWS) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 relative z-10">
            
            <!-- Card 1: Deposit Wallet -->
            <div class="p-4 rounded-2xl zivo-emerald-card space-y-2 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div class="zivo-icon-box">
                        <i data-lucide="wallet" class="w-4 h-4 text-emerald-400"></i>
                    </div>
                    <a href="{{ route('user.wallet.transfer') }}" class="px-2.5 py-1 rounded-full zivo-pill-emerald text-[10px] font-black uppercase shadow">
                        + ADD FUND
                    </a>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-neutral-400 block tracking-wider">DEPOSIT WALLET</span>
                    <div class="text-xl sm:text-2xl font-black text-white font-mono mt-0.5">₹{{ number_format($user->deposit_wallet, 2) }}</div>
                </div>
            </div>

            <!-- Card 2: Earning Wallet -->
            <div class="p-4 rounded-2xl zivo-emerald-card space-y-2 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div class="zivo-icon-box">
                        <i data-lucide="coins" class="w-4 h-4 text-emerald-400"></i>
                    </div>
                    <a href="{{ route('user.withdrawal.index') }}" class="px-3 py-1 rounded-full zivo-pill-outline text-[10px] font-bold uppercase">
                        WITHDRAW &rarr;
                    </a>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-neutral-400 block tracking-wider">EARNING WALLET</span>
                    <div class="text-xl sm:text-2xl font-black text-emerald-400 font-mono mt-0.5">₹{{ number_format($user->earning_wallet, 2) }}</div>
                </div>
            </div>

            <!-- Card 3: Withdrawal Wallet -->
            <div class="p-4 rounded-2xl zivo-emerald-card space-y-2 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div class="zivo-icon-box">
                        <i data-lucide="arrow-up-right" class="w-4 h-4 text-emerald-400"></i>
                    </div>
                    <a href="{{ route('user.withdrawal.index') }}" class="px-3 py-1 rounded-full zivo-pill-outline text-[10px] font-bold uppercase">
                        HISTORY &rarr;
                    </a>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-neutral-400 block tracking-wider">WITHDRAWAL WALLET</span>
                    <div class="text-xl sm:text-2xl font-black text-white font-mono mt-0.5">₹{{ number_format($totalWithdrawals, 2) }}</div>
                </div>
            </div>

            <!-- Card 4: Invested Capital -->
            <div class="p-4 rounded-2xl zivo-emerald-card space-y-2 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div class="zivo-icon-box">
                        <i data-lucide="layers" class="w-4 h-4 text-emerald-400"></i>
                    </div>
                    <span class="px-2.5 py-1 rounded-full zivo-pill-outline text-[10px] font-bold uppercase">
                        {{ $activeInvestments->count() }} ACTIVE
                    </span>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-neutral-400 block tracking-wider">INVESTED CAPITAL</span>
                    <div class="text-xl sm:text-2xl font-black text-white font-mono mt-0.5">₹{{ number_format($totalInvested, 2) }}</div>
                </div>
            </div>

            <!-- Card 5: Total Income Earned -->
            <div class="p-4 rounded-2xl zivo-emerald-card space-y-2 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div class="zivo-icon-box">
                        <i data-lucide="award" class="w-4 h-4 text-emerald-400"></i>
                    </div>
                    <span class="px-2.5 py-1 rounded-full zivo-pill-outline text-[10px] font-bold uppercase">
                        TOTAL EARNED
                    </span>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-neutral-400 block tracking-wider">TOTAL INCOME EARNED</span>
                    <div class="text-xl sm:text-2xl font-black text-emerald-400 font-mono mt-0.5">₹{{ number_format($totalIncomeEarned, 2) }}</div>
                </div>
            </div>

            <!-- Card 6: Direct Referrals -->
            <div class="p-4 rounded-2xl zivo-emerald-card space-y-2 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div class="zivo-icon-box">
                        <i data-lucide="user-check" class="w-4 h-4 text-emerald-400"></i>
                    </div>
                    <span class="px-2.5 py-1 rounded-full zivo-pill-outline text-[10px] font-bold uppercase">
                        {{ $activeDirectMembersCount }} ACTIVE
                    </span>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-neutral-400 block tracking-wider">DIRECT REFERRALS</span>
                    <div class="text-xl sm:text-2xl font-black text-white font-heading mt-0.5">{{ $directMembersCount }}</div>
                </div>
            </div>

            <!-- Card 7: Total Team Network -->
            <div class="p-4 rounded-2xl zivo-emerald-card space-y-2 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div class="zivo-icon-box">
                        <i data-lucide="users" class="w-4 h-4 text-emerald-400"></i>
                    </div>
                    <span class="px-2.5 py-1 rounded-full zivo-pill-outline text-[10px] font-bold uppercase">
                        {{ $totalActiveTeamCount }} ACTIVE
                    </span>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-neutral-400 block tracking-wider">TOTAL TEAM NETWORK</span>
                    <div class="text-xl sm:text-2xl font-black text-white font-heading mt-0.5">{{ $totalTeamCount }}</div>
                </div>
            </div>

            <!-- Card 8: Total Team Business Volume -->
            <div class="p-4 rounded-2xl zivo-emerald-card space-y-2 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div class="zivo-icon-box">
                        <i data-lucide="trending-up" class="w-4 h-4 text-emerald-400"></i>
                    </div>
                    <span class="px-2.5 py-1 rounded-full zivo-pill-outline text-[10px] font-bold uppercase">
                        TOTAL VOLUME
                    </span>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-neutral-400 block tracking-wider">TOTAL TEAM BUSINESS</span>
                    <div class="text-xl sm:text-2xl font-black text-emerald-400 font-mono mt-0.5">₹{{ number_format($totalTeamBusiness, 2) }}</div>
                </div>
            </div>

        </div>

        <!-- 4. SIDE-BY-SIDE 50%-50% ROW (EARNINGS SUMMARY col-sm-6  +  TEAM OVERVIEW col-sm-6) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 relative z-10 grid-2-col">

            <!-- LEFT CARD (col-sm-6): FINANCIAL OVERVIEW - Earnings Summary -->
            <div class="p-5 sm:p-6 rounded-3xl zivo-emerald-card space-y-4 flex flex-col justify-between">
                <div>
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-emerald-500/20 pb-3.5 mb-3">
                        <div>
                            <span class="text-[10px] font-black uppercase text-emerald-400 tracking-widest block">FINANCIAL OVERVIEW</span>
                            <h3 class="text-lg font-black text-white font-heading">Earnings Summary</h3>
                        </div>
                        <a href="{{ route('user.income.index') }}" class="px-3 py-1.5 rounded-full zivo-pill-outline text-xs font-bold uppercase transition flex items-center gap-1">
                            VIEW HISTORY &rarr;
                        </a>
                    </div>

                    <!-- Financial Overview Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#02180f] text-emerald-400 uppercase font-black text-[10.5px] tracking-wider border-b border-emerald-500/30">
                                <tr>
                                    <th class="py-2.5 px-3">INCOME TYPE / METRIC</th>
                                    <th class="py-2.5 px-3 text-center">TODAY INCOME</th>
                                    <th class="py-2.5 px-3 text-right">TOTAL INCOME / VOLUME</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-emerald-500/10 font-medium">
                                
                                <!-- 1. Total Team Business -->
                                <tr class="hover:bg-emerald-500/5 transition">
                                    <td class="py-2.5 px-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-xs font-bold">
                                                <i data-lucide="users" class="w-3.5 h-3.5"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-white block text-xs">Total Team Business</span>
                                                <span class="text-[9.5px] text-neutral-400 block">Direct Network: {{ $directMembersCount }} Members ({{ $activeDirectMembersCount }} Active)</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-mono text-neutral-500">-</td>
                                    <td class="py-2.5 px-3 text-right font-mono font-black text-white text-xs">₹{{ number_format($totalTeamBusiness, 2) }}</td>
                                </tr>

                                <!-- 2. Referral / Direct Income -->
                                <tr class="hover:bg-emerald-500/5 transition">
                                    <td class="py-2.5 px-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-xs font-bold">
                                                <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-white block text-xs">Referral / Direct Income</span>
                                                <span class="text-[9.5px] text-neutral-400 block">Flat 5% Instant Commission (₹150 on ₹3,000 activation)</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-mono font-bold text-emerald-300">₹{{ number_format($todayDirect, 2) }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono font-black text-emerald-400 text-xs">₹{{ number_format($totalDirectIncome, 2) }}</td>
                                </tr>

                                <!-- 3. Daily ROI Income -->
                                <tr class="hover:bg-emerald-500/5 transition">
                                    <td class="py-2.5 px-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-xs font-bold">
                                                <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-white block text-xs">Daily ROI Income</span>
                                                <span class="text-[9.5px] text-neutral-400 block">0.15%–0.30% Daily Yield (730 Days Return Cap)</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-mono font-bold text-emerald-300">₹{{ number_format($todayRoi, 2) }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono font-black text-emerald-400 text-xs">₹{{ number_format($totalRoiIncome, 2) }}</td>
                                </tr>

                                <!-- 4. 15-Level Subscription Income -->
                                <tr class="hover:bg-emerald-500/5 transition">
                                    <td class="py-2.5 px-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-xs font-bold">
                                                <i data-lucide="git-merge" class="w-3.5 h-3.5"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-white block text-xs">15-Level Subscription Income</span>
                                                <span class="text-[9.5px] text-neutral-400 block">5% L1 + 0.50% L2-L15 (Downline referral levels)</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-mono font-bold text-emerald-300">₹{{ number_format($todaySubLevel, 2) }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono font-black text-emerald-400 text-xs">₹{{ number_format($totalSubLevelIncome, 2) }}</td>
                                </tr>

                                <!-- 5. ROI Level Matching Income -->
                                <tr class="hover:bg-emerald-500/5 transition">
                                    <td class="py-2.5 px-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-xs font-bold">
                                                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-white block text-xs">ROI Level Matching Income</span>
                                                <span class="text-[9.5px] text-neutral-400 block">26% Total ROI Matching (15 Tiers ROI Matching)</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-mono font-bold text-emerald-300">₹{{ number_format($todayLevelRoi, 2) }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono font-black text-emerald-400 text-xs">₹{{ number_format($totalLevelRoiIncome, 2) }}</td>
                                </tr>

                                <!-- 6. Direct Business Reward -->
                                <tr class="hover:bg-emerald-500/5 transition">
                                    <td class="py-2.5 px-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-xs font-bold">
                                                <i data-lucide="gift" class="w-3.5 h-3.5"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-white block text-xs">Direct Business Reward</span>
                                                <span class="text-[9.5px] text-neutral-400 block">Direct turnover milestone rewards</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-mono font-bold text-emerald-300">₹{{ number_format($todayDirectReward, 2) }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono font-black text-emerald-400 text-xs">₹{{ number_format($totalDirectReward, 2) }}</td>
                                </tr>

                                <!-- 7. Team Bonus Reward -->
                                <tr class="hover:bg-emerald-500/5 transition">
                                    <td class="py-2.5 px-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-xs font-bold">
                                                <i data-lucide="crown" class="w-3.5 h-3.5"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-white block text-xs">Team Bonus Reward</span>
                                                <span class="text-[9.5px] text-neutral-400 block">Team volume leadership rewards</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-mono font-bold text-emerald-300">₹{{ number_format($todayTeamReward, 2) }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono font-black text-emerald-400 text-xs">₹{{ number_format($totalTeamReward, 2) }}</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- RIGHT CARD (col-sm-6): NETWORK OVERVIEW - Team Overview -->
            <div class="p-5 sm:p-6 rounded-3xl zivo-emerald-card space-y-4 flex flex-col justify-between">
                <div>
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-emerald-500/20 pb-3.5 mb-3">
                        <div>
                            <span class="text-[10px] font-black uppercase text-emerald-400 tracking-widest block">NETWORK OVERVIEW</span>
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-black text-white font-heading">Team Overview</h3>
                                <span class="px-2 py-0.5 rounded-full zivo-pill-emerald text-[9px] font-black uppercase shadow">LIVE NETWORK</span>
                            </div>
                        </div>
                        <a href="{{ route('user.network.direct') }}" class="px-3 py-1.5 rounded-full zivo-pill-outline text-xs font-bold uppercase transition flex items-center gap-1">
                            DIRECT TEAM &rarr;
                        </a>
                    </div>

                    <!-- Active Network Ratio Bar -->
                    <div class="p-3 rounded-2xl bg-[#02180f] border border-emerald-500/30 mb-4 space-y-1.5">
                        <div class="flex items-center justify-between text-[11px] font-extrabold uppercase">
                            <span class="text-emerald-400 flex items-center gap-1">⚡ ACTIVE NETWORK RATIO</span>
                            <span class="text-white font-mono">{{ $activeNetworkRatio }}% Active</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-[#01120b] overflow-hidden p-0.5 border border-emerald-500/30">
                            <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-500" style="width: {{ max(5, min(100, $activeNetworkRatio)) }}%;"></div>
                        </div>
                    </div>

                    <!-- Stacked Team Metric Items -->
                    <div class="space-y-2.5">
                        
                        <!-- 1. Active Directs & Business -->
                        <div class="p-3 rounded-2xl bg-[#02180f] border border-emerald-500/30 flex items-center justify-between gap-2 hover:border-emerald-400 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-400/40 flex items-center justify-center shrink-0 text-sm font-bold">
                                    <i data-lucide="user-check" class="w-4 h-4 text-emerald-400"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-white truncate">Active Directs & Business</h4>
                                    <span class="text-[10px] text-neutral-400 block font-mono">Volume: ₹{{ number_format($totalTeamBusiness, 2) }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-base font-black text-white font-mono">{{ $activeDirectMembersCount }}</span>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-400/40">ACTIVE</span>
                            </div>
                        </div>

                        <!-- 2. Inactive Direct Count -->
                        <div class="p-3 rounded-2xl bg-[#02180f] border border-emerald-500/30 flex items-center justify-between gap-2 hover:border-emerald-400 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-400/40 flex items-center justify-center shrink-0 text-sm font-bold">
                                    <i data-lucide="user-x" class="w-4 h-4 text-rose-400"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-white truncate">Inactive Direct Count</h4>
                                    <span class="text-[10px] text-neutral-400 block">Awaiting package activation</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-base font-black text-white font-mono">{{ $inactiveDirectMembersCount }}</span>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-rose-500/20 text-rose-400 border border-rose-400/40">INACTIVE</span>
                            </div>
                        </div>

                        <!-- 3. Total Team (Full Downline) -->
                        <div class="p-3 rounded-2xl bg-[#02180f] border border-emerald-500/30 flex items-center justify-between gap-2 hover:border-emerald-400 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-400/40 flex items-center justify-center shrink-0 text-sm font-bold">
                                    <i data-lucide="users" class="w-4 h-4 text-emerald-400"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-white truncate">Total Team</h4>
                                    <span class="text-[10px] text-neutral-400 block">Full downline (All Tiers)</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-base font-black text-white font-mono">{{ $totalTeamCount }}</span>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-400/40">ALL TIERS</span>
                            </div>
                        </div>

                        <!-- 4. Total Active Team -->
                        <div class="p-3 rounded-2xl bg-[#02180f] border border-emerald-500/30 flex items-center justify-between gap-2 hover:border-emerald-400 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-400/40 flex items-center justify-center shrink-0 text-sm font-bold">
                                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-white truncate">Total Active Team</h4>
                                    <span class="text-[10px] text-neutral-400 block">Active paid network members</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-base font-black text-white font-mono">{{ $totalActiveTeamCount }}</span>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-400/40">ACTIVE</span>
                            </div>
                        </div>

                        <!-- 5. Total Inactive Team -->
                        <div class="p-3 rounded-2xl bg-[#02180f] border border-emerald-500/30 flex items-center justify-between gap-2 hover:border-emerald-400 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-400/40 flex items-center justify-center shrink-0 text-sm font-bold">
                                    <i data-lucide="user-minus" class="w-4 h-4 text-rose-400"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-white truncate">Total Inactive Team</h4>
                                    <span class="text-[10px] text-neutral-400 block">Unpaid network members</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-base font-black text-white font-mono">{{ $totalInactiveTeamCount }}</span>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-rose-500/20 text-rose-400 border border-rose-400/40">INACTIVE</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- 5. LOWER SIDE-BY-SIDE 50%-50% ROW (MY ACTIVE PACKAGES col-sm-6  +  RECENT TRANSACTIONS col-sm-6) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 relative z-10 grid-2-col">

            <!-- LEFT CARD (col-sm-6): MY ACTIVE PACKAGES -->
            <div class="p-5 sm:p-6 rounded-3xl zivo-emerald-card space-y-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-emerald-500/20 pb-3.5 mb-3">
                        <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wide flex items-center gap-2 font-heading">
                            <span class="px-3 py-1 rounded-full zivo-pill-emerald text-white text-[10px] font-black uppercase">MY ACTIVE PACKAGES</span>
                        </h3>
                        <a href="{{ route('user.investment.index') }}" class="text-[11px] text-emerald-300 font-bold hover:underline flex items-center gap-1">
                            View All &rarr;
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#02180f] text-emerald-400 uppercase font-black text-[10px] tracking-wider border-b border-emerald-500/30">
                                <tr>
                                    <th class="py-2.5 px-3">PACKAGE</th>
                                    <th class="py-2.5 px-3">INVESTED</th>
                                    <th class="py-2.5 px-3">DAILY ROI</th>
                                    <th class="py-2.5 px-3 text-right">STATUS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-emerald-500/10 font-medium">
                                @forelse($activeInvestments as $investment)
                                    <tr class="hover:bg-emerald-500/5 transition">
                                        <td class="py-2.5 px-3">
                                            <span class="font-bold text-white block text-xs truncate max-w-[130px]">{{ $investment->plan_name ?? 'ZIVO CAPITAL PACKAGE' }}</span>
                                        </td>
                                        <td class="py-2.5 px-3 font-mono font-black text-white text-xs">₹{{ number_format($investment->amount, 2) }}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-emerald-300 text-xs">{{ $investment->daily_percentage ?? '0.15' }}%</td>
                                        <td class="py-2.5 px-3 text-right">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-400/40">
                                                ACTIVE
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 px-3 text-center text-neutral-400 font-bold text-xs">
                                            No active capital investments.
                                            <a href="{{ route('user.investment.index') }}" class="text-emerald-400 underline block mt-1">Activate Package &rarr;</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- RIGHT CARD (col-sm-6): RECENT TRANSACTIONS -->
            <div class="p-5 sm:p-6 rounded-3xl zivo-emerald-card space-y-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-emerald-500/20 pb-3.5 mb-3">
                        <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wide flex items-center gap-2 font-heading">
                            <span class="px-3 py-1 rounded-full zivo-pill-emerald text-white text-[10px] font-black uppercase">RECENT TRANSACTIONS</span>
                        </h3>
                        <a href="{{ route('user.income.index') }}" class="text-[11px] text-emerald-300 font-bold hover:underline flex items-center gap-1">
                            View All &rarr;
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#02180f] text-emerald-400 uppercase font-black text-[10px] tracking-wider border-b border-emerald-500/30">
                                <tr>
                                    <th class="py-2.5 px-3">TXN ID</th>
                                    <th class="py-2.5 px-3">TYPE</th>
                                    <th class="py-2.5 px-3">AMOUNT</th>
                                    <th class="py-2.5 px-3 text-right">DATE</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-emerald-500/10 font-medium">
                                @forelse($recentTransactions as $trx)
                                    <tr class="hover:bg-emerald-500/5 transition">
                                        <td class="py-2.5 px-3 font-mono font-bold text-emerald-300 text-[11px]">{{ $trx->trx_id }}</td>
                                        <td class="py-2.5 px-3">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-400/40">
                                                {{ strtoupper(str_replace('_', ' ', $trx->type)) }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 font-mono font-black text-xs {{ $trx->trx_type === '-' ? 'text-rose-400' : 'text-emerald-400' }}">
                                            {{ $trx->trx_type ?? '+' }}₹{{ number_format($trx->amount, 2) }}
                                        </td>
                                        <td class="py-2.5 px-3 text-right text-neutral-300 font-mono text-[10px]">
                                            {{ $trx->created_at ? $trx->created_at->format('M d, H:i') : '' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 px-3 text-center text-neutral-400 font-bold text-xs">No recent transaction records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
