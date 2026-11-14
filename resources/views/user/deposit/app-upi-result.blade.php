<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f4faf6">
    <title>{{ $deposit->status === 'approved' ? 'Payment successful' : 'Payment not completed' }} · ZIVO PAY</title>
    <style>
        * { box-sizing: border-box; }
        body { min-height: 100vh; min-height: 100svh; margin: 0; padding: max(18px, env(safe-area-inset-top)) 18px max(24px, env(safe-area-inset-bottom)); color: #172b25; background: radial-gradient(ellipse at 50% 42%, #fff 0, #f4faf6 65%); font: 15px/1.5 Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .shell { display: flex; width: min(100%, 430px); min-height: calc(100svh - max(18px, env(safe-area-inset-top)) - max(24px, env(safe-area-inset-bottom))); flex-direction: column; margin: 0 auto; }
        .topbar { display: flex; align-items: center; justify-content: space-between; }
        .brand { color: #08794d; font-size: 12px; font-weight: 850; letter-spacing: .14em; }
        .badge { padding: 6px 11px; border: 1px solid {{ $deposit->status === 'approved' ? '#b9e8ce' : '#f0d3d5' }}; border-radius: 999px; color: {{ $deposit->status === 'approved' ? '#08794d' : '#a32937' }}; background: {{ $deposit->status === 'approved' ? '#edf9f2' : '#fff4f4' }}; font-size: 11px; font-weight: 750; }
        .card { width: 100%; margin: auto 0; padding: 30px 24px 24px; border: 1px solid {{ $deposit->status === 'approved' ? '#d9eee2' : '#f1d8d8' }}; border-radius: 26px; background: rgba(255,255,255,.94); box-shadow: 0 22px 60px rgba(20, 72, 48, .10); text-align: center; animation: card-in .55s cubic-bezier(.2,.8,.2,1) both; }
        .icon-wrap { display: grid; width: 92px; height: 92px; margin: 0 auto 19px; place-items: center; border-radius: 50%; background: {{ $deposit->status === 'approved' ? '#e8f8ef' : '#fff0f1' }}; }
        .icon { display: grid; width: 66px; height: 66px; place-items: center; border-radius: 50%; color: #fff; background: {{ $deposit->status === 'approved' ? '#08ad62' : '#c33b48' }}; box-shadow: 0 9px 20px rgba(8,173,98,.22); }
        .icon svg { width: 34px; height: 34px; }
        .approved .icon { animation: pop-in .55s .12s cubic-bezier(.18,.89,.32,1.45) both; }
        h1 { margin: 0; color: #17352a; font-size: clamp(23px, 6vw, 28px); font-weight: 820; letter-spacing: -.035em; }
        p { margin: 8px 0 0; color: #61736b; font-size: 14px; }
        .amount { margin-top: 21px; color: #08794d; font-size: 36px; font-weight: 850; line-height: 1.1; }
        .amount small { color: #728079; font-size: 13px; font-weight: 650; }
        .credited { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 17px; padding: 12px; border-radius: 14px; color: #08794d; background: #edf9f2; font-size: 13px; font-weight: 700; }
        .credited svg { width: 17px; height: 17px; flex: 0 0 auto; }
        .reference { margin-top: 20px; padding-top: 16px; border-top: 1px solid #edf1ef; color: #7b8983; font-size: 11px; }
        .reference strong { display: block; margin-top: 4px; overflow-wrap: anywhere; color: #46574f; font: 600 12px ui-monospace, SFMono-Regular, Menlo, monospace; }
        .footnote { margin-top: 13px; color: #85928c; font-size: 11px; }
        @keyframes card-in { from { opacity: 0; transform: translateY(12px) scale(.985); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes pop-in { 0% { opacity: 0; transform: scale(.45) rotate(-18deg); } 75% { transform: scale(1.12) rotate(4deg); } 100% { opacity: 1; transform: scale(1) rotate(0); } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; scroll-behavior: auto !important; } }
    </style>
</head>
<body>
    <div class="shell">
      <header class="topbar">
        <div class="brand">ZIVO PAY</div>
        <div class="badge">{{ $deposit->status === 'approved' ? 'Payment successful' : 'Not completed' }}</div>
      </header>
      <main class="card {{ $deposit->status === 'approved' ? 'approved' : '' }}" role="status" aria-live="polite">
        <div class="icon-wrap"><div class="icon" aria-hidden="true">
            @if ($deposit->status === 'approved')
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg>
            @else
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            @endif
        </div></div>
        @if ($deposit->status === 'approved')
            <h1>Congratulations!</h1>
            <p>Your payment was completed successfully.</p>
            <div class="amount">₹{{ number_format((float) $deposit->amount_inr, 2) }} <small>INR</small></div>
            <div class="credited"><span aria-hidden="true">₹</span>Added to your Fund Wallet</div>
        @else
            <h1>Payment not completed</h1>
            <p>No funds were added. If money was deducted, contact ZIVO PAY support with this reference.</p>
        @endif
        <div class="reference">Payment reference<strong>{{ $deposit->deposit_ref }}</strong></div>
        @if ($deposit->status === 'approved')<div class="footnote">You can safely return to the app now.</div>@endif
      </main>
    </div>
    <script>
        (() => {
            const status = {{ Illuminate\Support\Js::from($deposit->status) }};
            const payload = JSON.stringify({ type: status === 'approved' ? 'payment_success' : 'payment_failed', deposit_id: {{ Illuminate\Support\Js::from($deposit->id) }}, deposit_ref: {{ Illuminate\Support\Js::from($deposit->deposit_ref) }}, status });
            if ({{ Illuminate\Support\Js::from($deposit->status === 'approved') }}) {
                if (window.ReactNativeWebView?.postMessage) window.ReactNativeWebView.postMessage(payload);
                if (window.webkit?.messageHandlers?.paymentStatus) window.webkit.messageHandlers.paymentStatus.postMessage(payload);
            }
        })();
    </script>
</body>
</html>
