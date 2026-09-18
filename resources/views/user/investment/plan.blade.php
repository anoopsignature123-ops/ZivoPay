@extends('user.layouts.app')

@section('title', 'Investment Tiers & Capital Growth Plans - ZIVO PAY')

@section('content')
<div class="space-y-5 font-sans">

    <!-- Compact Top Banner -->
    <div class="p-5 sm:p-6 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-[0_0_30px_rgba(16,185,129,0.25)] flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/40">
                    CAPITAL GROWTH TIERS
                </span>
                <span class="text-xs text-emerald-400 font-black tracking-wider uppercase">&bull; 24 MONTHS RETURNS</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-white uppercase tracking-tight font-heading">
                INVESTMENT TIERS & DAILY PROFIT PLANS
            </h1>
            <p class="text-xs text-neutral-300 mt-0.5">
                Earn 0.15% to 0.30% Daily Profit for 730 Days (24 Months) + 15-Level Downline Matching Income.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <div class="p-3 rounded-2xl bg-[#02180f] border border-emerald-500/40 text-right shrink-0">
                <span class="block text-[10px] font-bold text-emerald-400 uppercase">Available Fund Wallet</span>
                <span class="text-lg font-black text-white font-mono">₹{{ number_format($user->deposit_wallet, 2) }}</span>
            </div>
            <a href="#investFormSection" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-black font-black uppercase tracking-wider text-xs hover:shadow-[0_0_20px_rgba(16,185,129,0.6)] transition flex items-center gap-1.5 shadow">
                <i data-lucide="zap" class="w-4 h-4"></i> Activate Plan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-300 text-xs font-bold flex items-center gap-2 shadow-md">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400 shrink-0"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/20 border border-rose-500/50 text-rose-300 text-xs font-bold space-y-1 shadow-md">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400 shrink-0"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Investment Tiers Grid (Compact Clean Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Tier 1: Silver -->
        <div class="p-4 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg relative overflow-hidden group hover:border-emerald-400 transition space-y-3">
            <div class="flex items-center justify-between border-b border-emerald-500/20 pb-2">
                <span class="text-xs font-black text-emerald-400 uppercase tracking-wider font-heading">SILVER TIER</span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-[9px] font-black text-emerald-300">0.15% DAILY</span>
            </div>
            <div>
                <span class="text-[10px] text-neutral-400 block font-bold">Capital Range</span>
                <h3 class="text-base font-black text-white font-mono mt-0.5">₹1,000 - ₹99,999</h3>
            </div>
            <div class="space-y-1 text-[11px] text-neutral-300 pt-1">
                <div class="flex justify-between"><span>Daily Profit:</span> <strong class="text-emerald-400">0.15% / day</strong></div>
                <div class="flex justify-between"><span>Duration:</span> <strong class="text-white">730 Days (24 Mo)</strong></div>
                <div class="flex justify-between"><span>Direct Bonus:</span> <strong class="text-emerald-300">5% Level 1</strong></div>
            </div>
            <button type="button" onclick="selectAmount(5000)" class="w-full py-2 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold hover:bg-emerald-500 hover:text-black transition cursor-pointer">
                Select ₹5,000
            </button>
        </div>

        <!-- Tier 2: Gold -->
        <div class="p-4 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg relative overflow-hidden group hover:border-emerald-400 transition space-y-3">
            <div class="flex items-center justify-between border-b border-emerald-500/20 pb-2">
                <span class="text-xs font-black text-emerald-400 uppercase tracking-wider font-heading">GOLD TIER</span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-[9px] font-black text-emerald-300">0.20% DAILY</span>
            </div>
            <div>
                <span class="text-[10px] text-neutral-400 block font-bold">Capital Range</span>
                <h3 class="text-base font-black text-white font-mono mt-0.5">₹1,00,000 - ₹4,99,999</h3>
            </div>
            <div class="space-y-1 text-[11px] text-neutral-300 pt-1">
                <div class="flex justify-between"><span>Daily Profit:</span> <strong class="text-emerald-400">0.20% / day</strong></div>
                <div class="flex justify-between"><span>Duration:</span> <strong class="text-white">730 Days (24 Mo)</strong></div>
                <div class="flex justify-between"><span>Direct Bonus:</span> <strong class="text-emerald-300">5% Level 1</strong></div>
            </div>
            <button type="button" onclick="selectAmount(100000)" class="w-full py-2 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold hover:bg-emerald-500 hover:text-black transition cursor-pointer">
                Select ₹1,00,000
            </button>
        </div>

        <!-- Tier 3: Platinum -->
        <div class="p-4 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg relative overflow-hidden group hover:border-emerald-400 transition space-y-3">
            <div class="flex items-center justify-between border-b border-emerald-500/20 pb-2">
                <span class="text-xs font-black text-emerald-400 uppercase tracking-wider font-heading">PLATINUM TIER</span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-[9px] font-black text-emerald-300">0.25% DAILY</span>
            </div>
            <div>
                <span class="text-[10px] text-neutral-400 block font-bold">Capital Range</span>
                <h3 class="text-base font-black text-white font-mono mt-0.5">₹5,00,000 - ₹9,99,999</h3>
            </div>
            <div class="space-y-1 text-[11px] text-neutral-300 pt-1">
                <div class="flex justify-between"><span>Daily Profit:</span> <strong class="text-emerald-400">0.25% / day</strong></div>
                <div class="flex justify-between"><span>Duration:</span> <strong class="text-white">730 Days (24 Mo)</strong></div>
                <div class="flex justify-between"><span>Direct Bonus:</span> <strong class="text-emerald-300">5% Level 1</strong></div>
            </div>
            <button type="button" onclick="selectAmount(500000)" class="w-full py-2 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold hover:bg-emerald-500 hover:text-black transition cursor-pointer">
                Select ₹5,00,000
            </button>
        </div>

        <!-- Tier 4: Diamond -->
        <div class="p-4 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg relative overflow-hidden group hover:border-emerald-400 transition space-y-3">
            <div class="flex items-center justify-between border-b border-emerald-500/20 pb-2">
                <span class="text-xs font-black text-emerald-400 uppercase tracking-wider font-heading">DIAMOND TIER</span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-[9px] font-black text-emerald-300">0.30% DAILY</span>
            </div>
            <div>
                <span class="text-[10px] text-neutral-400 block font-bold">Capital Range</span>
                <h3 class="text-base font-black text-white font-mono mt-0.5">₹10,00,000+</h3>
            </div>
            <div class="space-y-1 text-[11px] text-neutral-300 pt-1">
                <div class="flex justify-between"><span>Daily Profit:</span> <strong class="text-emerald-400">0.30% / day</strong></div>
                <div class="flex justify-between"><span>Duration:</span> <strong class="text-white">730 Days (24 Mo)</strong></div>
                <div class="flex justify-between"><span>Direct Bonus:</span> <strong class="text-emerald-300">5% Level 1</strong></div>
            </div>
            <button type="button" onclick="selectAmount(1000000)" class="w-full py-2 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold hover:bg-emerald-500 hover:text-black transition cursor-pointer">
                Select ₹10,00,000
            </button>
        </div>
    </div>

    <!-- Interactive Returns Calculator & Plan Activation Form -->
    <div id="investFormSection" class="p-5 sm:p-6 rounded-3xl bg-[#042718] border-2 border-emerald-500/40 shadow-2xl relative">
        <div class="flex items-center justify-between border-b border-emerald-500/20 pb-3 mb-5">
            <h2 class="text-base font-black text-white uppercase tracking-tight flex items-center gap-2 font-heading">
                <i data-lucide="calculator" class="w-5 h-5 text-emerald-400"></i> Capital Investment Calculator & Activation Form
            </h2>
            <span class="text-[10px] px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 font-bold border border-emerald-500/30">Instant Processing</span>
        </div>

        <form action="{{ route('user.investment.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            @csrf

            <!-- Left 50% (lg:col-span-6): Calculator Inputs & Form -->
            <div class="lg:col-span-6 space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-emerald-400 uppercase">Enter Investment Amount (₹) *</label>
                        <span class="text-[11px] text-neutral-400">Min: <strong class="text-white">₹1,000</strong></span>
                    </div>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-400 font-bold text-base font-mono">₹</span>
                        <input type="number" id="investAmountInput" name="amount" min="1000" step="500" placeholder="e.g. 50000"
                            oninput="calculateReturns(); checkValidation();" required
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-mono font-bold text-base focus:outline-none focus:border-emerald-400 transition">
                    </div>
                    <div id="validationHint" class="mt-1.5 text-xs">
                        <span class="text-neutral-400">Available Fund Wallet: <strong class="text-emerald-400 font-mono">₹{{ number_format($user->deposit_wallet, 2) }}</strong></span>
                    </div>
                </div>

                <!-- Preset Quick Buttons -->
                <div>
                    <span class="text-[10px] font-bold text-neutral-400 uppercase block mb-1.5">Quick Presets:</span>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="selectAmount(5000)" class="px-3 py-1 rounded-lg bg-[#01140c] border border-emerald-500/30 text-xs font-bold text-emerald-300 hover:bg-emerald-500/30 transition cursor-pointer">₹5,000</button>
                        <button type="button" onclick="selectAmount(25000)" class="px-3 py-1 rounded-lg bg-[#01140c] border border-emerald-500/30 text-xs font-bold text-emerald-300 hover:bg-emerald-500/30 transition cursor-pointer">₹25,000</button>
                        <button type="button" onclick="selectAmount(100000)" class="px-3 py-1 rounded-lg bg-[#01140c] border border-emerald-500/30 text-xs font-bold text-emerald-300 hover:bg-emerald-500/30 transition cursor-pointer">₹1,00,000</button>
                        <button type="button" onclick="selectAmount(500000)" class="px-3 py-1 rounded-lg bg-[#01140c] border border-emerald-500/30 text-xs font-bold text-emerald-300 hover:bg-emerald-500/30 transition cursor-pointer">₹5,00,000</button>
                        <button type="button" onclick="selectAmount(1000000)" class="px-3 py-1 rounded-lg bg-[#01140c] border border-emerald-500/30 text-xs font-bold text-emerald-300 hover:bg-emerald-500/30 transition cursor-pointer">₹10,00,000</button>
                    </div>
                </div>

                <!-- Estimated Return Breakdown Box -->
                <div class="p-3.5 rounded-xl bg-[#01140c] border border-emerald-500/30 space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-neutral-400">Selected Tier Rate:</span>
                        <strong id="calcRate" class="text-emerald-400 font-bold">0.15% Daily</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-400">Estimated Daily Profit:</span>
                        <strong id="calcDaily" class="text-white font-mono">₹0.00 / day</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-400">Estimated Monthly Profit (30 Days):</span>
                        <strong id="calcMonthly" class="text-emerald-300 font-mono">₹0.00 / month</strong>
                    </div>
                    <div class="flex justify-between border-t border-emerald-500/20 pt-2 text-sm">
                        <span class="text-white font-bold">Total 24 Months Profit + Principal:</span>
                        <strong id="calcTotal" class="text-emerald-400 font-mono font-black text-base">₹0.00</strong>
                    </div>
                </div>

                <!-- Submit Button: Disabled until inputs are valid & filled -->
                <button type="submit" id="submitBtn" disabled
                    class="w-full py-3.5 rounded-xl bg-neutral-800 border border-neutral-700 text-neutral-500 font-black uppercase text-xs cursor-not-allowed transition flex items-center justify-center gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                    <span id="btnText">Confirm & Activate Capital Plan</span>
                </button>
            </div>

            <!-- Right 50% (lg:col-span-6): 15-Level Income & Rewards Summary -->
            <div class="lg:col-span-6 p-4 sm:p-5 rounded-2xl bg-[#02180f] border border-emerald-500/30 space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between border-b border-emerald-500/20 pb-2.5">
                        <h3 class="text-xs font-black text-emerald-400 uppercase tracking-wider font-heading">
                            15-LEVEL DOWNLINE & MATCHING INCOME RULES
                        </h3>
                        <span class="text-[10px] text-neutral-400 font-mono">24 MONTHS DURATION</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5 text-xs">
                        <div class="p-3 rounded-xl bg-[#01140c] border border-emerald-500/20 space-y-1">
                            <span class="block text-emerald-400 font-bold uppercase text-[10px]">Direct Level Income (12%)</span>
                            <span class="block text-white font-black text-sm">Level 1: 5.0%</span>
                            <span class="block text-neutral-400 text-[10px]">Levels 2 to 15: 0.5% per level</span>
                        </div>

                        <div class="p-3 rounded-xl bg-[#01140c] border border-emerald-500/20 space-y-1">
                            <span class="block text-emerald-400 font-bold uppercase text-[10px]">ROI Level Matching (26%)</span>
                            <span class="block text-white font-black text-sm">L1: 10% | L2: 3%</span>
                            <span class="block text-neutral-400 text-[10px]">L3 to L15: 1.0% per level</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-[#01140c] border border-emerald-500/20 space-y-1 text-xs">
                        <span class="text-emerald-400 font-bold uppercase text-[10px] block">🏆 Milestone Rewards Targets</span>
                        <div class="grid grid-cols-2 gap-1 text-[11px] text-neutral-300 pt-0.5 font-mono">
                            <div>₹5L Vol: <strong class="text-white font-sans">Mobile Phone</strong></div>
                            <div>₹10L Vol: <strong class="text-white font-sans">Laptop</strong></div>
                            <div>₹25L Vol: <strong class="text-white font-sans">EV Scooty</strong></div>
                            <div>₹1 Cr Vol: <strong class="text-white font-sans">Car DP ₹3L</strong></div>
                            <div>₹5 Cr Vol: <strong class="text-white font-sans">Tata Punch SUV</strong></div>
                            <div>₹10 Cr Vol: <strong class="text-white font-sans">Tata Sierra SUV</strong></div>
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-[11px] text-emerald-300 font-semibold flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400 shrink-0"></i>
                    <span>Your capital is backed by 24 months automated daily payouts directly into your Earning Wallet.</span>
                </div>
            </div>
        </form>
    </div>

    <!-- My Active Investment Packages Table -->
    <div class="p-5 sm:p-6 rounded-3xl bg-[#042718] border border-emerald-500/40 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-emerald-500/20 pb-3">
            <h2 class="text-sm font-black text-white uppercase tracking-tight flex items-center gap-2 font-heading">
                <i data-lucide="layers" class="w-4 h-4 text-emerald-400"></i> My Active Capital Investment Packages
            </h2>
            <span class="px-2.5 py-1 rounded-full zivo-pill-emerald text-[10px] font-black uppercase">
                {{ $investments->count() }} Active
            </span>
        </div>

        @if($investments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-neutral-300">
                    <thead class="bg-[#02180f] text-emerald-400 uppercase text-[10px] font-bold border-b border-emerald-500/30">
                        <tr>
                            <th class="p-3">Plan Name</th>
                            <th class="p-3">Capital Amount</th>
                            <th class="p-3">Daily ROI</th>
                            <th class="p-3">Days Completed</th>
                            <th class="p-3">Total Returned</th>
                            <th class="p-3">Activated At</th>
                            <th class="p-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-500/10 font-medium">
                        @foreach($investments as $inv)
                            <tr class="hover:bg-emerald-500/5 transition">
                                <td class="p-3 font-bold text-white">{{ $inv->plan_name }}</td>
                                <td class="p-3 font-mono font-bold text-emerald-400">₹{{ number_format($inv->amount, 2) }}</td>
                                <td class="p-3 font-semibold text-emerald-300">{{ $inv->daily_percentage }}% (₹{{ number_format($inv->daily_amount, 2) }}/day)</td>
                                <td class="p-3 font-mono font-bold text-white">{{ $inv->days_completed }} / 730 Days</td>
                                <td class="p-3 font-mono text-emerald-400 font-bold">₹{{ number_format($inv->total_returned, 2) }}</td>
                                <td class="p-3 text-neutral-400 font-mono text-[10px]">{{ $inv->activated_at ? $inv->activated_at->format('d M Y, H:i') : 'N/A' }}</td>
                                <td class="p-3 text-right">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase {{ $inv->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-400/40' : 'bg-neutral-800 text-neutral-400' }}">
                                        {{ strtoupper($inv->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6 text-center text-neutral-400 space-y-1">
                <i data-lucide="info" class="w-6 h-6 text-emerald-400/50 mx-auto"></i>
                <p class="text-xs font-bold text-white">No active investment packages found.</p>
                <p class="text-[11px] text-neutral-400">Select a tier above or enter an amount to activate your plan!</p>
            </div>
        @endif
    </div>

</div>

<script>
function selectAmount(amt) {
    let input = document.getElementById('investAmountInput');
    if (input) {
        input.value = amt;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
    calculateReturns();
    checkValidation();
}

function calculateReturns() {
    try {
        let input = document.getElementById('investAmountInput');
        let calcRate = document.getElementById('calcRate');
        let calcDaily = document.getElementById('calcDaily');
        let calcMonthly = document.getElementById('calcMonthly');
        let calcTotal = document.getElementById('calcTotal');

        if (!input || !calcRate || !calcDaily || !calcMonthly || !calcTotal) return;

        let rawVal = input.value || "0";
        let amt = parseFloat(String(rawVal).replace(/,/g, '')) || 0;
        let rate = 0.15;
        let rateText = "0.15% Daily (Silver)";

        if (amt >= 1000000) {
            rate = 0.30;
            rateText = "0.30% Daily (Diamond)";
        } else if (amt >= 500000) {
            rate = 0.25;
            rateText = "0.25% Daily (Platinum)";
        } else if (amt >= 100000) {
            rate = 0.20;
            rateText = "0.20% Daily (Gold)";
        } else if (amt >= 1000) {
            rate = 0.15;
            rateText = "0.15% Daily (Silver)";
        } else {
            rate = 0.15;
            rateText = "0.15% Daily";
        }

        let daily = amt * (rate / 100);
        let monthly = daily * 30;
        let total24Mo = (daily * 730) + amt;

        calcRate.innerText = rateText;
        calcDaily.innerText = '₹' + daily.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' / day';
        calcMonthly.innerText = '₹' + monthly.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' / month';
        calcTotal.innerText = '₹' + total24Mo.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    } catch (e) {
        console.error("calculateReturns error:", e);
    }
}

function checkValidation() {
    try {
        let input = document.getElementById('investAmountInput');
        let submitBtn = document.getElementById('submitBtn');
        let btnText = document.getElementById('btnText');
        let hintErr = document.getElementById('validationHint');

        if (!submitBtn || !btnText || !hintErr) return;

        let rawWalletStr = "{{ (float) ($user->deposit_wallet ?? 0) }}";
        let userWallet = parseFloat(String(rawWalletStr).replace(/,/g, '')) || 0;

        let rawVal = input ? input.value : "";
        let val = parseFloat(String(rawVal).replace(/,/g, ''));

        if (!input || !rawVal || isNaN(val) || val <= 0) {
            submitBtn.disabled = true;
            submitBtn.className = "w-full py-3.5 rounded-xl bg-neutral-800 border border-neutral-700 text-neutral-500 font-black uppercase text-xs cursor-not-allowed transition flex items-center justify-center gap-2 opacity-75";
            btnText.innerText = "Enter Investment Amount (Min ₹1,000)";
            hintErr.innerHTML = '<span class="text-neutral-400">Available Fund Wallet: <strong class="text-emerald-400 font-mono">₹' + userWallet.toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</strong></span>';
        } else if (val < 1000) {
            submitBtn.disabled = true;
            submitBtn.className = "w-full py-3.5 rounded-xl bg-neutral-800 border border-neutral-700 text-neutral-500 font-black uppercase text-xs cursor-not-allowed transition flex items-center justify-center gap-2 opacity-75";
            btnText.innerText = "Minimum Amount Required: ₹1,000";
            hintErr.innerHTML = '<span class="text-amber-400 font-bold">⚠️ Amount must be at least ₹1,000.</span>';
        } else if (val > userWallet) {
            submitBtn.disabled = true;
            submitBtn.className = "w-full py-3.5 rounded-xl bg-rose-950/60 border border-rose-500/50 text-rose-400 font-black uppercase text-xs cursor-not-allowed transition flex items-center justify-center gap-2 opacity-75";
            let diff = val - userWallet;
            btnText.innerText = "Insufficient Fund Wallet Balance";
            hintErr.innerHTML = '<span class="text-rose-400 font-bold">⚠️ Insufficient Fund Wallet Balance! Short by ₹' + diff.toLocaleString('en-IN', {minimumFractionDigits: 2}) + '. <a href="{{ route("user.wallet.transfer") }}" class="underline font-black text-emerald-400">Add Fund</a> first.</span>';
        } else {
            submitBtn.disabled = false;
            submitBtn.className = "w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-500 text-black font-black uppercase tracking-wider text-xs hover:shadow-[0_0_25px_rgba(16,185,129,0.8)] transition cursor-pointer flex items-center justify-center gap-2 shadow-lg opacity-100";
            btnText.innerText = "Confirm & Activate Capital Plan (₹" + val.toLocaleString('en-IN') + ")";
            hintErr.innerHTML = '<span class="text-emerald-400 font-bold">✓ Amount valid! Available: ₹' + userWallet.toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</span>';
        }
    } catch (e) {
        console.error("checkValidation error:", e);
    }
}

document.addEventListener("DOMContentLoaded", function() {
    let input = document.getElementById('investAmountInput');
    if (input) {
        input.addEventListener('input', function() { calculateReturns(); checkValidation(); });
        input.addEventListener('keyup', function() { calculateReturns(); checkValidation(); });
        input.addEventListener('change', function() { calculateReturns(); checkValidation(); });
    }
    calculateReturns();
    checkValidation();
});
</script>
@endsection
