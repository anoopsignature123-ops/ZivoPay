<!-- ======================================
     Start Admin Sidebar Area
     ====================================== -->
<div id="sidebarOverlay"
    class="fixed inset-0 bg-black/70 z-40 hidden lg:hidden transition-opacity duration-300 opacity-0"
    aria-hidden="true"></div>
<aside class="sidebar" id="sidebar">
    <!-- Logo Section -->
    <div class="logo-section flex items-center justify-between gap-2 px-4 py-4 border-b border-amber-500/30 shrink-0 bg-gradient-to-b from-amber-500/20 via-amber-500/5 to-transparent">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-center flex-1 min-w-0">
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
        
        <!-- 1. CORE MANAGEMENT SECTION -->
        <div class="nav-section-title px-5 pt-3 pb-2 text-[10px] font-black uppercase tracking-[2px] text-amber-400/70">
            Dashboard
        </div>

        <!-- Dashboard Link -->
        <a class='nav-item {{ request()->routeIs("admin.dashboard") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.dashboard") }}'>
            <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">Dashboard</span>
        </a>

        <!-- User Management Directory -->
        <a class='nav-item {{ request()->routeIs("admin.users*") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.users") }}'>
            <i data-lucide="users" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">User Management</span>
        </a>

        <!-- 2. ADD FUND & DEPOSITS SECTION -->
        <div class="nav-section-title px-5 pt-4 pb-2 mt-2 text-[10px] font-black uppercase tracking-[2px] text-amber-400/70">
            ADD FUND & WITHDRAWALS
        </div>

        <!-- Deposit History -->
        @php
$pendingWithdrawalsCount = \App\Models\Withdrawal::where('status', 'pending')->count();
        @endphp
        <a class='nav-item {{ request()->routeIs("admin.deposits*") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center justify-between mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.deposits.index") }}'>
            <div class="flex items-center gap-3">
                <i data-lucide="wallet" class="w-5 h-5 shrink-0 text-amber-400"></i>
                <span class="nav-text">Deposit History</span>
            </div>
        </a>

        <!-- Withdrawal Requests -->
        <a class='nav-item {{ request()->routeIs("admin.withdrawals*") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center justify-between mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.withdrawals.index") }}'>
            <div class="flex items-center gap-3">
                <i data-lucide="arrow-up-right" class="w-5 h-5 shrink-0 text-amber-400"></i>
                <span class="nav-text">Withdrawal Requests</span>
            </div>
            @if($pendingWithdrawalsCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500 text-white animate-pulse shadow-md border border-rose-400 shrink-0">
                    {{ $pendingWithdrawalsCount }} PENDING
                </span>
            @endif
        </a>

        <!-- 3. INVESTMENT SECTION -->
        <div class="nav-section-title px-5 pt-4 pb-2 mt-2 text-[10px] font-black uppercase tracking-[2px] text-amber-400/70">
            INVESTMENT
        </div>

        <!-- Investment Packages Management -->
        <a class='nav-item {{ request()->routeIs("admin.packages.index") || request()->routeIs("admin.packages.create") || request()->routeIs("admin.packages.edit") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.packages.index") }}'>
            <i data-lucide="package-check" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">Packages Management</span>
        </a>

        <!-- Investment History -->
        <a class='nav-item {{ request()->routeIs("admin.packages.history") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.packages.history") }}'>
            <i data-lucide="trending-up" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">Investment History</span>
        </a>

        <!-- 4. TRANSACTIONS SECTION -->
        <div class="nav-section-title px-5 pt-4 pb-2 mt-2 text-[10px] font-black uppercase tracking-[2px] text-amber-400/70">
            TRANSACTIONS
        </div>

        <!-- Transaction History Logs -->
        <a class='nav-item {{ request()->routeIs("admin.transactions*") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.transactions.index") }}'>
            <i data-lucide="receipt" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">Transaction History</span>
        </a>

        <!-- 5. INCOME REPORTS SECTION -->
        <div class="nav-section-title px-5 pt-4 pb-2 mt-2 text-[10px] font-black uppercase tracking-[2px] text-amber-400/70">
            DEX TRADE REPORTS
        </div>

        <!-- 0. Income Overview Summary -->
        <a class='nav-item {{ request()->routeIs("admin.reports.summary") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.reports.summary") }}'>
            <i data-lucide="bar-chart-3" class="w-4 h-4 shrink-0 text-amber-400"></i>
            <span class="nav-text">Income Overview Summary</span>
        </a>

        <!-- Daily ROI Income -->
        <a class='nav-item {{ request()->routeIs("admin.reports.roi") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.reports.roi") }}'>
            <i data-lucide="line-chart" class="w-4 h-4 shrink-0 text-amber-400"></i>
            <span class="nav-text">Daily ROI Income</span>
        </a>

        <!-- Direct Income -->
        <a class='nav-item {{ request()->routeIs("admin.reports.direct") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.reports.direct") }}'>
            <i data-lucide="user-plus" class="w-4 h-4 shrink-0 text-amber-400"></i>
            <span class="nav-text">Direct Income</span>
        </a>

        <!-- Matching Income -->
        <a class='nav-item {{ request()->routeIs("admin.reports.matching") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.reports.matching") }}'>
            <i data-lucide="git-merge" class="w-4 h-4 shrink-0 text-amber-400"></i>
            <span class="nav-text">Matching Income</span>
        </a>

        <!-- Referral ROI Income -->
        <a class='nav-item {{ request()->routeIs("admin.reports.referral-roi") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.reports.referral-roi") }}'>
            <i data-lucide="repeat" class="w-4 h-4 shrink-0 text-amber-400"></i>
            <span class="nav-text">Referral ROI Income</span>
        </a>

        <!-- Matching ROI Income -->
        <a class='nav-item {{ request()->routeIs("admin.reports.matching-roi") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.reports.matching-roi") }}'>
            <i data-lucide="layers" class="w-4 h-4 shrink-0 text-amber-400"></i>
            <span class="nav-text">Matching ROI Income</span>
        </a>

        <!-- Upline Matching Income -->
        <a class='nav-item {{ request()->routeIs("admin.reports.upline-matching") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.reports.upline-matching") }}'>
            <i data-lucide="share-2" class="w-4 h-4 shrink-0 text-amber-400"></i>
            <span class="nav-text">Upline Matching Income</span>
        </a>

        <!-- Salary Income -->
        <a class='nav-item {{ request()->routeIs("admin.reports.salary") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-2.5 rounded-xl text-xs font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.reports.salary") }}'>
            <i data-lucide="award" class="w-4 h-4 shrink-0 text-amber-400"></i>
            <span class="nav-text">Salary Income</span>
        </a>

        <!-- 6. HELP DESK & SUPPORT SECTION -->
        <div class="nav-section-title px-5 pt-4 pb-2 mt-2 text-[10px] font-black uppercase tracking-[2px] text-amber-400/70">
            HELP DESK & SUPPORT
        </div>

        @php
$pendingTicketCount = \App\Models\SupportTicket::whereIn('status', ['open', 'user_reply'])->count();
        @endphp
        <!-- Support Tickets -->
        <a class='nav-item {{ request()->routeIs("admin.tickets*") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center justify-between mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.tickets.index") }}'>
            <div class="flex items-center gap-3">
                <i data-lucide="headphones" class="w-5 h-5 shrink-0 text-amber-400"></i>
                <span class="nav-text">Support Tickets</span>
            </div>
            @if($pendingTicketCount > 0)
                <span class="px-2 py-0.5 rounded-full bg-rose-500 text-white font-mono font-black text-[10px] shadow-md animate-pulse">
                    {{ $pendingTicketCount }} OPEN
                </span>
            @endif
        <!-- Gateway Settings -->
        <a class='nav-item {{ request()->routeIs("admin.settings.gateway*") ? "active bg-amber-500/15 text-amber-300 border-r-4 border-amber-400 font-bold shadow-lg" : "" }} flex items-center gap-3 mx-3 my-0.5 px-4 py-3 rounded-xl text-sm font-medium text-neutral-300 hover:bg-amber-500/10 hover:text-amber-300 transition'
            href='{{ route("admin.settings.gateway") }}'>
            <i data-lucide="sliders" class="w-5 h-5 shrink-0 text-amber-400"></i>
            <span class="nav-text">Gateway Settings</span>
        </a>

    </nav>

    <!-- Admin Footprint -->
    <div class="user-section mt-auto p-4 border-t border-amber-500/30 shrink-0 bg-neutral-950/80">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 bg-gradient-to-br from-amber-500 via-yellow-500 to-amber-700 text-black font-black text-sm shadow-md">
                AD
            </div>
            <div class="user-info flex-1 min-w-0">
                <p class="font-bold text-sm text-white truncate">Super Admin</p>
                <p class="text-[11px] text-amber-400 font-semibold truncate">DEX TRADE</p>
            </div>
        </div>
    </div>
</aside>
<!-- ======================================
     End Admin Sidebar Area
     ====================================== -->