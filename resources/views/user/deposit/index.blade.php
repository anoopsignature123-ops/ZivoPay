@extends('user.layouts.app')

@section('title', 'Add Funds · ZIVO PAY')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">
        <header class="flex flex-col justify-between gap-4 rounded-2xl border border-emerald-500/30 bg-[#042718] p-5 shadow-xl sm:flex-row sm:items-center sm:p-6">
            <div>
                <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-300">Fund Wallet · Pay-in</span>
                <h1 class="mt-3 text-2xl font-black text-white">Choose how to add funds</h1>
                <p class="mt-1 text-sm text-neutral-400">Enter an amount, pay on the gateway QR, and your wallet updates after payment verification.</p>
            </div>
            <div class="rounded-xl border border-emerald-500/30 bg-[#01140c] px-5 py-3 sm:text-right">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-400">Fund wallet balance</span>
                <span class="font-mono text-2xl font-black text-white">₹{{ number_format((float) $user->deposit_wallet, 2) }}</span>
            </div>
        </header>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">{{ session('error') }}</div>
        @endif
        @if (session('info'))
            <div class="rounded-xl border border-sky-500/30 bg-sky-500/10 px-4 py-3 text-sm text-sky-100">{{ session('info') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">{{ $errors->first() }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-2xl border border-emerald-500/30 bg-[#042718] p-5 shadow-xl sm:p-6">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-500/15 text-xl text-emerald-300">▦</div>
                    <div>
                        <h2 class="font-bold text-white">Deposit INR · UPI</h2>
                        <p class="text-xs text-neutral-400">Dynamic UPI QR · Google Pay · PhonePe · Paytm</p>
                    </div>
                </div>
                @if ($gatewayEnabled)
                    <form id="upiDepositForm" action="{{ route('user.deposit.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="currency" value="INR">
                        <div>
                            <label for="depositAmount" class="mb-2 block text-xs font-bold uppercase tracking-wider text-emerald-300">Payment amount (INR)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 font-mono text-lg font-bold text-emerald-400">₹</span>
                                <input type="number" id="depositAmount" name="amount" min="{{ $minDeposit }}" max="{{ $maxDeposit }}" step="0.01" value="{{ old('currency') === 'INR' ? old('amount', 1000) : 1000 }}" required class="w-full rounded-xl border border-emerald-500/40 bg-[#01140c] py-3 pl-10 pr-4 font-mono text-lg font-bold text-white outline-none focus:border-emerald-300 focus:ring-2 focus:ring-emerald-400/20">
                            </div>
                        </div>
                        <div class="space-y-3 rounded-xl border border-emerald-500/20 bg-[#01140c] p-4 text-sm">
                            <div class="flex justify-between text-neutral-400"><span>You pay</span><span id="amountInr">₹1,000.00</span></div>
                            <div class="flex justify-between border-t border-emerald-500/15 pt-3 font-semibold text-white"><span>Wallet credit after success</span><span id="upiWalletCredit">₹1,000.00</span></div>
                        </div>
                        <button id="upiDepositSubmit" type="submit" class="w-full rounded-xl bg-gradient-to-r from-emerald-400 to-teal-300 px-5 py-3 font-black uppercase tracking-wider text-[#001b10] shadow-lg transition hover:brightness-110">Open UPI QR & Pay →</button>
                    </form>
                @else
                    <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 p-4 text-sm text-amber-200">UPIGateway is not enabled or configured. Contact your administrator.</div>
                @endif
            </section>

            <section class="rounded-2xl border border-sky-500/30 bg-slate-900/80 p-5 shadow-xl sm:p-6">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-500/15 text-lg font-black text-sky-300">$</div>
                        <div>
                            <h2 class="font-bold text-white">Deposit USD · iPaymentWallet</h2>
                            <p class="text-xs text-neutral-400">Scan the crypto QR; USD is converted to INR wallet credit</p>
                        </div>
                    </div>
                    <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider {{ $usdGatewayEnabled ? 'bg-emerald-500/15 text-emerald-200' : 'bg-amber-500/15 text-amber-200' }}">{{ $usdGatewayEnabled ? strtoupper(config('gateway.ipaymentwallet.mode')).' · READY' : 'SETUP PENDING' }}</span>
                </div>
                @if ($usdGatewayEnabled)
                    <form action="{{ route('user.deposit.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="currency" value="USD">
                        <label for="usdDepositAmount" class="block text-xs font-bold uppercase tracking-wider text-sky-300">Payment amount (USD)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-mono text-lg font-bold text-sky-400">$</span>
                        <input type="number" id="usdDepositAmount" name="amount" min="1" max="50000" step="0.01" value="{{ old('currency') === 'USD' ? old('amount', 10) : 10 }}" required class="w-full rounded-xl border border-sky-500/40 bg-slate-950/70 py-3 pl-10 pr-4 font-mono text-lg font-bold text-white outline-none focus:border-sky-300 focus:ring-2 focus:ring-sky-400/20">
                    </div>
                    <div class="rounded-xl border border-sky-500/20 bg-slate-950/70 p-4 text-sm text-slate-300">
                        <div class="flex justify-between"><span>Wallet credit</span><span id="usdWalletCredit" class="font-mono font-bold text-white">₹{{ number_format(10 * $conversionRate, 2) }}</span></div>
                        <p class="mt-3 border-t border-slate-700 pt-3 text-xs leading-relaxed text-slate-400">Pay the exact USD amount. At $1 = ₹{{ number_format($conversionRate, 2) }}, your wallet is credited in INR after automatic payment verification.</p>
                    </div>
                        <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-sky-400 to-cyan-300 px-5 py-3 font-black uppercase tracking-wider text-slate-950 shadow-lg transition hover:brightness-110">Create USD payment →</button>
                    </form>
                @else
                    <div class="space-y-4">
                        <label for="usdDepositAmount" class="block text-xs font-bold uppercase tracking-wider text-sky-300">Payment amount (USD)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-mono text-lg font-bold text-sky-400">$</span>
                            <input type="number" id="usdDepositAmount" value="10.00" disabled class="w-full cursor-not-allowed rounded-xl border border-slate-700 bg-slate-950/70 py-3 pl-10 pr-4 font-mono text-lg font-bold text-slate-400">
                        </div>
                        <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4 text-sm text-amber-100">
                            <p class="font-semibold">USD payment is not configured</p>
                            <p class="mt-2 text-xs leading-relaxed text-amber-100/70">Add the iPaymentWallet API key to <code>.env</code> and set <code>IPAYMENT_ENABLED=true</code>. Test mode reads the legacy <code>PAYMENT_GATEWAY_API_KEY</code>; Live mode needs <code>IPAYMENT_LIVE_API_KEY</code>. Your key is never shown here.</p>
                        </div>
                        <button type="button" disabled class="w-full cursor-not-allowed rounded-xl border border-slate-700 bg-slate-800 px-5 py-3 font-black uppercase tracking-wider text-slate-500">USD deposits unavailable</button>
                    </div>
                @endif
            </section>
        </div>

        <div class="rounded-xl border border-blue-500/20 bg-blue-500/5 p-4 text-xs leading-relaxed text-blue-100/80">Payments are verified with their gateway before wallet credit. If a payment remains pending, keep the reference and check the deposit history again shortly.</div>

        <section class="overflow-hidden rounded-2xl border border-emerald-500/25 bg-[#042718] shadow-xl">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-emerald-500/15 px-5 py-4">
                <div><h2 class="font-bold text-white">Deposit history</h2><p class="mt-1 text-xs text-neutral-400">Recent gateway and manual deposit records.</p></div>
                <span class="rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-mono text-emerald-300">{{ $deposits->total() }} records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-xs">
                    <thead class="bg-[#01140c] uppercase tracking-wider text-emerald-400"><tr><th class="px-5 py-3">Reference</th><th class="px-5 py-3">Method</th><th class="px-5 py-3">Wallet credit (INR)</th><th class="px-5 py-3">Gateway amount</th><th class="px-5 py-3">UTR</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Date</th></tr></thead>
                    <tbody class="divide-y divide-emerald-500/10 text-neutral-200">
                        @forelse ($deposits as $deposit)
                            <tr>
                                <td class="whitespace-nowrap px-5 py-4 font-mono text-emerald-200">{{ $deposit->deposit_ref }}</td>
                                <td class="px-5 py-4">{{ $deposit->payment_method }}</td>
                                <td class="whitespace-nowrap px-5 py-4 font-mono font-bold">₹{{ number_format((float) $deposit->final_amount, 2) }}</td>
                                <td class="whitespace-nowrap px-5 py-4 font-mono">{{ $deposit->payment_provider === 'ipaymentwallet' ? '$'.number_format((float) $deposit->amount, 2).' USD' : '₹'.number_format((float) ($deposit->amount_inr ?? $deposit->amount), 2) }}</td>
                                <td class="max-w-40 truncate px-5 py-4 font-mono text-neutral-400">{{ $deposit->trx_hash ?? '—' }}</td>
                                <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 font-bold uppercase {{ $deposit->status === 'approved' ? 'bg-emerald-500/15 text-emerald-300' : ($deposit->status === 'pending' ? 'bg-amber-500/15 text-amber-200' : 'bg-rose-500/15 text-rose-300') }}">{{ $deposit->status }}</span></td>
                                <td class="whitespace-nowrap px-5 py-4 text-neutral-400">{{ $deposit->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-10 text-center text-neutral-500">No deposits yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-emerald-500/15 px-5 py-4">{{ $deposits->withQueryString()->links() }}</div>
        </section>
    </div>

    @if (request()->query('deposit_ref'))
        <div id="deposit-status-check" data-url="{{ route('user.deposit.status', ['deposit_ref' => request()->query('deposit_ref')]) }}" data-return-url="{{ $depositReturnUrl ?? '' }}" class="rounded-xl border border-sky-500/30 bg-sky-500/10 px-4 py-3 text-sm text-sky-100" role="status" aria-live="polite">
            Payment received. Checking with UPIGateway… Keep this page open.
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        (() => {
            const amountInput = document.getElementById('depositAmount');
            if (amountInput) {
                const updatePreview = () => {
                    const amountInr = Number(amountInput.value) || 0;
                    const formattedAmount = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(amountInr);
                    document.getElementById('amountInr').textContent = formattedAmount;
                    document.getElementById('upiWalletCredit').textContent = formattedAmount;
                };
                amountInput.addEventListener('input', updatePreview);
                updatePreview();
            }

            const upiForm = document.getElementById('upiDepositForm');
            const upiSubmit = document.getElementById('upiDepositSubmit');
            if (upiForm && upiSubmit) {
                upiForm.addEventListener('submit', () => {
                    upiSubmit.disabled = true;
                    upiSubmit.textContent = 'Opening secure UPI QR…';
                    upiSubmit.classList.add('cursor-wait', 'opacity-70');
                });
            }

            const usdAmountInput = document.getElementById('usdDepositAmount');
            if (usdAmountInput) {
                const exchangeRate = {{ Illuminate\Support\Js::from($conversionRate) }};
                const updateUsdPreview = () => {
                    const amount = Number(usdAmountInput.value) || 0;
                    const credit = document.getElementById('usdWalletCredit');
                    if (credit) credit.textContent = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(amount * exchangeRate);
                };
                usdAmountInput.addEventListener('input', updateUsdPreview);
                updateUsdPreview();
            }

            const statusElement = document.getElementById('deposit-status-check');
            if (!statusElement) return;
            let checkingStatus = false;
            const checkStatus = async () => {
                if (checkingStatus) return;
                checkingStatus = true;
                try {
                    const response = await fetch(statusElement.dataset.url, { headers: { 'Accept': 'application/json' } });
                    const contentType = response.headers.get('content-type') || '';
                    if (response.redirected || !contentType.includes('application/json')) {
                        if (statusElement.dataset.returnUrl) {
                            window.location.replace(statusElement.dataset.returnUrl);
                            return;
                        }
                    }
                    if (!response.ok) {
                        statusElement.textContent = 'Payment status check is temporarily unavailable. Retrying automatically…';
                        window.setTimeout(checkStatus, 10000);
                        return;
                    }
                    const result = await response.json();
                    if (result.status === 'approved' || result.status === 'rejected') {
                        window.location.replace(statusElement.dataset.returnUrl);
                        return;
                    }
                    statusElement.textContent = 'Payment is still being confirmed. This page will update automatically.';
                } catch (_) {
                    statusElement.textContent = 'Connection interrupted. Retrying payment status check…';
                } finally {
                    checkingStatus = false;
                }
                window.setTimeout(checkStatus, 5000);
            };
            checkStatus();
        })();
    </script>
@endpush
