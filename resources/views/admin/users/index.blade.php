@extends('admin.layouts.app')

@section('content')
<div class="w-full space-y-6 font-sans">
    <!-- Header Banner -->
    <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="pdf-num-badge">UM</span>
                <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">DEX TRADE NETWORK</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient font-heading">USER MANAGEMENT MODULE</h1>
            <p class="text-xs text-neutral-300 mt-1">Inspect registered accounts, active investment packages, wallet balances, and direct fund credits.</p>
        </div>

        <!-- Right Side Header Controls -->
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <a href="{{ route('admin.users.create') }}" class="px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-black font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 transition flex items-center gap-2 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-black" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="16" y1="11" x2="22" y2="11"/></svg>
                ADD NEW USER
            </a>

            <!-- Search Form -->
            <form id="userSearchForm" action="{{ route('admin.users') }}" method="GET" class="flex items-center gap-2 flex-1 lg:flex-none">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <div class="relative flex-1 lg:w-72">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="liveSearchInput" name="search" value="{{ request('search') }}" placeholder="Search name, email, code..." class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400">
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-300 font-bold text-xs hover:bg-amber-500/30 transition flex items-center gap-1.5 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Search
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Quick Status Filter Tabs & Table Container -->
    <div class="bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30 space-y-6">
        <!-- Top Status Filter Tabs (Properly Isolated Row) -->
        <div class="border-b border-amber-500/20 pb-4">
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('admin.users', array_merge(request()->except('status'), ['status' => ''])) }}" class="whitespace-nowrap inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition {{ !request('status') ? 'bg-amber-500 text-black font-black shadow-md' : 'bg-bg border border-amber-500/30 text-neutral-300 hover:text-amber-400' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    All Users
                </a>
                <a href="{{ route('admin.users', array_merge(request()->except('status'), ['status' => 'active'])) }}" class="whitespace-nowrap inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition {{ request('status') === 'active' ? 'bg-emerald-500 text-black font-black shadow-md' : 'bg-bg border border-amber-500/30 text-neutral-300 hover:text-emerald-400' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
                    Active Users
                </a>
                <a href="{{ route('admin.users', array_merge(request()->except('status'), ['status' => 'inactive'])) }}" class="whitespace-nowrap inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition {{ request('status') === 'inactive' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/50 font-black shadow-md' : 'bg-bg border border-amber-500/30 text-neutral-300 hover:text-rose-400' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="17" y1="8" x2="22" y2="13"/><line x1="22" y1="8" x2="17" y2="13"/></svg>
                    Inactive Users
                </a>
            </div>
        </div>

        <!-- Table (FULL WIDTH BELOW TABS) -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-bg text-amber-400 uppercase text-xs font-bold font-heading tracking-wider border-b border-amber-500/30">
                    <tr>
                        <th class="p-4 rounded-l-xl min-w-[200px]">User Profile</th>
                        <th class="p-4 min-w-[180px]">Referral Code & Link</th>
                        <th class="p-4 min-w-[140px]">Sponsor Info</th>
                        <th class="p-4 min-w-[180px]">Active Package</th>
                        <th class="p-4 min-w-[160px]">Wallet Balances</th>
                        <th class="p-4 min-w-[140px]">Registration Date</th>
                        <th class="p-4 min-w-[140px]">Activation Date</th>
                        <th class="p-4 min-w-[120px]">Status</th>
                        <th class="p-4 min-w-[140px]">BOT Status</th>
                        <th class="p-4 rounded-r-xl text-center min-w-[290px]">DIRECT ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody" class="divide-y divide-amber-500/20 text-neutral-200">
                    @forelse($users as $user)
                    @php
                        $activePkg = $user->userPackages->where('status', 'active')->first();
                    @endphp
                    <tr class="hover:bg-amber-500/10 transition user-row">
                        <!-- User Profile -->
                        <td class="p-4 min-w-[200px]">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 text-black font-black text-sm flex items-center justify-center shadow-md shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-black text-white text-sm user-name">{{ $user->name }}</div>
                                    <div class="text-xs text-neutral-400 user-email">{{ $user->email }}</div>
                                    <div class="text-[11px] text-amber-400 font-mono">{{ $user->mobile ?? 'No Mobile' }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Referral Code & Link -->
                        <td class="p-4 min-w-[180px]">
                            <div class="space-y-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-black text-amber-400 font-mono text-sm user-code">{{ $user->referral_code }}</span>
                                    <button onclick="copyToClipboard('{{ $user->referral_code }}', 'Referral Code copied!')" class="p-1 rounded hover:bg-amber-500/20 text-amber-400 transition" title="Copy Referral Code">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    </button>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button onclick="copyToClipboard('{{ url('/user/register?sponsor=' . $user->referral_code . '&position=left') }}', 'Left Referral Link copied!')" class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 hover:bg-amber-500/40 text-[10px] font-bold font-mono border border-amber-500/40" title="Copy Left Leg Link">
                                        Copy Left
                                    </button>
                                    <button onclick="copyToClipboard('{{ url('/user/register?sponsor=' . $user->referral_code . '&position=right') }}', 'Right Referral Link copied!')" class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500/40 text-[10px] font-bold font-mono border border-emerald-500/40" title="Copy Right Leg Link">
                                        Copy Right
                                    </button>
                                </div>
                            </div>
                        </td>

                        <!-- Sponsor Info & Position -->
                        <td class="p-4 min-w-[140px]">
                            <div>
                                @if($user->sponsor)
                                    <div class="font-bold text-white text-xs">
                                        {{ $user->sponsor->name }}
                                    </div>
                                    <div class="text-[11px] text-amber-400 font-mono">
                                        {{ $user->sponsor_code }}
                                    </div>
                                @elseif($user->sponsor_code)
                                    <div class="font-bold text-white text-xs">
                                        {{ $user->sponsor_code }}
                                    </div>
                                    <div class="text-[11px] text-amber-400 font-mono">
                                        {{ $user->sponsor_code }}
                                    </div>
                                @else
                                    <div class="font-bold text-neutral-400 text-xs">
                                        No Sponsor
                                    </div>
                                    <div class="text-[11px] text-neutral-400 font-mono">
                                        N/A
                                    </div>
                                @endif

                                <div class="mt-1">
                                    @if(strtolower((string)$user->position) === 'left')
                                        <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/40 text-[9px] font-black uppercase">👈 LEFT LEG</span>
                                    @elseif(strtolower((string)$user->position) === 'right')
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 text-[9px] font-black uppercase">RIGHT LEG 👉</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-neutral-800 text-neutral-400 border border-neutral-700 text-[9px] font-bold uppercase">UNASSIGNED</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Active Package Column -->
                        <td class="p-4 min-w-[180px]">
                            @if($activePkg)
                                <div class="space-y-1">
                                    <span class="px-2.5 py-1 rounded bg-amber-500/20 text-amber-300 border border-amber-500/40 text-xs font-black uppercase font-heading block">
                                        📦 {{ $activePkg->package->name ?? 'Active Plan' }}
                                    </span>
                                    <div class="text-xs font-mono font-bold text-emerald-400">
                                        ${{ number_format($activePkg->invested_amount, 2) }} <span class="text-[10px] text-neutral-400 font-normal">({{ $activePkg->daily_roi }}% Daily ROI)</span>
                                    </div>
                                </div>
                            @else
                                <span class="px-2.5 py-1 rounded bg-neutral-800 text-neutral-400 border border-neutral-700 text-[10px] font-bold uppercase">
                                    NO ACTIVE PACKAGE
                                </span>
                            @endif
                        </td>

                        <!-- Wallet Balances Column -->
                        <td class="p-4 min-w-[160px] font-mono text-xs space-y-1">
                            <div class="flex items-center gap-1.5 text-neutral-300">
                                <span class="text-[10px] font-sans text-neutral-400">Deposit:</span>
                                <strong class="text-emerald-400 font-black">${{ number_format($user->deposit_wallet, 2) }}</strong>
                            </div>
                            <div class="flex items-center gap-1.5 text-neutral-300">
                                <span class="text-[10px] font-sans text-neutral-400">Earning:</span>
                                <strong class="text-amber-300 font-black">${{ number_format($user->earning_wallet, 2) }}</strong>
                            </div>
                        </td>

                        <!-- Registration Date & Time -->
                        <td class="p-4 min-w-[140px]">
                            <div class="text-xs font-semibold text-white">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</div>
                            <div class="text-[11px] text-neutral-400 font-mono">{{ $user->created_at ? $user->created_at->format('h:i A') : '' }}</div>
                        </td>

                        <!-- Activation Date & Time -->
                        <td class="p-4 min-w-[140px]">
                            @php
                                $rawDate = $user->activated_at ?? ($activePkg ? ($activePkg->purchased_at ?? $activePkg->created_at) : null);
                                $actDate = $rawDate ? \Carbon\Carbon::parse($rawDate) : null;
                            @endphp
                            @if($actDate)
                                <div class="text-xs font-semibold text-emerald-400">{{ $actDate->format('M d, Y') }}</div>
                                <div class="text-[11px] text-neutral-400 font-mono">{{ $actDate->format('h:i A') }}</div>
                            @else
                                <span class="px-2.5 py-1 rounded bg-rose-500/10 text-rose-400 border border-rose-500/30 text-[10px] font-bold uppercase">NOT ACTIVATED</span>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="p-4 min-w-[120px]">
                            @if($user->status === 'active')
                                <span class="px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 text-[10px] font-black uppercase">ACTIVE</span>
                            @else
                                <span class="px-2.5 py-1 rounded bg-rose-500/20 text-rose-300 border border-rose-500/40 text-[10px] font-black uppercase">INACTIVE</span>
                            @endif
                        </td>

                        <!-- BOT Status (Highlighted & Glowing/Blinking) -->
                        <td class="p-4 min-w-[140px]">
                            @if($user->is_bot_active)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/60 text-emerald-300 text-[10px] font-black uppercase tracking-wider animate-pulse shadow-[0_0_12px_rgba(16,185,129,0.5)]" title="Activated at: {{ $user->bot_activated_at?->format('d M Y H:i') }}">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    </span>
                                    BOT ACTIVE
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-[10px] font-bold uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                    BOT INACTIVE
                                </span>
                            @endif
                        </td>

                        <!-- DIRECT ACTIONS (PROPERLY ISOLATED WITH MIN-WIDTH & NO OVERLAPPING) -->
                        <td class="p-4 min-w-[290px]">
                            <div class="flex items-center justify-start gap-1.5 whitespace-nowrap">
                                <!-- 1. DIRECT ADD FUND BUTTON -->
                                <button onclick="openAddFundModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->referral_code }}')" class="px-3 py-1.5 rounded-xl border border-amber-400 bg-amber-500 text-black font-black text-xs hover:bg-amber-400 transition flex items-center gap-1 shadow shrink-0" title="Direct Add Fund to Wallet">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-black" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                    Add Fund
                                </button>

                                <!-- 2. LOGIN AS USER -->
                                <a href="{{ route('admin.users.impersonate', $user->id) }}" target="_blank" class="p-2 rounded-xl border border-amber-500/60 bg-amber-500/10 text-amber-300 hover:bg-amber-500/30 transition shadow flex items-center justify-center shrink-0" title="Login as User">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                </a>

                                <!-- 3. DIRECT MEMBERS VIEW -->
                                <a href="{{ route('admin.network.direct', ['search' => $user->referral_code]) }}" class="p-2 rounded-xl border border-amber-500/60 bg-amber-500/10 text-amber-300 hover:bg-amber-500/30 transition shadow flex items-center justify-center shrink-0" title="View Direct Members Sponsored By User">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                </a>

                                <!-- 4. BINARY TREE VIEW -->
                                <a href="{{ route('admin.network.tree', ['code' => $user->referral_code]) }}" class="p-2 rounded-xl border border-amber-500/60 bg-amber-500/10 text-amber-300 hover:bg-amber-500/30 transition shadow flex items-center justify-center shrink-0" title="View Binary Tree">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><path d="M6 9v12"/><path d="M18 9a9 9 0 0 0-9 9"/></svg>
                                </a>

                                <!-- 5. VIEW PROFILE -->
                                <a href="{{ route('admin.users.show', $user->id) }}" class="p-2 rounded-xl border border-amber-500/60 bg-amber-500/10 text-amber-300 hover:bg-amber-500/30 transition shadow flex items-center justify-center shrink-0" title="View Profile">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>

                                <!-- 6. EDIT MEMBER -->
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="p-2 rounded-xl border border-amber-500/60 bg-amber-500/10 text-amber-300 hover:bg-amber-500/30 transition shadow flex items-center justify-center shrink-0" title="Edit Member">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-8 text-center text-neutral-400 text-sm font-semibold">
                            No platform members found matching your search parameters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-2 border-t border-amber-500/20">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- DYNAMIC DIRECT ADD FUND MODAL (POPUP) -->
<div id="addFundModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-panel border border-amber-500/40 rounded-2xl p-6 w-full max-w-md shadow-2xl space-y-5 relative animate-in fade-in zoom-in duration-200">
        <button onclick="closeAddFundModal()" class="absolute right-4 top-4 text-neutral-400 hover:text-white p-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>

        <div class="flex items-center gap-3 border-b border-amber-500/20 pb-3">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 text-black font-black text-sm flex items-center justify-center shadow-md shrink-0">
                💰
            </div>
            <div>
                <h3 class="text-base font-black text-white font-heading uppercase">DIRECT ADD FUND</h3>
                <p class="text-xs text-neutral-400" id="modalUserMeta">Credit wallet balance directly from Admin</p>
            </div>
        </div>

        <form id="addFundForm" action="" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-amber-400 uppercase tracking-wider">Select Wallet</label>
                <select name="wallet_type" class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-bold text-xs focus:outline-none focus:border-amber-400 cursor-pointer" required>
                    <option value="deposit_wallet">💳 Deposit Wallet (For Purchasing Packages)</option>
                    <option value="earning_wallet">💰 Earning Wallet (For Withdrawals / Earnings)</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-amber-400 uppercase tracking-wider">Amount ($ USD)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-amber-400 font-bold">$</span>
                    <input type="number" step="0.01" min="0.01" name="amount" placeholder="100.00" class="w-full pl-8 pr-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-mono font-bold text-xs focus:outline-none focus:border-amber-400" required>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-amber-400 uppercase tracking-wider">Transaction Remark / Note</label>
                <input type="text" name="remark" value="Directly credited by Admin side" placeholder="Enter custom remark..." class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400" required>
                <p class="text-[10px] text-neutral-400">This remark will be logged in the member's transaction history.</p>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="closeAddFundModal()" class="px-5 py-3 rounded-xl bg-bg border border-neutral-700 text-neutral-300 font-bold text-xs hover:bg-neutral-800 transition">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-black font-black text-xs uppercase tracking-wider shadow-lg transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-black" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    Add Fund Now
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddFundModal(userId, userName, userCode) {
        const modal = document.getElementById('addFundModal');
        const form = document.getElementById('addFundForm');
        const meta = document.getElementById('modalUserMeta');

        if (modal && form && meta) {
            form.action = `/admin/users/${userId}/add-fund`;
            meta.innerText = `Crediting ${userName} (${userCode})`;
            modal.classList.remove('hidden');
        }
    }

    function closeAddFundModal() {
        const modal = document.getElementById('addFundModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function copyToClipboard(text, msg) {
        navigator.clipboard.writeText(text).then(() => {
            if (typeof showToast === 'function') {
                showToast('Copied!', msg, 'success');
            } else {
                alert(msg);
            }
        });
    }
</script>
@endsection
