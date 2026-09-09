@extends('user.layouts.app')

@section('title', 'My 17-Level Salary Income')

@section('content')
<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-amber-400 uppercase tracking-widest mb-1">
                <i data-lucide="award" class="w-4 h-4 text-amber-400"></i>
                <span>Dex Trade Incomes & Reports</span>
            </div>
            <h1 class="text-2xl font-black font-heading text-white uppercase tracking-wider">
                My 17-Level Salary Income
            </h1>
            <p class="text-xs text-neutral-400 mt-1">Milestone salary rewards based on Binary Matching Volume & Direct Team Business ($50/mo to $12 Lakh/mo over 5–25 Months)</p>
        </div>
    </div>

    <!-- SUMMARY KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-panel p-5 rounded-2xl border border-amber-500/30 shadow-xl flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center shrink-0">
                <i data-lucide="coins" class="w-6 h-6 text-amber-400"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-amber-400/80 uppercase">Total Salary Earned</p>
                <p class="text-2xl font-black text-white font-mono mt-0.5">${{ number_format($totalAmount, 2) }}</p>
            </div>
        </div>

        <div class="bg-panel p-5 rounded-2xl border border-amber-500/30 shadow-xl flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center shrink-0">
                <i data-lucide="receipt" class="w-6 h-6 text-amber-400"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-amber-400/80 uppercase">Total Salary Payouts</p>
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

    <!-- LOGS TABLE -->
    <div class="bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30 space-y-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-bg text-amber-400 uppercase text-xs font-bold font-heading tracking-wider border-b border-amber-500/30">
                    <tr>
                        <th class="p-4 rounded-l-xl">TXN NUMBER</th>
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
                        <td colspan="6" class="p-8 text-center text-neutral-400 font-medium">
                            No Salary Income records found.
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
