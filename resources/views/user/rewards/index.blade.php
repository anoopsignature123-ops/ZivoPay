@extends('user.layouts.app')

@section('title', 'Business Reward Offer Portal - ZIVO PAY')

@section('content')
<div class="space-y-6 font-sans">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-slate-900/90 border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.25)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] uppercase border border-emerald-500/40">SPECIAL OFFER</span>
                <span class="text-xs text-emerald-400 font-black tracking-[3px] uppercase">ZIVO PAY REWARDS</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight">BUSINESS REWARD OFFER</h1>
            <p class="text-xs text-neutral-300 mt-1">Earn smartphones, laptops, EV scooties, car downpayments, Tata Punch & Tata Sierra SUVs!</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="px-4 py-2.5 rounded-2xl bg-emerald-950/80 border border-emerald-500/40 text-center">
                <span class="text-[10px] text-emerald-400 font-extrabold uppercase block">Direct Business</span>
                <span class="text-lg font-black text-white font-mono">₹{{ number_format($directBusiness, 2) }}</span>
            </div>
            <div class="px-4 py-2.5 rounded-2xl bg-slate-950/80 border border-teal-500/40 text-center">
                <span class="text-[10px] text-teal-400 font-extrabold uppercase block">Team Business</span>
                <span class="text-lg font-black text-teal-300 font-mono">₹{{ number_format($teamBusiness, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Claimed / Achieved Rewards Notification -->
    @if($achievedRewards->count() > 0)
        <div class="p-5 rounded-3xl bg-emerald-500/10 border-2 border-emerald-500/50 shadow-xl space-y-3">
            <div class="flex items-center gap-2 text-emerald-400 font-extrabold text-sm uppercase">
                <i data-lucide="trophy" class="w-5 h-5 text-emerald-400"></i>
                <span>CONGRATULATIONS! YOUR ACHIEVED REWARDS</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($achievedRewards as $reward)
                    <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-emerald-500/40 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold shrink-0">
                            🏆
                        </div>
                        <div>
                            <p class="font-black text-white text-xs">{{ $reward->reward_name }}</p>
                            <p class="text-[10px] text-emerald-400 font-bold mt-0.5">Target: ₹{{ number_format($reward->target_amount, 2) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Reward Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Column A: Direct Business Rewards -->
        <div class="p-6 rounded-3xl bg-slate-900/90 border-2 border-emerald-500/40 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-emerald-500/20 pb-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="target" class="w-5 h-5 text-emerald-400"></i>
                    <h2 class="text-base font-black text-white uppercase">DIRECT BUSINESS REWARDS</h2>
                </div>
                <span class="text-[10px] bg-emerald-500/20 text-emerald-300 px-2.5 py-1 rounded-full font-mono font-bold">DIRECT TEAM</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/90 text-emerald-400 font-extrabold uppercase tracking-wider border-b border-emerald-500/30">
                        <tr>
                            <th class="px-4 py-3">Business Target</th>
                            <th class="px-4 py-3">Reward Gift</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-500/10 text-neutral-200 font-medium">
                        @foreach($directRewardTiers as $tier)
                            @php
                                $isAchieved = $directBusiness >= $tier['target'];
                            @endphp
                            <tr class="{{ $isAchieved ? 'bg-emerald-500/10' : '' }} hover:bg-emerald-500/5 transition">
                                <td class="px-4 py-3 font-mono font-bold text-white text-sm">₹{{ number_format($tier['target']) }}</td>
                                <td class="px-4 py-3 font-bold text-emerald-300">🎁 {{ $tier['reward'] }}</td>
                                <td class="px-4 py-3">
                                    @if($isAchieved)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                                            ✓ ACHIEVED
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-slate-800 text-neutral-400 border border-neutral-700">
                                            IN PROGRESS
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Column B: Team Business Rewards -->
        <div class="p-6 rounded-3xl bg-slate-900/90 border-2 border-teal-500/40 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-teal-500/20 pb-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="users" class="w-5 h-5 text-teal-400"></i>
                    <h2 class="text-base font-black text-white uppercase">TEAM BUSINESS REWARDS</h2>
                </div>
                <span class="text-[10px] bg-teal-500/20 text-teal-300 px-2.5 py-1 rounded-full font-mono font-bold">15-LEVEL TEAM</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/90 text-teal-400 font-extrabold uppercase tracking-wider border-b border-teal-500/30">
                        <tr>
                            <th class="px-4 py-3">Business Target</th>
                            <th class="px-4 py-3">Reward Gift</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-teal-500/10 text-neutral-200 font-medium">
                        @foreach($teamRewardTiers as $tier)
                            @php
                                $isAchieved = $teamBusiness >= $tier['target'];
                            @endphp
                            <tr class="{{ $isAchieved ? 'bg-teal-500/10' : '' }} hover:bg-teal-500/5 transition">
                                <td class="px-4 py-3 font-mono font-bold text-white text-sm">₹{{ number_format($tier['target']) }}</td>
                                <td class="px-4 py-3 font-bold text-teal-300">🏆 {{ $tier['reward'] }}</td>
                                <td class="px-4 py-3">
                                    @if($isAchieved)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-teal-500/20 text-teal-300 border border-teal-500/40">
                                            ✓ ACHIEVED
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-slate-800 text-neutral-400 border border-neutral-700">
                                            IN PROGRESS
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
