@extends('user.layouts.app')

@section('title', 'Add Fund (Deposit Wallet) - ZIVO PAY')

@section('content')
<div class="space-y-6 font-sans">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.25)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[11px] font-black uppercase tracking-widest border border-emerald-500/40">
                FUND WALLET RECHARGE
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight mt-1">ADD FUND TO WALLET</h1>
            <p class="text-xs text-neutral-300 mt-1">Add funds to your Fund Wallet via UPI, QR Code, USDT (BEP20), or Merchant Gateway to activate packages & investments.</p>
        </div>

        <div class="px-5 py-3 rounded-2xl bg-[#02180f] border border-emerald-500/40 text-right shrink-0">
            <span class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-wider block">Fund Wallet Balance</span>
            <span class="text-2xl font-black text-white font-mono">₹{{ number_format($user->deposit_wallet, 2) }}</span>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/20 border border-rose-500/50 text-rose-300 text-xs font-bold space-y-1">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i data-lucide="x-circle" class="w-4 h-4 text-rose-400"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Main Add Fund Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Column: Add Fund Payment Gateway Form (7 cols) -->
        <div class="lg:col-span-7 p-6 sm:p-8 rounded-3xl bg-[#042718] border-2 border-emerald-500/40 shadow-2xl space-y-6">
            <div class="flex items-center gap-3 border-b border-emerald-500/20 pb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black text-white uppercase tracking-tight">ADD FUND FORM</h2>
                    <p class="text-xs text-neutral-400">Enter deposit amount, choose gateway method, and enter transaction details.</p>
                </div>
            </div>

            <form action="{{ route('user.deposit.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                
                <!-- Amount Input -->
                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase tracking-wider mb-1.5">
                        Deposit Amount (₹) * <span class="text-neutral-400 font-normal">(Min ₹{{ number_format(config('gateway.min_deposit', 100)) }})</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-400 font-bold font-mono text-sm">₹</span>
                        <input type="number" 
                            name="amount" 
                            id="depositAmount"
                            min="{{ config('gateway.min_deposit', 100) }}" 
                            max="{{ config('gateway.max_deposit', 500000) }}" 
                            step="1" 
                            value="{{ old('amount', 3000) }}" 
                            required 
                            placeholder="Enter amount to add..." 
                            class="w-full pl-8 pr-4 py-3 rounded-2xl bg-[#01140c] border border-emerald-500/40 text-white font-mono font-bold text-base focus:outline-none focus:border-emerald-400 transition"
                            style="background-color: #01140c !important;">
                    </div>
                </div>

                <!-- Payment Method Selection Tabs -->
                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2">
                        Select Payment Method *
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="UPI" checked onclick="togglePaymentDetails('UPI')" class="peer sr-only">
                            <div class="p-3.5 rounded-2xl bg-[#01140c] border border-emerald-500/30 text-center peer-checked:border-emerald-400 peer-checked:bg-emerald-500/20 peer-checked:text-white transition">
                                <i data-lucide="qr-code" class="w-5 h-5 mx-auto mb-1 text-emerald-400"></i>
                                <span class="text-xs font-extrabold uppercase block">UPI / QR</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="USDT" onclick="togglePaymentDetails('USDT')" class="peer sr-only">
                            <div class="p-3.5 rounded-2xl bg-[#01140c] border border-emerald-500/30 text-center peer-checked:border-emerald-400 peer-checked:bg-emerald-500/20 peer-checked:text-white transition">
                                <i data-lucide="coins" class="w-5 h-5 mx-auto mb-1 text-teal-400"></i>
                                <span class="text-xs font-extrabold uppercase block">USDT (Crypto)</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="Online Gateway" onclick="togglePaymentDetails('Online Gateway')" class="peer sr-only">
                            <div class="p-3.5 rounded-2xl bg-[#01140c] border border-emerald-500/30 text-center peer-checked:border-emerald-400 peer-checked:bg-emerald-500/20 peer-checked:text-white transition">
                                <i data-lucide="credit-card" class="w-5 h-5 mx-auto mb-1 text-amber-400"></i>
                                <span class="text-xs font-extrabold uppercase block">Online API</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Payment Details Box (Dynamic Based on Method) -->
                <div id="upiDetailsBox" class="p-4 rounded-2xl bg-[#01140c] border border-emerald-500/30 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-neutral-300 uppercase">Official Admin UPI ID</span>
                        <button type="button" onclick="copyText('{{ config('gateway.upi_id') }}', 'UPI ID')" class="text-[11px] px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30 font-bold border border-emerald-500/40 transition">
                            Copy UPI ID
                        </button>
                    </div>
                    <p class="text-sm font-mono font-bold text-emerald-300">{{ config('gateway.upi_id') }}</p>
                    <p class="text-[10px] text-neutral-400">Scan QR Code or pay via GooglePay, PhonePe, Paytm using UPI ID, then enter UTR number below.</p>
                </div>

                <div id="usdtDetailsBox" class="hidden p-4 rounded-2xl bg-[#01140c] border border-teal-500/30 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-neutral-300 uppercase">USDT Wallet Address (BEP20)</span>
                        <button type="button" onclick="copyText('{{ config('gateway.usdt_wallet') }}', 'USDT Wallet')" class="text-[11px] px-2.5 py-1 rounded bg-teal-500/20 text-teal-300 hover:bg-teal-500/30 font-bold border border-teal-500/40 transition">
                            Copy Address
                        </button>
                    </div>
                    <p class="text-xs font-mono font-bold text-teal-300 break-all">{{ config('gateway.usdt_wallet') }}</p>
                    <p class="text-[10px] text-neutral-400">Network: BEP20 (Binance Smart Chain). Enter transaction Hash ID below.</p>
                </div>

                <div id="gatewayDetailsBox" class="hidden p-4 rounded-2xl bg-[#01140c] border border-amber-500/30 space-y-2">
                    <div class="flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4 text-amber-400"></i>
                        <span class="text-xs font-bold text-amber-300 uppercase">Merchant API Key Active</span>
                    </div>
                    <p class="text-xs text-neutral-300 font-mono">Merchant ID: {{ config('gateway.merchant_id') }}</p>
                    <p class="text-[10px] text-neutral-400">Instant gateway processing enabled with API Secret validation.</p>
                </div>

                <!-- Transaction Reference / UTR Number -->
                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase tracking-wider mb-1.5">
                        Transaction UTR / Reference No. / Hash *
                    </label>
                    <input type="text" 
                        name="trx_hash" 
                        value="{{ old('trx_hash') }}" 
                        required 
                        placeholder="e.g. 123456789012 or 0x8392a..." 
                        class="w-full px-4 py-3 rounded-2xl bg-[#01140c] border border-emerald-500/40 text-white font-mono font-bold text-sm focus:outline-none focus:border-emerald-400 transition"
                        style="background-color: #01140c !important;">
                </div>

                <!-- Payment Screenshot Upload -->
                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase tracking-wider mb-1.5">
                        Payment Proof Screenshot (Optional) <span class="text-neutral-400 font-normal">(JPG, PNG, WEBP max 5MB)</span>
                    </label>
                    <input type="file" 
                        name="proof_file" 
                        accept="image/*"
                        class="w-full px-4 py-2.5 rounded-2xl bg-[#01140c] border border-emerald-500/40 text-neutral-300 text-xs font-bold file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-emerald-500 file:text-black hover:file:bg-emerald-400 transition cursor-pointer"
                        style="background-color: #01140c !important;">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-500 text-black font-black text-sm uppercase tracking-wider shadow-[0_0_20px_rgba(16,185,129,0.4)] hover:scale-[1.01] transition-all cursor-pointer flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-5 h-5 text-black"></i>
                    SUBMIT ADD FUND REQUEST
                </button>
            </form>
        </div>

        <!-- Right Column: Recent Deposit Requests (5 cols) -->
        <div class="lg:col-span-5 p-6 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-emerald-500/20 pb-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="history" class="w-5 h-5 text-emerald-400"></i>
                    <h3 class="text-base font-black text-white uppercase tracking-tight">MY ADD FUND REQUESTS</h3>
                </div>
                <span class="text-xs text-neutral-400 font-mono font-bold">{{ $deposits->total() }} Total</span>
            </div>

            <!-- Deposit History Table / List -->
            <div class="space-y-3 overflow-y-auto max-h-[550px] pr-1">
                @forelse($deposits as $deposit)
                    <div class="p-4 rounded-2xl bg-[#01140c] border border-emerald-500/20 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-emerald-300">{{ $deposit->deposit_ref }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase 
                                {{ $deposit->status === 'approved' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : ($deposit->status === 'pending' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40') }}">
                                {{ $deposit->status }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <div>
                                <span class="text-lg font-black text-white font-mono">₹{{ number_format($deposit->amount, 2) }}</span>
                                <span class="text-[10px] text-neutral-400 block font-sans">{{ $deposit->payment_method }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-neutral-400 font-mono block">{{ $deposit->created_at->format('d M, h:i A') }}</span>
                                @if($deposit->trx_hash)
                                    <span class="text-[10px] text-emerald-400 font-mono truncate max-w-[120px] block" title="{{ $deposit->trx_hash }}">UTR: {{ $deposit->trx_hash }}</span>
                                @endif
                            </div>
                        </div>

                        @if($deposit->admin_remark)
                            <div class="pt-1.5 border-t border-emerald-500/10 text-[10px] text-neutral-300">
                                <strong class="text-emerald-400">Note:</strong> {{ $deposit->admin_remark }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center text-neutral-400 font-bold text-xs bg-[#01140c] rounded-2xl border border-emerald-500/20">
                        No Add Fund requests found.
                    </div>
                @endforelse
            </div>

            <div class="pt-2">
                {{ $deposits->links() }}
            </div>
        </div>
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
