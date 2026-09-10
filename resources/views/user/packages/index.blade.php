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
    </style>

    <div class="w-full space-y-6">

        <!-- Top Header Banner -->
        <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="pdf-num-badge">BP</span>
                    <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">DEX TRADE MEMBER PORTAL</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient font-heading">BUY INVESTMENT PACKAGE</h1>
                <p class="text-xs text-neutral-300 mt-1">Invest minimum $10 or any multiple of $10 ($10, $20, $30, $50, $100, $500...). Earn 0.5% Daily ROI Yield up to 200% (2X Return).</p>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3">
                <div class="px-5 py-3 rounded-xl bg-bg border border-amber-500/40 text-amber-300 text-xs font-bold font-mono flex items-center gap-2 shadow-lg shrink-0">
                    <i data-lucide="wallet" class="w-4 h-4 text-amber-400"></i>
                    <span>Deposit Wallet: <strong class="text-emerald-400 text-sm font-black">${{ number_format($user->deposit_wallet, 2) }}</strong></span>
                </div>
                <a href="{{ route('user.deposits.index') }}"
                    class="px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-black uppercase tracking-wider shadow-lg hover:scale-105 transition flex items-center gap-1.5 shrink-0">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-black"></i> Add Fund
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-500/20 border border-rose-500/50 text-rose-300 text-xs font-bold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400"></i> {{ session('error') }}
            </div>
        @endif

        <!-- MAIN INVESTMENT CARD & REAL-TIME CALCULATOR -->
        <div class="p-6 sm:p-8 rounded-3xl bg-black/90 border-2 border-amber-400 shadow-[0_0_35px_rgba(243,202,82,0.3)] space-y-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-black text-amber-400 uppercase tracking-wider font-heading flex items-center gap-2">
                        <i data-lucide="zap" class="w-5 h-5 text-amber-400"></i> SELECT OR ENTER INVESTMENT AMOUNT
                    </h3>
                    <p class="text-xs text-neutral-300 mt-1">Minimum investment amount is <strong>$10.00 USD</strong>. Amount must be an exact multiple of <strong>$10</strong> (e.g. $10, $20, $30, $50, $100, $500, $1,000).</p>
                </div>
                <div class="px-3 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/40 text-amber-400 text-[11px] font-mono font-bold tracking-wider uppercase">
                    0.5% DAILY ROI • 200% (2X) CAP
                </div>
            </div>

            <!-- Quick Amount Preset Buttons -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-amber-300/90 uppercase tracking-wider block font-mono">QUICK AMOUNT PRESETS:</label>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" onclick="setQuickAmount(10)" class="preset-btn px-4 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition hover:scale-105">$10</button>
                    <button type="button" onclick="setQuickAmount(20)" class="preset-btn px-4 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition hover:scale-105">$20</button>
                    <button type="button" onclick="setQuickAmount(30)" class="preset-btn px-4 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition hover:scale-105">$30</button>
                    <button type="button" onclick="setQuickAmount(40)" class="preset-btn px-4 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition hover:scale-105">$40</button>
                    <button type="button" onclick="setQuickAmount(50)" class="preset-btn px-4 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition hover:scale-105">$50</button>
                    <button type="button" onclick="setQuickAmount(100)" class="preset-btn px-4 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition hover:scale-105">$100</button>
                    <button type="button" onclick="setQuickAmount(200)" class="preset-btn px-4 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition hover:scale-105">$200</button>
                    <button type="button" onclick="setQuickAmount(500)" class="preset-btn px-4 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition hover:scale-105">$500</button>
                    <button type="button" onclick="setQuickAmount(1000)" class="preset-btn px-4 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition hover:scale-105">$1,000</button>
                    <button type="button" onclick="setQuickAmount(2500)" class="preset-btn px-4 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition hover:scale-105">$2,500</button>
                    <button type="button" onclick="setQuickAmount(5000)" class="preset-btn px-4 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold transition hover:scale-105">$5,000</button>
                </div>
            </div>


            <!-- Investment Form -->
            <form action="{{ route('user.packages.buy') }}" method="POST" id="investForm" class="space-y-5" onsubmit="return confirmInvest()">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-end">
                    <div class="lg:col-span-5 space-y-2">
                        <label for="investedAmount" class="text-xs font-bold text-amber-300 uppercase tracking-wider block font-mono">Investment Amount ($ USD)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-amber-400 font-black text-xl">$</span>
                            <input type="number" step="10" min="10" id="investedAmount" name="invested_amount" value="100"
                                class="w-full pl-10 pr-4 py-4 rounded-2xl bg-black border-2 border-amber-500/60 text-white font-mono font-black text-xl focus:outline-none focus:border-amber-400 transition"
                                required oninput="validateAndCalculatePackage(this.value)">
                        </div>
                    </div>

                    <div class="lg:col-span-7 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <button type="submit" id="submitInvestBtn"
                            class="w-full py-4 rounded-2xl pdf-gold-ribbon hover:brightness-110 text-black font-black text-base uppercase tracking-wider shadow-xl flex items-center justify-center gap-2 shrink-0 cursor-pointer transition">
                            <i data-lucide="sparkles" class="w-5 h-5 text-black"></i> INVEST NOW
                        </button>
                    </div>
                </div>

                <!-- LIVE REAL-TIME CALCULATION & VALIDATION DISPLAY BOX -->
                <div id="calcDisplayBox" class="p-5 rounded-2xl bg-emerald-500/10 border border-emerald-500/40 space-y-3 font-mono transition-all duration-300">
                    <div class="flex items-center justify-between border-b border-amber-500/20 pb-3">
                        <span class="text-xs font-bold uppercase tracking-wider flex items-center gap-2" id="valStatusIcon">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
                            <span id="valStatusText" class="text-emerald-300">Valid Investment Amount</span>
                        </span>
                        <span class="text-[11px] text-amber-400 font-black" id="valAmountText">$100.00 USD</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs pt-1">
                        <div class="p-3 rounded-xl bg-black/60 border border-amber-500/30">
                            <span class="text-neutral-400 font-sans block text-[11px]">Daily ROI Yield (0.50%):</span>
                            <strong class="text-amber-300 text-sm font-black" id="calcDailyRoi">$0.50 / Day</strong>
                        </div>
                        <div class="p-3 rounded-xl bg-black/60 border border-amber-500/30">
                            <span class="text-neutral-400 font-sans block text-[11px]">Total Max Return (2X Cap):</span>
                            <strong class="text-emerald-400 text-sm font-black" id="calcTotalReturn">$200.00 Return</strong>
                        </div>
                        <div class="p-3 rounded-xl bg-black/60 border border-amber-500/30">
                            <span class="text-neutral-400 font-sans block text-[11px]">Contract Period:</span>
                            <strong class="text-white text-sm font-black">400 Days</strong>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- ACTIVE PACKAGES / ACTIVE INVESTMENT HISTORY SECTION -->
        <div class="space-y-4 pt-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-amber-500/30 pb-3">
                <div>
                    <h2 class="text-xl font-black text-white font-heading flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-5 h-5 text-emerald-400"></i> YOUR ACTIVE INVESTMENT PACKAGES
                    </h2>
                    <p class="text-xs text-neutral-300">Below are all your active packages currently generating 0.5% daily ROI income.</p>
                </div>
                <div class="px-4 py-1.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 text-xs font-mono font-bold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>{{ $userActivePackages->count() }} ACTIVE PACKAGES • TOTAL CAPITAL: ${{ number_format($totalActiveCapital, 2) }}</span>
                </div>
            </div>

            @if($userActivePackages->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($userActivePackages as $item)
                        @php
                            $progressPct = $item->total_return_amount > 0 ? min(100, round(($item->paid_roi_amount / $item->total_return_amount) * 100, 1)) : 0;
                        @endphp
                        <div class="pdf-package-card rounded-3xl p-6 relative flex flex-col justify-between space-y-4">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="px-3 py-1 rounded-full bg-emerald-400 text-black text-[10px] font-black uppercase tracking-wider flex items-center gap-1 shadow">
                                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-black"></i> ACTIVE
                                    </div>
                                    <span class="text-[11px] font-mono text-neutral-400">{{ $item->purchased_at ? $item->purchased_at->format('M d, Y') : 'N/A' }}</span>
                                </div>

                                <div>
                                    <span class="text-xs text-neutral-400 font-mono">Invested Capital:</span>
                                    <h3 class="text-3xl font-black text-amber-400 font-mono">${{ number_format($item->invested_amount, 2) }}</h3>
                                </div>

                                <div class="p-3.5 rounded-2xl bg-black/70 border border-amber-500/30 space-y-2 font-mono text-xs">
                                    <div class="flex justify-between items-center">
                                        <span class="text-neutral-400">Daily Yield (0.5%):</span>
                                        <span class="text-amber-300 font-black">${{ number_format($item->daily_roi_amount, 2) }} / Day</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-neutral-400">Total Return Cap (2X):</span>
                                        <span class="text-emerald-400 font-black">${{ number_format($item->total_return_amount, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-neutral-400">Received ROI:</span>
                                        <span class="text-white font-bold">${{ number_format($item->paid_roi_amount, 2) }} ({{ $progressPct }}%)</span>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="w-full bg-neutral-900 h-2 rounded-full overflow-hidden border border-amber-500/30 mt-1">
                                        <div class="bg-gradient-to-r from-amber-500 to-emerald-400 h-full rounded-full transition-all duration-500" style="width: {{ $progressPct }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-[11px] text-neutral-400 font-mono flex items-center justify-between border-t border-amber-500/20 pt-2">
                                <span>Expires: {{ $item->expires_at ? $item->expires_at->format('M d, Y') : '400 Days' }}</span>
                                <span class="text-amber-400 font-bold">400 Days Contract</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 rounded-3xl bg-black/60 border border-amber-500/30 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-amber-500/10 border border-amber-500/30 flex items-center justify-center mx-auto text-amber-400">
                        <i data-lucide="package-open" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-white">No Active Packages Found</h3>
                    <p class="text-xs text-neutral-400 max-w-md mx-auto">You currently have no active investment package. Select an amount above ($10 minimum, multiple of $10) and click <strong>Invest Now</strong> to activate your account and start receiving 0.5% daily ROI yield.</p>
                </div>
            @endif
        </div>

    </div>

    <script>
        function setQuickAmount(amt) {
            const input = document.getElementById('investedAmount');
            if (input) {
                input.value = amt;
                validateAndCalculatePackage(amt);
            }
        }

        function validateAndCalculatePackage(val) {
            const amt = parseFloat(val) || 0;
            const box = document.getElementById('calcDisplayBox');
            const statusText = document.getElementById('valStatusText');
            const amountText = document.getElementById('valAmountText');
            const dailyRoi = document.getElementById('calcDailyRoi');
            const totalReturn = document.getElementById('calcTotalReturn');
            const submitBtn = document.getElementById('submitInvestBtn');

            if (!box || !submitBtn) return;

            const isMinValid = amt >= 10;
            const isMultipleValid = (amt % 10 === 0);

            if (!isMinValid) {
                box.className = "p-5 rounded-2xl bg-rose-500/10 border border-rose-500/40 space-y-3 font-mono transition-all duration-300";
                statusText.className = "text-rose-300";
                statusText.textContent = "⚠️ Minimum investment amount is $10.00 USD";
                amountText.textContent = `$${amt.toFixed(2)} USD`;
                dailyRoi.textContent = "$0.00 / Day";
                totalReturn.textContent = "$0.00 Return";
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                return;
            }

            if (!isMultipleValid) {
                box.className = "p-5 rounded-2xl bg-rose-500/10 border border-rose-500/40 space-y-3 font-mono transition-all duration-300";
                statusText.className = "text-rose-300";
                statusText.textContent = "⚠️ Investment amount must be a multiple of $10 (e.g. $10, $20, $30, $50, $100...)";
                amountText.textContent = `$${amt.toFixed(2)} USD`;
                dailyRoi.textContent = "$0.00 / Day";
                totalReturn.textContent = "$0.00 Return";
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                return;
            }

            // Valid state
            box.className = "p-5 rounded-2xl bg-emerald-500/10 border border-emerald-500/40 space-y-3 font-mono transition-all duration-300";
            statusText.className = "text-emerald-300";
            statusText.textContent = "✓ Valid Investment Amount ($10 Multiple Satisfied)";
            amountText.textContent = `$${amt.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} USD`;

            const dailyVal = (amt * 0.005).toFixed(2);
            const totalVal = (amt * 2.00).toFixed(2);

            dailyRoi.textContent = `$${parseFloat(dailyVal).toLocaleString('en-US', {minimumFractionDigits: 2})} / Day`;
            totalReturn.textContent = `$${parseFloat(totalVal).toLocaleString('en-US', {minimumFractionDigits: 2})} Return`;

            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        function confirmInvest() {
            const amt = parseFloat(document.getElementById('investedAmount')?.value) || 0;
            if (amt < 10 || amt % 10 !== 0) {
                alert('Investment amount must be minimum $10 and an exact multiple of $10!');
                return false;
            }
            return confirm(`Are you sure you want to invest $${amt.toFixed(2)} in Dex Trade Package?\n\n- Daily ROI Yield: $${(amt*0.005).toFixed(2)}/day (0.5%)\n- Max Cap (2X Return): $${(amt*2.0).toFixed(2)}\n- Wallet: Deposit Wallet`);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const initialVal = document.getElementById('investedAmount')?.value || 100;
            validateAndCalculatePackage(initialVal);
        });
    </script>
@endsection