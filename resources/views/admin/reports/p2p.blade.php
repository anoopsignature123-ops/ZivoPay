@extends('admin.layouts.app')

@section('title', 'P2P Member Transfers Audit - Admin ZIVO PAY')

@section('content')
<div class="w-full space-y-6 font-sans">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-slate-900/95 border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.3)] flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] uppercase border border-emerald-500/40">P2P AUDIT</span>
                <span class="text-xs text-emerald-400 font-black tracking-[3px] uppercase">ZIVO PAY FINANCIALS</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white font-heading">P2P MEMBER TRANSFERS REPORT</h1>
            <p class="text-xs text-neutral-300 mt-1">Audit log of all Peer-to-Peer member fund transfers executed across the platform.</p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <div class="px-5 py-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/40 text-right">
                <span class="text-[10px] uppercase tracking-wider text-emerald-400 font-bold block">Total P2P Volume</span>
                <span class="text-xl sm:text-2xl font-black text-white font-mono">₹{{ number_format($totalVolume, 2) }}</span>
            </div>
            <div class="px-5 py-3 rounded-2xl bg-teal-500/10 border border-teal-500/40 text-right">
                <span class="text-[10px] uppercase tracking-wider text-teal-300 font-bold block">Total Transfers</span>
                <span class="text-xl sm:text-2xl font-black text-white font-mono">{{ number_format($totalCount) }}</span>
            </div>
        </div>
    </div>

    <!-- Filter Form Bar -->
    <div class="bg-slate-900/90 p-4 sm:p-5 rounded-3xl border border-emerald-500/30 shadow-xl">
        <form action="{{ route('admin.reports.p2p') }}" method="GET" class="flex flex-wrap items-end gap-3">
            <div class="w-36 sm:w-40 shrink-0">
                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-3 py-2.5 rounded-xl bg-bg border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
            </div>

            <div class="w-36 sm:w-40 shrink-0">
                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-3 py-2.5 rounded-xl bg-bg border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
            </div>

            <div class="flex-1 min-w-[220px]">
                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Search Sender / Receiver / TRX</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="TRX ID, Sender Code, Receiver Code, Name..." class="w-full pl-9 pr-3.5 py-2.5 rounded-xl bg-bg border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
                    <i data-lucide="search" class="w-4 h-4 text-emerald-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs uppercase tracking-wider transition flex items-center justify-center gap-1.5 shadow-md">
                    <i data-lucide="filter" class="w-4 h-4"></i> Filter
                </button>
                <a href="{{ route('admin.reports.p2p') }}" class="px-3.5 py-2.5 rounded-xl bg-bg border border-emerald-500/30 text-neutral-400 hover:text-white text-xs font-bold transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- P2P Transfers Table -->
    <div class="rounded-3xl bg-panel border border-emerald-500/30 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-neutral-300">
                <thead class="bg-neutral-900/90 text-emerald-400 font-extrabold uppercase tracking-wider border-b border-emerald-500/20 text-[10px]">
                    <tr>
                        <th class="px-4 py-3.5">TRX ID</th>
                        <th class="px-4 py-3.5">Sender Details</th>
                        <th class="px-4 py-3.5">Receiver Details</th>
                        <th class="px-4 py-3.5">Source Wallet</th>
                        <th class="px-4 py-3.5">Amount (₹)</th>
                        <th class="px-4 py-3.5">Remarks</th>
                        <th class="px-4 py-3.5 text-right">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/10">
                    @forelse($transfers as $trf)
                        <tr class="hover:bg-emerald-500/5 transition">
                            <td class="px-4 py-3.5 font-mono font-bold text-white whitespace-nowrap">
                                {{ $trf->trx_id }}
                            </td>
                            <td class="px-4 py-3.5">
                                <p class="font-bold text-white text-xs">{{ $trf->sender->name ?? 'N/A' }}</p>
                                <p class="text-[10px] text-teal-400 font-mono">Code: {{ $trf->sender->referral_code ?? 'N/A' }}</p>
                            </td>
                            <td class="px-4 py-3.5">
                                <p class="font-bold text-white text-xs">{{ $trf->receiver->name ?? 'N/A' }}</p>
                                <p class="text-[10px] text-emerald-400 font-mono">Code: {{ $trf->receiver->referral_code ?? 'N/A' }}</p>
                            </td>
                            <td class="px-4 py-3.5 font-semibold capitalize">
                                {{ str_replace('_', ' ', $trf->from_wallet) }}
                            </td>
                            <td class="px-4 py-3.5 font-mono font-black text-sm text-emerald-400 whitespace-nowrap">
                                ₹{{ number_format($trf->amount, 2) }}
                            </td>
                            <td class="px-4 py-3.5 text-neutral-400 text-[11px]">
                                {{ $trf->remarks ?: '-' }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right font-mono text-[11px] text-neutral-400">
                                {{ $trf->created_at->format('d M Y, h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-neutral-400">
                                No P2P member transfer records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transfers->hasPages())
            <div class="p-4 border-t border-emerald-500/20">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
