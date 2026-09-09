@extends('user.layouts.app')

@section('content')
    <div class="w-full space-y-6">

        <!-- Top Header Banner -->
        <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="pdf-num-badge">PAY</span>
                    <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">SECURE CHECKOUT</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient font-heading">COMPLETE YOUR PAYMENT</h1>
                <p class="text-xs text-neutral-300 mt-1">Transfer the exact amount to fund your deposit wallet instantly via iPaymentWallet gateway.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('user.deposits.history') }}"
                    class="px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-black font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 transition flex items-center gap-2 shrink-0">
                    <i data-lucide="clock" class="w-4 h-4 text-black"></i> View Deposit History
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i> {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- LEFT CARD: QR Code & Wallet Address -->
            <div class="bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30 space-y-5 flex flex-col justify-between">
                <div class="space-y-4">
                    
                    <!-- Important Warning Alert -->
                    <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-xs text-amber-300 flex items-start gap-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-400 shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="font-bold">Important:</strong> Only send USDT BEP-20 (Binance Smart Chain) to this address. Other cryptocurrencies cannot be recovered.
                        </div>
                    </div>

                    <div class="text-center space-y-2">
                        <span class="text-xs text-neutral-400 uppercase tracking-wider font-bold">Send Exactly</span>
                        <h2 class="text-3xl font-black text-gold-gradient">${{ number_format($deposit->amount, 2) }} USDT</h2>
                        <span class="inline-block px-3 py-1 rounded-full bg-black border border-amber-500/40 text-amber-300 text-xs font-mono font-bold">
                            Network: <b>BEP20 (BSC)</b>
                        </span>
                    </div>

                    <!-- QR Code Box -->
                    <div class="flex flex-col items-center justify-center p-4 bg-bg rounded-2xl border border-amber-500/30 text-center">
                        @php
                            $qrImage = "https://quickchart.io/qr?text=" . urlencode($deposit->wallet_address ?? '') . "&size=180&margin=1";
                        @endphp
                        <div class="p-2.5 bg-white rounded-2xl shadow-xl border-4 border-amber-400">
                            <img src="{{ $qrImage }}" alt="Payment QR Code" class="w-44 h-44 rounded-lg">
                        </div>
                    </div>

                    <!-- Payment Address Box -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-amber-400 uppercase tracking-wider block">Wallet Address:</label>
                        <div class="p-3 rounded-xl bg-black border border-amber-500/40 text-xs font-mono text-amber-300 break-all flex items-center justify-between gap-2 shadow-inner">
                            <span id="walletAddressText">{{ $deposit->wallet_address ?? 'Generating payment address...' }}</span>
                            <button onclick="copyAddress()" class="px-3 py-1.5 rounded-lg bg-amber-500 text-black font-black text-xs uppercase hover:bg-amber-400 transition shrink-0 shadow">
                                Copy
                            </button>
                        </div>
                    </div>

                    <!-- Timer & Status -->
                    <div class="flex items-center justify-between p-4 rounded-xl bg-bg border border-amber-500/20">
                        <div class="text-rose-400 font-bold text-xs flex items-center gap-1.5" id="paymentTimer">
                            <i data-lucide="clock" class="w-4 h-4 text-rose-400"></i> Expires in: <span id="countdown" class="font-mono text-sm font-black">29:59</span>
                        </div>
                        <div id="statusSection" class="text-amber-400 text-xs font-bold flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                            <span>Waiting for payment...</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- RIGHT CARD: Instructions & Status -->
            <div class="bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30 space-y-5 flex flex-col justify-between">
                <div class="space-y-5">
                    <div class="flex items-center gap-3 border-b border-amber-500/20 pb-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 text-black font-black text-sm flex items-center justify-center shadow-md shrink-0">
                            💡
                        </div>
                        <div>
                            <h3 class="text-base font-black text-white font-heading">HOW TO PAY</h3>
                            <p class="text-xs text-neutral-400">Follow these simple steps to complete your deposit</p>
                        </div>
                    </div>

                    <ul class="space-y-4 text-xs text-neutral-300 leading-relaxed">
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 font-bold flex items-center justify-center shrink-0">1</span>
                            <span>Scan the QR code using your crypto wallet app (Trust Wallet, Binance, MetaMask, etc.) or copy the BEP20 address.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 font-bold flex items-center justify-center shrink-0">2</span>
                            <span>Open your wallet, choose <b>"Send"</b> or <b>"Withdraw"</b>, and select network <b>BEP20 (Binance Smart Chain)</b>.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 font-bold flex items-center justify-center shrink-0">3</span>
                            <span>Input the exact payment amount: <strong class="text-emerald-400">${{ number_format($deposit->amount, 2) }} USDT</strong>.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 font-bold flex items-center justify-center shrink-0">4</span>
                            <span>Confirm the transfer in your wallet. This page will automatically update once the payment is verified.</span>
                        </li>
                    </ul>

                    <div class="p-4 rounded-xl bg-bg border border-amber-500/30 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-neutral-400">Deposit Reference:</span>
                            <span class="font-mono font-bold text-amber-300">{{ $deposit->deposit_ref }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-neutral-400">Gateway Provider:</span>
                            <span class="font-bold text-white">iPaymentWallet (Automated)</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <a href="{{ route('user.deposits.history') }}" class="w-full py-3 rounded-xl bg-bg border border-amber-500/40 text-amber-300 hover:bg-amber-500 hover:text-black font-bold text-xs uppercase tracking-wider transition flex items-center justify-center gap-2 shadow">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> View Deposit History
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Script for Copy, Timer, & Live Polling -->
    <script>
        function copyAddress() {
            const address = document.getElementById('walletAddressText').innerText.trim();
            if (address && address !== 'Generating payment address...') {
                navigator.clipboard.writeText(address);
                if (typeof showToast === 'function') {
                    showToast('Copied!', 'Wallet address copied to clipboard.', 'success');
                } else {
                    alert('Wallet address copied to clipboard!');
                }
            }
        }

        // Countdown Timer (30 minutes)
        let timeRemaining = 30 * 60;
        const countdownEl = document.getElementById('countdown');
        
        const timerInterval = setInterval(() => {
            let minutes = Math.floor(timeRemaining / 60);
            let seconds = timeRemaining % 60;
            if (countdownEl) {
                countdownEl.innerText = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
            
            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                const timerWrap = document.getElementById('paymentTimer');
                if (timerWrap) {
                    timerWrap.innerHTML = '<span class="text-rose-500 font-mono font-bold">Session Expired</span>';
                }
            }
            timeRemaining--;
        }, 1000);

        // Live AJAX Status Polling (Every 5 seconds)
        const checkStatusUrl = "{{ route('user.deposits.check-status', $deposit->id) }}";
        
        const statusInterval = setInterval(() => {
            fetch(checkStatusUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        clearInterval(statusInterval);
                        clearInterval(timerInterval);
                        
                        const statusSection = document.getElementById('statusSection');
                        if (statusSection) {
                            statusSection.innerHTML = `
                                <div class="text-emerald-400 font-bold flex items-center gap-1.5">
                                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-400"></i>
                                    <span>Payment Successful!</span>
                                </div>
                            `;
                        }
                        
                        setTimeout(() => {
                            window.location.href = "{{ route('user.deposits.history') }}";
                        }, 2500);
                    }
                })
                .catch(error => console.error('Error checking status:', error));
        }, 5000);
    </script>
@endsection
