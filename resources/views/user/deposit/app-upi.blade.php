<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#031a18">
    <title>UPI payment · ZIVO PAY</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 24px; color: #e2e8f0; background: radial-gradient(ellipse at top, #064b39 0, #031a18 48%, #020b0a 100%); font: 15px/1.5 system-ui, sans-serif; }
        .page { width: min(100%, 1080px); margin: 0 auto; }
        .header, .card { border: 1px solid rgba(16, 185, 129, .35); border-radius: 20px; background: rgba(3, 26, 24, .88); box-shadow: 0 20px 60px rgba(0, 0, 0, .22); }
        .header { margin-bottom: 20px; padding: 22px 26px; }
        .eyebrow { color: #34d399; font-size: 12px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
        h1 { margin: 7px 0 2px; color: #fff; font-size: clamp(22px, 4vw, 30px); line-height: 1.2; }
        .subtle { margin: 0; color: #94a3b8; }
        .columns { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; align-items: stretch; }
        .card { padding: 22px; }
        .row { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .label { color: #34d399; font-size: 12px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .ref { overflow-wrap: anywhere; margin-top: 4px; color: #fff; font: 13px ui-monospace, monospace; }
        .badge { padding: 5px 11px; border-radius: 999px; color: #fde68a; background: rgba(245, 158, 11, .14); font-size: 11px; font-weight: 800; text-transform: uppercase; }
        .amount { margin: 19px 0 14px; color: #fff; font-size: 28px; font-weight: 850; }
        .qr-wrap { display: grid; min-height: 300px; place-items: center; overflow: hidden; border-radius: 16px; background: #fff; }
        .qr-image { width: min(100%, 290px); height: 290px; object-fit: contain; }
        .checkout { width: min(100%, 380px); height: 480px; border: 0; background: #fff; }
        .open-link { display: block; margin-top: 14px; padding: 12px 16px; border-radius: 12px; color: #032018; background: linear-gradient(90deg, #34d399, #2dd4bf); text-align: center; text-decoration: none; font-weight: 850; }
        .note { margin: 12px 0 0; color: #94a3b8; font-size: 12px; text-align: center; }
        .steps { display: grid; gap: 16px; margin: 20px 0; padding: 0; list-style: none; }
        .steps li { display: flex; gap: 12px; color: #cbd5e1; }
        .num { display: grid; width: 28px; height: 28px; flex: 0 0 28px; place-items: center; border-radius: 50%; color: #6ee7b7; background: rgba(16, 185, 129, .14); font-size: 12px; font-weight: 800; }
        .warning, .status-box { padding: 15px; border-radius: 13px; }
        .warning { border: 1px solid rgba(245, 158, 11, .28); color: #fde68a; background: rgba(245, 158, 11, .07); font-size: 13px; }
        .status-box { margin-top: 18px; border: 1px solid rgba(51, 65, 85, .8); background: rgba(2, 6, 23, .55); }
        .status-box p { margin: 5px 0 0; color: #94a3b8; font-size: 13px; }
        @media (max-width: 760px) { body { padding: 14px; } .columns { grid-template-columns: 1fr; gap: 14px; } .header, .card { border-radius: 16px; } .header, .card { padding: 18px; } .qr-wrap { min-height: 260px; } .qr-image { width: 250px; height: 250px; } .checkout { height: 450px; } }
    </style>
</head>
<body>
    <main class="page">
        <header class="header">
            <div class="eyebrow">ZIVO PAY · Secure UPI</div>
            <h1>Complete your payment</h1>
            <p class="subtle">Scan the QR code and pay exactly ₹{{ number_format((float) $deposit->amount_inr, 2) }}.</p>
        </header>

        <div class="columns">
            <section class="card">
                <div class="row">
                    <div><div class="label">Order reference</div><div class="ref">{{ $deposit->deposit_ref }}</div></div>
                    <span class="badge" id="paymentStatus">{{ $deposit->status }}</span>
                </div>
                <div class="amount">₹{{ number_format((float) $deposit->amount_inr, 2) }} <span class="subtle" style="font-size:14px;font-weight:600">INR</span></div>
                <div class="qr-wrap" id="paymentCheckout" @if ($deposit->status === 'approved') style="display:none" @endif>
                    @if ($qrUrl && filter_var($qrUrl, FILTER_VALIDATE_URL) && parse_url($qrUrl, PHP_URL_SCHEME) === 'https')
                        <img id="gatewayQr" class="qr-image" src="{{ $qrUrl }}" alt="UPIGateway payment QR">
                    @elseif ($checkoutUrl)
                        <iframe class="checkout" src="{{ $checkoutUrl }}" title="UPIGateway secure payment QR" referrerpolicy="no-referrer" allow="payment *"></iframe>
                    @else
                        <p style="padding:20px;color:#64748b;text-align:center">Payment QR is unavailable. Please contact support.</p>
                    @endif
                </div>
                <div id="paymentSuccess" class="status-box" role="status" aria-live="polite" @if ($deposit->status !== 'approved') style="display:none" @endif>
                    <strong style="color:#a7f3d0">Payment confirmed</strong>
                    <p>Your payment has been verified and added to your Fund Wallet. You can return to the app.</p>
                </div>
                @if ($checkoutUrl)
                    <a id="openCheckout" class="open-link" href="{{ $checkoutUrl }}" target="_blank" rel="noopener noreferrer" @if ($deposit->status === 'approved') style="display:none" @endif>Open secure UPI checkout ↗</a>
                @endif
                <p class="note" id="paymentMessage" role="status" aria-live="polite">{{ $deposit->status === 'approved' ? 'Payment confirmed. You can return to the app now.' : 'Waiting for gateway confirmation. This page checks automatically.' }}</p>
            </section>

            <aside class="card">
                <div class="eyebrow">Payment instructions</div>
                <h2 style="margin:7px 0 0;color:#fff;font-size:21px">How to pay</h2>
                <ol class="steps">
                    <li><span class="num">1</span><span>Open any UPI app and scan the QR code.</span></li>
                    <li><span class="num">2</span><span>Pay the exact amount of <strong style="color:#fff">₹{{ number_format((float) $deposit->amount_inr, 2) }}</strong>.</span></li>
                    <li><span class="num">3</span><span>Keep this page open while we verify the transaction.</span></li>
                </ol>
                <div class="warning">Do not pay again while this order is pending. Wallet funds are added only after gateway confirmation.</div>
                <div class="status-box"><strong style="color:#fff">Wallet credit after verification</strong><p>₹{{ number_format((float) $deposit->final_amount, 2) }} INR will be added to your Fund Wallet.</p></div>
            </aside>
        </div>
    </main>
    <script>
        (() => {
            const statusUrl = {{ Illuminate\Support\Js::from($statusUrl) }};
            const status = document.getElementById('paymentStatus');
            const message = document.getElementById('paymentMessage');
            const checkout = document.getElementById('paymentCheckout');
            const success = document.getElementById('paymentSuccess');
            const openCheckout = document.getElementById('openCheckout');
            let busy = false;
            let completionNotified = false;

            const showSuccess = (result) => {
                status.textContent = 'APPROVED';
                status.style.color = '#a7f3d0';
                checkout.style.display = 'none';
                success.style.display = 'block';
                if (openCheckout) openCheckout.style.display = 'none';
                message.textContent = 'Payment confirmed. You can return to the app now.';
                if (completionNotified) return;
                completionNotified = true;
                const payload = JSON.stringify({ type: 'payment_success', deposit_id: result.deposit_id, deposit_ref: result.deposit_ref, status: result.status });
                if (window.ReactNativeWebView?.postMessage) window.ReactNativeWebView.postMessage(payload);
                if (window.webkit?.messageHandlers?.paymentStatus) window.webkit.messageHandlers.paymentStatus.postMessage(payload);
            };

            window.addEventListener('message', (event) => {
                if (event.origin !== window.location.origin
                    || event.source !== document.querySelector('.checkout')?.contentWindow
                    || event.data?.type !== 'upi_returned'
                    || event.data?.deposit_id !== {{ Illuminate\Support\Js::from($deposit->id) }}) {
                    return;
                }

                poll();
            });

            if (status.textContent.trim().toLowerCase() === 'approved') {
                showSuccess({ deposit_id: {{ Illuminate\Support\Js::from($deposit->id) }}, deposit_ref: {{ Illuminate\Support\Js::from($deposit->deposit_ref) }}, status: 'approved' });
            }

            const poll = async () => {
                if (busy) return;
                busy = true;
                try {
                    const response = await fetch(statusUrl, { headers: { Accept: 'application/json' } });
                    if (!response.ok) throw new Error('status check failed');
                    const result = await response.json();
                    status.textContent = result.status;
                    if (result.status === 'approved') {
                        showSuccess(result);
                        return;
                    }
                    if (result.status === 'rejected') {
                        status.style.color = '#fda4af';
                        message.textContent = 'Gateway did not confirm this payment. Contact support if money was deducted.';
                        return;
                    }
                    message.textContent = 'Payment is still pending. Checking again automatically…';
                } catch (_) {
                    message.textContent = 'Status check unavailable. Retrying automatically…';
                } finally {
                    busy = false;
                }
                window.setTimeout(poll, 5000);
            };
            poll();
        })();
    </script>
</body>
</html>
