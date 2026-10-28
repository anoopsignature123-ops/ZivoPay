@extends('user.layouts.app')

@section('title', 'Complete UPI Payment · ZIVO PAY')

@section('content')
    <div class="mx-auto w-full space-y-6">
        <header class="rounded-2xl border border-emerald-500/25 bg-slate-900/80 p-5 sm:p-6">
            <a href="{{ route('user.deposit.index') }}" class="text-xs font-bold text-emerald-300 hover:text-emerald-200">← Back to deposits</a>
            <h1 class="mt-3 text-2xl font-black text-white">Complete UPI payment</h1>
            <p class="mt-1 text-sm text-slate-400">Scan the gateway QR and pay exactly <strong class="text-white">₹{{ number_format((float) $deposit->amount_inr, 2) }}</strong>.</p>
        </header>

        <div class="grid items-start gap-5 xl:grid-cols-2">
            <section class="space-y-4 rounded-2xl border border-emerald-500/30 bg-[#031a18] p-4 sm:p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-emerald-300">Order reference</p>
                        <p class="mt-1 break-all font-mono text-sm text-white">{{ $deposit->deposit_ref }}</p>
                    </div>
                    <span id="paymentStatus" class="rounded-full bg-amber-500/15 px-3 py-1 text-xs font-bold uppercase text-amber-200">{{ $deposit->status }}</span>
                </div>

                @if ($qrUrl && filter_var($qrUrl, FILTER_VALIDATE_URL) && parse_url($qrUrl, PHP_URL_SCHEME) === 'https')
                    <div class="flex justify-center rounded-2xl bg-white p-3 sm:p-4">
                        <img src="{{ $qrUrl }}" alt="UPIGateway payment QR" class="h-56 w-56 object-contain sm:h-64 sm:w-64" referrerpolicy="no-referrer">
                    </div>
                @elseif ($checkoutUrl)
                    <div class="mx-auto w-full overflow-hidden rounded-2xl border border-slate-700 bg-white shadow-xl" style="max-width: 390px;">
                        <iframe src="{{ $checkoutUrl }}" title="UPIGateway secure payment QR" class="block w-full bg-white" style="height: 500px; min-height: 500px; border: 0;" referrerpolicy="no-referrer" allow="payment *"></iframe>
                    </div>
                @endif

                @if ($checkoutUrl)
                    <a href="{{ $checkoutUrl }}" target="_blank" rel="noopener noreferrer" class="block rounded-xl bg-gradient-to-r from-emerald-400 to-teal-300 px-4 py-3 text-center text-sm font-black uppercase tracking-wide text-slate-950">Open secure UPI checkout ↗</a>
                @endif

                <div class="rounded-xl border border-emerald-500/20 bg-slate-950/70 p-3 text-sm text-slate-300">
                    <div class="flex justify-between gap-3"><span>Pay</span><strong class="font-mono text-white">₹{{ number_format((float) $deposit->amount_inr, 2) }} INR</strong></div>
                    <div class="mt-3 flex justify-between gap-3 border-t border-slate-700 pt-3"><span>Wallet credit after verification</span><strong class="font-mono text-emerald-300">₹{{ number_format((float) $deposit->final_amount, 2) }} INR</strong></div>
                </div>
                <p id="paymentMessage" class="text-sm text-slate-400" role="status" aria-live="polite">Checking payment status with UPIGateway automatically…</p>
            </section>

            <aside class="space-y-4 rounded-2xl border border-slate-700 bg-slate-900/70 p-4 sm:p-5">
                <h2 class="font-bold text-white">Payment steps</h2>
                <ol class="space-y-4 text-sm text-slate-300">
                    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-xs font-bold text-emerald-300">1</span><span>Scan the QR shown by UPIGateway using your UPI app.</span></li>
                    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-xs font-bold text-emerald-300">2</span><span>Pay the exact amount shown above.</span></li>
                    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-xs font-bold text-emerald-300">3</span><span>We check the gateway status and add funds after final confirmation.</span></li>
                </ol>
                <p class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4 text-xs leading-relaxed text-amber-100/80">Keep this page open while the gateway confirms payment. Do not pay again while this transaction is pending.</p>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const statusUrl = {{ Illuminate\Support\Js::from(route('user.deposit.status', ['deposit_ref' => $deposit->deposit_ref])) }};
            const statusLabel = document.getElementById('paymentStatus');
            const message = document.getElementById('paymentMessage');
            let checking = false;

            const checkStatus = async () => {
                if (checking) return;
                checking = true;
                try {
                    const response = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
                    if (!response.ok) throw new Error('Status request failed');
                    const result = await response.json();
                    statusLabel.textContent = result.status;
                    if (result.status === 'approved') {
                        message.textContent = 'Payment verified. Redirecting to your wallet…';
                        window.location.replace({{ Illuminate\Support\Js::from(route('user.deposit.index', ['deposit_ref' => $deposit->deposit_ref])) }});
                        return;
                    }
                    if (result.status === 'rejected') {
                        message.textContent = 'Gateway did not confirm this payment. Contact support if money was deducted; please do not pay again.';
                        statusLabel.className = 'rounded-full bg-rose-500/15 px-3 py-1 text-xs font-bold uppercase text-rose-200';
                        return;
                    }
                    message.textContent = 'Payment is still pending gateway confirmation. Checking again automatically…';
                } catch (_) {
                    message.textContent = 'Could not reach the status service. Retrying automatically…';
                } finally {
                    checking = false;
                }
                window.setTimeout(checkStatus, 5000);
            };

            checkStatus();
        })();
    </script>
@endpush
