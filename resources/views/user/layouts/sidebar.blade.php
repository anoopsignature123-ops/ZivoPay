<!-- ======================================
     Start User Sidebar Area
     ====================================== -->
<div id="sidebarOverlay"
    class="fixed inset-0 bg-black/70 z-40 hidden lg:hidden transition-opacity duration-300 opacity-0"
    aria-hidden="true"></div>
<aside class="sidebar" id="sidebar">
    <!-- Logo Section -->
    <div
        class="logo-section flex items-center justify-between gap-2 px-4 py-4 border-b border-emerald-500/30 shrink-0 bg-gradient-to-b from-emerald-500/20 via-emerald-500/5 to-transparent">
        <a href="{{ route('user.dashboard') }}" class="flex items-center justify-center flex-1 min-w-0 group py-1">
            <div class="full-logo flex items-center justify-center w-full">
                <img src="{{ asset('images/logo.png') }}?v=999" alt="ZIVO PAY"
                    class="h-14 sm:h-16 w-auto max-w-[220px] object-contain drop-shadow-[0_4px_15px_rgba(16,185,129,0.7)]">
            </div>
            <div class="mini-logo hidden w-10 h-10 mx-auto items-center justify-center">
                <img src="{{ asset('images/fav.png') }}?v=999" alt="ZIVO PAY Mini Logo"
                    class="w-9 h-9 object-contain drop-shadow-[0_2px_8px_rgba(16,185,129,0.7)]">
            </div>
        </a>
        <button
            class="lg:hidden! flex w-8 h-8 items-center justify-center rounded-lg text-emerald-400 hover:bg-emerald-500/20 transition js-mobile-menu-toggle shrink-0"
            aria-label="Close sidebar">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="py-4 flex-1 overflow-y-auto space-y-1">

        <!-- DASHBOARD -->
        <div class="nav-section-title px-5 pt-2 pb-1 text-[10px] font-black uppercase tracking-[2px] text-emerald-400">
            DASHBOARD
        </div>

        <a class='nav-item {{ request()->routeIs("user.dashboard") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.dashboard") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Dashboard</span>
        </a>

        <!-- ADD FUND SECTION -->
        <div class="nav-section-title px-5 pt-3 pb-1 mt-1 text-[10px] font-black uppercase tracking-[2px] text-emerald-400">
            ADD FUND SECTION
        </div>

        <a class='nav-item {{ request()->routeIs("user.wallet.transfer*") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.wallet.transfer") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Add / Transfer Fund</span>
            </a>
        <a class='nav-item {{ request()->routeIs("user.reports.deposits") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.reports.deposits") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="history" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Deposit Fund History</span>
            </a>

        <!-- BUY PACKAGE ₹3,000 -->
        <div class="nav-section-title px-5 pt-3 pb-1 mt-1 text-[10px] font-black uppercase tracking-[2px] text-emerald-400">
            BUY PACKAGE
        </div>

        <a class='nav-item {{ request()->routeIs("user.package.buy") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.package.buy") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="zap" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Buy Package ₹3,000 (Activate)</span>
        </a>

        <a class='nav-item {{ request()->routeIs("user.reports.package-history") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.reports.package-history") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="receipt" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Package Purchase History</span>
        </a>

        <a class='nav-item {{ request()->routeIs("user.income.subscription-direct") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.income.subscription-direct") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Direct Bonus Referral Income</span>
        </a>

        <a class='nav-item {{ request()->routeIs("user.income.subscription-team") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.income.subscription-team") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="users-round" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Team Bonus Referral Income</span>
        </a>

        <!-- FUND ACTIVATION (24H PROFIT) -->
        <div class="nav-section-title px-5 pt-3 pb-1 mt-1 text-[10px] font-black uppercase tracking-[2px] text-emerald-400">
            FUND ACTIVATION
        </div>

        <a class='nav-item {{ request()->routeIs("user.income.roi") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.income.roi") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="trending-up" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">ROI Income (Fund Wallet Profit)</span>
        </a>

        <a class='nav-item {{ request()->routeIs("user.income.level-roi") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.income.level-roi") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="badge-percent" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">ROI on Level Income (Secondary)</span>
            </a>
            
            <!-- INVESTMENT SECTION (5 REPORTS) -->
            <div class="nav-section-title px-5 pt-3 pb-1 mt-1 text-[10px] font-black uppercase tracking-[2px] text-emerald-400">
                INVESTMENT SECTION
            </div>

        <a class='nav-item {{ request()->routeIs("user.investment*") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.investment.index") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="package" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Investment Packages Plan</span>
        </a>

        <a class='nav-item {{ request()->routeIs("user.reports.investments") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.reports.investments") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="pie-chart" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">My Investment History</span>
        </a>

        <a class='nav-item {{ request()->routeIs("user.income.roi") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.income.roi") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="circle-dollar-sign" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">ROI Income </span>
        </a>

        <a class='nav-item {{ request()->routeIs("user.income.level-direct") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.income.level-direct") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="layers" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Business Level Income</span>
        </a>

        <a class='nav-item {{ request()->routeIs("user.income.level-roi") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.income.level-roi") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">ROI on ROI Level Income</span>
        </a>

        <a class='nav-item {{ request()->routeIs("user.income.direct-business") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.income.direct-business") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="award" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Direct Business Income</span>
        </a>

        <a class='nav-item {{ request()->routeIs("user.rewards*") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.rewards.index") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="trophy" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Team Business Income / Rewards</span>
        </a>

        <!-- REPORTS & PAYOUTS -->
        <div class="nav-section-title px-5 pt-3 pb-1 mt-1 text-[10px] font-black uppercase tracking-[2px] text-emerald-400">
            REPORTS & PAYOUTS
        </div>
        <a class='nav-item {{ request()->routeIs("user.income.index") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.income.index") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="receipt" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">All Transaction Ledger</span>
        </a>

        <a class='nav-item {{ request()->routeIs("user.withdrawal*") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.withdrawal.index") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Withdrawal</span>
        </a>

        <!-- MY TEAM & NETWORK -->
        <div class="nav-section-title px-5 pt-3 pb-1 mt-1 text-[10px] font-black uppercase tracking-[2px] text-emerald-400">
            MY TEAM & NETWORK
        </div>

        <a class='nav-item {{ request()->routeIs("user.network.direct*") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.network.direct") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="users" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Direct Members List</span>
        </a>

        <a class='nav-item {{ request()->routeIs("user.network.tree*") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.network.tree") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="network" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">My Network Tree</span>
        </a>


        <!-- MY ACCOUNT -->
        <div class="nav-section-title px-5 pt-3 pb-1 mt-1 text-[10px] font-black uppercase tracking-[2px] text-emerald-400">
            MY ACCOUNT
        </div>

        <a class='nav-item {{ request()->routeIs("user.profile*") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("user.profile") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="user-cog" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">My Profile</span>
        </a>
    </nav>

    <!-- User Footprint -->
    <div class="user-section mt-auto p-4 border-t border-emerald-500/30 shrink-0 bg-neutral-950/80">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 bg-gradient-to-br from-emerald-500 to-teal-600 text-white font-black text-sm shadow-md">
                US
            </div>
            <div class="user-info flex-1 min-w-0">
                <p class="font-bold text-sm text-white truncate">{{ Auth::user() ? Auth::user()->name : 'Member' }}</p>
                <p
                    class="text-[11px] font-semibold truncate {{ Auth::user() && Auth::user()->is_subscription_active ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ Auth::user() && Auth::user()->is_subscription_active ? 'Active (₹3,000 Paid)' : 'Inactive Account' }}
                </p>
            </div>
        </div>
    </div>
</aside>
<!-- ======================================
     End User Sidebar Area
     ====================================== -->