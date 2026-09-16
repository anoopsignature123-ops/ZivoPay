@extends('user.layouts.app')

@section('title', 'Package Purchase History - ZIVO PAY')

@section('content')
<div class="space-y-6 font-sans">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.25)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[11px] font-black uppercase tracking-widest border border-emerald-500/40">
                ACTIVATION RECORD
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight mt-1">PACKAGE ACTIVATION HISTORY</h1>
            <p class="text-xs text-neutral-300 mt-1">Audit log of mandatory ₹3,000 Zivo Family Kit package activations.</p>
        </div>

        <div class="px-5 py-3 rounded-2xl bg-[#02180f] border border-emerald-500/40 text-right">
            <span class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-wider block">Current Subscription Status</span>
            <span class="text-xl font-black {{ $user->is_subscription_active ? 'text-emerald-400' : 'text-rose-400' }}">
                {{ $user->is_subscription_active ? 'ACTIVE' : 'INACTIVE' }}
            </span>
        </div>
    </div>

    <!-- Report Table -->
    <div class="bg-[#042718] rounded-3xl border border-emerald-500/30 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#02180f] text-emerald-400 font-extrabold uppercase tracking-wider border-b border-emerald-500/30">
                    <tr>
                        <th class="px-5 py-4">TRX ID</th>
                        <th class="px-5 py-4">Package</th>
                        <th class="px-5 py-4">Price Paid</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Activation Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/10 text-neutral-200 font-medium">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-emerald-500/5 transition">
                            <td class="px-5 py-4 font-mono font-bold text-emerald-300">{{ $trx->trx_id }}</td>
                            <td class="px-5 py-4">
                                <span class="font-extrabold text-white">ZIVO FAMILY KIT</span>
                            </td>
                            <td class="px-5 py-4 font-bold text-emerald-400 text-sm">₹{{ number_format($trx->amount, 2) }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                    ACTIVATED
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-white font-bold">{{ $trx->created_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-neutral-400 font-mono">{{ $trx->created_at->format('h:i:s A') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-neutral-400 font-bold">
                                No package purchase history found. Please purchase the mandatory ₹3,000 Zivo Family Kit to activate your account.
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
