@extends('user.layouts.app')

@section('title', 'ZIVO PAY - Income Ledger & Earnings Summary')

@section('content')
<div class="space-y-6 font-sans">

    <!-- Top Banner & Summary Cards -->
    <div class="p-6 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-2xl space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[11px] font-black uppercase tracking-widest border border-emerald-500/40">
                    EARNINGS & INCOMES
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight mt-1 font-heading">INCOME LEDGER & SUMMARY</h1>
                <p class="text-xs text-neutral-300 mt-1">Audit log of all daily ROI returns, direct level commissions, ROI level matching incomes, and wallet transfers.</p>
            </div>
            <a href="{{ route('user.withdrawal.index') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-black font-black uppercase tracking-wider text-xs hover:shadow-[0_0_20px_rgba(16,185,129,0.7)] transition flex items-center gap-2 shrink-0">
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

    <!-- Official 1-Row Filter Bar Component -->
    <div class="p-4 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-xl">
        <form action="{{ route('user.income.index') }}" method="GET" class="zivo-filter-bar">
            <!-- FROM DATE -->
            <div class="zivo-filter-field-date">
                <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">FROM DATE</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}"
                    class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
            </div>

            <!-- TO DATE -->
            <div class="zivo-filter-field-date">
                <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">TO DATE</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}"
                    class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
            </div>

            <!-- TYPE FILTER -->
            <div class="zivo-filter-field-select">
                <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">INCOME TYPE</label>
                <select name="type" class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400 transition cursor-pointer">
                    <option value="">All Income Types</option>
                    <option value="daily_roi" {{ request('type') === 'daily_roi' ? 'selected' : '' }}>Daily ROI Income</option>
                    <option value="direct_bonus" {{ request('type') === 'direct_bonus' ? 'selected' : '' }}>Direct Bonus</option>
                    <option value="level_income" {{ request('type') === 'level_income' ? 'selected' : '' }}>15-Level Subscription</option>
                    <option value="roi_level_income" {{ request('type') === 'roi_level_income' ? 'selected' : '' }}>ROI Level Matching</option>
                    <option value="direct_reward" {{ request('type') === 'direct_reward' ? 'selected' : '' }}>Direct Business Reward</option>
                    <option value="team_reward" {{ request('type') === 'team_reward' ? 'selected' : '' }}>Team Leadership Reward</option>
                </select>
            </div>

            <!-- SEARCH (FLEX-1 EXPANDS IN MIDDLE) -->
            <div class="zivo-filter-field-search">
                <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">SEARCH REF / UTR / METHOD</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-emerald-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ref ID, UTR Number, TRX..."
                        class="w-full pl-9 pr-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="zivo-filter-actions">
                <button type="submit" class="py-2 px-5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-black font-black uppercase tracking-wider text-xs hover:shadow-[0_0_20px_rgba(16,185,129,0.7)] transition flex items-center justify-center gap-1.5 shadow cursor-pointer">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i> FILTER
                </button>
                <a href="{{ route('user.income.index') }}" class="py-2 px-3.5 rounded-xl bg-black/60 border border-emerald-500/30 text-neutral-300 font-bold text-xs hover:text-white hover:border-emerald-400 transition text-center shrink-0">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Achieved Rewards Cards Section -->
    @if($achievedRewards->count() > 0)
    <div class="p-5 rounded-3xl bg-[#02180f] border-2 border-emerald-400 shadow-xl space-y-3">
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

    <!-- Transactions Table Section -->
    <div class="p-6 rounded-3xl bg-[#042718] border border-emerald-500/40 shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-emerald-500/20 pb-4">
            <h2 class="text-lg font-black text-white uppercase tracking-tight flex items-center gap-2 font-heading">
                <i data-lucide="receipt" class="w-5 h-5 text-emerald-400"></i> Detailed Transaction Ledger
            </h2>
        </div>

        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-neutral-300">
                    <thead class="bg-[#02180f] text-emerald-400 uppercase text-[10px] font-bold border-b border-emerald-500/30">
                        <tr>
                            <th class="p-3.5">TRX ID</th>
                            <th class="p-3.5">TYPE</th>
                            <th class="p-3.5">LEVEL</th>
                            <th class="p-3.5">AMOUNT</th>
                            <th class="p-3.5">DESCRIPTION</th>
                            <th class="p-3.5 text-right">DATE & TIME</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-500/10 font-medium">
                        @foreach($transactions as $trx)
                            <tr class="hover:bg-emerald-500/5 transition">
                                <td class="p-3.5 font-mono font-bold text-emerald-300 text-xs">{{ $trx->trx_id }}</td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-400/40">
                                        {{ strtoupper(str_replace('_', ' ', $trx->type)) }}
                                    </span>
                                </td>
                                <td class="p-3.5 font-mono font-bold text-white">{{ $trx->level ? 'Level '.$trx->level : '-' }}</td>
                                <td class="p-3.5 font-mono font-black text-sm {{ $trx->trx_type === '-' ? 'text-rose-400' : 'text-emerald-400' }}">
                                    {{ $trx->trx_type ?? '+' }}₹{{ number_format($trx->amount, 2) }}
                                </td>
                                <td class="p-3.5 text-neutral-300">{{ $trx->description }}</td>
                                <td class="p-3.5 text-right text-neutral-400 font-mono text-[11px]">{{ $trx->created_at ? $trx->created_at->format('d M Y, H:i A') : 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-4 border-t border-emerald-500/20">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="p-8 text-center text-neutral-400 space-y-2">
                <i data-lucide="info" class="w-8 h-8 text-emerald-400/50 mx-auto"></i>
                <p class="text-xs font-bold text-white">No income transaction records found matching your filter.</p>
            </div>
        @endif
    </div>

</div>
@endsection
