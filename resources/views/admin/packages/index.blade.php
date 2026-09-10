@extends('admin.layouts.app')

@section('content')
<style>
.gold-3d-badge {
    background: linear-gradient(180deg, #fef08a 0%, #f59e0b 50%, #b45309 100%) !important;
    border: 2px solid #fef08a !important;
    box-shadow: 0 4px 15px rgba(243, 202, 82, 0.4), inset 0 2px 4px rgba(255, 255, 255, 0.6) !important;
}
</style>

<div class="w-full space-y-6">
    <!-- Header Banner -->
    <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="pdf-num-badge">PM</span>
                <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">DEX TRADE NETWORK</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient font-heading">PACKAGES MANAGEMENT MODULE</h1>
            <p class="text-xs text-neutral-300 mt-1">Manage investment rules, $10 multiple parameters, daily ROI yields, and live member investments.</p>
        </div>

        <!-- Right Side Controls -->
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <a href="{{ route('admin.packages.history') }}" class="px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-black font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 transition flex items-center gap-2 shrink-0">
                <i data-lucide="history" class="w-4 h-4 text-black"></i> ALL USER INVESTMENTS
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i> {{ session('success') }}
        </div>
    @endif

    <!-- TOP KPI SUMMARY STATS (4 COLUMNS FULL WIDTH) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-panel p-5 rounded-2xl border border-amber-500/30 shadow-xl space-y-2">
            <div class="flex items-center justify-between text-xs text-neutral-400 font-bold uppercase tracking-wider">
                <span>Minimum Amount</span>
                <i data-lucide="shield-check" class="w-4 h-4 text-amber-400"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-amber-400 font-mono">$10.00 USD</div>
            <div class="text-[11px] text-emerald-400 font-mono">Multiple Rule: $10 Step</div>
        </div>

        <div class="bg-panel p-5 rounded-2xl border border-amber-500/30 shadow-xl space-y-2">
            <div class="flex items-center justify-between text-xs text-neutral-400 font-bold uppercase tracking-wider">
                <span>Daily ROI Rate</span>
                <i data-lucide="percent" class="w-4 h-4 text-amber-400"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-white font-mono">0.50% / Day</div>
            <div class="text-[11px] text-amber-300 font-mono">400 Days • 2.0X (200% Cap)</div>
        </div>

        <div class="bg-panel p-5 rounded-2xl border border-amber-500/30 shadow-xl space-y-2">
            <div class="flex items-center justify-between text-xs text-neutral-400 font-bold uppercase tracking-wider">
                <span>Total Capital Invested</span>
                <i data-lucide="coins" class="w-4 h-4 text-amber-400"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-emerald-400 font-mono">${{ number_format($totalCapitalInvested, 2) }}</div>
            <div class="text-[11px] text-neutral-400 font-mono">Across All Members</div>
        </div>

        <div class="bg-panel p-5 rounded-2xl border border-amber-500/30 shadow-xl space-y-2">
            <div class="flex items-center justify-between text-xs text-neutral-400 font-bold uppercase tracking-wider">
                <span>Active Packages</span>
                <i data-lucide="package-check" class="w-4 h-4 text-emerald-400"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-amber-400 font-mono">{{ $activePackagesCount }} Active</div>
            <div class="text-[11px] text-neutral-400 font-mono">Total Paid ROI: ${{ number_format($totalRoiPaid, 2) }}</div>
        </div>
    </div>

    <!-- MAIN SYSTEM PACKAGE PLAN CONFIGURATION PANEL -->
    <div class="bg-panel p-6 shadow-2xl rounded-3xl border border-amber-500/40 space-y-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-amber-500/20 pb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-black text-gold-gradient font-heading uppercase flex items-center gap-2">
                        <i data-lucide="settings" class="w-5 h-5 text-amber-400"></i> DYNAMIC PACKAGE PLAN CONFIGURATION
                    </h2>
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                        ACTIVE NOW
                    </span>
                </div>
                <p class="text-xs text-neutral-300 mt-1">Official Dex Trade Investment Plan configuration. Members can enter any custom investment amount starting at $10 in exact multiples of $10.</p>
            </div>

            @if($package)
                <a href="{{ route('admin.packages.edit', $package->id) }}" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-black font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 transition flex items-center gap-1.5 shrink-0">
                    <i data-lucide="edit-3" class="w-4 h-4 text-black"></i> EDIT PLAN CONFIGURATION
                </a>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 font-mono text-xs">
            <div class="p-4 rounded-2xl bg-black/60 border border-amber-500/30 space-y-1">
                <span class="text-neutral-400 font-sans block text-[11px]">Minimum Investment:</span>
                <strong class="text-amber-300 text-base font-black">$10.00 USD</strong>
            </div>

            <div class="p-4 rounded-2xl bg-black/60 border border-amber-500/30 space-y-1">
                <span class="text-neutral-400 font-sans block text-[11px]">Investment Multiples:</span>
                <strong class="text-emerald-400 text-base font-black">Multiples of $10 ($10, $20...)</strong>
            </div>

            <div class="p-4 rounded-2xl bg-black/60 border border-amber-500/30 space-y-1">
                <span class="text-neutral-400 font-sans block text-[11px]">Daily ROI Percentage:</span>
                <strong class="text-amber-300 text-base font-black">{{ number_format($package->daily_roi ?? 0.50, 2) }}% / Day</strong>
            </div>

            <div class="p-4 rounded-2xl bg-black/60 border border-amber-500/30 space-y-1">
                <span class="text-neutral-400 font-sans block text-[11px]">Total Max Return Cap:</span>
                <strong class="text-white text-base font-black">{{ number_format($package->total_return_multiplier ?? 2.0, 1) }}X (200% Cap)</strong>
            </div>
        </div>
    </div>

    <!-- RECENT USER INVESTMENTS & ACTIVE PACKAGES TABLE -->
    <div class="bg-panel p-6 shadow-2xl rounded-3xl border border-amber-500/30 space-y-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-amber-500/20 pb-4">
            <div>
                <h3 class="text-lg font-black text-white font-heading uppercase flex items-center gap-2">
                    <i data-lucide="history" class="w-5 h-5 text-amber-400"></i> RECENT MEMBER PACKAGE PURCHASES
                </h3>
                <p class="text-xs text-neutral-300">Live overview of recent member investments across the platform.</p>
            </div>
            <a href="{{ route('admin.packages.history') }}" class="text-xs font-bold text-amber-400 hover:underline uppercase flex items-center gap-1">
                View All User Investments <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-amber-500/20">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-black/60 text-amber-400 text-xs font-black uppercase font-mono tracking-wider border-b border-amber-500/20">
                        <th class="p-4"># ID</th>
                        <th class="p-4">MEMBER</th>
                        <th class="p-4">INVESTED CAPITAL</th>
                        <th class="p-4">DAILY YIELD (0.5%)</th>
                        <th class="p-4">MAX CAP (2X)</th>
                        <th class="p-4">PAID ROI</th>
                        <th class="p-4">PURCHASE DATE</th>
                        <th class="p-4 text-center">STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-500/10 text-xs font-mono">
                    @forelse($recentInvestments as $item)
                        <tr class="hover:bg-amber-500/5 transition">
                            <td class="p-4 font-bold text-neutral-400">#{{ $item->id }}</td>
                            <td class="p-4">
                                <div class="font-bold text-white">{{ $item->user->name ?? 'N/A' }}</div>
                                <div class="text-[11px] text-amber-400 font-mono">{{ $item->user->referral_code ?? '' }} ({{ $item->user->email ?? '' }})</div>
                            </td>
                            <td class="p-4 font-black text-amber-400 text-sm">${{ number_format($item->invested_amount, 2) }}</td>
                            <td class="p-4 font-bold text-amber-300">${{ number_format($item->daily_roi_amount, 2) }}/day</td>
                            <td class="p-4 font-bold text-emerald-400">${{ number_format($item->total_return_amount, 2) }}</td>
                            <td class="p-4 text-white font-bold">${{ number_format($item->paid_roi_amount, 2) }}</td>
                            <td class="p-4 text-neutral-400">{{ $item->purchased_at ? $item->purchased_at->format('M d, Y') : 'N/A' }}</td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase border {{ $item->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40' : 'bg-neutral-500/20 text-neutral-400 border-neutral-500/40' }}">
                                    {{ strtoupper($item->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-neutral-400 font-sans">
                                No member package purchases recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentInvestments->hasPages())
            <div class="pt-2">
                {{ $recentInvestments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

