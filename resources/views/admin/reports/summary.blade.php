@extends('admin.layouts.app')

@section('title', 'Master Income Overview')

@section('content')
<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-amber-400 uppercase tracking-widest mb-1">
                <i data-lucide="bar-chart-3" class="w-4 h-4 text-amber-400"></i>
                <span>Master Financial Reports</span>
            </div>
            <h1 class="text-2xl font-black font-heading text-white uppercase tracking-wider">
                System Income Overview
            </h1>
            <p class="text-xs text-neutral-400 mt-1">High-level financial audit dashboard summarizing payouts across all 7 Dex Trade income streams</p>
        </div>
        <div class="px-5 py-3 rounded-2xl bg-amber-500/10 border border-amber-500/30 shrink-0">
            <span class="text-[10px] font-bold text-amber-400 uppercase block">Grand Total Incomes Distributed</span>
            <span class="text-2xl font-black font-mono text-emerald-400">${{ number_format($grandTotal, 2) }}</span>
        </div>
    </div>

    <!-- 7 DEX TRADE INCOME SUMMARY KPI TILES -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- 1. Daily ROI Income -->
        <a href="{{ route('admin.reports.roi') }}" class="group bg-panel p-4 rounded-2xl border border-amber-500/30 hover:border-amber-400 shadow-lg transition block">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold text-amber-400 uppercase tracking-wider">1. Daily ROI Yield</span>
                <i data-lucide="line-chart" class="w-4 h-4 text-amber-400 group-hover:scale-110 transition"></i>
            </div>
            <p class="text-xl font-black text-white font-mono">${{ number_format($roiTotal, 2) }}</p>
            <p class="text-[10px] text-neutral-400 mt-1 flex items-center justify-between">
                <span>0.5% Daily Yield (400 Days)</span>
                <span class="text-amber-400 group-hover:underline">View Audit &rarr;</span>
            </p>
        </a>

        <!-- 2. Direct Income -->
        <a href="{{ route('admin.reports.direct') }}" class="group bg-panel p-4 rounded-2xl border border-amber-500/30 hover:border-amber-400 shadow-lg transition block">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold text-sky-400 uppercase tracking-wider">2. Direct Income (10%)</span>
                <i data-lucide="user-plus" class="w-4 h-4 text-sky-400 group-hover:scale-110 transition"></i>
            </div>
            <p class="text-xl font-black text-white font-mono">${{ number_format($directTotal, 2) }}</p>
            <p class="text-[10px] text-neutral-400 mt-1 flex items-center justify-between">
                <span>10% Instant Referral Share</span>
                <span class="text-amber-400 group-hover:underline">View Audit &rarr;</span>
            </p>
        </a>

        <!-- 3. Matching Income -->
        <a href="{{ route('admin.reports.matching') }}" class="group bg-panel p-4 rounded-2xl border border-amber-500/30 hover:border-amber-400 shadow-lg transition block">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold text-purple-400 uppercase tracking-wider">3. Matching Income (10%)</span>
                <i data-lucide="git-merge" class="w-4 h-4 text-purple-400 group-hover:scale-110 transition"></i>
            </div>
            <p class="text-xl font-black text-white font-mono">${{ number_format($matchingTotal, 2) }}</p>
            <p class="text-[10px] text-neutral-400 mt-1 flex items-center justify-between">
                <span>10% Binary Matching (1:1 Req)</span>
                <span class="text-amber-400 group-hover:underline">View Audit &rarr;</span>
            </p>
        </a>

        <!-- 4. Referral ROI Income -->
        <a href="{{ route('admin.reports.referral-roi') }}" class="group bg-panel p-4 rounded-2xl border border-amber-500/30 hover:border-amber-400 shadow-lg transition block">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold text-teal-400 uppercase tracking-wider">4. Referral ROI Income</span>
                <i data-lucide="repeat" class="w-4 h-4 text-teal-400 group-hover:scale-110 transition"></i>
            </div>
            <p class="text-xl font-black text-white font-mono">${{ number_format($referralRoiTotal, 2) }}</p>
            <p class="text-[10px] text-neutral-400 mt-1 flex items-center justify-between">
                <span>0.5% Daily Direct Investment</span>
                <span class="text-amber-400 group-hover:underline">View Audit &rarr;</span>
            </p>
        </a>

        <!-- 5. Matching ROI Income -->
        <a href="{{ route('admin.reports.matching-roi') }}" class="group bg-panel p-4 rounded-2xl border border-amber-500/30 hover:border-amber-400 shadow-lg transition block">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold text-indigo-400 uppercase tracking-wider">5. Matching ROI Income</span>
                <i data-lucide="layers" class="w-4 h-4 text-indigo-400 group-hover:scale-110 transition"></i>
            </div>
            <p class="text-xl font-black text-white font-mono">${{ number_format($matchingRoiTotal, 2) }}</p>
            <p class="text-[10px] text-neutral-400 mt-1 flex items-center justify-between">
                <span>0.5% Daily Matching Bonus</span>
                <span class="text-amber-400 group-hover:underline">View Audit &rarr;</span>
            </p>
        </a>

        <!-- 6. Upline Matching Income -->
        <a href="{{ route('admin.reports.upline-matching') }}" class="group bg-panel p-4 rounded-2xl border border-amber-500/30 hover:border-amber-400 shadow-lg transition block">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold text-rose-400 uppercase tracking-wider">6. Upline Matching Income</span>
                <i data-lucide="share-2" class="w-4 h-4 text-rose-400 group-hover:scale-110 transition"></i>
            </div>
            <p class="text-xl font-black text-white font-mono">${{ number_format($uplineMatchingTotal, 2) }}</p>
            <p class="text-[10px] text-neutral-400 mt-1 flex items-center justify-between">
                <span>10% Upline Deduction Share</span>
                <span class="text-amber-400 group-hover:underline">View Audit &rarr;</span>
            </p>
        </a>

        <!-- 7. Salary Income -->
        <a href="{{ route('admin.reports.salary') }}" class="group bg-panel p-4 rounded-2xl border border-amber-500/30 hover:border-amber-400 shadow-lg transition block">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold text-emerald-400 uppercase tracking-wider">7. Salary Income</span>
                <i data-lucide="award" class="w-4 h-4 text-emerald-400 group-hover:scale-110 transition"></i>
            </div>
            <p class="text-xl font-black text-white font-mono">${{ number_format($salaryTotal, 2) }}</p>
            <p class="text-[10px] text-neutral-400 mt-1 flex items-center justify-between">
                <span>17 Milestone Ranks</span>
                <span class="text-amber-400 group-hover:underline">View Audit &rarr;</span>
            </p>
        </a>

    </div>

    <!-- RECENT TRANSACTIONS TABLE -->
    <div class="bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-black font-heading text-white uppercase tracking-wider">
                    Recent Platform Financial Transactions
                </h3>
                <p class="text-xs text-neutral-400">Live ledger log of all income distributions</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-amber-500/30 bg-amber-500/5">
                        <th class="py-3 px-4 text-xs font-bold text-amber-400 uppercase">Txn ID</th>
                        <th class="py-3 px-4 text-xs font-bold text-amber-400 uppercase">Member Details</th>
                        <th class="py-3 px-4 text-xs font-bold text-amber-400 uppercase">Type</th>
                        <th class="py-3 px-4 text-xs font-bold text-amber-400 uppercase">Amount</th>
                        <th class="py-3 px-4 text-xs font-bold text-amber-400 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-500/10 text-xs">
                    @forelse($recentIncomes as $income)
                        <tr class="hover:bg-amber-500/5 transition">
                            <td class="py-3 px-4 font-mono text-neutral-300">{{ $income->txn_number }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-white">{{ $income->user->name ?? 'User' }}</div>
                                <div class="text-[10px] text-amber-400/80 font-mono">{{ $income->user->referral_code ?? '' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border bg-amber-500/10 text-amber-400 border-amber-500/30">
                                    {{ str_replace('_', ' ', $income->type) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-emerald-400">+${{ number_format($income->amount, 2) }}</td>
                            <td class="py-3 px-4 text-neutral-400 font-mono">{{ $income->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-neutral-400 italic">No income transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $recentIncomes->links() }}
        </div>
    </div>

</div>
@endsection
