@extends('user.layouts.app')

@section('content')
    <style>
        /* Force Exactly 2 Cards Side-by-Side in 1 Row on Screens >= 768px */
        @media (min-width: 768px) {
            .grid-2-cards {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 1.5rem !important;
            }
        }
    </style>

    <div class="w-full space-y-6">

        <!-- Top Header Banner -->
        <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="pdf-num-badge">AF</span>
                    <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">NEXTGEN FOREX MEMBER
                        PORTAL</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient font-heading">ADD FUND / DEPOSIT WALLET</h1>
                <p class="text-xs text-neutral-300 mt-1">Deposit USDT (BEP20) into your Deposit Wallet to purchase NextGen
                    Forex Investment Packages.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('user.deposits.history') }}"
                    class="px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-black font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 transition flex items-center gap-2 shrink-0">
                    <i data-lucide="clock" class="w-4 h-4 text-black"></i> View Deposit History
                </a>
                <div
                    class="px-5 py-3 rounded-xl bg-bg border border-amber-500/40 text-amber-300 text-xs font-bold font-mono flex items-center gap-2 shadow-lg shrink-0">
                    <i data-lucide="wallet" class="w-4 h-4 text-amber-400"></i>
                    <span>Deposit Wallet: <strong
                            class="text-emerald-400 text-sm font-black">${{ number_format($user->deposit_wallet, 2) }}</strong></span>
                </div>
            </div>
            </div>

        
         
        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-500/20 border border-rose-500/50 text-rose-300 text-xs font-bold space-y-1">
                <div class="flex items-center gap-2 text-rose-400">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                    <span>Deposit Validation / Gateway Error:</span>
                </div>
                <ul class="list-disc list-inside pl-6 space-y-0.5 text-rose-200 text-[11px]">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Deposit Details & Form Split (FORCE BOTH CARDS SIDE-BY-SIDE IN 1 ROW via .grid-2-cards) -->
        <div class="grid grid-cols-1 grid-2-cards gap-6">

            <!-- LEFT CARD: Automated Gateway Deposit Information & Guide -->
            <div class="bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30 space-y-5 flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="flex items-center gap-3 border-b border-amber-500/20 pb-3">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 text-black font-black text-sm flex items-center justify-center shadow-md shrink-0">
                            🛡️
                        </div>
                        <div>
                            <h3 class="text-base font-black text-white font-heading">AUTOMATED USDT (BEP20) GATEWAY</h3>
                            <p class="text-xs text-neutral-400">Instant 24/7 Deposit Credit via Binance Smart Chain</p>
                        </div>
                    </div>

                    <!-- Step-by-Step Flow Instructions -->
                    <div class="p-4 rounded-xl bg-bg border border-amber-500/30 space-y-3">
                        <span class="text-xs font-bold text-amber-400 uppercase tracking-wider block border-b border-amber-500/20 pb-1.5">
                            ⚡ How Automated Deposit Works:
                        </span>
                        <ul class="space-y-2.5 text-xs text-neutral-300">
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">1</span>
                                <span>Enter your desired deposit amount ($10 to $50,000 USDT) and click <b>"Proceed to Payment Checkout"</b>.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">2</span>
                                <span>A dedicated <b>Checkout Payment Screen</b> will generate your secure QR Code & BEP20 Payment Address.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">3</span>
                                <span>Transfer USDT from any wallet app (Binance, Trust Wallet, MetaMask, etc.). Payment automatically verifies & credits your Deposit Wallet!</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div
                    class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-xs text-neutral-300 space-y-1 leading-relaxed mt-2">
                    <p class="font-bold text-amber-300">📌 Deposit Rules & Terms (PDF Terms Page 20):</p>
                    <ul class="list-disc list-inside space-y-1 text-neutral-300">
                        <li>Minimum deposit amount is <strong class="text-emerald-400">$10.00 USDT</strong>.</li>
                        <li>Network: <strong class="text-amber-300">USDT BEP20 (Binance Smart Chain)</strong> only.</li>
                        <li>Deposits are automatically verified & credited 24/7 to your Deposit Wallet.</li>
                    </ul>
                </div>
            </div>

            <!-- RIGHT CARD: Submit Deposit Form -->
            <div class="bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30 space-y-5 flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="flex items-center gap-3 border-b border-amber-500/20 pb-3">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 text-black font-black text-sm flex items-center justify-center shadow-md shrink-0">
                            📝
                        </div>
                        <div>
                            <h3 class="text-base font-black text-white font-heading">INITIATE DEPOSIT CHECKOUT</h3>
                            <p class="text-xs text-neutral-400">Enter amount to generate your secure BEP20 USDT payment session</p>
                        </div>
                    </div>

                    <form action="{{ route('user.deposits.store') }}" method="POST" class="space-y-4" id="depositSubmitForm">
                        @csrf

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-amber-400 uppercase tracking-wider">Deposit Amount ($ USDT)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-amber-400 font-bold">$</span>
                                <input type="number" step="0.01" min="10" max="50000" name="amount" value="{{ old('amount', 100) }}" placeholder="100.00"
                                    class="w-full pl-8 pr-4 py-3 rounded-xl bg-bg border @error('amount') border-rose-500 @else border-amber-500/40 @enderror text-white font-mono font-bold text-xs focus:outline-none focus:border-amber-400"
                                    required>
                            </div>
                            @error('amount')
                                <span class="text-rose-400 text-xs font-bold block mt-1">⚠️ {{ $message }}</span>
                            @enderror
                            <p class="text-[11px] text-neutral-400">Min Deposit: $10.00 | Max Deposit: $50,000.00</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-amber-400 uppercase tracking-wider">Payment Network</label>
                            <div class="p-3 rounded-xl bg-black border border-amber-500/40 text-xs font-bold text-emerald-400 flex items-center justify-between">
                                <span>USDT - Binance Smart Chain (BEP20)</span>
                                <span class="px-2 py-0.5 rounded bg-emerald-500/20 border border-emerald-500/40 text-[10px]">Automated Gateway</span>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-[11px] text-neutral-300 space-y-1">
                            <p class="font-bold text-amber-300">⚡ Automated Instant Checkout:</p>
                            <p>Clicking <strong>Proceed to Payment Checkout</strong> will generate a unique BEP20 wallet address and QR Code for instant payment verification.</p>
                        </div>
                    </form>
                </div>

                <div class="pt-4 border-t border-amber-500/20">
                    <button type="submit" form="depositSubmitForm"
                        class="w-full py-3.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-black font-black text-xs uppercase tracking-wider shadow-lg hover:scale-[1.01] transition flex items-center justify-center gap-2">
                        <i data-lucide="arrow-right-circle" class="w-4 h-4 text-black"></i> Proceed to Payment Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
