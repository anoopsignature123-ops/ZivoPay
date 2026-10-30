<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#031a18">
    <title>Confirming payment · ZIVO PAY</title>
    <style>
        * { box-sizing: border-box; }
        body { display: grid; min-height: 100vh; margin: 0; padding: 24px; place-items: center; color: #e2e8f0; background: radial-gradient(ellipse at top, #064b39 0, #031a18 48%, #020b0a 100%); font: 15px/1.5 system-ui, sans-serif; }
        .card { width: min(100%, 460px); padding: 36px 28px; border: 1px solid rgba(16, 185, 129, .45); border-radius: 22px; background: rgba(3, 26, 24, .92); box-shadow: 0 24px 70px rgba(0, 0, 0, .35); text-align: center; }
        .spinner { width: 54px; height: 54px; margin: 0 auto 20px; border: 4px solid rgba(52, 211, 153, .2); border-top-color: #34d399; border-radius: 50%; animation: spin .8s linear infinite; }
        h1 { margin: 0; color: #fff; font-size: 23px; }
        p { margin: 10px 0 0; color: #94a3b8; }
        .reference { margin-top: 22px; overflow-wrap: anywhere; color: #6ee7b7; font: 12px ui-monospace, monospace; }
        .failed .spinner { display: none; }
        .failed h1 { color: #fda4af; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (prefers-reduced-motion: reduce) { .spinner { animation-duration: 2s; } }
    </style>
</head>
<body>
    <main class="card" id="paymentCard" aria-live="polite">
        <div class="spinner" aria-hidden="true"></div>
        <h1 id="paymentTitle">Confirming your payment</h1>
        <p id="paymentMessage">Payment received. We are waiting for UPIGateway’s final confirmation. Please keep this page open.</p>
        <div class="reference">Order: {{ $deposit->deposit_ref }}</div>
    </main>
    <script>
        (() => {
            const statusUrl = {{ Illuminate\Support\Js::from($statusUrl) }};
            const successUrl = {{ Illuminate\Support\Js::from($successUrl) }};
            const appReturn = {{ Illuminate\Support\Js::from($appReturn ?? false) }};
            const card = document.getElementById('paymentCard');
            const title = document.getElementById('paymentTitle');
            const message = document.getElementById('paymentMessage');
            let checking = false;

            const checkStatus = async () => {
                if (checking) return;
                checking = true;
                try {
                    const response = await fetch(statusUrl, { headers: { Accept: 'application/json' } });
                    if (!response.ok) throw new Error('Status check failed');
                    const result = await response.json();
                    if (result.status === 'approved') {
                        title.textContent = 'Payment successful';
                        message.textContent = appReturn
                            ? 'Your payment is confirmed. You can return to the app.'
                            : 'Your payment is confirmed. Redirecting…';
                        if (appReturn) {
                            const payload = JSON.stringify({ type: 'payment_success', deposit_id: result.deposit_id, deposit_ref: result.deposit_ref, status: result.status });
                            if (window.ReactNativeWebView?.postMessage) window.ReactNativeWebView.postMessage(payload);
                            if (window.webkit?.messageHandlers?.paymentStatus) window.webkit.messageHandlers.paymentStatus.postMessage(payload);
                        } else {
                            window.location.replace(successUrl);
                        }
                        return;
                    }
                    if (result.status === 'rejected') {
                        card.classList.add('failed');
                        title.textContent = 'Payment was not confirmed';
                        message.textContent = 'No funds were added. If money was deducted, contact support with this order reference.';
                        return;
                    }
                    message.textContent = 'Payment received. Waiting for UPIGateway’s final confirmation…';
                } catch (_) {
                    message.textContent = 'Checking payment status. We will retry automatically…';
                } finally {
                    checking = false;
                }
                window.setTimeout(checkStatus, 4000);
            };

            checkStatus();
        })();
    </script>
</body>
</html>
