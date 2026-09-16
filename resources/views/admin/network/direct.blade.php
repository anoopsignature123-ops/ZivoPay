@extends('admin.layouts.app')

@section('content')
<div class="w-full space-y-6">
    <!-- Header Banner -->
    <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="pdf-num-badge">DM</span>
                <span class="text-xs text-emerald-400 font-extrabold tracking-[3px] uppercase">ZIVO PAY NETWORK</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white font-heading">DIRECT MEMBERS DIRECTORY</h1>
            <p class="text-xs text-neutral-300 mt-1">Overview of all sponsored members across the platform network.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.network.tree') }}" class="px-5 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 transition flex items-center gap-2">
                <i data-lucide="git-merge" class="w-4 h-4 text-white"></i> VIEW TEAM TREE
            </a>
        </div>
    </div>

    <!-- Stat Cards Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-panel border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-neutral-400">Total Sponsored Members</span>
                <i data-lucide="users" class="w-5 h-5 text-emerald-400"></i>
            </div>
            <h3 class="text-2xl font-black text-white mt-2">{{ $stats['total'] }}</h3>
        </div>
        <div class="p-5 rounded-2xl bg-panel border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-neutral-400">Active Directs</span>
                <i data-lucide="user-check" class="w-5 h-5 text-emerald-400"></i>
            </div>
            <h3 class="text-2xl font-black text-emerald-400 mt-2">{{ $stats['active'] }}</h3>
        </div>
        <div class="p-5 rounded-2xl bg-panel border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-neutral-400">Inactive Directs</span>
                <i data-lucide="user-x" class="w-5 h-5 text-rose-400"></i>
            </div>
            <h3 class="text-2xl font-black text-rose-400 mt-2">{{ $stats['inactive'] }}</h3>
        </div>
    </div>

    <!-- Filter & Table Container -->
    <div class="bg-panel p-6 shadow-2xl rounded-2xl border border-emerald-500/30 space-y-6">
        <!-- Search & Filter Form -->
        <form action="{{ route('admin.network.direct') }}" method="GET" class="flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-emerald-500/20 pb-4">
            <div class="relative w-full sm:w-72">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, code, sponsor..." class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400">
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl bg-bg border border-emerald-500/40 text-emerald-400 font-bold text-xs focus:outline-none cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Members</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Members</option>
                </select>
            </div>
        </form>

        <!-- Direct Members Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap poster-clean-table">
                <thead>
                    <tr>
                        <th class="p-4 rounded-l-xl">Direct Member</th>
                        <th class="p-4">Referral Code</th>
                        <th class="p-4">Sponsor Info</th>
                        <th class="p-4">Registration Date</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 rounded-r-xl text-center">DIRECT ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($directs as $direct)
                    <tr>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-emerald-700 text-white font-black text-xs flex items-center justify-center shadow">
                                    {{ strtoupper(substr($direct->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="poster-table-title">{{ $direct->name }}</div>
                                    <div class="poster-table-sub">{{ $direct->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 poster-table-code">{{ $direct->referral_code }}</td>
                        <td class="p-4">
                            @if($direct->sponsor)
                                <div class="poster-table-title text-xs">{{ $direct->sponsor->name }}</div>
                                <div class="poster-table-code text-[11px]">{{ $direct->sponsor_code }}</div>
                            @elseif($direct->sponsor_code)
                                <div class="poster-table-code text-xs">{{ $direct->sponsor_code }}</div>
                            @else
                                <div class="poster-table-sub text-xs italic">No Sponsor</div>
                            @endif
                        </td>
                        <td class="p-4 poster-table-sub">{{ $direct->created_at ? $direct->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
                        <td class="p-4">
                            @if($direct->status === 'active')
                                <span class="poster-badge-active">ACTIVE</span>
                            @else
                                <span class="poster-badge-inactive">INACTIVE</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.users.impersonate', $direct->id) }}" target="_blank" 
                                    title="Login as Member ({{ $direct->name }})" 
                                    aria-label="Login as Member" 
                                    class="w-9 h-8 rounded-2xl bg-[#02180f] border border-amber-500/60 text-amber-300 hover:bg-amber-400 hover:text-black hover:border-amber-400 font-bold transition-all duration-200 flex items-center justify-center shadow-[0_0_10px_rgba(245,158,11,0.15)] hover:scale-105 shrink-0">
                                    <i data-lucide="external-link" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.network.tree', ['search' => $direct->referral_code]) }}" 
                                    title="View Team Tree ({{ $direct->referral_code }})" 
                                    aria-label="View Team Tree" 
                                    class="w-9 h-8 rounded-2xl bg-[#02180f] border border-amber-500/60 text-amber-300 hover:bg-amber-400 hover:text-black hover:border-amber-400 font-bold transition-all duration-200 flex items-center justify-center shadow-[0_0_10px_rgba(245,158,11,0.15)] hover:scale-105 shrink-0">
                                    <i data-lucide="git-fork" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.users.show', $direct->id) }}" 
                                    title="View Profile Details ({{ $direct->name }})" 
                                    aria-label="View Profile Details" 
                                    class="w-9 h-8 rounded-2xl bg-[#02180f] border border-amber-500/60 text-amber-300 hover:bg-amber-400 hover:text-black hover:border-amber-400 font-bold transition-all duration-200 flex items-center justify-center shadow-[0_0_10px_rgba(245,158,11,0.15)] hover:scale-105 shrink-0">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $direct->id) }}" 
                                    title="Edit Member Profile ({{ $direct->name }})" 
                                    aria-label="Edit Member Profile" 
                                    class="w-9 h-8 rounded-2xl bg-[#02180f] border border-amber-500/60 text-amber-300 hover:bg-amber-400 hover:text-black hover:border-amber-400 font-bold transition-all duration-200 flex items-center justify-center shadow-[0_0_10px_rgba(245,158,11,0.15)] hover:scale-105 shrink-0">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500 text-sm font-semibold">
                            No sponsored members found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-emerald-500/20">
            {{ $directs->links() }}
        </div>
    </div>
</div>
@endsection
