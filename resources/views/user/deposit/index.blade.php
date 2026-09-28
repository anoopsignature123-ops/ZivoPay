@extends('user.layouts.app')

@section('title', 'Add Fund (Fund Wallet) - ZIVO PAY')

@section('content')
<div class="max-w-6xl mx-auto space-y-5 font-sans">
    <!-- Header Banner -->
    <div class="p-5 sm:p-6 rounded-2xl bg-[#042718] border border-emerald-500/40 shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-wider border border-emerald-500/30">
                    FUND WALLET RECHARGE
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-white uppercase tracking-tight font-heading">ADD FUND TO WALLET</h1>
            <p class="text-xs text-neutral-300 mt-0.5">Instant automated wallet recharge via UPI, QR Code, USDT (BEP20), or Merchant Gateway.</p>
        </div>

        <div class="px-4 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/40 text-right shrink-0">
            <span class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-wider block">Fund Wallet Balance</span>
            <span class="text-xl font-black text-white font-mono">₹{{ number_format($user->deposit_wallet, 2) }}</span>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-3.5 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-3.5 rounded-xl bg-rose-500/20 border border-rose-500/50 text-rose-300 text-xs font-bold space-y-1">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i data-lucide="x-circle" class="w-4 h-4 text-rose-400 shrink-0"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Main Add Fund 50%-50% Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">
        
        <!-- Left Column (50%): Add Fund Form -->
        <div class="p-5 sm:p-6 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-xl space-y-4">
            <div class="flex items-center gap-2.5 border-b border-emerald-500/20 pb-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold shrink-0 border border-emerald-500/30">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-base font-black text-white uppercase tracking-tight font-heading">ADD FUND FORM</h2>
                    <p class="text-[11px] text-neutral-400">Select payment gateway method and enter deposit details.</p>
                </div>
            </div>

            <form action="{{ route('user.deposit.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <!-- Deposit Amount -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-emerald-400 uppercase tracking-wider">
                            Add Fund Amount (₹) *
                        </label>
                        <span class="text-[11px] text-neutral-400">Min: <strong class="text-white">₹{{ number_format(config('gateway.min_deposit', 100)) }}</strong></span>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-400 font-bold font-mono text-sm">₹</span>
                        <input type="number" 
                            name="amount" 
                            id="depositAmount"
                            min="{{ config('gateway.min_deposit', 100) }}" 
                            max="{{ config('gateway.max_deposit', 500000) }}" 
                            step="1" 
                            value="{{ old('amount', 3000) }}" 
                            required 
                            placeholder="Enter amount..." 
                            class="w-full pl-8 pr-4 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-mono font-bold text-sm focus:outline-none focus:border-emerald-400 transition"
                            style="background-color: #01140c !important;">
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase tracking-wider mb-1.5">
                        Select Payment Gateway *
                    </label>
                    <div class="grid grid-cols-3 gap-2.5">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="UPI" checked onclick="togglePaymentDetails('UPI')" class="peer sr-only">
                            <div class="p-2.5 rounded-xl bg-[#01140c] border border-emerald-500/30 text-center peer-checked:border-emerald-400 peer-checked:bg-emerald-500/20 peer-checked:text-white transition">
                                <i data-lucide="qr-code" class="w-4 h-4 mx-auto mb-1 text-emerald-400"></i>
                                <span class="text-[11px] font-extrabold uppercase block">UPI / QR</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="USDT" onclick="togglePaymentDetails('USDT')" class="peer sr-only">
                            <div class="p-2.5 rounded-xl bg-[#01140c] border border-emerald-500/30 text-center peer-checked:border-emerald-400 peer-checked:bg-emerald-500/20 peer-checked:text-white transition">
                                <i data-lucide="coins" class="w-4 h-4 mx-auto mb-1 text-teal-400"></i>
                                <span class="text-[11px] font-extrabold uppercase block">USDT (Crypto)</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="Online Gateway" onclick="togglePaymentDetails('Online Gateway')" class="peer sr-only">
                            <div class="p-2.5 rounded-xl bg-[#01140c] border border-emerald-500/30 text-center peer-checked:border-emerald-400 peer-checked:bg-emerald-500/20 peer-checked:text-white transition">
                                <i data-lucide="credit-card" class="w-4 h-4 mx-auto mb-1 text-amber-400"></i>
                                <span class="text-[11px] font-extrabold uppercase block">Online API</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Dynamic Gateway Details Box -->
                <div id="upiDetailsBox" class="p-3.5 rounded-xl bg-[#01140c] border border-emerald-500/30 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-neutral-300 uppercase">Official Admin UPI ID</span>
                        <button type="button" onclick="copyText('{{ config('gateway.upi_id') }}', 'UPI ID')" class="text-[10px] px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30 font-bold border border-emerald-500/40 transition">
                            Copy UPI ID
                        </button>
                    </div>
                    <p class="text-xs font-mono font-bold text-emerald-300">{{ config('gateway.upi_id') }}</p>
                    <p class="text-[10px] text-neutral-400">Pay via GooglePay, PhonePe, or Paytm using UPI ID, then enter UTR number below.</p>
                </div>

                <div id="usdtDetailsBox" class="hidden p-3.5 rounded-xl bg-[#01140c] border border-teal-500/30 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-neutral-300 uppercase">USDT Wallet Address (BEP20)</span>
                        <button type="button" onclick="copyText('{{ config('gateway.usdt_wallet') }}', 'USDT Wallet')" class="text-[10px] px-2 py-0.5 rounded bg-teal-500/20 text-teal-300 hover:bg-teal-500/30 font-bold border border-teal-500/40 transition">
                            Copy Address
                        </button>
                    </div>
                    <p class="text-xs font-mono font-bold text-teal-300 break-all">{{ config('gateway.usdt_wallet') }}</p>
                    <p class="text-[10px] text-neutral-400">Network: BEP20 (Binance Smart Chain). Enter transaction Hash ID below.</p>
                </div>

                <div id="gatewayDetailsBox" class="hidden p-3.5 rounded-xl bg-[#01140c] border border-amber-500/30 space-y-1.5">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span class="text-[11px] font-bold text-amber-300 uppercase">Merchant API Key Active</span>
                    </div>
                    <p class="text-xs text-neutral-300 font-mono">Merchant ID: {{ config('gateway.merchant_id') }}</p>
                    <p class="text-[10px] text-neutral-400">Instant gateway processing enabled with API Secret validation.</p>
                </div>

                <!-- Transaction UTR / Reference -->
                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase tracking-wider mb-1">
                        Transaction UTR / Reference No. / Hash *
                    </label>
                    <input type="text" 
                        name="trx_hash" 
                        value="{{ old('trx_hash') }}" 
                        required 
                        placeholder="e.g. 123456789012 or 0x8392a..." 
                        class="w-full px-3.5 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-mono font-bold text-xs focus:outline-none focus:border-emerald-400 transition"
                        style="background-color: #01140c !important;">
                </div>

                <!-- Proof File Upload -->
                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase tracking-wider mb-1">
                        Payment Proof Screenshot (Optional)
                    </label>
                    <input type="file" 
                        name="proof_file" 
                        accept="image/*"
                        class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-neutral-300 text-xs font-bold file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-black file:bg-emerald-500 file:text-black hover:file:bg-emerald-400 transition cursor-pointer"
                        style="background-color: #01140c !important;">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-500 text-black font-black text-xs uppercase tracking-wider shadow-[0_0_15px_rgba(16,185,129,0.35)] hover:scale-[1.01] transition-all cursor-pointer flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4 text-black"></i>
                    SUBMIT ADD FUND REQUEST
                </button>
            </form>
        </div>

        <!-- Right Column (50%): Fund Wallet Daily Profit Slabs & Features Card -->
        <div class="p-5 sm:p-6 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-xl space-y-4">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-emerald-500/20 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold shrink-0 border border-emerald-500/30">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-black text-white uppercase tracking-tight font-heading">DAILY PROFIT SLABS</h2>
                        <p class="text-[11px] text-neutral-400">Wallet deposit balance auto-yields daily ROI profit.</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[10px] font-black uppercase">
                    AUTO PROFIT ⚡
                </span>
            </div>

            <!-- 4 Small Slabs Grid (2x2) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                
                <!-- Slab 1 -->
                <div class="p-3.5 rounded-xl bg-[#01140c] border border-emerald-500/30 space-y-1 hover:border-emerald-400 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-neutral-400 uppercase tracking-wider">SLAB 1</span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-black border border-emerald-500/30">
                            0.15% DAILY
                        </span>
                    </div>
                    <div class="text-xs font-black text-white font-mono">₹1,000 – ₹99,999</div>
                    <p class="text-[10px] text-neutral-400 font-sans">Daily 0.15% profit credited automatically.</p>
                </div>

                <!-- Slab 2 -->
                <div class="p-3.5 rounded-xl bg-[#01140c] border border-emerald-500/30 space-y-1 hover:border-emerald-400 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-neutral-400 uppercase tracking-wider">SLAB 2</span>
                        <span class="px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300 text-[10px] font-black border border-teal-500/30">
                            0.20% DAILY
                        </span>
                    </div>
                    <div class="text-xs font-black text-white font-mono">₹1,00,000 – ₹4,99,999</div>
                    <p class="text-[10px] text-neutral-400 font-sans">Daily 0.20% profit credited automatically.</p>
                </div>

                <!-- Slab 3 -->
                <div class="p-3.5 rounded-xl bg-[#01140c] border border-emerald-500/30 space-y-1 hover:border-emerald-400 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-neutral-400 uppercase tracking-wider">SLAB 3</span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-black border border-emerald-500/30">
                            0.25% DAILY
                        </span>
                    </div>
                    <div class="text-xs font-black text-white font-mono">₹5,00,000 – ₹9,99,999</div>
                    <p class="text-[10px] text-neutral-400 font-sans">Daily 0.25% profit credited automatically.</p>
                </div>

                <!-- Slab 4 -->
                <div class="p-3.5 rounded-xl bg-[#01140c] border border-emerald-500/30 space-y-1 hover:border-emerald-400 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-neutral-400 uppercase tracking-wider">SLAB 4 (MAX)</span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-400/20 text-emerald-300 text-[10px] font-black border border-emerald-400/40">
                            0.30% DAILY
                        </span>
                    </div>
                    <div class="text-xs font-black text-white font-mono">₹10,00,000 & Above</div>
                    <p class="text-[10px] text-neutral-400 font-sans">Maximum 0.30% daily yield credited.</p>
                </div>

            </div>

            <!-- Features & Flexibility Cards -->
            <div class="p-4 rounded-xl bg-[#01140c] border border-emerald-500/30 space-y-2.5">
                <div class="flex items-center gap-2 text-xs font-black text-emerald-400 uppercase tracking-wider">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i>
                    <span>Wallet Flexibility & Benefits</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-left">
                    <div class="p-2.5 rounded-lg bg-[#042718] border border-emerald-500/20">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-emerald-400 mb-1"></i>
                        <h4 class="text-[11px] font-bold text-white">24x7 Withdrawal</h4>
                        <p class="text-[9.5px] text-neutral-400">Profit withdraw facility anytime.</p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-[#042718] border border-emerald-500/20">
                        <i data-lucide="coins" class="w-3.5 h-3.5 text-teal-400 mb-1"></i>
                        <h4 class="text-[11px] font-bold text-white">Auto Compound</h4>
                        <p class="text-[9.5px] text-neutral-400">Daily yield on balance.</p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-[#042718] border border-emerald-500/20">
                        <i data-lucide="shopping-bag" class="w-3.5 h-3.5 text-amber-400 mb-1"></i>
                        <h4 class="text-[11px] font-bold text-white">Multi-Utility</h4>
                        <p class="text-[9.5px] text-neutral-400">Bank, Recharge, Mart.</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Bottom Section: MY ADD FUND REQUESTS HISTORY (Full Width Table) -->
    <div class="p-5 sm:p-6 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-emerald-500/20 pb-3 gap-2">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold shrink-0 border border-emerald-500/30">
                    <i data-lucide="history" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-white uppercase tracking-tight font-heading">MY ADD FUND REQUESTS HISTORY</h3>
                    <p class="text-[11px] text-neutral-400">Track status of all your deposit wallet recharge requests.</p>
                </div>
            </div>
            <span class="text-xs text-emerald-400 font-mono font-bold px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30">
                Total Requests: {{ $deposits->total() }}
            </span>
        </div>

        <!-- Official Filter Bar Component -->
        <div class="p-4 rounded-2xl bg-[#01140c] border border-emerald-500/30 shadow-xl">
            <form action="{{ route('user.deposit.index') }}" method="GET" class="zivo-filter-bar">
                <!-- FROM DATE -->
                <div class="zivo-filter-field-date">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">FROM DATE</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}"
                        class="w-full px-3 py-2 rounded-xl bg-[#042718] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
                </div>

                <!-- TO DATE -->
                <div class="zivo-filter-field-date">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">TO DATE</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}"
                        class="w-full px-3 py-2 rounded-xl bg-[#042718] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
                </div>

                <!-- STATUS -->
                <div class="zivo-filter-field-select">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">STATUS</label>
                    <select name="status" class="w-full px-3 py-2 rounded-xl bg-[#042718] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400 transition cursor-pointer">
                        <option value="">All Statuses</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- SEARCH REF / UTR / METHOD -->
                <div class="zivo-filter-field-search">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">SEARCH REF / UTR / METHOD</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 text-emerald-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ref ID, UTR Number, UPI..."
                            class="w-full pl-9 pr-3 py-2 rounded-xl bg-[#042718] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="zivo-filter-actions">
                    <button type="submit" class="py-2 px-5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-black font-black uppercase tracking-wider text-xs hover:shadow-[0_0_20px_rgba(16,185,129,0.7)] transition flex items-center justify-center gap-1.5 shadow cursor-pointer">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i> FILTER
                    </button>
                    <a href="{{ route('user.deposit.index') }}" class="py-2 px-3.5 rounded-xl bg-black/60 border border-emerald-500/30 text-neutral-300 font-bold text-xs hover:text-white hover:border-emerald-400 transition text-center shrink-0">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Requests Table -->
        <div class="overflow-x-auto rounded-xl border border-emerald-500/20">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#02180f] text-emerald-400 font-extrabold uppercase tracking-wider border-b border-emerald-500/30">
                    <tr>
                        <th class="px-4 py-3">REF CODE</th>
                        <th class="px-4 py-3">AMOUNT</th>
                        <th class="px-4 py-3">METHOD</th>
                        <th class="px-4 py-3">UTR / HASH</th>
                        <th class="px-4 py-3">STATUS</th>
                        <th class="px-4 py-3 text-right">DATE & TIME</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/10 text-neutral-200 font-medium bg-[#01140c]">
                    @forelse($deposits as $deposit)
                        <tr class="hover:bg-emerald-500/5 transition">
                            <td class="px-4 py-3 font-mono font-bold text-emerald-300">{{ $deposit->deposit_ref }}</td>
                            <td class="px-4 py-3 font-mono font-black text-white text-sm">₹{{ number_format($deposit->amount, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    {{ $deposit->payment_method }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono text-neutral-300 text-xs">{{ $deposit->trx_hash ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase 
                                    {{ $deposit->status === 'approved' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : ($deposit->status === 'pending' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40') }}">
                                    {{ $deposit->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="text-white font-bold">{{ $deposit->created_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-neutral-400 font-mono">{{ $deposit->created_at->format('h:i:s A') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-neutral-400 font-bold">
                                No Add Fund requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($deposits->hasPages())
            <div class="pt-2">
                {{ $deposits->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function togglePaymentDetails(method) {
    document.getElementById('upiDetailsBox').classList.add('hidden');
    document.getElementById('usdtDetailsBox').classList.add('hidden');
    document.getElementById('gatewayDetailsBox').classList.add('hidden');

    if (method === 'UPI') {
        document.getElementById('upiDetailsBox').classList.remove('hidden');
    } else if (method === 'USDT') {
        document.getElementById('usdtDetailsBox').classList.remove('hidden');
    } else if (method === 'Online Gateway') {
        document.getElementById('gatewayDetailsBox').classList.remove('hidden');
    }
}

function copyText(text, label) {
    navigator.clipboard.writeText(text).then(function() {
        alert(label + ' copied to clipboard:\n' + text);
    }).catch(function() {
        prompt('Copy ' + label + ':', text);
    });
}
</script>
@endsection
