@extends('admin.layouts.app')

@section('title', 'ZIVO PAY - Admin Investment & ROI Control')

@section('content')
<div class="space-y-6">

    <!-- Header & Manual Trigger Button -->
    <div class="p-6 rounded-3xl bg-slate-900/90 border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.25)] flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[11px] font-black uppercase tracking-widest border border-emerald-500/40">
                SYSTEM CONTROL
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight mt-1">USER INVESTMENTS & DAILY ROI CONTROL</h1>
            <p class="text-xs text-neutral-400">Monitor all member active plans, total returns, and execute manual Daily ROI payouts.</p>
        </div>

        <form action="{{ route('admin.trigger-daily-roi') }}" method="POST">
            @csrf
            <button type="submit" onclick="return confirm('Are you sure you want to process Daily ROI & 15-Level Income distribution for all active investments now?');"
                class="px-6 py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-black font-black uppercase tracking-wider text-xs hover:shadow-[0_0_25px_rgba(16,185,129,0.8)] transition flex items-center gap-2">
                <i data-lucide="play-circle" class="w-5 h-5"></i> Trigger Daily ROI Payout Now
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-emerald-950/80 border border-emerald-500/40">
            <span class="block text-xs font-bold text-emerald-400 uppercase tracking-wider">Total Active Investments</span>
            <span class="text-2xl font-black text-white font-mono mt-1 block">₹{{ number_format($totalActiveInvestments, 2) }}</span>
        </div>

        <div class="p-5 rounded-2xl bg-emerald-950/80 border border-emerald-500/40">
            <span class="block text-xs font-bold text-emerald-400 uppercase tracking-wider">Total Distributed Returns</span>
            <span class="text-2xl font-black text-emerald-300 font-mono mt-1 block">₹{{ number_format($totalReturned, 2) }}</span>
        </div>

        <div class="p-5 rounded-2xl bg-emerald-950/80 border border-emerald-500/40">
            <span class="block text-xs font-bold text-emerald-400 uppercase tracking-wider">Total Packages Count</span>
            <span class="text-2xl font-black text-teal-300 font-mono mt-1 block">{{ $investments->total() }}</span>
        </div>
    </div>

    <!-- Table -->
    <div class="p-6 rounded-3xl bg-slate-900/90 border-2 border-emerald-500/40 shadow-xl space-y-4">
        <h2 class="text-lg font-black text-white uppercase tracking-tight flex items-center gap-2">
            <i data-lucide="layers" class="w-5 h-5 text-emerald-400"></i> All Member Packages
        </h2>

        @if($investments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-neutral-300">
                    <thead class="bg-emerald-950/80 text-emerald-400 uppercase text-[10px] font-bold border-b border-emerald-500/30">
                        <tr>
                            <th class="p-3.5">User Details</th>
                            <th class="p-3.5">Plan Name</th>
                            <th class="p-3.5">Invested Amount</th>
                            <th class="p-3.5">Daily ROI Rate</th>
                            <th class="p-3.5">Progress</th>
                            <th class="p-3.5">Total Returned</th>
                            <th class="p-3.5 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-500/10">
                        @foreach($investments as $inv)
                            <tr class="hover:bg-emerald-500/5 transition">
                                <td class="p-3.5">
                                    <span class="font-bold text-white block">{{ $inv->user ? $inv->user->name : 'N/A' }}</span>
                                    <span class="text-[10px] text-emerald-400 font-mono block">{{ $inv->user ? $inv->user->referral_code : '' }}</span>
                                </td>
                                <td class="p-3.5 font-bold text-emerald-300">{{ $inv->plan_name }}</td>
                                <td class="p-3.5 font-mono font-bold text-white">₹{{ number_format($inv->amount, 2) }}</td>
                                <td class="p-3.5 font-semibold text-emerald-400">{{ $inv->daily_percentage }}% (₹{{ number_format($inv->daily_amount, 2) }}/day)</td>
                                <td class="p-3.5 font-bold">{{ $inv->days_completed }} / 730 Days</td>
                                <td class="p-3.5 font-mono font-bold text-emerald-300">₹{{ number_format($inv->total_returned, 2) }}</td>
                                <td class="p-3.5 text-right">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase {{ $inv->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-neutral-800 text-neutral-400' }}">
                                        {{ $inv->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-4">
                {{ $investments->links() }}
            </div>
        @else
            <div class="p-8 text-center text-neutral-400">No member investments found.</div>
        @endif
    </div>

</div>
@endsection
