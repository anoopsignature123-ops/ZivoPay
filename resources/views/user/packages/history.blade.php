@extends('user.layouts.app')

@section('content')
    <div class="w-full space-y-6">

        <!-- Top Header Banner (Matching User Management & Admin Flow Exactly) -->
        <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="pdf-num-badge">PH</span>
                    <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">DEX TRADE MEMBER
                        PORTAL</span>
                    </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient font-heading">INVESTMENT HISTORY</h1>
                <p class="text-xs text-neutral-300 mt-1">Review your active Dex Trade growth plans, daily ROI earnings,
                    contract duration, and status.</p>
                </div>

            <a href="{{ route('user.packages.index') }}"
                class="px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-black uppercase tracking-wider shadow-lg hover:scale-105 transition shrink-0 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-black"></i> Buy Another Package
            </a>
            </div>

        @if(session('success'))
            <div
                class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i> {{ session('success') }}
            </div>
        @endif

        <!-- PACKAGES HISTORY TABLE CONTAINER -->
        <div class="bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-amber-500/20">
                <h3 class="text-base font-black text-white font-heading">PACKAGES DIRECTORY</h3>
                <span class="text-xs text-neutral-400 font-bold">Total Active Contracts: {{ $userPackages->total() }}</span>
                </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead
                        class="bg-bg text-amber-400 uppercase text-xs font-bold font-heading tracking-wider border-b border-amber-500/30">
                        <tr>
                            <th class="p-4 rounded-l-xl">PACKAGE</th>
                            <th class="p-4">INVESTED AMOUNT</th>
                            <th class="p-4">DAILY ROI (%)</th>
                            <th class="p-4">DAILY ROI ($)</th>
                            <th class="p-4">TOTAL RETURN (2X)</th>
                            <th class="p-4">PAID ROI ($)</th>
                            <th class="p-4"> DATE & TIME</th>
                            <th class="p-4 rounded-r-xl">STATUS</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-500/20 text-neutral-200">
                                @forelse($userPackages as $userPkg)
                                    <tr class="hover:bg-amber-500/10 transition">
                                        <td class="p-4 font-bold text-white">
                                            <div class="flex items-center gap-2.5">
                                                <span
                                                    class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center font-bold text-xs">🚀</span>
                                                <span>{{ $userPkg->package->name ?? 'Dex Trade Package' }}</span>
                                            </div>
                                        </td>

                                        <td class="p-4 font-mono font-black text-amber-300 text-base">
                                            ${{ number_format($userPkg->invested_amount, 2) }}
                                        </td>

                                        <td class="p-4 font-mono font-bold text-emerald-400">
                                            {{ number_format($userPkg->daily_roi, 2) }}% / Day
                                        </td>

                                        <td class="p-4 font-mono font-bold text-emerald-400">
                                            ${{ number_format($userPkg->daily_roi_amount, 2) }}
                                        </td>

                                        <td class="p-4 font-mono font-bold text-purple-300">
                                            ${{ number_format($userPkg->total_return_amount, 2) }}
                                        </td>

                                        <td class="p-4 font-mono font-bold text-sky-400">
                                            ${{ number_format($userPkg->paid_roi_amount, 2) }}
                                        </td>

                                        <td class="p-4">
                                            <div class="text-xs font-semibold text-white">
                                                {{ $userPkg->purchased_at ? $userPkg->purchased_at->format('M d, Y') : 'N/A' }}
                                            </div>
                                            <div class="text-[10px] text-neutral-400 font-mono">
                                                {{ $userPkg->purchased_at ? $userPkg->purchased_at->format('h:i A') : '' }}
                                            </div>
                                        </td>

                                        <td class="p-4">
                                            @if($userPkg->status === 'active')
                                                <span
                                                    class="px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 text-[10px] font-black uppercase">ACTIVE</span>
                                            @else
                                                <span
                                                    class="px-2.5 py-1 rounded bg-neutral-800 text-neutral-400 border border-neutral-700 text-[10px] font-black uppercase">COMPLETED</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="p-8 text-center text-neutral-400 text-sm">
                                            You have not purchased any investment packages yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            </table>
                            </div>

                            <div class="pt-4 border-t border-amber-500/20">
                                {{ $userPackages->links() }}
                            </div>
                            </div>

            </div>
@endsection
