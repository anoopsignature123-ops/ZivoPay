@extends('user.layouts.app')

@section('title', 'ZIVO PAY - Income Ledger & Earnings Summary')

@section('content')
<div class="space-y-6">

    <!-- Top Banner & Summary Cards -->
    <div class="p-6 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-2xl space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[11px] font-black uppercase tracking-widest border border-emerald-500/40">
                    EARNINGS & INCOMES
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight mt-1">INCOME LEDGER & SUMMARY</h1>
            </div>
            <a href="{{ route('user.withdrawal.index') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-black font-black uppercase tracking-wider text-xs hover:shadow-lg transition flex items-center gap-2">
                <i data-lucide="arrow-up-right" class="w-4 h-4"></i> 24x7 Withdraw Income
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
            <!-- 1. Total Daily ROI -->
            <div class="p-4 rounded-2xl bg-[#02180f] border border-emerald-500/40">
                <span class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Total Daily ROI</span>
                <span class="text-2xl font-black text-white font-mono mt-1 block">₹{{ number_format($totalRoi, 2) }}</span>
                <span class="text-[10px] text-neutral-400">0.15% to 0.30% Daily Return</span>
            </div>

            <!-- 2. Direct Level Income -->
            <div class="p-4 rounded-2xl bg-[#02180f] border border-emerald-500/40">
                <span class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider">15-Level Direct Income</span>
                <span class="text-2xl font-black text-emerald-300 font-mono mt-1 block">₹{{ number_format($totalLevelIncome, 2) }}</span>
                <span class="text-[10px] text-neutral-400">5% L1, 0.5% L2-L15 (12% Total)</span>
            </div>

            <!-- 3. ROI Level Income -->
            <div class="p-4 rounded-2xl bg-[#02180f] border border-emerald-500/40">
                <span class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider">15-Level ROI Income</span>
                <span class="text-2xl font-black text-teal-300 font-mono mt-1 block">₹{{ number_format($totalRoiLevelIncome, 2) }}</span>
                <span class="text-[10px] text-neutral-400">10% L1, 3% L2, 1% L3-L15 (26% Total)</span>
            </div>

            <!-- 4. Earning Wallet -->
            <div class="p-4 rounded-2xl bg-[#02180f] border-2 border-emerald-400">
                <span class="block text-[11px] font-bold text-emerald-300 uppercase tracking-wider">Available Earning Wallet</span>
                <span class="text-2xl font-black text-white font-mono mt-1 block">₹{{ number_format($user->earning_wallet, 2) }}</span>
                <span class="text-[10px] text-emerald-400 font-semibold">Withdrawable 24x7</span>
            </div>
        </div>
    </div>

    <!-- Achieved Rewards Cards Section -->
    @if($achievedRewards->count() > 0)
    <div class="p-6 rounded-3xl bg-[#02180f] border-2 border-emerald-400 shadow-xl space-y-3">
        <h2 class="text-base font-black text-white uppercase tracking-tight flex items-center gap-2">
            <i data-lucide="award" class="w-5 h-5 text-emerald-400"></i> Achieved Business Rewards
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($achievedRewards as $reward)
                <div class="p-3.5 rounded-xl bg-[#01140c] border border-emerald-500/40 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-emerald-400 block">{{ $reward->reward_title }}</span>
                        <span class="text-sm font-black text-white block">{{ $reward->reward_item }}</span>
                        <span class="text-[10px] text-neutral-400 font-mono">Business: ₹{{ number_format($reward->business_required) }}</span>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 border border-emerald-400 text-emerald-300 text-[10px] font-black uppercase">
                        {{ $reward->status }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Transactions Filter & Table -->
    <div class="p-6 rounded-3xl bg-[#042718] border-2 border-emerald-500/40 shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-emerald-500/20 pb-4">
            <h2 class="text-lg font-black text-white uppercase tracking-tight flex items-center gap-2">
                <i data-lucide="receipt" class="w-5 h-5 text-emerald-400"></i> Detailed Transaction Ledger
            </h2>

            <!-- Filter Buttons -->
            <div class="flex flex-wrap gap-1.5 text-xs">
                <a href="{{ route('user.income.index') }}" class="px-3 py-1.5 rounded-lg {{ !request('type') ? 'bg-emerald-500 text-black font-bold' : 'bg-emerald-500/10 text-neutral-300 hover:bg-emerald-500/20' }}">All</a>
                <a href="{{ route('user.income.index', ['type' => 'daily_roi']) }}" class="px-3 py-1.5 rounded-lg {{ request('type') == 'daily_roi' ? 'bg-emerald-500 text-black font-bold' : 'bg-emerald-500/10 text-neutral-300 hover:bg-emerald-500/20' }}">Daily ROI</a>
                <a href="{{ route('user.income.index', ['type' => 'level_income']) }}" class="px-3 py-1.5 rounded-lg {{ request('type') == 'level_income' ? 'bg-emerald-500 text-black font-bold' : 'bg-emerald-500/10 text-neutral-300 hover:bg-emerald-500/20' }}">15-Level Income</a>
                <a href="{{ route('user.income.index', ['type' => 'roi_level_income']) }}" class="px-3 py-1.5 rounded-lg {{ request('type') == 'roi_level_income' ? 'bg-emerald-500 text-black font-bold' : 'bg-emerald-500/10 text-neutral-300 hover:bg-emerald-500/20' }}">ROI Level Income</a>
                <a href="{{ route('user.income.index', ['type' => 'withdrawal']) }}" class="px-3 py-1.5 rounded-lg {{ request('type') == 'withdrawal' ? 'bg-emerald-500 text-black font-bold' : 'bg-emerald-500/10 text-neutral-300 hover:bg-emerald-500/20' }}">Withdrawals</a>
            </div>
        </div>

        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-neutral-300">
                    <thead class="bg-emerald-950 text-emerald-400 uppercase text-[10px] font-bold border-b border-emerald-500/30">
                        <tr>
                            <th class="p-3.5">Trx ID</th>
                            <th class="p-3.5">Type</th>
                            <th class="p-3.5">Level</th>
                            <th class="p-3.5">Description</th>
                            <th class="p-3.5">Amount</th>
                            <th class="p-3.5 text-right">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-500/10">
                        @foreach($transactions as $trx)
                            <tr class="hover:bg-emerald-500/5 transition">
                                <td class="p-3.5 font-mono font-bold text-white">{{ $trx->trx_id }}</td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                        {{ $trx->type === 'daily_roi' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : '' }}
                                        {{ $trx->type === 'level_income' ? 'bg-teal-500/20 text-teal-300 border border-teal-500/40' : '' }}
                                        {{ $trx->type === 'roi_level_income' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : '' }}
                                        {{ $trx->type === 'withdrawal' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : '' }}
                                        {{ $trx->type === 'investment' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : '' }}">
                                        {{ str_replace('_', ' ', $trx->type) }}
                                    </span>
                                </td>
                                <td class="p-3.5 font-bold text-emerald-400">{{ $trx->level ? 'L'.$trx->level : '-' }}</td>
                                <td class="p-3.5 font-medium text-neutral-200">{{ $trx->description }}</td>
                                <td class="p-3.5 font-mono font-bold text-base {{ $trx->type === 'withdrawal' ? 'text-rose-400' : 'text-emerald-400' }}">
                                    {{ $trx->type === 'withdrawal' ? '-' : '+' }}₹{{ number_format($trx->amount, 2) }}
                                </td>
                                <td class="p-3.5 text-right text-neutral-400">{{ $trx->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-4">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="p-8 text-center text-neutral-400 space-y-2">
                <i data-lucide="inbox" class="w-8 h-8 text-emerald-400/50 mx-auto"></i>
                <p>No transactions found for the selected filter.</p>
            </div>
        @endif
    </div>

</div>
@endsection
