<!-- ======================================
     Start Admin Sidebar Area
     ====================================== -->
<div id="sidebarOverlay"
    class="fixed inset-0 bg-black/70 z-40 hidden lg:hidden transition-opacity duration-300 opacity-0"
    aria-hidden="true"></div>
<aside class="sidebar" id="sidebar">
    <!-- Logo Section -->
    <div
        class="logo-section flex items-center justify-between gap-2 px-4 py-4 border-b border-emerald-500/30 shrink-0 bg-gradient-to-b from-emerald-500/20 via-emerald-500/5 to-transparent">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-center flex-1 min-w-0 group py-1">
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

        <a class='nav-item {{ request()->routeIs("admin.dashboard") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.dashboard") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Admin Dashboard</span>
        </a>

        <!-- USER MANAGEMENT -->
        <div class="nav-section-title px-5 pt-3 pb-1 mt-1 text-[10px] font-black uppercase tracking-[2px] text-emerald-400">
            USER MANAGEMENT
        </div>
        
        <a class='nav-item {{ request()->routeIs("admin.users*") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.users") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="users" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">User Management</span>
        </a>

        <a class='nav-item {{ request()->routeIs("admin.network.tree*") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.network.tree") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="network" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Downline Network Tree</span>
            </a>

        <!-- PACKAGE ACTIVATION AUDIT -->
        <div class="nav-section-title px-5 pt-3 pb-1 mt-1 text-[10px] font-black uppercase tracking-[2px] text-emerald-400">
            PACKAGE ACTIVATION
        </div>
        <a class='nav-item {{ request()->routeIs("admin.reports.subscriptions") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.reports.subscriptions") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">₹3,000 Activations</span>
        </a>

        <a class='nav-item {{ request()->routeIs("admin.reports.subscription-direct") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.reports.subscription-direct") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Direct Bonus Referral</span>
        </a>

        <a class='nav-item {{ request()->routeIs("admin.reports.subscription-team") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.reports.subscription-team") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="users-round" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Team Bonus Referral</span>
            </a>

        <!-- FUND WALLET 24H ROI INCOME AUDIT -->
        <div class="nav-section-title px-5 pt-3 pb-1 mt-1 text-[10px] font-black uppercase tracking-[2px] text-emerald-400">
            FUND WALLET 24H ROI INCOME
        </div>
        
        <a class='nav-item {{ request()->routeIs("admin.reports.roi") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.reports.roi") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center border border-teal-500/30 shrink-0">
                <i data-lucide="circle-dollar-sign" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">ROI Income Payouts</span>
        </a>

        <a class='nav-item {{ request()->routeIs("admin.reports.level-roi") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.reports.level-roi") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center border border-teal-500/30 shrink-0">
                <i data-lucide="trending-up" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">ROI on Level Income</span>
        </a>

        <!-- INVESTMENT SECTION (5 REPORTS AUDIT) -->
        <div class="nav-section-title px-5 pt-3 pb-1 mt-1 text-[10px] font-black uppercase tracking-[2px] text-emerald-400">
            INVESTMENT SECTION
        </div>

        <a class='nav-item {{ request()->routeIs("admin.packages*") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.packages.index") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="package" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Package Management Portal</span>
        </a>

        <a class='nav-item {{ request()->routeIs("admin.investments*") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.investments") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="pie-chart" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Investments Control Portal</span>
        </a>

        <a class='nav-item {{ request()->routeIs("admin.reports.roi") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.reports.roi") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="badge-percent" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Daily ROI Income</span>
        </a>

        <a class='nav-item {{ request()->routeIs("admin.reports.level-direct") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.reports.level-direct") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="layers" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Business Level Income</span>
        </a>
        
        <a class='nav-item {{ request()->routeIs("admin.reports.level-roi") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.reports.level-roi") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">ROI on ROI Level Income </span>
        </a>

        <a class='nav-item {{ request()->routeIs("admin.reports.direct-business") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.reports.direct-business") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="award" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Direct Business Income</span>
        </a>

        <a class='nav-item {{ request()->routeIs("admin.reports.rewards") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.reports.rewards") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="trophy" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Team Business Income / Rewards</span>
        </a>

        <!-- WITHDRAWAL MANAGEMENT -->
        <div class="nav-section-title px-5 pt-3 pb-1 mt-1 text-[10px] font-black uppercase tracking-[2px] text-emerald-400">
            WITHDRAWAL MANAGEMENT
        </div>
        
        <a class='nav-item {{ request()->routeIs("admin.withdrawals*") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.withdrawals") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Withdrawal Requests</span>
        </a>
        <!-- REPORTS & AUDITS -->
        <div class="nav-section-title px-5 pt-3 pb-1 mt-1 text-[10px] font-black uppercase tracking-[2px] text-emerald-400">
            REPORTS & AUDITS
        </div>

        <a class='nav-item {{ request()->routeIs("admin.reports.deposits") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.reports.deposits") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="wallet-cards" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Deposit Fund History</span>
        </a>

        <a class='nav-item {{ request()->routeIs("admin.reports.transactions") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.reports.transactions") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center border border-teal-500/30 shrink-0">
                <i data-lucide="receipt" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Transaction History</span>
        </a>

        <!-- ACCOUNT SETTINGS -->
        <div class="nav-section-title px-5 pt-3 pb-1 mt-1 text-[10px] font-black uppercase tracking-[2px] text-emerald-400">
            ACCOUNT SETTINGS
        </div>

        <a class='nav-item {{ request()->routeIs("admin.profile*") ? "active bg-emerald-500/20 text-white font-bold shadow-lg border-l-4 border-emerald-400" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-neutral-300 hover:bg-emerald-500/10 hover:text-emerald-300 transition'
            href='{{ route("admin.profile") }}'>
            <div
                class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                <i data-lucide="user-cog" class="w-4 h-4"></i>
            </div>
            <span class="nav-text">Admin Profile</span>
        </a>
    </nav>

    <!-- Admin Footprint -->
    <div class="user-section mt-auto p-4 border-t border-emerald-500/30 shrink-0 bg-neutral-950/80">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 bg-gradient-to-br from-emerald-500 via-teal-600 to-emerald-800 text-white font-black text-sm shadow-md">
                AD
            </div>
            <div class="user-info flex-1 min-w-0">
                <p class="font-bold text-sm text-white truncate">Super Admin</p>
                <p class="text-[11px] text-emerald-400 font-semibold truncate">ZIVO PAY CONTROL</p>
            </div>
        </div>
    </div>
</aside>
<!-- ======================================
     End Admin Sidebar Area
     ====================================== -->