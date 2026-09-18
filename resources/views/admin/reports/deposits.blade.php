@extends('admin.layouts.app')

@section('title', 'Fund Wallet History - Admin ZIVO PAY')

@section('content')
<div class="w-full space-y-6 font-sans">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-slate-900/95 border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.3)] flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] uppercase border border-emerald-500/40">REPORTS</span>
                <span class="text-xs text-emerald-400 font-black tracking-[3px] uppercase">ZIVO PAY FINANCIALS</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white font-heading">FUND WALLET HISTORY</h1>
            <p class="text-xs text-neutral-300 mt-1">Audit log of all user deposits, admin fund credits, and capital additions.</p>
        </div>

        <div class="px-5 py-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/40 text-right">
            <span class="text-[10px] uppercase tracking-wider text-emerald-400 font-bold block">Filtered Total Volume</span>
            <span class="text-xl sm:text-2xl font-black text-white">₹{{ number_format($totalDepositAmount, 2) }}</span>
        </div>
    </div>

    <!-- Filter Form Bar -->
    <div class="bg-slate-900/90 p-4 sm:p-5 rounded-3xl border border-emerald-500/30 shadow-xl">
        <form action="{{ route('admin.reports.deposits') }}" method="GET" class="flex flex-wrap items-end gap-3">
            <div class="w-36 sm:w-40 shrink-0">
                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-3 py-2.5 rounded-xl bg-bg border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
            </div>

            <div class="w-36 sm:w-40 shrink-0">
                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-3 py-2.5 rounded-xl bg-bg border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
            </div>

            <div class="w-40 sm:w-48 shrink-0">
                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Deposit Type</label>
                <select name="type" class="w-full px-3.5 py-2.5 rounded-xl bg-bg border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
                    <option value="">All Types</option>
                    <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Direct Add Fund</option>
                    <option value="admin_credit" {{ request('type') == 'admin_credit' ? 'selected' : '' }}>Admin Fund Credit</option>
                    <option value="investment" {{ request('type') == 'investment' ? 'selected' : '' }}>Investment Capital</option>
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Search User / TRX</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="TRX ID, Name, Email, Code..." class="w-full pl-9 pr-3.5 py-2.5 rounded-xl bg-bg border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
                    <i data-lucide="search" class="w-4 h-4 text-emerald-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs uppercase tracking-wider transition flex items-center justify-center gap-1.5 shadow-md">
                    <i data-lucide="filter" class="w-4 h-4"></i> Filter
                </button>
                <a href="{{ route('admin.reports.deposits') }}" class="px-3.5 py-2.5 rounded-xl bg-bg border border-emerald-500/30 text-neutral-400 hover:text-white text-xs font-bold transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Deposits Table -->
    <div class="bg-slate-900/90 rounded-3xl border border-emerald-500/30 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/90 text-emerald-400 font-extrabold uppercase tracking-wider border-b border-emerald-500/30">
                    <tr>
                        <th class="px-5 py-4">Transaction ID</th>
                        <th class="px-5 py-4">User Details</th>
                        <th class="px-5 py-4">Type</th>
                        <th class="px-5 py-4">Amount</th>
                        <th class="px-5 py-4">Description</th>
                        <th class="px-5 py-4">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/10 text-neutral-200 font-medium">
                    @forelse($deposits as $deposit)
                        <tr class="hover:bg-emerald-500/5 transition">
                            <td class="px-5 py-4">
                                <span class="font-mono font-bold text-emerald-300 text-xs">{{ $deposit->trx_id }}</span>
                            </td>
                            <td class="px-5 py-4">
                                @if($deposit->user)
                                    <div>
                                        <p class="font-bold text-white">{{ $deposit->user->name }}</p>
                                        <p class="text-[11px] text-neutral-400">{{ $deposit->user->email }}</p>
                                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-mono text-[10px] border border-emerald-500/30">
                                            Code: {{ $deposit->user->referral_code }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-neutral-500 italic">User Deleted</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($deposit->type === 'deposit')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                                        Direct Deposit
                                    </span>
                                @elseif($deposit->type === 'admin_credit')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-teal-500/20 text-teal-300 border border-teal-500/40">
                                        Admin Credit
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-slate-800 text-neutral-300 border border-neutral-700">
                                        {{ str_replace('_', ' ', $deposit->type) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-bold text-emerald-400 text-sm">
                                    +₹{{ number_format($deposit->amount, 2) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-neutral-300 text-xs max-w-xs truncate">
                                {{ $deposit->description ?: 'Fund Wallet Addition' }}
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-white font-bold text-xs">{{ $deposit->created_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-neutral-400 font-mono">{{ $deposit->created_at->format('h:i:s A') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-neutral-400">
                                <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 text-emerald-500/40"></i>
                                <p class="font-bold text-sm">No deposit records found matching your filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($deposits->hasPages())
            <div class="p-4 border-t border-emerald-500/20">
                {{ $deposits->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
