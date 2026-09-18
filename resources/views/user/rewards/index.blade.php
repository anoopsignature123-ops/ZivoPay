@extends('user.layouts.app')

@section('title', 'Business Reward Offer Portal - ZIVO PAY')

@section('content')
    <style>
        @keyframes rewardFloat { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-7px); } }
        @keyframes rewardGlow { 0%, 100% { box-shadow: 0 18px 50px rgba(3, 54, 34, .35); } 50% { box-shadow: 0 22px 60px rgba(16, 185, 129, .3); } }
        .reward-hero { background-image: linear-gradient(110deg, rgba(2, 28, 16, .97), rgba(3, 53, 34, .88), rgba(2, 28, 16, .9)); animation: rewardGlow 5s ease-in-out infinite; }
        .reward-trophy { animation: rewardFloat 3.5s ease-in-out infinite; }
        .reward-thumb-box { width: 48px; height: 48px; min-width: 48px; min-height: 48px; max-width: 48px; max-height: 48px; }
    </style>

    <div class="space-y-5 font-sans relative select-none">
        <!-- Ambient Glow Background Decorator -->
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-full max-w-[950px] h-[400px] bg-emerald-500/10 blur-[130px] pointer-events-none rounded-full"></div>

        <!-- 1. HERO BANNER -->
        <section class="reward-hero relative overflow-hidden rounded-3xl border border-emerald-500/20 p-4.5 sm:p-6 shadow-2xl relative z-10">
            <div class="absolute -right-8 -top-10 h-40 w-40 rounded-full bg-emerald-400/10 blur-2xl"></div>
            <div class="relative flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-emerald-500/20 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-widest text-emerald-300 border border-emerald-500/30">Special offer</span>
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-emerald-400">• ZIVO PAY REWARDS</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="reward-trophy mt-0.5 flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            <i data-lucide="trophy" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-black tracking-tight text-white font-heading sm:text-2xl">Business Reward Offer</h1>
                            <p class="mt-0.5 max-w-xl text-xs leading-relaxed text-neutral-300">Unlock milestone rewards as your direct and team business grows—from gadgets to exciting vehicle rewards.</p>
                        </div>
                    </div>
                </div>
                <div class="grid w-full grid-cols-2 gap-3 lg:w-auto">
                    <div class="rounded-2xl border border-emerald-500/20 bg-[#01140c] p-3 text-center shadow-lg sm:min-w-40">
                        <span class="block text-[10px] font-black uppercase tracking-wider text-emerald-400">Direct Business</span>
                        <span class="mt-0.5 block text-base font-black text-white font-mono sm:text-lg">₹{{ number_format($directBusiness, 2) }}</span>
                    </div>
                    <div class="rounded-2xl border border-teal-500/20 bg-[#01140c] p-3 text-center shadow-lg sm:min-w-40">
                        <span class="block text-[10px] font-black uppercase tracking-wider text-teal-300">Team Business</span>
                        <span class="mt-0.5 block text-base font-black text-teal-200 font-mono sm:text-lg">₹{{ number_format($teamBusiness, 2) }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. CONGRATULATIONS ACHIEVED REWARDS (If any) -->
        @if ($achievedRewards->count() > 0)
            <section class="rounded-3xl border border-emerald-500/20 bg-[#02180f] p-4 shadow-xl relative z-10 space-y-3">
                <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-emerald-400">
                    <i data-lucide="badge-check" class="h-4 w-4 text-emerald-300"></i>
                    <span>Congratulations — Achieved Rewards</span>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($achievedRewards as $reward)
                        <article class="flex items-center gap-3 rounded-2xl border border-emerald-500/15 bg-[#01140c] p-3 transition duration-300 hover:bg-[#021d12]">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/20">
                                <i data-lucide="award" class="h-4 w-4"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-xs font-black text-white">{{ $reward->reward_name }}</p>
                                <p class="mt-0.5 text-[10px] font-mono font-bold text-emerald-400">Target: ₹{{ number_format($reward->target_amount, 2) }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 3. REWARD BOARDS GRID (DIRECT BUSINESS & TEAM BUSINESS) -->
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-2 relative z-10">
            @foreach ([
                [
                    'title' => 'Direct Business Rewards',
                    'subtitle' => 'Keep building to unlock every milestone.',
                    'badge' => 'Direct Team',
                    'icon' => 'target',
                    'tiers' => $directRewardTiers,
                    'business' => $directBusiness,
                    'badgeClass' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/20',
                    'iconClass' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/20',
                ],
                [
                    'title' => 'Team Business Rewards',
                    'subtitle' => 'Keep building to unlock every milestone.',
                    'badge' => '15-Level Team',
                    'icon' => 'users',
                    'tiers' => $teamRewardTiers,
                    'business' => $teamBusiness,
                    'badgeClass' => 'bg-teal-500/20 text-teal-300 border-teal-500/20',
                    'iconClass' => 'bg-teal-500/20 text-teal-300 border-teal-500/20',
                ]
            ] as $rewardGroup)
                <section class="rounded-3xl border border-emerald-500/20 bg-[#02180f] p-4 sm:p-5 shadow-xl space-y-3.5 flex flex-col justify-between">
                    <div>
                        <!-- Board Top Header -->
                        <div class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-[#01140c] border border-emerald-500/15 mb-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border {{ $rewardGroup['iconClass'] }} text-xs font-bold">
                                    <i data-lucide="{{ $rewardGroup['icon'] }}" class="h-4 w-4"></i>
                                </div>
                                <div class="min-w-0">
                                    <h2 class="text-xs sm:text-sm font-black uppercase tracking-wide text-white font-heading truncate">{{ $rewardGroup['title'] }}</h2>
                                    <p class="text-[10px] font-medium text-neutral-400 truncate mt-0.5">{{ $rewardGroup['subtitle'] }}</p>
                                </div>
                            </div>
                            <span class="shrink-0 rounded-full border px-2.5 py-0.5 text-[9.5px] font-black uppercase tracking-wider {{ $rewardGroup['badgeClass'] }}">
                                {{ $rewardGroup['badge'] }}
                            </span>
                        </div>

                        <!-- Header Column Label Bar -->
                        <div class="flex items-center justify-between bg-[#01140c] px-3.5 py-2 rounded-xl border border-emerald-500/15 text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-3">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="bar-chart-2" class="w-3.5 h-3.5 text-emerald-400"></i>
                                BUSINESS TARGET
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="gift" class="w-3.5 h-3.5 text-emerald-300"></i>
                                GIFT REWARD & STATUS
                            </span>
                        </div>

                        <!-- Compact Tiers Card List -->
                        <div class="grid gap-4">
                            @foreach ($rewardGroup['tiers'] as $tier)
                                @php
                                    $isAchieved = $rewardGroup['business'] >= $tier['target'];
                                    $progress = $tier['target'] > 0 ? min(($rewardGroup['business'] / $tier['target']) * 100, 100) : 0;
                                    $rewardImage = match (true) {
                                        str_contains($tier['reward'], 'Mobile Phone') => 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?auto=format&fit=crop&w=160&q=80',
                                        str_contains($tier['reward'], 'Laptop') => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=160&q=80',
                                        str_contains($tier['reward'], 'Scooty') => 'https://images.unsplash.com/photo-1571068316344-75bc76f77890?auto=format&fit=crop&w=160&q=80',
                                        default => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=160&q=80',
                                    };
                                @endphp
                                <article class="rounded-2xl border border-emerald-500/35 bg-[#01140c] p-5 space-y-3 transition-all duration-200 hover:border-emerald-400/60 hover:bg-[#021d12] sm:p-6">
                                    <div class="flex gap-3 items-center">
                                        <!-- Fixed 48px Square Compact Product Thumbnail -->
                                        <div class="reward-thumb-box shrink-0 rounded-xl border border-emerald-500/20 bg-[#02180f] p-1 overflow-hidden flex items-center justify-center">
                                            <img src="{{ $rewardImage }}" alt="{{ $tier['reward'] }}" class="w-full h-full object-cover rounded-lg" loading="lazy">
                                        </div>

                                        <!-- Tier Info -->
                                        <div class="min-w-0 flex-1 space-y-1">
                                            <div class="flex items-start justify-between gap-2">
                                                <div>
                                                    <span class="text-[9.5px] font-extrabold uppercase tracking-wider text-neutral-400 block">BUSINESS TARGET</span>
                                                    <p class="mt-0.5 text-xs sm:text-sm font-black text-white font-mono">₹{{ number_format($tier['target']) }}</p>
                                                </div>
                                                @if($isAchieved)
                                                    <span class="shrink-0 rounded-full border border-emerald-400/30 bg-emerald-500/20 text-emerald-300 px-2.5 py-0.5 text-[9.5px] font-black uppercase tracking-wide shadow-[0_0_8px_rgba(16,185,129,0.3)]">
                                                        ⚡ Achieved
                                                    </span>
                                                @else
                                                    <span class="shrink-0 rounded-full border border-emerald-500/15 bg-emerald-500/10 text-neutral-400 px-2 py-0.5 text-[9.5px] font-bold uppercase tracking-wide">
                                                        In Progress
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Gift Reward Name -->
                                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-[#02180f] border border-emerald-500/20 text-[11px] font-bold text-emerald-300">
                                                <i data-lucide="gift" class="h-3 w-3 shrink-0 text-emerald-400"></i>
                                                <span class="truncate">{{ $tier['reward'] }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="pt-0.5 border-t border-emerald-500/10">
                                        <div class="flex items-center justify-between gap-2 text-[9.5px] font-mono font-bold mb-1">
                                            <span class="text-neutral-400 font-sans font-semibold uppercase tracking-wider text-[9px]">MILESTONE PROGRESS</span>
                                            <span class="text-emerald-400 font-mono font-black">{{ number_format($progress, 0) }}% Completed</span>
                                        </div>
                                        <div class="w-full h-2 rounded-full bg-[#010e08] overflow-hidden p-0.5 border border-emerald-500/20">
                                            <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-400 transition-all duration-700 shadow-[0_0_8px_rgba(16,185,129,0.6)]" style="width: {{ $progress }}%;"></div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
@endsection
