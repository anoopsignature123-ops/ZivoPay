<!-- ======================================
     Start User Sidebar Area
     ====================================== -->
<div id="sidebarOverlay"
    class="fixed inset-0 bg-black/70 z-40 hidden lg:hidden transition-opacity duration-300 opacity-0"
    aria-hidden="true"></div>
<aside class="sidebar" id="sidebar">
    <!-- Logo Section -->
    <div class="logo-section flex items-center justify-between gap-2 px-4 py-4 border-b border-amber-500/30 shrink-0 bg-gradient-to-b from-amber-500/20 via-amber-500/5 to-transparent">
        <a href="{{ route('user.dashboard') }}" class="flex items-center justify-center flex-1 min-w-0">
            <img src="{{ asset('images/dextrade_logo.png') }}" alt="DEX TRADE Logo" class="full-logo h-12 sm:h-14 w-auto max-w-[210px] object-contain drop-shadow-[0_0_16px_rgba(243,202,82,0.9)] hover:scale-105 transition duration-300">
            <img src="{{ asset('images/dextrade_emblem.png') }}" alt="DEX TRADE Emblem" class="mini-logo hidden w-10 h-10 object-contain drop-shadow-[0_0_15px_rgba(243,202,82,0.9)] hover:scale-110 transition duration-300 mx-auto">
        </a>
        <button
            class="lg:hidden! flex w-8 h-8 items-center justify-center rounded-lg text-amber-400 hover:bg-amber-500/20 transition js-mobile-menu-toggle shrink-0"
            aria-label="Close sidebar">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="py-4 flex-1 overflow-y-auto space-y-1">
        
        <!-- 0. MAIN OVERVIEW SECTION -->
        <div class="nav-section-title px-5 pt-3 pb-2 text-[10px] font-black uppercase tracking-[2px] text-amber-400/70">
        Dashboard
        </div>

        <!-- Dashboard Link -->
        <a class='nav-item {{ request()->routeIs("user.dashboard") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.dashboard") }}'>
            <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">Dashboard</span>
        </a>

        <style>
            @keyframes arbitrageShimmer {
                0% { background-position: -200% 0; }
                100% { background-position: 200% 0; }
            }

            @keyframes activeGlowPulse {
                0%, 100% {
                    box-shadow: 0 0 18px rgba(243, 202, 82, 0.8), 0 0 35px rgba(212, 175, 55, 0.5);
                }
                50% {
                    box-shadow: 0 0 30px rgba(243, 202, 82, 1), 0 0 48px rgba(0, 230, 118, 0.6);
                }
            }

            .arbitrage-pill-active {
                background: linear-gradient(90deg, #d4af37 0%, #fef08a 35%, #f3ca52 65%, #d4af37 100%) !important;
                background-size: 200% 100% !important;
                animation: arbitrageShimmer 4s infinite linear, activeGlowPulse 2.5s infinite ease-in-out !important;
                border-radius: 9999px !important;
                border: 2px solid #ffffff !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            .arbitrage-pill-active > .flex > .nav-text,
            .arbitrage-pill-active > .flex > i,
            .arbitrage-pill-active > .flex > svg {
                color: #000000 !important;
                font-weight: 900 !important;
                stroke: #000000 !important;
            }
            .arbitrage-pill-active:hover {
                transform: scale(1.04) !important;
            }

            .arbitrage-pill-inactive {
                background: rgba(6, 56, 36, 0.75) !important;
                border: 1.5px solid rgba(243, 202, 82, 0.75) !important;
                border-radius: 9999px !important;
                transition: all 0.25s ease-in-out !important;
            }
            .arbitrage-pill-inactive > .flex > .nav-text {
                color: #f3ca52 !important;
                font-weight: 800 !important;
            }
            .arbitrage-pill-inactive > .flex > i,
            .arbitrage-pill-inactive > .flex > svg {
                color: #f3ca52 !important;
                stroke: #f3ca52 !important;
            }
            .arbitrage-pill-inactive:hover {
                background: rgba(243, 202, 82, 0.25) !important;
                border-color: #f3ca52 !important;
                box-shadow: 0 0 20px rgba(243, 202, 82, 0.5) !important;
                transform: translateX(4px) scale(1.02) !important;
            }

            .badge-live-pulse {
                background: #000000 !important;
                color: #fef08a !important;
                border: 1px solid rgba(243, 202, 82, 0.6) !important;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.6) !important;
            }
            .badge-live-pulse * {
                color: #fef08a !important;
            }
        </style>

        <!-- Arbitrage Link -->
        @if(request()->routeIs('user.arbitrage'))
            <a class='nav-item arbitrage-pill-active flex items-center justify-between gap-2 mx-3 my-1.5 px-4 py-2.5 rounded-full text-sm font-black shadow-2xl transition'
                href='{{ route("user.arbitrage") }}'>
                <div class="flex items-center gap-3 min-w-0">
                    <i data-lucide="rocket" class="w-5 h-5 shrink-0 text-black"></i>
                    <span class="nav-text text-black font-black tracking-wide">Arbitrage</span>
                </div>
                <span class="badge-live-pulse inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-widest uppercase shrink-0">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span style="color: #fef08a !important;">LIVE</span>
                </span>
            </a>
        @else
            <a class='nav-item arbitrage-pill-inactive flex items-center justify-between gap-2 mx-3 my-1.5 px-4 py-2.5 rounded-full text-sm font-bold shadow-lg transition'
                href='{{ route("user.arbitrage") }}'>
                <div class="flex items-center gap-3 min-w-0">
                    <i data-lucide="rocket" class="w-5 h-5 shrink-0 text-amber-400"></i>
                    <span class="nav-text text-amber-300 font-bold tracking-wide">Arbitrage</span>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/50 text-[10px] font-black tracking-widest uppercase shrink-0">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    <span>LIVE</span>
                </span>
            </a>
        @endif

        <!-- 1. ADD FUND & WITHDRAWAL SECTION -->
        <div class="nav-section-title px-5 pt-4 pb-2 mt-2 text-[10px] font-black uppercase tracking-[2px] text-amber-400/70">
            ADD FUND & WITHDRAWAL
        </div>

        <!-- Add Fund / Deposit -->
        <a class='nav-item {{ request()->routeIs("user.deposits.index") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.deposits.index") }}'>
            <i data-lucide="wallet" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">Add Fund / Deposit</span>
        </a>

        <!-- Deposit History -->
        <a class='nav-item {{ request()->routeIs("user.deposits.history") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.deposits.history") }}'>
            <i data-lucide="clock" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">Deposit History</span>
        </a>

        <!-- Withdrawal Request -->
        <a class='nav-item {{ request()->routeIs("user.withdrawals.index") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.withdrawals.index") }}'>
            <i data-lucide="arrow-up-right" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">Withdrawal</span>
        </a>

        <!-- Withdrawal History -->
        <a class='nav-item {{ request()->routeIs("user.withdrawals.history") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.withdrawals.history") }}'>
            <i data-lucide="receipt" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">Withdrawal History</span>
        </a>

        <!-- 2. INVESTMENT SECTION -->
        <div class="nav-section-title px-5 pt-4 pb-2 mt-2 text-[10px] font-black uppercase tracking-[2px] text-amber-400/70">
            INVESTMENT
        </div>

        <!-- Buy Packages -->
        <a class='nav-item {{ request()->routeIs("user.packages.index") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.packages.index") }}'>
            <i data-lucide="package-check" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">Buy Packages</span>
        </a>

        <!-- BOT (WITH LIVE TRADING GLOW & ANIMATED BADGE) -->
        <a class='nav-item {{ request()->routeIs("user.bot*") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center justify-between gap-2 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition group'
            href='{{ route("user.bot.index") }}'>
            <div class="flex items-center gap-3 min-w-0">
                <div class="relative flex items-center justify-center shrink-0">
                    <i data-lucide="bot" class="w-5 h-5 text-amber-400 group-hover:scale-110 transition duration-300"></i>
                    <span class="absolute -top-0.5 -right-0.5 flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ (auth()->user()?->is_bot_active) ? 'bg-emerald-400' : 'bg-amber-400' }} opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 {{ (auth()->user()?->is_bot_active) ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                    </span>
                </div>
                <span class="nav-text font-extrabold tracking-wide">BOT</span>
            </div>

            @if(auth()->user()?->is_bot_active)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/60 text-[9px] font-black tracking-widest uppercase animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)] shrink-0">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>LIVE 24/7</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/60 text-[9px] font-black tracking-widest uppercase animate-pulse shadow-[0_0_10px_rgba(243,202,82,0.4)] shrink-0">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    <span>AI TRADING</span>
                </span>
            @endif
        </a>

        <!-- Packages History -->
        <a class='nav-item {{ request()->routeIs("user.packages.history") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.packages.history") }}'>
            <i data-lucide="history" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">Packages History</span>
        </a>

        <!-- 3. TRANSACTIONS SECTION -->
        <div class="nav-section-title px-5 pt-4 pb-2 mt-2 text-[10px] font-black uppercase tracking-[2px] text-amber-400/70">
            TRANSACTIONS
        </div>

        <!-- Transaction Logs -->
        <a class='nav-item {{ request()->routeIs("user.transactions*") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.transactions.index") }}'>
            <i data-lucide="receipt" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">Transaction History</span>
        </a>

        <!-- 4. MY NETWORK SECTION -->
        <div class="nav-section-title px-5 pt-4 pb-2 mt-2 text-[10px] font-black uppercase tracking-[2px] text-amber-400/70">
            MY NETWORK & TEAM
        </div>

        <!-- Direct Members -->
        <a class='nav-item {{ request()->routeIs("user.network.direct*") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.network.direct") }}'>
            <i data-lucide="users" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">Direct Members</span>
        </a>

        <!-- My Team Tree -->
        <a class='nav-item {{ request()->routeIs("user.network.tree*") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.network.tree") }}'>
            <i data-lucide="git-merge" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">My Team Tree</span>
        </a>

        <!-- 5. MY INCOMES & REPORTS SECTION -->
        <div class="nav-section-title px-5 pt-4 pb-2 mt-2 text-[10px] font-black uppercase tracking-[2px] text-amber-400/70">
            DEX TRADE REPORTS
        </div>

        <!-- 0. Income Overview Summary -->
        <a class='nav-item {{ request()->routeIs("user.reports.summary") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.reports.summary") }}'>
            <i data-lucide="bar-chart-3" class="w-4 h-4 shrink-0 text-amber-400"></i>
            <span class="nav-text">Income Overview Summary</span>
        </a>

        <!-- Daily ROI Income -->
        <a class='nav-item {{ request()->routeIs("user.reports.roi") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.reports.roi") }}'>
            <i data-lucide="line-chart" class="w-4 h-4 shrink-0 text-amber-400"></i>
            <span class="nav-text">Daily ROI Income</span>
        </a>

        <!-- Direct Income -->
        <a class='nav-item {{ request()->routeIs("user.reports.direct") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.reports.direct") }}'>
            <i data-lucide="user-plus" class="w-4 h-4 shrink-0 text-amber-400"></i>
            <span class="nav-text">Direct Income</span>
        </a>

        <!-- Matching Income -->
        <a class='nav-item {{ request()->routeIs("user.reports.matching") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.reports.matching") }}'>
            <i data-lucide="git-merge" class="w-4 h-4 shrink-0 text-amber-400"></i>
            <span class="nav-text">Matching Income</span>
        </a>

        <!-- Referral ROI Income -->
        <a class='nav-item {{ request()->routeIs("user.reports.referral-roi") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.reports.referral-roi") }}'>
            <i data-lucide="repeat" class="w-4 h-4 shrink-0 text-amber-400"></i>
            <span class="nav-text">Referral ROI Income</span>
        </a>

        <!-- Matching ROI Income -->
        <a class='nav-item {{ request()->routeIs("user.reports.matching-roi") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.reports.matching-roi") }}'>
            <i data-lucide="layers" class="w-4 h-4 shrink-0 text-amber-400"></i>
            <span class="nav-text">Matching ROI Income</span>
        </a>

        <!-- Upline Matching Income -->
        <a class='nav-item {{ request()->routeIs("user.reports.upline-matching") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.reports.upline-matching") }}'>
            <i data-lucide="share-2" class="w-4 h-4 shrink-0 text-amber-400"></i>
            <span class="nav-text">Upline Matching Income</span>
        </a>

        <!-- Salary Income -->
        <a class='nav-item {{ request()->routeIs("user.reports.salary") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.reports.salary") }}'>
            <i data-lucide="award" class="w-4 h-4 shrink-0 text-amber-400"></i>
            <span class="nav-text">Salary Income</span>
        </a>

        <!-- 6. ACCOUNT & SECURITY SECTION -->
        <div class="nav-section-title px-5 pt-4 pb-2 mt-2 text-[10px] font-black uppercase tracking-[2px] text-amber-400/70">
            ACCOUNT & SECURITY
        </div>

        <!-- My Profile -->
        <a class='nav-item {{ request()->routeIs("user.profile*") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.profile") }}'>
            <i data-lucide="user-cog" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">My Profile</span>
        </a>

        <!-- Support Tickets -->
        <a class='nav-item {{ request()->routeIs("user.tickets*") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("user.tickets.index") }}'>
            <i data-lucide="headphones" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">Support Tickets</span>
        </a>

    </nav>

    <!-- User Footprint -->
    <div class="user-section mt-auto p-4 border-t border-amber-500/30 shrink-0 bg-neutral-950/80">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 bg-gradient-to-br from-emerald-500 to-teal-600 text-white font-black text-sm shadow-md">
                US
            </div>
            <div class="user-info flex-1 min-w-0">
                <p class="font-bold text-sm text-white truncate">{{ Auth::user() ? Auth::user()->name : 'Member' }}</p>
                <p class="text-[11px] font-semibold truncate {{ Auth::user() && Auth::user()->status === 'active' ? 'text-emerald-400' : 'text-amber-400' }}">
                    {{ Auth::user() && Auth::user()->status === 'active' ? 'Active Member' : 'Inactive Member' }}
                </p>
            </div>
        </div>
    </div>
</aside>
<!-- ======================================
     End User Sidebar Area
     ====================================== -->
