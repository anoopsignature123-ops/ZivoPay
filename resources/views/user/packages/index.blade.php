@extends('user.layouts.app')

@section('content')
    <style>
        .pdf-gold-badge {
            background: linear-gradient(180deg, #fef08a 0%, #f59e0b 50%, #b45309 100%) !important;
            border: 2px solid #fef08a !important;
            box-shadow: 0 0 20px rgba(243, 202, 82, 0.7), inset 0 2px 4px rgba(255, 255, 255, 0.8) !important;
        }

        .pdf-gold-ribbon {
            background: linear-gradient(90deg, #d97706 0%, #fef08a 50%, #d97706 100%) !important;
            color: #000000 !important;
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5) !important;
        }

        .pdf-package-card {
            background: linear-gradient(180deg, #063824 0%, #021d12 50%, #000000 100%) !important;
            border: 2px solid rgba(243, 202, 82, 0.8) !important;
            box-shadow: 0 0 25px rgba(243, 202, 82, 0.25), inset 0 1px 2px rgba(255, 255, 255, 0.2) !important;
        }

        /* Force Exactly 3 Cards Per Row on Screens >= 768px */
        @media (min-width: 768px) {
            .grid-3-cards {
                display: grid !important;
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                gap: 2rem !important;
            }
        }
    </style>

    <div class="w-full space-y-6">

        <!-- Top Header Banner -->
        <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="pdf-num-badge">BP</span>
                    <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">DEX TRADE MEMBER
                        PORTAL</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient font-heading">BUY INVESTMENT PACKAGE</h1>
                <p class="text-xs text-neutral-300 mt-1">Invest $10 or any multiple of $10 and start earning 0.5% daily ROI yield.</p>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3">
                <div
                    class="px-5 py-3 rounded-xl bg-bg border border-amber-500/40 text-amber-300 text-xs font-bold font-mono flex items-center gap-2 shadow-lg shrink-0">
                    <i data-lucide="wallet" class="w-4 h-4 text-amber-400"></i>
                    <span>Deposit Wallet: <strong
                            class="text-emerald-400 text-sm font-black">${{ number_format($user->deposit_wallet, 2) }}</strong></span>
                </div>
                <a href="{{ route('user.deposits.index') }}"
                    class="px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-black uppercase tracking-wider shadow-lg hover:scale-105 transition flex items-center gap-1.5 shrink-0">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-black"></i> Add Fund
                </a>
            </div>
        </div>

        @if(session('success'))
            <div
                class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div
                class="p-4 rounded-xl bg-rose-500/20 border border-rose-500/50 text-rose-300 text-xs font-bold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400"></i> {{ session('error') }}
            </div>
        @endif

        <!-- TOP MAIN INVESTMENT & PACKAGE ACTIVATOR BAR -->
        <div
            class="p-6 sm:p-8 rounded-3xl bg-black/90 border-2 border-amber-400 shadow-[0_0_35px_rgba(243,202,82,0.3)] space-y-5">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <h3
                        class="text-base font-black text-amber-400 uppercase tracking-wider font-mono flex items-center gap-2">
                        <i data-lucide="zap" class="w-5 h-5 text-amber-400"></i> ENTER INVESTMENT AMOUNT & ACTIVATE
                    </h3>
                    <p class="text-xs text-neutral-300 mt-1">Enter your desired investment amount ($10+). The system
                        automatically assigns the corresponding tier and daily ROI.</p>
                </div>
            </div>

            <!-- Quick Preset Amount Buttons -->
            <div class="flex flex-wrap items-center gap-2 pt-1">
                <span class="text-xs font-bold text-amber-400/80 uppercase font-mono mr-1">Presets:</span>
                <button type="button" onclick="setQuickAmount(10)"
                    class="px-3.5 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition">$10</button>
                <button type="button" onclick="setQuickAmount(50)"
                    class="px-3.5 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition">$50</button>
                <button type="button" onclick="setQuickAmount(100)"
                    class="px-3.5 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition">$100</button>
                <button type="button" onclick="setQuickAmount(250)"
                    class="px-3.5 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition">$250</button>
                <button type="button" onclick="setQuickAmount(500)"
                    class="px-3.5 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition">$500</button>
                <button type="button" onclick="setQuickAmount(1000)"
                    class="px-3.5 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition">$1,000</button>
                <button type="button" onclick="setQuickAmount(5000)"
                    class="px-3.5 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition">$5,000</button>
            </div>

            <form action="{{ route('user.packages.buy') }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end"
                onsubmit="return confirm('Confirm activating package for this investment amount?')">
                @csrf
                <div class="md:col-span-5 space-y-1.5">
                    <label class="text-xs font-bold text-amber-300 uppercase tracking-wider block">Investment Amount ($
                        USD)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-amber-400 font-black text-base">$</span>
                        <input type="number" step="0.01" min="10" id="quickInvestAmount" name="invested_amount" value="100"
                            class="w-full pl-9 pr-4 py-3.5 rounded-2xl bg-black border-2 border-amber-500/60 text-white font-mono font-black text-lg focus:outline-none focus:border-amber-400"
                            required oninput="calculateQuickPackage(this.value)">
                    </div>
                </div>

                <div class="md:col-span-7 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div id="quickPkgStatus"
                        class="flex-1 p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/40 text-xs font-mono font-bold text-emerald-300 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                        <!-- Filled dynamically by JS -->
                    </div>

                    <button type="submit"
                        class="px-8 py-4 rounded-2xl pdf-gold-ribbon hover:brightness-110 text-black font-black text-sm uppercase tracking-wider shadow-xl flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                        <i data-lucide="sparkles" class="w-5 h-5 text-black"></i> INVEST NOW
                    </button>
                </div>
            </form>
        </div>

        <!-- Section Header Banner matching PDF -->
        <div class="text-center space-y-2 pt-4">
            <div
                class="inline-flex items-center justify-center px-8 py-2.5 rounded-full pdf-gold-ribbon text-base font-black uppercase tracking-widest shadow-xl">
                👑 AVAILABLE INVESTMENT TIERS
            </div>
            <p class="text-xs text-amber-400 font-extrabold tracking-widest uppercase">TIER SUMMARY & RETURN RATES</p>
        </div>

        <!-- PACKAGES GRID CARDS (PURE DISPLAY CARDS - NO BUTTONS & NO INPUTS INSIDE) -->
        <div class="grid grid-cols-1 grid-3-cards gap-8 pt-2">
            @foreach($packages as $pkg)
                @php 
                                $hasActive = isset($userActivePackages) && $userActivePackages->has($pkg->id);
                    $activePkg = $hasActive ? $userActivePackages->get($pkg->id) : null;
                @endphp
                <div id="pkgCard_{{ $pkg->id }}" onclick="setQuickAmount({{ $pkg->min_amount }})"
                    class="pdf-package-card rounded-3xl p-6 relative flex flex-col justify-between space-y-5 text-center cursor-pointer transition-all duration-300 hover:scale-[1.03] {{ $hasActive ? 'ring-2 ring-emerald-400/80 shadow-[0_0_30px_rgba(16,185,129,0.3)]' : '' }}">

                    <div class="space-y-4">
                        @if($hasActive)
                            <!-- TOP ACTIVE BADGE PILL -->
                            <div
                                class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-emerald-400 text-black text-[10px] font-black uppercase tracking-widest border border-white shadow-[0_0_15px_rgba(16,185,129,0.8)] mx-auto">
                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-black"></i>
                                <span>ACTIVE PACKAGE</span>
                            </div>
                        @endif

                        <!-- DYNAMIC MATCHED SELECTION PILL (SHOWN WHEN MATCHED BY TOP INPUT) -->
                        <div id="selectedPill_{{ $pkg->id }}"
                            class="hidden inline-flex items-center gap-1.5 px-4 py-1 rounded-full bg-amber-400 text-black text-[10px] font-black uppercase tracking-widest border border-white shadow-[0_0_15px_rgba(245,158,11,0.8)] mx-auto animate-pulse">
                            <i data-lucide="target" class="w-3.5 h-3.5 text-black"></i>
                            <span>SELECTED TIER</span>
                        </div>

                        <!-- TOP CIRCULAR GOLD BADGE -->
                        <div
                            class="w-14 h-14 rounded-full pdf-gold-badge text-black flex items-center justify-center font-black text-xl shadow-2xl mx-auto">
                            @if($loop->index == 0)
                                <i data-lucide="coins" class="w-7 h-7 text-black"></i>
                            @elseif($loop->index == 1)
                                <i data-lucide="rocket" class="w-7 h-7 text-black"></i>
                            @elseif($loop->index == 2)
                                <i data-lucide="trending-up" class="w-7 h-7 text-black"></i>
                            @elseif($loop->index == 3)
                                <i data-lucide="globe" class="w-7 h-7 text-black"></i>
                            @else
                                <i data-lucide="shield-check" class="w-7 h-7 text-black"></i>
                            @endif
                        </div>

                        <!-- GOLD RIBBON PACKAGE NAME HEADER -->
                        <div>
                            <div
                                class="inline-block px-6 py-1.5 rounded-full pdf-gold-ribbon text-xs font-black uppercase tracking-widest shadow-md">
                                PACKAGE {{ $loop->iteration }}
                            </div>
                        </div>

                        <!-- METALLIC PRICE RANGE TITLE (e.g. $10 to $100) -->
                        <h3 class="text-2xl font-black text-white font-mono tracking-tight pt-1">
                            ${{ number_format($pkg->min_amount, 0) }} to
                            {{ $pkg->max_amount >= 999999 ? 'Above' : '$' . number_format($pkg->max_amount, 0) }}
                        </h3>

                        <!-- PACKAGE DETAILS DISPLAY BOX -->
                        <div class="p-4 rounded-2xl bg-black/70 border border-amber-500/40 space-y-2.5 font-mono text-left">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-neutral-400 font-sans font-bold">Daily Rate:</span>
                                <span class="text-amber-300 font-black text-sm">{{ number_format($pkg->daily_roi, 2) }}% /
                                    Day</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-neutral-400 font-sans font-bold">Duration:</span>
                                <span class="text-white font-black text-sm">{{ $pkg->duration_days }} Days</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-neutral-400 font-sans font-bold">Total Cap:</span>
                                <span
                                    class="text-amber-300 font-black text-sm">{{ number_format($pkg->total_return_multiplier, 1) }}X
                                    Return</span>
                            </div>

                            @if($hasActive && $activePkg)
                                <div class="pt-2 border-t border-amber-500/20 space-y-1.5 font-mono text-xs">
                                    <div class="flex justify-between items-center">
                                        <span class="text-emerald-400 font-sans font-bold flex items-center gap-1">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> Total Active
                                            Capital:
                                        </span>
                                        <span
                                            class="text-emerald-300 font-black text-sm">${{ number_format($activePkg->total_invested, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[11px]">
                                        <span class="text-neutral-400 font-sans font-medium">Active Deposits:</span>
                                        <span class="text-amber-300 font-bold">{{ $activePkg->active_count }} Active</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="text-[11px] text-amber-400/80 font-mono font-bold tracking-wider uppercase pt-1">
                        💡 Click card to select minimum (${{ number_format($pkg->min_amount, 0) }})
                    </div>

                </div>
            @endforeach
        </div>
    </div>

    </div>

    <script>
        const pkgRanges = [
            { id: {{ $packages[0]->id ?? 1 }}, name: 'PACKAGE 1', min: 10, max: 100, roiRate: 0.50, roiStr: '0.50% / Day' },
            { id: {{ $packages[1]->id ?? 2 }}, name: 'PACKAGE 2', min: 101, max: 500, roiRate: 0.75, roiStr: '0.75% / Day' },
            { id: {{ $packages[2]->id ?? 3 }}, name: 'PACKAGE 3', min: 501, max: 1000, roiRate: 1.00, roiStr: '1.00% / Day' },
            { id: {{ $packages[3]->id ?? 4 }}, name: 'PACKAGE 4', min: 1001, max: 5000, roiRate: 1.25, roiStr: '1.25% / Day' },
            { id: {{ $packages[4]->id ?? 5 }}, name: 'PACKAGE 5', min: 5001, max: 999999, roiRate: 1.50, roiStr: '1.50% / Day' }
        ];

        function setQuickAmount(amt) {
            const input = document.getElementById('quickInvestAmount');
            if (input) {
                input.value = amt;
                calculateQuickPackage(amt);
            }
        }

        function calculateQuickPackage(val) {
            const amt = parseFloat(val) || 0;
            const box = document.getElementById('quickPkgStatus');
            if (!box) return;

            // Reset highlights on all cards
            pkgRanges.forEach(p => {
                const card = document.getElementById('pkgCard_' + p.id);
                const pill = document.getElementById('selectedPill_' + p.id);
                if (card) {
                    card.classList.remove('ring-4', 'ring-amber-400', 'scale-[1.04]', 'shadow-[0_0_40px_rgba(245,158,11,0.5)]');
                }
                if (pill) pill.classList.add('hidden');
            });

            if (amt < 10) {
                box.className = "flex-1 p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/40 text-xs font-mono font-bold text-rose-300 flex items-center justify-between";
                box.innerHTML = `<span>⚠️ Minimum investment amount is $10.00</span>`;
                return;
            }

            const found = pkgRanges.find(p => amt >= p.min && amt <= p.max) || pkgRanges[pkgRanges.length - 1];
            const dailyUsd = ((amt * found.roiRate) / 100).toFixed(2);
            const totalReturn = (amt * 2).toFixed(2);

            // Highlight matching card dynamically
            const activeCard = document.getElementById('pkgCard_' + found.id);
            const activePill = document.getElementById('selectedPill_' + found.id);
            if (activeCard) {
                activeCard.classList.add('ring-4', 'ring-amber-400', 'scale-[1.04]', 'shadow-[0_0_40px_rgba(245,158,11,0.5)]');
            }
            if (activePill) {
                activePill.classList.remove('hidden');
            }

            box.className = "flex-1 p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/40 text-xs font-mono font-bold text-emerald-300 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2";
            box.innerHTML = `
                <div>
                    <span class="text-neutral-300">Matched Tier:</span> <strong class="text-white font-extrabold">${found.name}</strong> 
                    <span class="text-neutral-400">($${found.min} - ${found.max >= 999999 ? 'Above' : '$' + found.max})</span>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="text-amber-400 font-black">${found.roiStr} ($${dailyUsd}/day)</span>
                    <span class="text-emerald-400 font-black">2X Return: $${totalReturn}</span>
                </div>
            `;
        }

        // Initialize default calculation on page load
        document.addEventListener('DOMContentLoaded', function () {
            const initialVal = document.getElementById('quickInvestAmount')?.value || 100;
            calculateQuickPackage(initialVal);
        });
    </script>
@endsection