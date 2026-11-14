<!doctype html>
<html lang="en">
@php
    $hasQrImage = $qrUrl && filter_var($qrUrl, FILTER_VALIDATE_URL) && parse_url($qrUrl, PHP_URL_SCHEME) === 'https';
    $fullScreenCheckout = ! $hasQrImage && filled($checkoutUrl);
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f7faf9">
    <title>Scan to pay · ZIVO PAY</title>
    <style>
        * { box-sizing: border-box; }
        :root { color-scheme: light; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        body { min-height: 100vh; min-height: 100svh; margin: 0; color: #172b25; background: #f7faf9; }
        .page { display: flex; min-height: 100vh; min-height: 100svh; flex-direction: column; padding: max(18px, env(safe-area-inset-top)) 18px max(22px, env(safe-area-inset-bottom)); }
        .top { display: flex; align-items: center; justify-content: space-between; gap: 12px; width: min(100%, 520px); margin: 0 auto; }
        .brand { color: #08794d; font-size: 12px; font-weight: 850; letter-spacing: .11em; }
        .status { flex: 0 0 auto; padding: 6px 11px; border: 1px solid #ead9a4; border-radius: 999px; color: #8a5a00; background: #fff8df; font-size: 11px; font-weight: 800; text-transform: capitalize; }
        .status[data-status="approved"] { border-color: #b8e8ce; color: #08794d; background: #e9f8ef; }
        .status[data-status="rejected"] { border-color: #f1c2c6; color: #a21c2a; background: #fff0f1; }
        main { display: flex; width: min(100%, 520px); flex: 1; flex-direction: column; align-items: center; justify-content: center; gap: 14px; margin: 0 auto; padding: 22px 0; }
        h1 { margin: 0; color: #142b23; font-size: clamp(20px, 5vw, 25px); font-weight: 800; letter-spacing: -.03em; text-align: center; }
        .amount { color: #08794d; font-size: clamp(28px, 8vw, 38px); font-weight: 850; line-height: 1.1; text-align: center; }
        .amount small { color: #66766f; font-size: 14px; font-weight: 650; }
        .qr-panel { display: grid; width: min(100%, 430px); min-height: 280px; flex: 0 1 auto; place-items: center; overflow: hidden; padding: 12px; border: 1px solid #e4ece8; border-radius: 24px; background: #fff; box-shadow: 0 12px 34px rgba(19, 60, 43, .08); }
        .qr-image, .checkout { display: block; width: min(100%, 390px); height: min(64svh, 390px); min-height: 260px; border: 0; border-radius: 14px; background: #fff; object-fit: contain; }
        .checkout { width: 100%; height: min(60svh, 480px); min-height: 340px; overflow: hidden; }
        .message { min-height: 20px; margin: 0; color: #61736b; font-size: 13px; line-height: 1.45; text-align: center; }
        .open-link { display: inline-flex; min-height: 44px; align-items: center; justify-content: center; padding: 0 17px; border: 1px solid #b9dfce; border-radius: 13px; color: #08794d; background: #effaf4; font-size: 13px; font-weight: 750; text-decoration: none; }
        [hidden] { display: none !important; }
        .page--checkout { min-height: 100vh; min-height: 100svh; padding-top: max(14px, env(safe-area-inset-top)); padding-bottom: max(14px, env(safe-area-inset-bottom)); }
        .page--checkout .top { margin-bottom: 4px; }
        .page--checkout main { gap: 10px; padding: 8px 0; }
        .page--checkout .qr-panel { width: min(100%, 460px); min-height: 360px; padding: 8px; }
        .page--checkout .checkout { width: 100%; height: min(560px, calc(100svh - 210px)); min-height: 360px; border-radius: 14px; }
        .page--checkout .open-link { display: none; }
        @media (max-height: 620px) {
            .page { padding-top: max(12px, env(safe-area-inset-top)); padding-bottom: max(12px, env(safe-area-inset-bottom)); }
            main { gap: 9px; padding: 12px 0; }
            .qr-panel { min-height: 220px; padding: 8px; }
            .qr-image { height: min(55svh, 300px); min-height: 210px; }
            .checkout { height: min(50svh, 280px); min-height: 220px; }
            .page--checkout .checkout { height: max(340px, calc(100svh - 210px)); max-height: 440px; min-height: 340px; }
        }
    </style>
</head>
<body>
    <div class="page {{ $fullScreenCheckout ? 'page--checkout' : '' }}">
        <header class="top">
            <div class="brand">ZIVO PAY</div>
            <span class="status" id="paymentStatus" data-status="{{ $deposit->status }}">{{ str($deposit->status)->headline() }}</span>
        </header>

        <main>
            <h1>Scan to add funds</h1>
            <div class="amount">₹{{ number_format((float) $deposit->amount_inr, 2) }} <small>INR</small></div>

            <section class="qr-panel" id="paymentCheckout" aria-label="UPI payment QR code" @if ($deposit->status === 'approved') hidden @endif>
                @if ($hasQrImage)
                    <img id="gatewayQr" class="qr-image" src="{{ $qrUrl }}" alt="Scan this UPI QR code to pay" referrerpolicy="no-referrer">
                @elseif ($checkoutUrl)
                    <iframe class="checkout" src="{{ $checkoutUrl }}" title="Secure UPI payment QR" referrerpolicy="no-referrer" scrolling="no" allow="payment *"></iframe>
                @else
                    <p style="padding:20px;color:#64748b;text-align:center">Payment QR is unavailable. Please contact support.</p>
                @endif
            </section>

            @if (! $fullScreenCheckout && ! $hasQrImage && $checkoutUrl)
                <a id="openCheckout" class="open-link" href="{{ $checkoutUrl }}" target="_blank" rel="noopener noreferrer" @if ($deposit->status === 'approved') hidden @endif>Open payment page</a>
            @endif
            <p class="message" id="paymentMessage" role="status" aria-live="polite">{{ $deposit->status === 'approved' ? 'Payment confirmed. You can return to the app now.' : 'Waiting for payment confirmation…' }}</p>
        </main>
    </div>

    <script>
        (() => {
            const statusUrl = {{ Illuminate\Support\Js::from($statusUrl) }};
            const resultUrl = {{ Illuminate\Support\Js::from($resultUrl) }};
            const depositId = {{ Illuminate\Support\Js::from($deposit->id) }};
            const status = document.getElementById('paymentStatus');
            const message = document.getElementById('paymentMessage');
            const checkout = document.getElementById('paymentCheckout');
            let busy = false;

            window.addEventListener('message', (event) => {
                if (event.origin !== window.location.origin
                    || event.source !== document.querySelector('.checkout')?.contentWindow
                    || event.data?.type !== 'upi_returned'
                    || event.data?.deposit_id !== depositId) {
                    return;
                }
                poll();
            });

            const poll = async () => {
                if (busy) return;
                busy = true;
                try {
                    const response = await fetch(statusUrl, { headers: { Accept: 'application/json' } });
                    if (!response.ok) throw new Error('status check failed');
                    const result = await response.json();
                    status.textContent = result.status.replaceAll('_', ' ');
                    status.dataset.status = result.status;
                    if (result.status === 'approved') {
                        message.textContent = 'Payment confirmed. Opening your success page…';
                        window.location.replace(resultUrl);
                        return;
                    }
                    if (result.status === 'rejected') {
                        window.location.replace(resultUrl);
                        return;
                    }
                    message.textContent = 'Waiting for payment confirmation…';
                } catch (_) {
                    message.textContent = 'Checking payment status…';
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
