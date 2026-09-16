@extends('user.layouts.app')

@section('title', 'Subscription Income Report - ZIVO PAY')

@section('content')
<div class="space-y-6 font-sans">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[11px] font-black uppercase tracking-widest border border-emerald-500/40">
                12% TOTAL 15-LEVEL REFERRAL COMMISSIONS
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight mt-1">{{ $reportTitle ?? '15-LEVEL SUBSCRIPTION INCOME' }}</h1>
            <p class="text-xs text-neutral-300 mt-1">Instant referral commissions (Level 1: 5%, Levels 2-15: 0.50%) earned on mandatory ₹3,000 package activations.</p>
        </div>

        <div class="px-5 py-3 rounded-2xl bg-[#02180f] border border-emerald-500/40 text-right">
            <span class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-wider block">Total Level Income Earned</span>
            <span class="text-2xl font-black text-white font-mono">₹{{ number_format($totalAmount, 2) }}</span>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-[#042718] p-4 rounded-2xl border border-emerald-500/30">
        <form action="{{ request()->url() }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div>
                <label class="block text-[10px] text-emerald-400 font-bold uppercase mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
            </div>
            <div>
                <label class="block text-[10px] text-emerald-400 font-bold uppercase mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
            </div>
            <div class="pt-5 flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs rounded-xl uppercase tracking-wider transition">
                    Filter
                </button>
                <a href="{{ request()->url() }}" class="px-3 py-2 bg-[#01140c] border border-emerald-500/30 text-neutral-400 text-xs rounded-xl hover:text-white transition">
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
                        <th class="px-5 py-4">TRX ID</th>
                        <th class="px-5 py-4">From Member</th>
                        <th class="px-5 py-4">Level</th>
                        <th class="px-5 py-4">Amount Earned</th>
                        <th class="px-5 py-4">Description</th>
                        <th class="px-5 py-4">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/10 text-neutral-200 font-medium">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-emerald-500/5 transition">
                            <td class="px-5 py-4 font-mono font-bold text-emerald-300">{{ $trx->trx_id }}</td>
                            <td class="px-5 py-4">
                                @if($trx->fromUser)
                                    <p class="font-bold text-white">{{ $trx->fromUser->name }}</p>
                                    <p class="text-[10px] text-neutral-400 font-mono">{{ $trx->fromUser->referral_code }}</p>
                                @else
                                    <span class="text-neutral-500 italic">Account Activation</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                    {{ $trx->level ? 'Level ' . $trx->level : 'Direct' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-bold text-emerald-400 text-sm">+₹{{ number_format($trx->amount, 2) }}</td>
                            <td class="px-5 py-4 text-neutral-300">{{ $trx->description }}</td>
                            <td class="px-5 py-4">
                                <p class="text-white font-bold">{{ $trx->created_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-neutral-400 font-mono">{{ $trx->created_at->format('h:i:s A') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-neutral-400 font-bold">
                                No subscription referral income records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-emerald-500/20">
                {{ $transactions->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
