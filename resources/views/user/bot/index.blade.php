@extends('user.layouts.app')

@section('title', 'Trading BOT Overview')

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

        @keyframes floatIcon {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
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

        .bot-card-animated {
            background: linear-gradient(180deg, #063824 0%, #021d12 50%, #010905 100%) !important;
            border: 2px solid rgba(243, 202, 82, 0.85) !important;
            border-radius: 20px !important;
            box-shadow: 0 0 25px rgba(243, 202, 82, 0.25), inset 0 1px 2px rgba(255, 255, 255, 0.2) !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            position: relative;
            overflow: hidden;
        }

        .bot-card-animated:hover {
            transform: translateY(-5px) scale(1.02) !important;
            border-color: #fff5c0 !important;
            box-shadow: 0 0 40px rgba(243, 202, 82, 0.55), inset 0 1px 3px rgba(255, 255, 255, 0.35) !important;
        }

        .icon-circle-gold {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(243, 202, 82, 0.25) 0%, rgba(212, 175, 55, 0.1) 100%);
            border: 1.5px solid rgba(243, 202, 82, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 15px rgba(243, 202, 82, 0.3);
            animation: floatIcon 3.5s ease-in-out infinite;
        }

        .btn-gold-action {
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

        .btn-gold-action:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 0 35px rgba(243, 202, 82, 0.85) !important;
        }

        /* Force Exactly 3 Cards in 1 Row on screens >= 640px */
        @media (min-width: 640px) {
            .bot-cards-row {
                display: grid !important;
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                gap: 1.25rem !important;
            }
        }
    </style>

    <div class="w-full space-y-6">

        <!-- Top Header Banner (Dex Trade Deep Emerald & Gold Luxury Theme) -->
        <div class="ng-banner-title p-5 sm:p-7 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div class="space-y-1.5 w-full lg:w-auto">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-400/50 text-amber-300 text-[10px] font-black tracking-widest uppercase">BOT OVERVIEW</span>
                    <span class="text-[10px] sm:text-[11px] text-amber-400 font-extrabold tracking-[2px] uppercase">DEX TRADE QUANT SYSTEM</span>
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-gold-gradient-animated font-heading uppercase flex items-center gap-3">
                    <i class="fa-solid fa-robot text-amber-400 animate-pulse"></i>
                    QUANT TRADING BOT SYSTEM
                </h1>
                <p class="text-xs sm:text-sm text-neutral-200 max-w-2xl font-medium">
                    Activate Dex Trade Autonomous Quant Bot to execute high-frequency crypto & forex trading and compound your daily ROI.
                </p>
            </div>

            <div class="w-full lg:w-auto flex items-center justify-start lg:justify-end shrink-0">
                @if($user->is_bot_active)
                    <div class="w-full sm:w-auto px-5 py-3 rounded-xl bg-emerald-500/20 border border-emerald-500/80 text-emerald-300 text-xs font-black flex items-center justify-center gap-2.5 shadow-[0_0_20px_rgba(0,230,118,0.4)] animate-pulse">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                        </span>
                        <span>BOT ACTIVE (Since {{ $user->bot_activated_at?->format('M d, Y H:i') }})</span>
                    </div>
                @else
                    <div class="w-full sm:w-auto px-5 py-3 rounded-xl bg-amber-500/20 border border-amber-400/80 text-amber-300 text-xs font-black flex items-center justify-center gap-2.5 shadow-[0_0_20px_rgba(243,202,82,0.4)]">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                        </span>
                        <span>STATUS: READY FOR ACTIVATION</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- 3 High-Tech Animated Feature Cards -->
        <div class="grid grid-cols-1 bot-cards-row gap-4 sm:gap-5">

            <!-- Card 1 -->
            <div class="bot-card-animated p-5 sm:p-6 flex flex-col justify-between">
                <div>
                    <div class="icon-circle-gold mb-4">
                        <i class="fa-solid fa-microchip text-xl text-amber-400"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-amber-300 mb-2">High-Frequency Quant Engine</h3>
                    <p class="text-xs text-neutral-200 leading-relaxed font-medium">
                        Our Quant Bot continuously scans sub-second liquidity orderbooks across Binance, OKX, and Bybit to capture arbitrage opportunities.
                    </p>
                </div>
                <div class="mt-5 pt-3 border-t border-amber-500/30 text-[11px] text-amber-300 font-mono font-bold flex items-center gap-2">
                    <i class="fa-solid fa-bolt text-amber-400 animate-pulse"></i>
                    <span>Sub-millisecond Execution</span>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bot-card-animated p-5 sm:p-6 flex flex-col justify-between">
                <div>
                    <div class="icon-circle-gold mb-4" style="animation-delay: 0.5s;">
                        <i class="fa-solid fa-chart-line text-xl text-amber-400"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-amber-300 mb-2">Automated Daily ROI Mining</h3>
                    <p class="text-xs text-neutral-200 leading-relaxed font-medium">
                        Once triggered, the Bot manages position sizing and risk profiles automatically, compounding your daily yield 24/7.
                    </p>
                </div>
                <div class="mt-5 pt-3 border-t border-amber-500/30 text-[11px] text-emerald-400 font-mono font-bold flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-emerald-400 animate-spin-slow"></i>
                    <span>24/7 Yield Compounding</span>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bot-card-animated p-5 sm:p-6 flex flex-col justify-between">
                <div>
                    <div class="icon-circle-gold mb-4" style="animation-delay: 1s;">
                        <i class="fa-solid fa-shield-halved text-xl text-amber-400"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-amber-300 mb-2">One-Time Lifetime Activation</h3>
                    <p class="text-xs text-neutral-200 leading-relaxed font-medium">
                        Activation requires only one simple click on our Live Trading Terminal. No re-subscriptions or manual setups.
                    </p>
                </div>
                <div class="mt-5 pt-3 border-t border-amber-500/30 text-[11px] text-amber-300 font-mono font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-amber-300"></i>
                    <span>Single Click Setup</span>
                </div>
            </div>

        </div>

        <!-- Full Width Luxury Gold CTA Card -->
        <div class="bot-card-animated w-full p-5 sm:p-7 flex flex-col md:flex-row items-center justify-between gap-5">
            <div class="space-y-1.5 text-center md:text-left w-full md:w-auto">
                <div class="flex items-center justify-center md:justify-start gap-2">
                    <i class="fa-solid fa-display text-amber-400"></i>
                    <span class="text-[10px] font-black text-amber-400 tracking-[2px] uppercase">LIVE TERMINAL CONTROL</span>
                </div>
                <h2 class="text-base sm:text-xl font-black text-amber-300 tracking-wide">
                    Ready to Launch Live Trading Terminal?
                </h2>
                <p class="text-xs text-neutral-200 font-medium">
                    Open the interactive TradingView market terminal, monitor real-time crypto charts, select trading pairs, and activate your Quant Bot.
                </p>
            </div>
            <div class="shrink-0 w-full md:w-auto flex justify-center">
                <a href="{{ route('user.bot.trading') }}"
                    class="btn-gold-action w-full sm:w-auto px-8 py-3.5 rounded-xl text-black font-black text-xs sm:text-sm uppercase tracking-wider flex items-center justify-center gap-2.5">
                    <i class="fa-solid fa-circle-play text-black text-base"></i>
                    <span>{{ $user->is_bot_active ? 'View Live Trading Terminal' : 'Start BOT & Open Terminal' }}</span>
                </a>
            </div>
        </div>

    </div>
@endsection
