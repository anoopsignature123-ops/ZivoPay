@extends('user.layouts.app')

@section('title', 'My Referral ROI Income')

@section('content')
<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-amber-400 uppercase tracking-widest mb-1">
                <i data-lucide="repeat" class="w-4 h-4 text-amber-400"></i>
                <span>Dex Trade Incomes & Reports</span>
            </div>
            <h1 class="text-2xl font-black font-heading text-white uppercase tracking-wider">
                My Referral ROI Income
            </h1>
            <p class="text-xs text-neutral-400 mt-1">Earn 0.5% daily of total investment from all your direct members daily for 150 days</p>
        </div>
    </div>

    <!-- SUMMARY KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-panel p-5 rounded-2xl border border-amber-500/30 shadow-xl flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center shrink-0">
                <i data-lucide="coins" class="w-6 h-6 text-amber-400"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-amber-400/80 uppercase">Total Referral ROI Earned</p>
                <p class="text-2xl font-black text-white font-mono mt-0.5">${{ number_format($totalAmount, 2) }}</p>
            </div>
        </div>

        <div class="bg-panel p-5 rounded-2xl border border-amber-500/30 shadow-xl flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center shrink-0">
                <i data-lucide="receipt" class="w-6 h-6 text-amber-400"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-amber-400/80 uppercase">Total Payout Transactions</p>
                <p class="text-2xl font-black text-white font-mono mt-0.5">{{ number_format($totalCount) }}</p>
            </div>
        </div>

        <div class="bg-panel p-5 rounded-2xl border border-amber-500/30 shadow-xl flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center shrink-0">
                <i data-lucide="shield-check" class="w-6 h-6 text-amber-400"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-amber-400/80 uppercase">Working Cap Rule</p>
                <p class="text-sm font-bold text-emerald-400 mt-1">8X Working Income Cap</p>
            </div>
        </div>
    </div>

    <!-- 1-ROW COMPACT MULTI-FILTER FORM -->
    <div class="p-3.5 rounded-2xl bg-bg/80 border border-amber-500/40 shadow-lg">
        <form action="{{ route('user.reports.referral-roi') }}" method="GET" class="flex flex-nowrap items-end gap-3 w-full overflow-x-auto text-xs font-sans pb-1">
            
            <div class="w-40 shrink-0">
                <label class="block text-[10px] font-extrabold text-amber-400 uppercase tracking-wider mb-1">FROM DATE</label>
                <div class="relative">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-amber-400"></i>
                    <input type="text" name="start_date" value="{{ request('start_date') }}" placeholder="YYYY-MM-DD" class="datepicker w-full pl-8 pr-3 py-2.5 rounded-xl bg-black/60 border border-amber-500/40 text-white font-mono text-xs focus:outline-none focus:border-amber-400">
                </div>
            </div>

            <div class="w-40 shrink-0">
                <label class="block text-[10px] font-extrabold text-amber-400 uppercase tracking-wider mb-1">TO DATE</label>
                <div class="relative">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-amber-400"></i>
                    <input type="text" name="end_date" value="{{ request('end_date') }}" placeholder="YYYY-MM-DD" class="datepicker w-full pl-8 pr-3 py-2.5 rounded-xl bg-black/60 border border-amber-500/40 text-white font-mono text-xs focus:outline-none focus:border-amber-400">
                </div>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-extrabold text-amber-400 uppercase tracking-wider mb-1">SEARCH TXN # / REMARK</label>
                <div class="relative">
                    <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-amber-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Txn #..." class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-black/60 border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400">
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-black font-black text-xs uppercase tracking-wider shadow-[0_0_15px_rgba(243,202,82,0.5)] transition flex items-center justify-center gap-1.5 shrink-0">
                    <i data-lucide="filter" class="w-3.5 h-3.5 text-black"></i> FILTER
                </button>
                <a href="{{ route('user.reports.referral-roi') }}" class="py-2.5 px-4 rounded-xl bg-black/60 border border-white/60 text-white hover:bg-white/10 font-bold text-xs transition flex items-center justify-center shrink-0">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- LOGS TABLE -->
    <div class="bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30 space-y-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-bg text-amber-400 uppercase text-xs font-bold font-heading tracking-wider border-b border-amber-500/30">
                    <tr>
                        <th class="p-4 rounded-l-xl">TXN NUMBER</th>
                        <th class="p-4">SOURCE MEMBER (DOWNLINE)</th>
                        <th class="p-4">AMOUNT ($)</th>
                        <th class="p-4">POST BALANCE</th>
                        <th class="p-4">DESCRIPTION / REMARK</th>
                        <th class="p-4">DATE & TIME</th>
                        <th class="p-4 rounded-r-xl">STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-500/20 text-neutral-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-amber-500/10 transition">
                        <td class="p-4 font-mono font-bold text-amber-400 text-xs">{{ $log->txn_number }}</td>
                        <td class="p-4">
                            @if($log->source_member)
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($log->source_member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-white text-xs">{{ $log->source_member->name }}</div>
                                        <div class="text-[11px] text-amber-400 font-mono">{{ $log->source_member->referral_code }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-neutral-400 text-xs font-mono">Direct Referral</span>
                            @endif
                        </td>
                        <td class="p-4 font-mono font-black text-emerald-400">+${{ number_format($log->amount, 2) }}</td>
                        <td class="p-4 font-mono text-neutral-300">${{ number_format($log->post_balance, 2) }}</td>
                        <td class="p-4 text-xs text-neutral-300 max-w-xs truncate">{{ $log->description }}</td>
                        <td class="p-4 text-xs text-neutral-400 font-mono">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 uppercase">
                                Completed
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-neutral-400 font-medium">
                            No Referral ROI Income records found matching your filter parameters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="pt-4 border-t border-amber-500/20">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
