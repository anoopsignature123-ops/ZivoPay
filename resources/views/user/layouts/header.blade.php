<!-- ======================================
     Start User Header Area
     ====================================== -->
@if(session()->has('impersonated_by'))
    <div class="w-full bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-500 text-black px-4 py-2.5 text-xs font-black flex flex-col sm:flex-row items-center justify-between gap-2 shadow-xl sticky top-0 z-[100] border-b-2 border-black">
        <div class="flex items-center gap-2">
            <i data-lucide="shield-alert" class="w-4 h-4 text-black shrink-0"></i>
            <span>ADMIN IMPERSONATION MODE • Logged in as Member: <strong class="uppercase text-black underline">{{ Auth::user()->name }}</strong> ({{ Auth::user()->referral_code }})</span>
        </div>
        <a href="{{ route('user.stop-impersonate') }}" class="px-3 py-1.5 rounded-lg bg-black text-amber-400 font-bold hover:bg-neutral-900 transition text-[11px] uppercase tracking-wider flex items-center gap-1.5 shadow shrink-0">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-amber-400"></i> Return to Admin Panel
        </a>
    </div>
@endif

<header class="sticky top-0 z-50 glass @container">
    <div class="flex items-center justify-between px-2 sm:px-6 py-2 sm:py-4 relative">
        <div class="flex items-center gap-2 sm:gap-4 z-10">
            <!-- Mobile Menu Button -->
            <button
                class="lg:hidden flex size-11 items-center justify-center rounded-xl bg-panel border border-border text-text hover:bg-amber-500/10 transition js-mobile-menu-toggle"
                id="mobileMenuBtn" aria-label="Toggle Mobile Menu">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>

            <!-- Sidebar Toggle (Desktop) -->
            <button
                class="hidden lg:flex size-11 items-center justify-center rounded-xl bg-panel border border-border text-text hover:bg-amber-500/10 transition js-sidebar-toggle"
                aria-label="Toggle Sidebar">
                <i data-lucide="panel-left" class="w-5 h-5"></i>
            </button>

            <!-- Search -->
            <div class="relative hidden md:block">
                <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-muted"></i>
                <input type="text" id="globalSearch" placeholder="Search dashboard stats..."
                    class="w-80 pl-10 pr-4 py-2.5 rounded-xl bg-bg border border-border text-sm text-text placeholder:text-muted focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition" />
            </div>
        </div>

        <!-- Centered Mobile Brand Logo -->
        <div class="lg:hidden absolute left-1/2 -translate-x-1/2 flex items-center justify-center pointer-events-none z-10">
            <a href="{{ route('user.dashboard') }}" class="pointer-events-auto flex items-center justify-center">
                <img src="{{ asset('images/dextrade_logo.png') }}" alt="DEX TRADE Logo" class="h-11 sm:h-12 w-auto max-w-[210px] sm:max-w-[240px] object-contain drop-shadow-[0_0_16px_rgba(243,202,82,0.95)]">
            </a>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <!-- Live Status Badge -->
            @if(Auth::user() && Auth::user()->status === 'active')
                <div class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[11px] font-semibold">
                    <span class="live-dot !bg-emerald-400"></span>
                    ACTIVE MEMBER • BEP20 WALLET
                </div>
            @else
                <div class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[11px] font-semibold">
                    <span class="live-dot !bg-amber-400"></span>
                    INACTIVE MEMBER • INVEST TO ACTIVATE
                </div>
            @endif


            <!-- User Profile Dropdown -->
            <div class="relative z-50">
                <button
                    type="button"
                    id="userDropdownBtn"
                    onclick="toggleUserProfileMenu(event)"
                    class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-white/10 transition cursor-pointer border border-amber-500/30 bg-black/40">
                    <span
                        class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-md border border-amber-300">
                        <span class="text-black font-black text-sm">{{ strtoupper(substr(Auth::user() ? Auth::user()->name : 'US', 0, 2)) }}</span>
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-amber-400 hidden sm:block"></i>
                </button>

                <div
                    id="userProfileMenu"
                    style="display: none; background-color: #041d13 !important; z-index: 999999 !important;"
                    class="!absolute right-0 top-full mt-2 w-64 rounded-2xl bg-[#041d13] border-2 border-amber-400/80 shadow-[0_20px_50px_rgba(0,0,0,0.95)] overflow-hidden p-3 z-[999999] opacity-100">

                    <div class="p-3 mb-2 rounded-xl bg-black/80 border border-amber-500/30">
                        <p class="font-bold text-white flex items-center justify-between text-sm">
                            <span class="truncate max-w-[130px]">{{ Auth::user() ? Auth::user()->name : 'Member' }}</span>
                            @if(Auth::user() && Auth::user()->status === 'active')
                                <span class="px-2 py-0.5 text-[10px] bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 rounded-full font-bold">ACTIVE</span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] bg-amber-500/20 text-amber-400 border border-amber-500/40 rounded-full font-bold">INACTIVE</span>
                            @endif
                        </p>
                        <p class="text-xs text-neutral-400 truncate mt-0.5 font-mono">{{ Auth::user() ? Auth::user()->email : 'user@dextrade.com' }}</p>
                    </div>
                    
                    @if(session()->has('impersonated_by'))
                        <a class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-amber-400 hover:bg-amber-500/10 transition border border-amber-500/20 mb-2"
                            href="{{ route('user.stop-impersonate') }}">
                            <i data-lucide="arrow-left" class="w-4 h-4 text-amber-400"></i>
                            <span>Return to Admin Panel</span>
                        </a>
                    @endif

                    <div class="my-1 border-t border-amber-500/20"></div>

                    <form action="{{ route('user.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-rose-400 hover:bg-rose-500/20 transition cursor-pointer">
                            <i data-lucide="log-out" class="w-4 h-4 text-rose-400"></i>
                            <span>Logout Account</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function toggleUserProfileMenu(event) {
        event.stopPropagation();
        event.preventDefault();
        const menu = document.getElementById('userProfileMenu');
        if (menu) {
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        }
    }

    document.addEventListener('click', function (e) {
        const menu = document.getElementById('userProfileMenu');
        const btn = document.getElementById('userDropdownBtn');
        if (menu && btn && !btn.contains(e.target) && !menu.contains(e.target)) {
            menu.style.display = 'none';
        }
    });
</script>
<!-- ======================================
     End Header Area
     ====================================== -->
