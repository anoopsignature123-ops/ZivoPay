@extends('user.layouts.app')

@section('title', 'ZIVO PAY - Income Plan Chart & Investment Tiers')

@section('content')
<div class="space-y-6">

    <!-- Top Banner & Quick Stats -->
    <div class="p-6 rounded-3xl bg-gradient-to-r from-emerald-950/90 via-emerald-900/90 to-emerald-950/90 border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.3)] flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-2 text-center md:text-left">
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[11px] font-black uppercase tracking-widest border border-emerald-500/40">
                OFFICIAL FINANCIAL PLAN
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight font-heading">
                INCOME PLAN & INVESTMENT TIERS
            </h1>
            <p class="text-xs text-neutral-300 max-w-xl">
                ZIVO E-Commerce & Financial Services • 24 Months Continuous Returns (0.15% to 0.30% Daily Profit) • 15-Level Income • 24x7 Withdrawals
            </p>
        </div>

        <div class="flex items-center gap-4 shrink-0">
            <div class="bg-black/50 border border-emerald-500/40 p-4 rounded-2xl text-center">
                <span class="block text-[10px] font-bold text-emerald-400 uppercase">Deposit Wallet</span>
                <span class="text-xl font-black text-white font-mono">₹{{ number_format($user->deposit_wallet, 2) }}</span>
            </div>
            <a href="#investFormSection" class="px-5 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-black font-black uppercase tracking-wider text-xs hover:shadow-[0_0_20px_rgba(16,185,129,0.8)] transition flex items-center gap-2">
                <i data-lucide="zap" class="w-4 h-4"></i> Activate Plan
            </a>
        </div>
    </div>

    <!-- Tiers Cards Grid (Seeded Capital Packages) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($packages as $pkg)
            <div class="p-5 rounded-2xl bg-emerald-950/80 border-2 border-emerald-500/40 shadow-lg relative overflow-hidden group hover:border-emerald-400 transition">
                <div class="absolute top-3 right-3 px-2.5 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-[10px] font-bold text-emerald-300">{{ number_format($pkg->daily_roi_percentage, 2) }}% Daily</div>
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">{{ $pkg->name }}</span>
                <h3 class="text-lg font-black text-white mt-1">₹{{ number_format($pkg->min_amount) }} - ₹{{ number_format($pkg->max_amount) }}</h3>
                <div class="mt-4 pt-3 border-t border-emerald-500/20 space-y-1.5 text-xs text-neutral-300">
                    <div class="flex justify-between"><span>Daily Profit:</span> <strong class="text-emerald-400">{{ number_format($pkg->daily_roi_percentage, 2) }}% / day</strong></div>
                    <div class="flex justify-between"><span>Duration:</span> <strong class="text-white">{{ $pkg->duration_days }} Days (24 Mo)</strong></div>
                    <div class="flex justify-between"><span>Direct Bonus:</span> <strong class="text-emerald-300">{{ number_format($pkg->direct_bonus_percentage, 2) }}% Level 1</strong></div>
                </div>
                <button onclick="selectAmount({{ (int)$pkg->min_amount }})" class="w-full mt-4 py-2 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-300 text-xs font-bold hover:bg-emerald-500 hover:text-black transition">Select ₹{{ number_format($pkg->min_amount) }}</button>
            </div>
        @empty
            <!-- Tier 1 -->
            <div class="p-5 rounded-2xl bg-emerald-950/80 border-2 border-emerald-500/40 shadow-lg relative overflow-hidden group hover:border-emerald-400 transition">
                <div class="absolute top-3 right-3 px-2.5 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-[10px] font-bold text-emerald-300">0.15% Daily</div>
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">SILVER TIER</span>
                <h3 class="text-lg font-black text-white mt-1">₹1,000 - ₹99,999</h3>
                <div class="mt-4 pt-3 border-t border-emerald-500/20 space-y-1.5 text-xs text-neutral-300">
                    <div class="flex justify-between"><span>Daily Profit:</span> <strong class="text-emerald-400">0.15% / day</strong></div>
                    <div class="flex justify-between"><span>Duration:</span> <strong class="text-white">730 Days (24 Mo)</strong></div>
                    <div class="flex justify-between"><span>Principal Return:</span> <strong class="text-emerald-300">100% at 24 Months</strong></div>
                </div>
                <button onclick="selectAmount(10000)" class="w-full mt-4 py-2 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-300 text-xs font-bold hover:bg-emerald-500 hover:text-black transition">Select ₹10,000</button>
            </div>
        @endforelse
    </div>

    <!-- Interactive Returns Calculator & Activation Form -->
    <div id="investFormSection" class="p-6 sm:p-8 rounded-3xl bg-slate-900 border-2 border-emerald-500/60 shadow-[0_15px_50px_rgba(0,0,0,0.8)] relative">
        <h2 class="text-xl font-black text-white uppercase tracking-tight flex items-center gap-2 mb-6">
            <i data-lucide="calculator" class="w-6 h-6 text-emerald-400"></i> Interactive Returns Calculator & Plan Activation
        </h2>

        <form action="{{ route('user.investment.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            @csrf

            <!-- Left: Calculator Inputs -->
            <div class="lg:col-span-6 space-y-6">
                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase mb-2">Enter Investment Amount (₹):</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-400 font-bold text-lg">₹</span>
                        <input type="number" id="investAmountInput" name="amount" min="1000" step="500" value="100000" oninput="calculateReturns()" required
                            class="w-full pl-10 pr-4 py-3.5 rounded-xl bg-bg border-2 border-emerald-500/50 text-white font-mono font-bold text-lg focus:outline-none focus:border-emerald-400">
                    </div>
                    <p class="text-[11px] text-neutral-400 mt-1">Minimum amount: ₹1,000 | Available Wallet Balance: <strong class="text-emerald-400">₹{{ number_format($user->deposit_wallet, 2) }}</strong></p>
                </div>

                <!-- Preset Quick Buttons -->
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="selectAmount(5000)" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-xs font-semibold hover:bg-emerald-500/30 transition">₹5,000</button>
                    <button type="button" onclick="selectAmount(25000)" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-xs font-semibold hover:bg-emerald-500/30 transition">₹25,000</button>
                    <button type="button" onclick="selectAmount(100000)" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-xs font-semibold hover:bg-emerald-500/30 transition">₹1,00,000</button>
                    <button type="button" onclick="selectAmount(500000)" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-xs font-semibold hover:bg-emerald-500/30 transition">₹5,00,000</button>
                    <button type="button" onclick="selectAmount(1000000)" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-xs font-semibold hover:bg-emerald-500/30 transition">₹10,00,000</button>
                </div>

                <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/30 space-y-2">
                    <div class="flex justify-between text-xs">
                        <span class="text-neutral-400">Selected Tier Rate:</span>
                        <strong id="calcRate" class="text-emerald-400 font-bold">0.20% Daily</strong>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-neutral-400">Daily Profit:</span>
                        <strong id="calcDaily" class="text-white font-mono">₹200.00 / day</strong>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-neutral-400">Monthly Profit (30 Days):</span>
                        <strong id="calcMonthly" class="text-emerald-300 font-mono">₹6,000.00 / month</strong>
                    </div>
                    <div class="flex justify-between text-xs border-t border-emerald-500/20 pt-2">
                        <span class="text-neutral-300 font-bold">Total 24 Months Profit + Return:</span>
                        <strong id="calcTotal" class="text-emerald-400 font-mono text-base font-black">₹2,46,000.00</strong>
                    </div>
                </div>

                <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-500 text-black font-black uppercase tracking-wider text-sm hover:shadow-[0_0_30px_rgba(16,185,129,0.8)] transition flex items-center justify-center gap-2">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i> Confirm & Activate Plan Now
                </button>
            </div>

            <!-- Right: 15-Level Direct & ROI Income Rules Overview -->
            <div class="lg:col-span-6 p-5 rounded-2xl bg-emerald-950/40 border border-emerald-500/30 space-y-4">
                <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wider border-b border-emerald-500/20 pb-2">
                    15-LEVEL INCOME STRUCTURE (OFFICIAL poster rules)
                </h3>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-black/40 border border-emerald-500/20">
                        <span class="block text-emerald-400 font-bold uppercase text-[10px]">Direct Level Income (12%)</span>
                        <span class="block text-white font-black text-sm mt-1">Level 1: 5.0%</span>
                        <span class="block text-neutral-400 text-[11px]">Level 2 to 15: 0.5% per level</span>
                    </div>

                    <div class="p-3 rounded-xl bg-black/40 border border-emerald-500/20">
                        <span class="block text-emerald-400 font-bold uppercase text-[10px]">ROI Level Matching (26%)</span>
                        <span class="block text-white font-black text-sm mt-1">L1: 10% | L2: 3%</span>
                        <span class="block text-neutral-400 text-[11px]">L3 to 15: 1.0% per level (24 Mo)</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-emerald-900/30 border border-emerald-500/30 space-y-1.5 text-xs">
                    <span class="text-emerald-300 font-bold uppercase text-[10px] block">🏆 Business Rewards Tiers (Sep-Dec 2026)</span>
                    <p class="text-neutral-300 text-[11px]">₹5 Lakh Business = <strong>Mobile Phone</strong></p>
                    <p class="text-neutral-300 text-[11px]">₹10 Lakh Business = <strong>Laptop</strong></p>
                    <p class="text-neutral-300 text-[11px]">₹25 Lakh Business = <strong>EV Scooty</strong></p>
                    <p class="text-neutral-300 text-[11px]">₹1 Crore Business = <strong>Car DP ₹3 Lakh</strong></p>
                    <p class="text-neutral-300 text-[11px]">₹5 Crore Business = <strong>Tata Punch SUV</strong></p>
                    <p class="text-neutral-300 text-[11px]">₹10 Crore Business = <strong>Tata Sierra SUV</strong></p>
                </div>
            </div>
        </form>
    </div>

    <!-- Official Presentation Poster Cards Gallery -->
    <div class="space-y-4">
        <h2 class="text-lg font-black text-white uppercase tracking-tight flex items-center gap-2">
            <i data-lucide="image" class="w-5 h-5 text-emerald-400"></i> Official ZIVO PAY Plan Posters
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="rounded-2xl overflow-hidden border-2 border-emerald-500/40 bg-black/60 shadow-xl group hover:border-emerald-400 transition">
                <img src="{{ asset('images/posters/plan_poster_1.jpg') }}?v=1" alt="ZIVO PAY Poster 1" class="w-full h-auto object-cover group-hover:scale-105 transition duration-300">
                <div class="p-3 text-center border-t border-emerald-500/30">
                    <span class="text-xs font-bold text-emerald-400 uppercase">Daily Profit & Wallet Flexibility</span>
                </div>
            </div>

            <div class="rounded-2xl overflow-hidden border-2 border-emerald-500/40 bg-black/60 shadow-xl group hover:border-emerald-400 transition">
                <img src="{{ asset('images/posters/plan_poster_2.jpg') }}?v=1" alt="ZIVO PAY Poster 2" class="w-full h-auto object-cover group-hover:scale-105 transition duration-300">
                <div class="p-3 text-center border-t border-emerald-500/30">
                    <span class="text-xs font-bold text-emerald-400 uppercase">15-Level & ROI Income Chart</span>
                </div>
            </div>

            <div class="rounded-2xl overflow-hidden border-2 border-emerald-500/40 bg-black/60 shadow-xl group hover:border-emerald-400 transition">
                <img src="{{ asset('images/posters/plan_poster_3.jpg') }}?v=1" alt="ZIVO PAY Poster 3" class="w-full h-auto object-cover group-hover:scale-105 transition duration-300">
                <div class="p-3 text-center border-t border-emerald-500/30">
                    <span class="text-xs font-bold text-emerald-400 uppercase">Rewards Offer & Targets</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Investments Table -->
    <div class="p-6 rounded-3xl bg-slate-900/90 border-2 border-emerald-500/40 shadow-xl space-y-4">
        <h2 class="text-lg font-black text-white uppercase tracking-tight flex items-center gap-2">
            <i data-lucide="layers" class="w-5 h-5 text-emerald-400"></i> My Active Investment Packages
        </h2>

        @if($investments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-neutral-300">
                    <thead class="bg-emerald-950/80 text-emerald-400 uppercase text-[10px] font-bold border-b border-emerald-500/30">
                        <tr>
                            <th class="p-3.5">Plan Name</th>
                            <th class="p-3.5">Amount</th>
                            <th class="p-3.5">Daily ROI</th>
                            <th class="p-3.5">Days Active</th>
                            <th class="p-3.5">Total Paid</th>
                            <th class="p-3.5">Activated At</th>
                            <th class="p-3.5 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-500/10">
                        @foreach($investments as $inv)
                            <tr class="hover:bg-emerald-500/5 transition">
                                <td class="p-3.5 font-bold text-white">{{ $inv->plan_name }}</td>
                                <td class="p-3.5 font-mono font-bold text-emerald-400">₹{{ number_format($inv->amount, 2) }}</td>
                                <td class="p-3.5 font-semibold text-emerald-300">{{ $inv->daily_percentage }}% (₹{{ number_format($inv->daily_amount, 2) }}/day)</td>
                                <td class="p-3.5 font-bold">{{ $inv->days_completed }} / 730 Days</td>
                                <td class="p-3.5 font-mono text-white font-bold">₹{{ number_format($inv->total_returned, 2) }}</td>
                                <td class="p-3.5 text-neutral-400">{{ $inv->activated_at->format('d M Y, h:i A') }}</td>
                                <td class="p-3.5 text-right">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase {{ $inv->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-neutral-800 text-neutral-400' }}">
                                        {{ $inv->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-neutral-400 space-y-2">
                <i data-lucide="info" class="w-8 h-8 text-emerald-400/50 mx-auto"></i>
                <p>No active investment plans found. Choose a tier above to activate your financial plan!</p>
            </div>
        @endif
    </div>

</div>

<script>
function selectAmount(amt) {
    document.getElementById('investAmountInput').value = amt;
    calculateReturns();
}

function calculateReturns() {
    let amt = parseFloat(document.getElementById('investAmountInput').value) || 0;
    let rate = 0.15;
    let rateText = "0.15% Daily";

    if (amt >= 1000000) {
        rate = 0.30;
        rateText = "0.30% Daily (Diamond)";
    } else if (amt >= 500000) {
        rate = 0.25;
        rateText = "0.25% Daily (Platinum)";
    } else if (amt >= 100000) {
        rate = 0.20;
        rateText = "0.20% Daily (Gold)";
    } else {
        rate = 0.15;
        rateText = "0.15% Daily (Silver)";
    }

    let daily = amt * (rate / 100);
    let monthly = daily * 30;
    let total24Mo = (daily * 730) + amt;

    document.getElementById('calcRate').innerText = rateText;
    document.getElementById('calcDaily').innerText = '₹' + daily.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' / day';
    document.getElementById('calcMonthly').innerText = '₹' + monthly.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' / month';
    document.getElementById('calcTotal').innerText = '₹' + total24Mo.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}
document.addEventListener("DOMContentLoaded", calculateReturns);
</script>
@endsection
