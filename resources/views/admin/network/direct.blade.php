@extends('admin.layouts.app')

@section('content')
<div class="w-full space-y-6">
    <!-- Header Banner -->
    <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="pdf-num-badge">DM</span>
                <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">DEX TRADE NETWORK</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient font-heading">DIRECT MEMBERS DIRECTORY</h1>
            <p class="text-xs text-neutral-300 mt-1">Overview of all sponsored members across the platform network.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.network.tree') }}" class="px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-black font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 transition flex items-center gap-2">
                <i data-lucide="git-merge" class="w-4 h-4 text-black"></i> VIEW BINARY TEAM TREE
            </a>
        </div>
    </div>

    <!-- Stat Cards Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-panel border border-amber-500/30 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-neutral-400">Total Sponsored Members</span>
                <i data-lucide="users" class="w-5 h-5 text-amber-400"></i>
            </div>
            <h3 class="text-2xl font-black text-white mt-2">{{ $stats['total'] }}</h3>
        </div>
        <div class="p-5 rounded-2xl bg-panel border border-amber-500/30 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-neutral-400">Active Directs</span>
                <i data-lucide="user-check" class="w-5 h-5 text-emerald-400"></i>
            </div>
            <h3 class="text-2xl font-black text-emerald-400 mt-2">{{ $stats['active'] }}</h3>
        </div>
        <div class="p-5 rounded-2xl pdf-package-card shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-neutral-400">Team A Network</span>
                <i data-lucide="users" class="w-5 h-5 text-amber-400"></i>
            </div>
            <h3 class="text-2xl font-black text-amber-300 mt-2">{{ $stats['left'] }}</h3>
        </div>
        <div class="p-5 rounded-2xl pdf-package-card shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-neutral-400">Team B Network</span>
                <i data-lucide="users" class="w-5 h-5 text-emerald-400"></i>
            </div>
            <h3 class="text-2xl font-black text-emerald-400 mt-2">{{ $stats['right'] }}</h3>
        </div>
    </div>

    <!-- Filter & Table Container -->
    <div class="pdf-package-card p-6 shadow-2xl rounded-2xl space-y-6">
        <!-- Search & Filter Form -->
        <form action="{{ route('admin.network.direct') }}" method="GET" class="flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-amber-500/20 pb-4">
            <div class="relative w-full sm:w-72">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-amber-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, code, sponsor..." class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400">
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <select name="position" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl bg-bg border border-amber-500/40 text-amber-400 font-bold text-xs focus:outline-none cursor-pointer">
                    <option value="">All Team Branches</option>
                    <option value="left" {{ request('position') === 'left' ? 'selected' : '' }}>Team A Network</option>
                    <option value="right" {{ request('position') === 'right' ? 'selected' : '' }}>Team B Network</option>
                </select>

                <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl bg-bg border border-amber-500/40 text-amber-400 font-bold text-xs focus:outline-none cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Members</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Members</option>
                </select>
            </div>
        </form>

        <!-- Direct Members Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-black/80 text-amber-400 uppercase text-xs font-bold font-heading tracking-wider border-b border-amber-500/30">
                    <tr>
                        <th class="p-4 rounded-l-xl">Direct Member</th>
                        <th class="p-4">Referral Code</th>
                        <th class="p-4">Sponsor Info</th>
                        <th class="p-4">Team Branch</th>
                        <th class="p-4">Registration Date</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 rounded-r-xl text-center">DIRECT ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-500/20 text-neutral-200">
                    @forelse($directs as $direct)
                    <tr class="hover:bg-amber-500/10 transition">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full pdf-gold-badge text-black font-black text-xs flex items-center justify-center shadow">
                                    {{ strtoupper(substr($direct->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-white text-sm">{{ $direct->name }}</div>
                                    <div class="text-xs text-neutral-400">{{ $direct->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 font-mono font-bold text-amber-400">{{ $direct->referral_code }}</td>
                        <td class="p-4">
                            @if($direct->sponsor)
                                <div class="font-bold text-white text-xs">{{ $direct->sponsor->name }}</div>
                                <div class="text-[11px] text-amber-400 font-mono">{{ $direct->sponsor_code }}</div>
                            @elseif($direct->sponsor_code)
                                <div class="font-bold text-white text-xs">{{ $direct->sponsor_code }}</div>
                                <div class="text-[11px] text-amber-400 font-mono">{{ $direct->sponsor_code }}</div>
                            @else
                                <div class="font-bold text-neutral-400 text-xs">No Sponsor</div>
                                <div class="text-[11px] text-neutral-400 font-mono">N/A</div>
                            @endif
                        </td>
                        <td class="p-4">
                            @if(strtolower((string)$direct->position) === 'left')
                                <span class="px-2.5 py-1 rounded bg-amber-500/20 text-amber-300 border border-amber-500/40 text-[10px] font-black uppercase">TEAM A</span>
                            @elseif(strtolower((string)$direct->position) === 'right')
                                <span class="px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 text-[10px] font-black uppercase">TEAM B</span>
                            @else
                                <span class="px-2.5 py-1 rounded bg-amber-500/10 text-amber-400 border border-amber-500/30 text-[10px] font-black uppercase">DIRECT</span>
                            @endif
                        </td>
                        <td class="p-4 text-xs font-medium text-neutral-300">{{ $direct->created_at ? $direct->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
                        <td class="p-4">
                            @if($direct->status === 'active')
                                <span class="px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 text-[10px] font-black uppercase">ACTIVE</span>
                            @else
                                <span class="px-2.5 py-1 rounded bg-rose-500/20 text-rose-300 border border-rose-500/40 text-[10px] font-black uppercase">INACTIVE</span>
                            @endif
                        </td>
                        <!-- DIRECT ACTIONS (Matching Gold Outline Pill & Square Buttons) -->
                        <td class="p-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.users.impersonate', $direct->id) }}" target="_blank" class="px-3.5 py-1.5 rounded-xl border border-amber-500/60 bg-amber-500/10 text-amber-300 hover:bg-amber-500/30 hover:border-amber-400 transition flex items-center gap-1.5 text-xs font-bold shadow" title="Login as User in New Tab">
                                    <i data-lucide="external-link" class="w-4 h-4 text-amber-400"></i> Login as User
                                </a>
                                <a href="{{ route('admin.network.tree', ['code' => $direct->referral_code]) }}" class="p-2 rounded-xl border border-amber-500/60 bg-amber-500/10 text-amber-300 hover:bg-amber-500/30 hover:border-amber-400 transition shadow flex items-center justify-center" title="View Binary Tree">
                                    <i data-lucide="git-merge" class="w-4 h-4 text-amber-400"></i>
                                </a>
                                <a href="{{ route('admin.users.show', $direct->id) }}" class="p-2 rounded-xl border border-amber-500/60 bg-amber-500/10 text-amber-300 hover:bg-amber-500/30 hover:border-amber-400 transition shadow flex items-center justify-center" title="View Profile">
                                    <i data-lucide="eye" class="w-4 h-4 text-amber-400"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $direct->id) }}" class="p-2 rounded-xl border border-amber-500/60 bg-amber-500/10 text-amber-300 hover:bg-amber-500/30 hover:border-amber-400 transition shadow flex items-center justify-center" title="Edit Member">
                                    <i data-lucide="edit-3" class="w-4 h-4 text-amber-400"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-neutral-400 text-sm font-semibold">
                            No sponsored members found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-amber-500/20">
            {{ $directs->links() }}
        </div>
    </div>
</div>
@endsection
