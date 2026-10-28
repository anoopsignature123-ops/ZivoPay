@extends('user.layouts.app')

@section('title', 'Complete USD Payment · ZIVO PAY')

@section('content')
    <div class="mx-auto w-full space-y-6">
        <header class="rounded-2xl border border-sky-500/30 bg-slate-900/80 p-5 sm:p-6">
            <a href="{{ route('user.deposit.index') }}" class="text-xs font-bold text-sky-300 hover:text-sky-200">← Back to deposits</a>
            <h1 class="mt-3 text-2xl font-black text-white">Complete USD payment</h1>
            <p class="mt-1 text-sm text-slate-400">Send exactly <strong class="text-white">{{ number_format((float) $deposit->amount, 6) }} USDT</strong> for this order.</p>
        </header>

        @if (session('info'))
            <div class="rounded-xl border border-sky-500/30 bg-sky-500/10 px-4 py-3 text-sm text-sky-100">{{ session('info') }}</div>
        @endif

        <div class="grid items-stretch gap-5 lg:grid-cols-2">
            <section class="space-y-5 rounded-2xl border border-sky-500/30 bg-[#031a18] p-5 sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-sky-300">Order reference</p>
                        <p class="mt-1 break-all font-mono text-white">{{ $deposit->deposit_ref }}</p>
                    </div>
                </div>

                @if ($paymentUrl)
                    <a href="{{ $paymentUrl }}" target="_blank" rel="noopener noreferrer" class="mt-6 block rounded-xl bg-gradient-to-r from-sky-400 to-cyan-300 px-5 py-3 text-center font-black uppercase tracking-wide text-slate-950">Open iPaymentWallet checkout ↗</a>
                @endif

                @if ($qrImage)
                    <div class="mx-auto flex min-h-72 w-full items-center justify-center rounded-2xl border border-slate-700 bg-white p-4 shadow-xl" style="max-width: 390px;">
                        <img src="{{ $qrImage }}" alt="iPaymentWallet payment QR" class="h-60 w-60 object-contain" referrerpolicy="no-referrer">
                    </div>
                @elseif ($qrPageUrl)
                    <div class="mx-auto mt-6 w-full rounded-2xl border border-sky-500/25 bg-slate-950/80 p-5 text-center shadow-xl sm:p-7" style="max-width: 430px;">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-500/10 text-2xl text-sky-300">▦</div>
                        <h2 class="mt-4 text-lg font-bold text-white">Your payment QR is ready</h2>
                        <p class="mt-2 text-sm text-slate-400">Open the secure iPaymentWallet page to view and scan its QR code.</p>
                        <a href="{{ $qrPageUrl }}" target="_blank" rel="noopener noreferrer" class="mt-5 block rounded-xl bg-gradient-to-r from-sky-400 to-cyan-300 px-4 py-3 text-center text-sm font-black uppercase tracking-wide text-slate-950">View payment QR ↗</a>
                    </div>
                @endif

                @if ($paymentAddress)
                    <div class="rounded-xl border border-slate-700 bg-slate-950 p-4">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Payment address</p>
                            <button type="button" id="copyPaymentAddress" class="rounded-lg border border-slate-600 px-3 py-1.5 text-xs font-bold text-sky-200 hover:bg-slate-800">Copy address</button>
                        </div>
                        <p id="paymentAddress" class="mt-2 break-all font-mono text-sm text-white">{{ $paymentAddress }}</p>
                    </div>
                @endif

                <div class="mt-6 rounded-xl border border-sky-500/20 bg-slate-950/70 p-4 text-sm text-slate-300">
                    <div class="flex justify-between gap-3"><span>Send exactly</span><strong class="font-mono text-white">{{ number_format((float) $deposit->amount, 6) }} USDT</strong></div>
                    <div class="mt-3 flex justify-between gap-3 border-t border-slate-700 pt-3"><span>Network</span><strong class="text-sky-200">{{ $paymentNetwork }}</strong></div>
                    <div class="mt-3 flex justify-between gap-3 border-t border-slate-700 pt-3"><span>Wallet credit after verification</span><strong class="font-mono text-emerald-300">₹{{ number_format((float) $deposit->final_amount, 2) }} INR</strong></div>
                </div>
            </section>

            <aside class="space-y-5 rounded-2xl border border-emerald-500/30 bg-[#031a18] p-5 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-lg font-bold text-white">How to pay</h2>
                    <span class="rounded-full bg-amber-500/15 px-3 py-1 text-xs font-bold uppercase text-amber-200" id="paymentStatus">{{ $deposit->status }}</span>
                </div>
                <div class="rounded-xl border border-amber-400/30 bg-amber-500/10 p-4 text-sm text-amber-100">
                    <strong>Important:</strong> Send USDT only on {{ $paymentNetwork }}. Other networks or coins may not be recoverable.
                </div>
                <ol class="space-y-4 text-sm leading-relaxed text-slate-300">
                    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-xs font-bold text-emerald-300">1</span><span>Scan the QR code or copy the wallet address.</span></li>
                    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-xs font-bold text-emerald-300">2</span><span>Open your crypto wallet, choose Send, and select the {{ $paymentNetwork }} network.</span></li>
                    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-xs font-bold text-emerald-300">3</span><span>Send exactly <strong class="text-white">{{ number_format((float) $deposit->amount, 6) }} USDT</strong>. Check the address and network before confirming.</span></li>
                </ol>
                <div class="rounded-xl border border-slate-700 bg-slate-950/70 p-4">
                    <p class="text-sm font-semibold text-white">Payment status</p>
                    <p id="paymentStatusMessage" class="mt-1 text-sm text-slate-400">Waiting for payment confirmation. Status checks automatically.</p>
                    <button type="button" id="refreshPaymentStatus" class="mt-4 w-full rounded-xl border border-emerald-500/40 px-4 py-3 text-sm font-bold text-emerald-200 transition hover:bg-emerald-500/10">Refresh payment status</button>
                </div>
                <p class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4 text-xs leading-relaxed text-amber-100/80">After confirmation, the USD amount is converted at $1 = ₹{{ number_format((float) $deposit->conversion_rate, 2) }} and credited to your INR wallet. Do not send another payment while this order is pending.</p>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const copyButton = document.getElementById('copyPaymentAddress');
            const address = document.getElementById('paymentAddress');
            if (copyButton && address) {
                copyButton.addEventListener('click', async () => {
                    await navigator.clipboard.writeText(address.textContent.trim());
                    copyButton.textContent = 'Copied';
                    window.setTimeout(() => copyButton.textContent = 'Copy address', 1800);
                });
            }

            const statusUrl = {{ Illuminate\Support\Js::from(route('user.deposit.ipayment.status', $deposit)) }};
            const statusLabel = document.getElementById('paymentStatus');
            const statusMessage = document.getElementById('paymentStatusMessage');
            const refreshButton = document.getElementById('refreshPaymentStatus');
            let checking = false;

            const checkStatus = async () => {
                if (checking) return;
                checking = true;
                refreshButton.disabled = true;
                try {
                    const response = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
                    const result = await response.json();
                    statusLabel.textContent = result.status;
                    if (result.status === 'approved') {
                        statusLabel.className = 'rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-bold uppercase text-emerald-200';
                        statusMessage.textContent = 'Payment confirmed. Redirecting to your wallet…';
                        window.location.replace({{ Illuminate\Support\Js::from(route('user.deposit.index', ['deposit_ref' => $deposit->deposit_ref])) }});
                    } else if (result.status === 'rejected') {
                        statusLabel.className = 'rounded-full bg-rose-500/15 px-3 py-1 text-xs font-bold uppercase text-rose-200';
                        statusMessage.textContent = 'Payment was not confirmed. Contact support if funds were sent.';
                    } else {
                        statusMessage.textContent = 'Waiting for payment confirmation. Status checks automatically.';
                    }
                } catch (_) {
                    statusMessage.textContent = 'Status check is temporarily unavailable. Retrying automatically…';
                } finally {
                    checking = false;
                    refreshButton.disabled = false;
                }
            };

            refreshButton.addEventListener('click', checkStatus);
            checkStatus();
            window.setInterval(checkStatus, 10000);
        })();
    </script>
@endpush
