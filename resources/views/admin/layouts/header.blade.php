<!-- ======================================
     Start Header Area
     ====================================== -->
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

            <!-- Global Search Form with Live Autocomplete Suggestions -->
            <div class="relative hidden md:block z-50">
                <form action="{{ route('admin.users') }}" method="GET" id="globalSearchForm">
                    <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-muted pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" id="globalSearch" autocomplete="off" placeholder="Global search members, email, code..."
                        class="w-80 pl-10 pr-4 py-2.5 rounded-xl bg-bg border border-border text-sm text-text placeholder:text-muted focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition" />
                </form>

                <!-- Live Autocomplete Suggestions Dropdown Box -->
                <div id="globalSearchSuggestions" style="display: none; background-color: #041d13 !important; z-index: 999999 !important;" class="!absolute left-0 top-full mt-2 w-96 rounded-2xl bg-[#041d13] border-2 border-amber-400/80 shadow-[0_20px_50px_rgba(0,0,0,0.95)] overflow-hidden p-3 z-[999999] opacity-100">

                    <div class="p-2 border-b border-amber-500/20 text-[11px] font-bold text-amber-400 uppercase tracking-wider flex items-center justify-between">
                        <span>SUGGESTED MEMBERS</span>
                        <span id="searchResultCount" class="text-neutral-400 font-mono text-[10px]">0 found</span>
                    </div>
                    <div id="suggestionsList" class="divide-y divide-amber-500/10 max-h-72 overflow-y-auto py-1">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Centered Mobile Brand Logo -->
        <div class="lg:hidden absolute left-1/2 -translate-x-1/2 flex items-center justify-center pointer-events-none z-10">
            <a href="{{ route('admin.dashboard') }}" class="pointer-events-auto flex items-center justify-center">
                <img src="{{ asset('images/dextrade_logo.png') }}" alt="DEX TRADE Logo" class="h-11 sm:h-12 w-auto max-w-[210px] sm:max-w-[240px] object-contain drop-shadow-[0_0_16px_rgba(243,202,82,0.95)]">
            </a>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <!-- Live Status -->
            <div
                class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[11px] font-semibold">
                <span class="live-dot !bg-amber-400"></span>
                ADMIN MODE • USA BASED
            </div>

            <!-- Live Pending Withdrawals Notification Alert Badge -->
            @php
                $headerPendingWithdrawals = \App\Models\Withdrawal::where('status', 'pending')->count();
                $headerPendingTickets = \App\Models\SupportTicket::whereIn('status', ['open', 'user_reply'])->count();
            @endphp
            @if($headerPendingWithdrawals > 0)
                <a href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/50 text-[11px] font-black animate-pulse shadow-lg hover:bg-rose-500/30 transition" title="Click to view pending withdrawal requests">
                    <i data-lucide="bell" class="w-3.5 h-3.5 text-rose-400"></i>
                    <span>{{ $headerPendingWithdrawals }} PENDING WITHDRAWAL</span>
                </a>
            @endif

            @if($headerPendingTickets > 0)
                <a href="{{ route('admin.tickets.index', ['status' => 'pending']) }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/50 text-[11px] font-black animate-pulse shadow-lg hover:bg-amber-500/30 transition" title="Click to view pending support tickets">
                    <i data-lucide="headphones" class="w-3.5 h-3.5 text-amber-400"></i>
                    <span>{{ $headerPendingTickets }} OPEN TICKET</span>
                </a>
            @endif


            <!-- Profile Dropdown -->
            <div class="relative z-50">
                <button
                    type="button"
                    id="adminDropdownBtn"
                    onclick="toggleAdminProfileMenu(event)"
                    class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-white/10 transition cursor-pointer border border-amber-500/30 bg-black/40">
                    <span
                        class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-md border border-amber-300">
                        <span class="text-black font-black text-sm">{{ strtoupper(substr(Auth::user() ? Auth::user()->name : 'AD', 0, 2)) }}</span>
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-amber-400 hidden sm:block"></i>
                </button>

                <div
                    id="adminProfileMenu"
                    style="display: none; background-color: #041d13 !important; z-index: 999999 !important;"
                    class="!absolute right-0 top-full mt-2 w-64 rounded-2xl bg-[#041d13] border-2 border-amber-400/80 shadow-[0_20px_50px_rgba(0,0,0,0.95)] overflow-hidden p-3 z-[999999] opacity-100">

                    <div class="p-3 mb-2 rounded-xl bg-black/80 border border-amber-500/30">
                        <p class="font-bold text-white flex items-center justify-between text-sm">
                            <span class="truncate max-w-[130px]">{{ Auth::user() ? Auth::user()->name : 'Super Admin' }}</span>
                            <span class="px-2 py-0.5 text-[10px] bg-amber-500/20 text-amber-400 border border-amber-500/40 rounded-full font-bold">ROOT</span>
                        </p>
                        <p class="text-xs text-neutral-400 truncate mt-0.5 font-mono">{{ Auth::user() ? Auth::user()->email : 'admin@dextrade.com' }}</p>
                    </div>
                    
                    <a class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-white hover:bg-amber-500/10 transition border border-amber-500/20 mb-2"
                        href="{{ route('admin.profile') }}">
                        <i data-lucide="user-cog" class="w-4 h-4 text-amber-400"></i>
                        <span>Profile & Password</span>
                    </a>

                    <div class="my-1 border-t border-amber-500/20"></div>

                    <form action="{{ route('admin.logout') }}" method="POST">
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
    function toggleAdminProfileMenu(event) {
        event.stopPropagation();
        event.preventDefault();
        const menu = document.getElementById('adminProfileMenu');
        if (menu) {
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('globalSearch');
        const dropdown = document.getElementById('globalSearchSuggestions');
        const suggestionsList = document.getElementById('suggestionsList');
        const countSpan = document.getElementById('searchResultCount');

        if (searchInput && dropdown && suggestionsList) {
            let debounceTimer = null;

            searchInput.addEventListener('input', function() {
                const query = this.value.trim();

                if (query.length < 2) {
                    dropdown.style.display = 'none';
                    return;
                }

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetch(`{{ route('admin.global-search-suggestions') }}?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                countSpan.textContent = `${data.length} found`;
                                suggestionsList.innerHTML = data.map(item => `
                                    <a href="${item.url}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-amber-500/15 transition group">
                                        <div class="flex items-center gap-3 overflow-hidden">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 text-black font-black text-xs flex items-center justify-center shrink-0 shadow">
                                                ${item.initial}
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="font-bold text-white text-xs group-hover:text-amber-400 transition truncate">${item.title}</div>
                                                <div class="text-[11px] text-neutral-400 font-mono truncate">${item.subtitle}</div>
                                            </div>
                                        </div>
                                        <span class="px-2 py-0.5 rounded text-[9px] font-black tracking-wider ${item.status === 'ACTIVE' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40'} shrink-0">
                                            ${item.status}
                                        </span>
                                    </a>
                                `).join('');
                                dropdown.style.display = 'block';
                            } else {
                                countSpan.textContent = '0 found';
                                suggestionsList.innerHTML = `
                                    <div class="p-3 text-center text-xs text-neutral-400 font-medium">
                                        No members matching "${query}"
                                    </div>
                                `;
                                dropdown.style.display = 'block';
                            }
                        })
                        .catch(() => {
                            dropdown.style.display = 'none';
                        });
                }, 200);
            });

            document.addEventListener('click', function(e) {
                const btn = document.getElementById('adminDropdownBtn');
                const menu = document.getElementById('adminProfileMenu');
                
                if (menu && btn && !btn.contains(e.target) && !menu.contains(e.target)) {
                    menu.style.display = 'none';
                }

                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });
        }
    });
</script>
<!-- ======================================
     End Header Area
     ====================================== -->