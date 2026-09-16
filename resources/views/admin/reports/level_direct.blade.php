@extends('admin.layouts.app')

@section('title', 'Direct Business Level Income Audit Log - Admin ZIVO PAY')

@section('content')
<div class="w-full space-y-6 font-sans">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-slate-900/95 border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.3)] flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] uppercase border border-emerald-500/40">AUDIT LOG</span>
                <span class="text-xs text-emerald-400 font-black tracking-[3px] uppercase">ZIVO PAY DIRECT LEVEL BONUS</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white font-heading">DIRECT BUSINESS LEVEL INCOME REPORT</h1>
            <p class="text-xs text-neutral-300 mt-1">Audit log of 12% total 15-level direct referral commissions (5% Level 1, 0.50% Levels 2-15) paid on investment packages.</p>
        </div>

        <div class="px-5 py-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/40 text-right">
            <span class="text-[10px] uppercase tracking-wider text-emerald-400 font-bold block">Total Direct Bonus Volume</span>
            <span class="text-xl sm:text-2xl font-black text-white">₹{{ number_format($totalAmount, 2) }}</span>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-slate-900/90 p-4 rounded-3xl border border-emerald-500/30">
        <form action="{{ route('admin.reports.level-direct') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div>
                <label class="block text-[10px] text-emerald-400 font-bold uppercase mb-1">Search User / TRX</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="TRX ID, User, Code..." class="px-3 py-2 rounded-xl bg-bg border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
            </div>
            <div>
                <label class="block text-[10px] text-emerald-400 font-bold uppercase mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="px-3 py-2 rounded-xl bg-bg border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
            </div>
            <div>
                <label class="block text-[10px] text-emerald-400 font-bold uppercase mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="px-3 py-2 rounded-xl bg-bg border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
            </div>
            <div class="pt-5 flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs rounded-xl uppercase tracking-wider transition">
                    Filter
                </button>
                <a href="{{ route('admin.reports.level-direct') }}" class="px-3 py-2 bg-bg border border-emerald-500/30 text-neutral-400 text-xs rounded-xl hover:text-white transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-slate-900/90 rounded-3xl border border-emerald-500/30 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/90 text-emerald-400 font-extrabold uppercase tracking-wider border-b border-emerald-500/30">
                    <tr>
                        <th class="px-5 py-4">Transaction ID</th>
                        <th class="px-5 py-4">Sponsor Member</th>
                        <th class="px-5 py-4">From Downline</th>
                        <th class="px-5 py-4">Amount</th>
                        <th class="px-5 py-4">Description</th>
                        <th class="px-5 py-4">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/10 text-neutral-200 font-medium">
                    @forelse($directs as $dir)
                        <tr class="hover:bg-emerald-500/5 transition">
                            <td class="px-5 py-4 font-mono font-bold text-emerald-300">{{ $dir->trx_id }}</td>
                            <td class="px-5 py-4">
                                @if($dir->user)
                                    <p class="font-bold text-white">{{ $dir->user->name }}</p>
                                    <p class="text-[11px] text-neutral-400">{{ $dir->user->email }} ({{ $dir->user->referral_code }})</p>
                                @else
                                    <span class="text-neutral-500 italic">User Deleted</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($dir->fromUser)
                                    <p class="font-bold text-white">{{ $dir->fromUser->name }}</p>
                                    <p class="text-[11px] text-neutral-400 font-mono">{{ $dir->fromUser->referral_code }}</p>
                                @else
                                    <span class="text-neutral-400 italic">Downline Member</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 font-bold text-emerald-400 text-sm">₹{{ number_format($dir->amount, 2) }}</td>
                            <td class="px-5 py-4 text-neutral-300 max-w-xs truncate">{{ $dir->description }}</td>
                            <td class="px-5 py-4">
                                <p class="text-white font-bold">{{ $dir->created_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-neutral-400 font-mono">{{ $dir->created_at->format('h:i:s A') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-neutral-400 font-bold">
                                No direct business level income records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($directs->hasPages())
            <div class="p-4 border-t border-emerald-500/20">
                {{ $directs->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
