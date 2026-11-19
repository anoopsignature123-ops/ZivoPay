@extends('user.layouts.app')

@section('title', 'Activation Income Report - ZIVO PAY')

@section('content')
    <div class="space-y-6 font-sans">
        <div class="p-6 sm:p-8 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[11px] font-black uppercase tracking-widest border border-emerald-500/40">ACTIVATION PAYOUT</span>
                <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight mt-1">{{ $reportTitle }}</h1>
                <p class="text-xs text-neutral-300 mt-1">Shopping Wallet credit recorded for your successful Family Kit activation.</p>
            </div>
            <div class="px-5 py-3 rounded-2xl bg-[#02180f] border border-emerald-500/40 text-right">
                <span class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-wider block">Total Activation Credit</span>
                <span class="text-2xl font-black text-white font-mono">₹{{ number_format($totalAmount, 2) }}</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-xl">
            <form action="{{ url()->current() }}" method="GET" class="zivo-filter-bar">
                <div class="zivo-filter-field-date">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">FROM DATE</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
                </div>
                <div class="zivo-filter-field-date">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">TO DATE</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
                </div>
                <div class="zivo-filter-field-search">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">SEARCH REF / TRX ID</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 text-emerald-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="TRX ID, Description..." class="w-full pl-9 pr-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
                    </div>
                </div>
                <div class="zivo-filter-actions">
                    <button type="submit" class="py-2 px-5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-black font-black uppercase tracking-wider text-xs flex items-center justify-center gap-1.5 shadow cursor-pointer">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i> FILTER
                    </button>
                    <a href="{{ url()->current() }}" class="py-2 px-3.5 rounded-xl bg-black/60 border border-emerald-500/30 text-neutral-300 font-bold text-xs text-center shrink-0">Reset</a>
                </div>
            </form>
        </div>

        <div class="bg-[#042718] rounded-3xl border border-emerald-500/30 overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#02180f] text-emerald-400 font-extrabold uppercase tracking-wider border-b border-emerald-500/30">
                        <tr>
                            <th class="px-5 py-4">TRX ID</th>
                            <th class="px-5 py-4">Wallet Type</th>
                            <th class="px-5 py-4">Amount Credited</th>
                            <th class="px-5 py-4">Description</th>
                            <th class="px-5 py-4 text-right">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-500/10 text-neutral-200 font-medium">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-emerald-500/5 transition">
                                <td class="px-5 py-4 font-mono font-bold text-emerald-300">{{ $transaction->trx_id }}</td>
                                <td class="px-5 py-4"><span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-teal-500/20 text-teal-300 border border-teal-500/40">Shopping Wallet</span></td>
                                <td class="px-5 py-4 font-bold text-emerald-400 text-sm">+₹{{ number_format($transaction->amount, 2) }}</td>
                                <td class="px-5 py-4 text-neutral-300">{{ $transaction->description }}</td>
                                <td class="px-5 py-4 text-right"><p class="text-white font-bold">{{ $transaction->created_at->format('d M Y') }}</p><p class="text-[10px] text-neutral-400 font-mono">{{ $transaction->created_at->format('h:i:s A') }}</p></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-8 text-center text-neutral-400 font-bold">No activation income records found matching your filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($transactions->hasPages())
                <div class="p-4 border-t border-emerald-500/20">{{ $transactions->links() }}</div>
            @endif
        </div>
    </div>
@endsection
