@extends('user.layouts.app')

@section('title', 'My Investments History - ZIVO PAY')

@section('content')
<div class="space-y-6 font-sans">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.25)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[11px] font-black uppercase tracking-widest border border-emerald-500/40">
                CAPITAL PORTFOLIO AUDIT
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight mt-1 font-heading">CAPITAL INVESTMENT HISTORY</h1>
            <p class="text-xs text-neutral-300 mt-1">Audit log of your active and past capital package investments.</p>
        </div>

        <div class="px-5 py-3 rounded-2xl bg-[#02180f] border border-emerald-500/40 text-right">
            <span class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-wider block">Total Active Investment</span>
            <span class="text-2xl font-black text-white font-mono">₹{{ number_format($totalAmount, 2) }}</span>
        </div>
    </div>

    <!-- Official Filter Bar Component -->
    <div class="p-4 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-xl">
        <form action="{{ route('user.reports.investments') }}" method="GET" class="zivo-filter-bar">
            <!-- FROM DATE -->
            <div class="zivo-filter-field-date">
                <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">FROM DATE</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}"
                    class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
            </div>

            <!-- TO DATE -->
            <div class="zivo-filter-field-date">
                <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">TO DATE</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}"
                    class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
            </div>

            <!-- STATUS -->
            <div class="zivo-filter-field-select">
                <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">STATUS</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400 transition cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- SEARCH PLAN / REF -->
            <div class="zivo-filter-field-search">
                <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">SEARCH REF / PLAN</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-emerald-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Plan Name, Tier..."
                        class="w-full pl-9 pr-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="zivo-filter-actions">
                <button type="submit" class="py-2 px-5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-black font-black uppercase tracking-wider text-xs hover:shadow-[0_0_20px_rgba(16,185,129,0.7)] transition flex items-center justify-center gap-1.5 shadow cursor-pointer">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i> FILTER
                </button>
                <a href="{{ route('user.reports.investments') }}" class="py-2 px-3.5 rounded-xl bg-black/60 border border-emerald-500/30 text-neutral-300 font-bold text-xs hover:text-white hover:border-emerald-400 transition text-center shrink-0">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Report Table -->
    <div class="bg-[#042718] rounded-3xl border border-emerald-500/30 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#02180f] text-emerald-400 font-extrabold uppercase tracking-wider border-b border-emerald-500/30">
                    <tr>
                        <th class="px-5 py-4">ID</th>
                        <th class="px-5 py-4">Package</th>
                        <th class="px-5 py-4">Capital Invested</th>
                        <th class="px-5 py-4">Daily Return Rate</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Activated At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/10 text-neutral-200 font-medium">
                    @forelse($investments as $inv)
                        <tr class="hover:bg-emerald-500/5 transition">
                            <td class="px-5 py-4 font-mono font-bold text-emerald-300">#INV-{{ $inv->id }}</td>
                            <td class="px-5 py-4">
                                <span class="font-extrabold text-white uppercase">{{ $inv->plan_name ?? $inv->package_name ?? 'Capital Package' }}</span>
                            </td>
                            <td class="px-5 py-4 font-bold text-emerald-400 text-sm">₹{{ number_format($inv->amount, 2) }}</td>
                            <td class="px-5 py-4 text-emerald-300 font-bold">
                                {{ $inv->daily_percentage ?? number_format(($inv->daily_roi_rate ?? 0.0015) * 100, 2) }}% / day
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $inv->status === 'active' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-neutral-500/20 text-neutral-300 border border-neutral-500/40' }}">
                                    {{ strtoupper($inv->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-white font-bold">{{ $inv->created_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-neutral-400 font-mono">{{ $inv->created_at->format('h:i:s A') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-neutral-400 font-bold">
                                No investment records found matching your filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($investments->hasPages())
            <div class="p-4 border-t border-emerald-500/20">
                {{ $investments->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
