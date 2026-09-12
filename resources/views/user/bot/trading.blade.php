@extends('user.layouts.app')

@section('title', 'Live Trading Terminal & Bot Activation')

@section('content')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --font-heading: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --accent-gold: #f3ca52;
            --accent-emerald: #00e676;
        }

        /* Dex Trade Motion & Animation Engine */
        @keyframes goldShimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        @keyframes scanPass {
            0% { left: -100%; }
            100% { left: 200%; }
        }

        @keyframes pulseGoldGlow {
            0%, 100% {
                box-shadow: 0 0 20px rgba(243, 202, 82, 0.3), inset 0 1px 2px rgba(255, 255, 255, 0.2);
            }
            50% {
                box-shadow: 0 0 35px rgba(243, 202, 82, 0.6), 0 0 12px rgba(0, 230, 118, 0.4), inset 0 1px 3px rgba(255, 255, 255, 0.4);
            }
        }

        .ng-banner-title {
            position: relative;
            background: linear-gradient(180deg, #063824 0%, #021d12 50%, #010905 100%) !important;
            border: 2px solid rgba(243, 202, 82, 0.85) !important;
            border-radius: 24px !important;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.85), 0 0 30px rgba(243, 202, 82, 0.4) !important;
            overflow: hidden;
            animation: pulseGoldGlow 4s infinite ease-in-out;
        }

        .ng-banner-title::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(243, 202, 82, 0.15), transparent);
            animation: scanPass 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .text-gold-gradient-animated {
            background: linear-gradient(135deg, #ffffff 0%, #fff5c0 25%, #f3ca52 50%, #d4af37 75%, #aa771c 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: goldShimmer 6s infinite linear;
        }

        .bot-panel-animated {
            background: linear-gradient(180deg, #063824 0%, #021d12 50%, #010905 100%) !important;
            border: 2px solid rgba(243, 202, 82, 0.85) !important;
            border-radius: 20px !important;
            box-shadow: 0 0 25px rgba(243, 202, 82, 0.25), inset 0 1px 2px rgba(255, 255, 255, 0.2) !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        /* Pair Switcher Styling */
        .pair-switcher-scroll::-webkit-scrollbar {
            height: 4px;
        }
        .pair-switcher-scroll::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.6);
            border-radius: 9999px;
        }
        .pair-switcher-scroll::-webkit-scrollbar-thumb {
            background: #f3ca52;
            border-radius: 9999px;
        }

        .pair-btn-active {
            background: linear-gradient(90deg, #d4af37 0%, #fef08a 35%, #f3ca52 65%, #d4af37 100%) !important;
            background-size: 200% 100% !important;
            animation: goldShimmer 4s infinite linear;
            color: #000000 !important;
            font-weight: 900 !important;
            border: 1.5px solid #ffffff !important;
            box-shadow: 0 0 20px rgba(243, 202, 82, 0.8) !important;
            transform: scale(1.03);
            position: relative;
            z-index: 2;
        }
        .pair-btn-active * {
            color: #000000 !important;
            font-weight: 900 !important;
        }

        .pair-btn-inactive {
            background: rgba(6, 56, 36, 0.8) !important;
            border: 1.5px solid rgba(243, 202, 82, 0.5) !important;
            color: #f3ca52 !important;
            font-weight: 700 !important;
            transition: all 0.25s ease-in-out !important;
            position: relative;
            z-index: 1;
        }
        .pair-btn-inactive:hover {
            background: rgba(243, 202, 82, 0.25) !important;
            border-color: #f3ca52 !important;
            box-shadow: 0 0 16px rgba(243, 202, 82, 0.45) !important;
            transform: translateY(-2px) !important;
            z-index: 2;
        }

        .btn-start-bot {
            background: linear-gradient(90deg, #d4af37 0%, #f3ca52 35%, #fff5c0 50%, #f3ca52 65%, #aa771c 100%) !important;
            background-size: 200% 100% !important;
            animation: goldShimmer 4s infinite linear;
            color: #000000 !important;
            font-weight: 900 !important;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
            box-shadow: 0 4px 20px rgba(243, 202, 82, 0.6) !important;
            border: 1px solid #fff5c0 !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .btn-start-bot:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 0 35px rgba(243, 202, 82, 0.85) !important;
        }
    </style>

    <div class="w-full space-y-6">

        <!-- Top Header Banner (Dex Trade Deep Emerald & Gold Theme) -->
        <div class="ng-banner-title p-5 sm:p-7 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div class="space-y-1.5 w-full lg:w-auto">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-400/50 text-amber-300 text-[10px] font-black tracking-widest uppercase">LIVE ENGINE</span>
                    <span class="text-[10px] sm:text-[11px] text-amber-400 font-extrabold tracking-[2px] uppercase">TRADINGVIEW ENGINE</span>
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-gold-gradient-animated font-heading uppercase flex items-center gap-3">
                    <i class="fa-solid fa-display text-amber-400 animate-pulse"></i>
                    QUANT TRADING TERMINAL
                </h1>
                <p class="text-xs sm:text-sm text-neutral-200 max-w-2xl font-medium">
                    Monitor real-time crypto markets, switch live trading pairs, and launch your Dex Trade Autonomous Bot.
                </p>
            </div>

            <div class="w-full lg:w-auto flex items-center justify-start lg:justify-end shrink-0">
                <a href="{{ route('user.bot.index') }}"
                    class="px-3.5 py-2 sm:px-5 sm:py-3 rounded-xl bg-black/60 hover:bg-black text-amber-300 font-bold text-[11px] sm:text-xs border border-amber-400/60 hover:border-amber-400 shadow-lg transition flex items-center justify-center gap-1.5 shrink-0">
                    <i class="fa-solid fa-arrow-left text-amber-400 text-xs"></i>
                    <span>Back to Overview</span>
                </a>
            </div>
        </div>

        @if($user->status !== 'active')
            <div class="p-3.5 sm:p-4 rounded-xl bg-rose-500/20 border border-rose-500/80 text-rose-300 text-xs sm:text-sm font-bold flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-[0_0_20px_rgba(244,63,94,0.35)] text-left">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-rose-400 text-base sm:text-lg shrink-0 animate-pulse"></i>
                    <span>⚠️ Please activate your account first by purchasing an investment package before starting the Quant Trading BOT!</span>
                </div>
                <a href="{{ route('user.packages.index') }}" class="px-3.5 py-2 sm:px-4 sm:py-2 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-500 hover:from-amber-300 hover:to-yellow-400 text-black font-black text-[11px] sm:text-xs uppercase tracking-wider shrink-0 shadow transition whitespace-nowrap">
                    Activate Account
                </a>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 sm:p-4 rounded-xl bg-rose-500/20 border border-rose-500/80 text-rose-300 text-xs sm:text-sm font-bold flex items-center justify-between shadow-[0_0_20px_rgba(244,63,94,0.3)] text-left">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-rose-400 text-base sm:text-lg shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <a href="{{ route('user.packages.index') }}" class="px-3.5 py-2 sm:px-4 sm:py-2 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-500 text-black font-black text-[11px] sm:text-xs uppercase tracking-wider shrink-0 shadow whitespace-nowrap">
                    Activate Account
                </a>
            </div>
        @endif

        @if(session('success'))
            <div class="p-3.5 sm:p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/80 text-emerald-300 text-xs sm:text-sm font-bold flex items-center justify-between shadow-[0_0_20px_rgba(0,230,118,0.3)] text-left">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-base sm:text-lg shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="p-3.5 sm:p-4 rounded-xl bg-amber-500/20 border border-amber-500/80 text-amber-300 text-xs sm:text-sm font-bold flex items-center justify-between shadow-[0_0_20px_rgba(243,202,82,0.3)] text-left">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-info text-amber-400 text-base sm:text-lg shrink-0"></i>
                    <span>{{ session('info') }}</span>
                </div>
            </div>
        @endif

        <!-- Pair Selection & High-Definition Responsive TradingView Container -->
        <div class="bot-panel-animated p-5 sm:p-7 space-y-5">
            
            <!-- Pair Selector Controls Bar -->
            <div class="flex flex-col xl:flex-row items-start xl:items-center justify-between gap-4 border-b border-amber-500/30 pb-5">
                <div>
                    <h2 class="text-base sm:text-lg font-black text-amber-300 flex items-center gap-2.5">
                        <i class="fa-solid fa-sliders text-amber-400"></i>
                        Crypto Pair Selector
                    </h2>
                    <p class="text-xs text-neutral-300 mt-0.5">Select a cryptocurrency pair to update the live TradingView market chart.</p>
                </div>

                <!-- Touch-scrollable pair buttons on mobile -->
                <div class="flex flex-nowrap sm:flex-wrap overflow-x-auto pair-switcher-scroll w-full xl:w-auto gap-2.5 sm:gap-3.5 py-3 px-1.5 items-center" id="pairSelectorButtons">
                    <button type="button" onclick="switchPair('BTCUSDT', this)" 
                        class="pair-btn pair-btn-active shrink-0 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-black tracking-wide border flex items-center gap-2">
                        <i class="fa-brands fa-btc text-base"></i>
                        <span>BTC / USDT</span>
                    </button>
                    <button type="button" onclick="switchPair('ETHUSDT', this)" 
                        class="pair-btn pair-btn-inactive shrink-0 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold tracking-wide border flex items-center gap-2">
                        <i class="fa-brands fa-ethereum text-base"></i>
                        <span>ETH / USDT</span>
                    </button>
                    <button type="button" onclick="switchPair('SOLUSDT', this)" 
                        class="pair-btn pair-btn-inactive shrink-0 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold tracking-wide border flex items-center gap-2">
                        <i class="fa-solid fa-cube text-base"></i>
                        <span>SOL / USDT</span>
                    </button>
                    <button type="button" onclick="switchPair('BNBUSDT', this)" 
                        class="pair-btn pair-btn-inactive shrink-0 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold tracking-wide border flex items-center gap-2">
                        <i class="fa-solid fa-coins text-base"></i>
                        <span>BNB / USDT</span>
                    </button>
                    <button type="button" onclick="switchPair('XRPUSDT', this)" 
                        class="pair-btn pair-btn-inactive shrink-0 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold tracking-wide border flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-base"></i>
                        <span>XRP / USDT</span>
                    </button>
                </div>
            </div>

            <!-- Fully Mobile Responsive TradingView Chart Container -->
            <div id="chartContainerFrame" class="relative w-full rounded-2xl overflow-hidden border-2 border-amber-500/40 bg-black shadow-2xl" style="min-height: 450px;">
                <div id="tradingview_widget_container" class="w-full" style="height: 650px; min-height: 450px;"></div>
            </div>
        </div>

        <!-- Bot Activation Control Box -->
        <div class="bot-panel-animated w-full p-5 sm:p-7 flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden">
            <div class="space-y-2 text-left flex-1 min-w-0 w-full md:w-auto">
                <div class="flex flex-wrap items-center gap-2.5">
                    <i class="fa-solid fa-microchip text-amber-400 text-sm animate-pulse"></i>
                    <span class="text-xs font-black text-amber-400 tracking-[2px] uppercase">QUANT ENGINE CONTROL</span>
                    @if($user->is_bot_active)
                        <span class="px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/80 text-emerald-400 text-[11px] font-extrabold uppercase flex items-center gap-2 shadow-[0_0_12px_rgba(0,230,118,0.3)]">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            BOT ACTIVE
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-amber-500/20 border border-amber-400/80 text-amber-300 text-[11px] font-extrabold uppercase tracking-wider">
                            ⚡ PENDING ACTIVATION
                        </span>
                    @endif
                </div>
                <h3 class="text-lg sm:text-2xl font-black text-gold-gradient-animated font-heading tracking-tight">
                    {{ $user->is_bot_active ? 'Trading BOT is Active & Mining ROI 24/7' : 'Ready to Launch Quant Trading BOT?' }}
                </h3>
                <p class="text-xs sm:text-sm text-neutral-200 font-medium leading-relaxed">
                    @if($user->is_bot_active)
                        Activated on <strong class="text-amber-300 font-mono">{{ $user->bot_activated_at?->format('F d, Y \a\t H:i A') }}</strong>. Position sizing & daily ROI compounding running 24/7.
                    @else
                        Clicking <strong class="text-amber-300 font-bold">START BOT</strong> triggers one-time activation of your Dex Trade Quant Trading Engine to unlock daily ROI.
                    @endif
                </p>
            </div>

            <div class="shrink-0 w-full md:w-auto flex justify-center md:justify-end">
                @if($user->is_bot_active)
                    <button type="button" disabled
                        class="w-full sm:w-auto px-5 py-2.5 sm:px-7 sm:py-3.5 rounded-xl bg-emerald-500/20 border-2 border-emerald-500/80 text-emerald-300 font-black text-xs uppercase tracking-wider shadow-[0_0_20px_rgba(0,230,118,0.4)] flex items-center justify-center gap-2 cursor-not-allowed opacity-95 whitespace-nowrap">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-sm sm:text-base"></i>
                        <span>BOT ACTIVE & MINING 24/7</span>
                    </button>
                @elseif($user->status === 'active')
                    <form action="{{ route('user.bot.activate') }}" method="POST" onsubmit="return confirm('Are you sure you want to START the Trading BOT? This will initiate automated ROI mining.');" class="w-full md:w-auto">
                        @csrf
                        <button type="submit"
                            class="btn-start-bot w-full sm:w-auto px-6 py-2.5 sm:px-8 sm:py-3.5 rounded-xl text-black font-black text-xs sm:text-sm uppercase tracking-wider flex items-center justify-center gap-2 cursor-pointer whitespace-nowrap">
                            <i class="fa-solid fa-bolt text-black text-sm sm:text-base"></i>
                            <span>START BOT</span>
                        </button>
                    </form>
                @else
                    <button type="button" disabled
                        title="Account Activation Required: Purchase an investment package first to start BOT"
                        class="w-full sm:w-auto px-5 py-2.5 sm:px-8 sm:py-3.5 rounded-xl bg-rose-500/20 border-2 border-rose-500/60 text-rose-300 font-black text-xs sm:text-sm uppercase tracking-wider flex items-center justify-center gap-2 cursor-not-allowed opacity-85 shadow whitespace-nowrap">
                        <i class="fa-solid fa-lock text-rose-400 text-sm sm:text-base"></i>
                        <span>START BOT (DISABLED)</span>
                    </button>
                @endif
            </div>
        </div>

    </div>

    <!-- TradingView Embed Script -->
    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
    <script type="text/javascript">
        let tvWidget = null;

        function getResponsiveChartHeight() {
            return window.innerWidth < 640 ? 450 : 650;
        }

        function loadTradingViewChart(symbol) {
            const container = document.getElementById('tradingview_widget_container');
            const frame = document.getElementById('chartContainerFrame');
            if (!container) return;
            container.innerHTML = '';

            const height = getResponsiveChartHeight();
            if (frame) frame.style.minHeight = height + 'px';
            container.style.height = height + 'px';
            container.style.minHeight = height + 'px';

            if (typeof TradingView !== 'undefined') {
                tvWidget = new TradingView.widget({
                    "width": "100%",
                    "height": height,
                    "symbol": "BINANCE:" + symbol,
                    "interval": "D",
                    "timezone": "Etc/UTC",
                    "theme": "dark",
                    "style": "1",
                    "locale": "en",
                    "toolbar_bg": "#010905",
                    "enable_publishing": false,
                    "hide_side_toolbar": false,
                    "allow_symbol_change": true,
                    "container_id": "tradingview_widget_container"
                });
            }
        }

        function switchPair(symbol, btnElement) {
            const buttons = document.querySelectorAll('.pair-btn');
            buttons.forEach(btn => {
                btn.className = 'pair-btn pair-btn-inactive shrink-0 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold tracking-wide border flex items-center gap-2';
            });

            btnElement.className = 'pair-btn pair-btn-active shrink-0 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-black tracking-wide border flex items-center gap-2';

            loadTradingViewChart(symbol);
        }

        document.addEventListener("DOMContentLoaded", function() {
            loadTradingViewChart('BTCUSDT');
        });
    </script>
@endsection
