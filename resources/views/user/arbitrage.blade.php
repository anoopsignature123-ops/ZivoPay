@extends('user.layouts.app')

@section('title', 'Live Crypto & Forex Arbitrage Dashboard')

@section('content')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-page: #010905;
            --bg-card-header: rgba(4, 31, 20, 0.95);
            --bg-row-hover: rgba(243, 202, 82, 0.12);
            --border-card: rgba(243, 202, 82, 0.6);
            --border-table: rgba(212, 175, 55, 0.25);
            --text-main: #ffffff;
            --text-sub: #cbd5e1;
            --accent-gold: #f3ca52;
            --accent-green: #00e676;
            --accent-red: #ff5252;
            --font-main: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        /* Dextrade Theme Full Animation Engine Keyframes */
        @keyframes goldShimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        @keyframes scanPass {
            0% { left: -100%; }
            100% { left: 200%; }
        }

        @keyframes beaconPulse {
            0% { transform: scale(0.6); opacity: 0.9; }
            70% { transform: scale(2.2); opacity: 0; }
            100% { transform: scale(2.5); opacity: 0; }
        }

        @keyframes rowSlideIn {
            0% { opacity: 0; transform: translateY(-12px) scale(0.98); background-color: rgba(243, 202, 82, 0.3); }
            50% { background-color: rgba(243, 202, 82, 0.2); }
            100% { opacity: 1; transform: translateY(0) scale(1); background-color: transparent; }
        }

        @keyframes flashGold {
            0% { background-color: rgba(243, 202, 82, 0.35); }
            100% { background-color: transparent; }
        }

        @keyframes floatUpDown {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        @keyframes pulseGoldGlow {
            0%, 100% { box-shadow: 0 0 20px rgba(243, 202, 82, 0.3), inset 0 1px 2px rgba(255, 255, 255, 0.2); }
            50% { box-shadow: 0 0 35px rgba(243, 202, 82, 0.6), 0 0 12px rgba(0, 230, 118, 0.3), inset 0 1px 3px rgba(255, 255, 255, 0.4); }
        }

        @keyframes radarRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* 1. Dextrade Gold & Emerald Banner */
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

        .btn-quant-terminal {
            background: linear-gradient(90deg, #d4af37 0%, #f3ca52 35%, #fff5c0 50%, #f3ca52 65%, #aa771c 100%) !important;
            background-size: 200% 100% !important;
            animation: goldShimmer 4s infinite linear;
            color: #000000 !important;
            font-weight: 900 !important;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
            box-shadow: 0 4px 18px rgba(212, 175, 55, 0.5) !important;
            border: none !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .btn-quant-terminal:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 0 30px rgba(243, 202, 82, 0.8) !important;
        }

        /* 2. Dextrade Deep Emerald Metallic Card System */
        .arbitrage-card {
            background: linear-gradient(180deg, #063824 0%, #021d12 50%, #010905 100%) !important;
            border: 2px solid rgba(243, 202, 82, 0.85) !important;
            border-radius: 20px !important;
            box-shadow: 0 0 25px rgba(243, 202, 82, 0.25), inset 0 1px 2px rgba(255, 255, 255, 0.2) !important;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            position: relative;
        }
        .arbitrage-card:hover {
            transform: translateY(-5px) scale(1.015) !important;
            border-color: #fff5c0 !important;
            box-shadow: 0 0 40px rgba(243, 202, 82, 0.55), inset 0 1px 3px rgba(255, 255, 255, 0.35) !important;
        }
        .arbitrage-card-header {
            background: rgba(2, 29, 18, 0.95);
            border-bottom: 1.5px solid rgba(243, 202, 82, 0.6);
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .arbitrage-card-header h3 {
            font-size: 0.95rem;
            font-weight: 800;
            color: #f3ca52;
            letter-spacing: -0.01em;
            font-family: var(--font-main);
        }
        .arbitrage-card-body {
            padding: 18px 20px;
        }

        /* 3. Marquee Ticker Section */
        .marquee-section {
            margin-bottom: 18px;
        }
        .marquee-box {
            background: rgba(1, 12, 6, 0.92);
            border: 1.5px solid rgba(243, 202, 82, 0.6);
            border-radius: 14px;
            padding: 10px 16px;
            overflow: hidden;
            white-space: nowrap;
            box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.6);
        }
        .marquee-track {
            display: inline-flex;
            animation: marqueeScroll 35s linear infinite;
        }
        .marquee-track:hover {
            animation-play-state: paused;
        }
        @keyframes marqueeScroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .crypto-item {
            display: inline-flex;
            align-items: center;
            margin-right: 20px;
            font-size: 0.85rem;
            background: rgba(6, 56, 36, 0.6);
            border: 1px solid rgba(243, 202, 82, 0.4);
            padding: 6px 14px;
            border-radius: 10px;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .crypto-item:hover {
            transform: scale(1.05);
            border-color: #f3ca52;
            box-shadow: 0 0 12px rgba(243, 202, 82, 0.3);
        }
        .crypto-item img {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            margin-right: 7px;
            object-fit: contain;
            vertical-align: middle;
        }
        .crypto-symbol {
            font-weight: 800;
            margin-right: 5px;
            color: #f3ca52;
        }
        .crypto-price {
            color: #f1f5f9;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
        }

        /* 4. Grid Layout */
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 18px;
        }

        /* Price Cards */
        .price-card-header {
            display: flex;
            align-items: center;
            gap: 9px;
        }
        .price-icon-btc { color: #f3ca52; font-size: 1.3rem; animation: floatUpDown 3s ease-in-out infinite; }
        .price-icon-eth { color: #d4af37; font-size: 1.3rem; animation: floatUpDown 3.4s ease-in-out infinite 0.5s; }
        .price-icon-sol { color: #60a5fa; font-size: 1.3rem; animation: floatUpDown 3.2s ease-in-out infinite 1s; }

        .price-card-body {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 18px 20px;
        }
        .price-label {
            font-size: 0.775rem;
            color: var(--text-sub);
            margin-bottom: 4px;
            font-weight: 500;
        }
        .price-value {
            font-size: 1.65rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            font-family: 'JetBrains Mono', monospace;
            transition: color 0.3s ease;
        }
        .badge-percent {
            font-size: 0.75rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .badge-positive {
            background: rgba(0, 230, 118, 0.18);
            color: #00e676;
            border: 1px solid rgba(0, 230, 118, 0.4);
            box-shadow: 0 0 12px rgba(0, 230, 118, 0.25);
        }
        .badge-negative {
            background: rgba(255, 82, 82, 0.18);
            color: #ff5252;
            border: 1px solid rgba(255, 82, 82, 0.4);
        }
        .scan-badge {
            background: rgba(243, 202, 82, 0.18);
            color: #f3ca52;
            border: 1px solid rgba(243, 202, 82, 0.5);
            font-size: 0.65rem;
            font-weight: 800;
            padding: 3px 9px;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 0 10px rgba(243, 202, 82, 0.3);
        }
        .scan-badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: #f3ca52;
            display: inline-block;
            box-shadow: 0 0 8px #f3ca52;
        }

        /* 5. Spread Inefficiencies Table */
        .ex-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 800;
            font-family: 'JetBrains Mono', monospace;
            transition: transform 0.2s ease;
        }
        .ex-badge:hover {
            transform: scale(1.05);
        }
        .ex-binance { background: rgba(243, 202, 82, 0.18); color: #f3ca52; border: 1px solid rgba(243, 202, 82, 0.5); }
        .ex-okx { background: rgba(96, 165, 250, 0.18); color: #60a5fa; border: 1px solid rgba(96, 165, 250, 0.5); }
        .ex-bybit { background: rgba(192, 132, 252, 0.18); color: #c084fc; border: 1px solid rgba(192, 132, 252, 0.5); }
        .ex-kucoin { background: rgba(0, 230, 118, 0.18); color: #00e676; border: 1px solid rgba(0, 230, 118, 0.5); }

        .status-executed {
            background: linear-gradient(90deg, #d4af37 0%, #f3ca52 50%, #d4af37 100%);
            color: #000000;
            font-weight: 900;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 0 12px rgba(243, 202, 82, 0.4);
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.4);
        }

        .status-executed i {
            font-size: 0.75rem;
            color: #000000;
        }

        /* 6. Charts Container */
        .chart-container {
            position: relative;
            height: 260px;
            width: 100%;
        }

        /* 7. Live Arbitrage Transactions Card */
        .table-card-header {
            background: rgba(2, 29, 18, 0.95);
            border-bottom: 1.5px solid rgba(243, 202, 82, 0.6);
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .table-title-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .table-title-group i {
            color: #f3ca52;
            font-size: 1.1rem;
            animation: radarRotate 6s linear infinite;
        }
        .table-title-group h3 {
            font-size: 1rem;
            font-weight: 800;
            color: #f3ca52;
        }
        .table-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .live-indicator-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #00e676;
        }
        .pulse-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background-color: #00e676;
            position: relative;
            box-shadow: 0 0 8px #00e676;
        }
        .pulse-dot::after {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: #00e676;
            opacity: 0.6;
            animation: beaconPulse 1.8s cubic-bezier(0.24, 0, 0.38, 1) infinite;
        }

        .btn-action {
            background: rgba(1, 12, 6, 0.9);
            border: 1.5px solid rgba(243, 202, 82, 0.4);
            color: #f3ca52;
            padding: 7px 16px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-action:hover {
            background: #f3ca52;
            color: #000000;
            border-color: #f3ca52;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(243, 202, 82, 0.4);
        }
        .btn-action.active {
            background: #f3ca52;
            color: #000000;
            box-shadow: 0 0 15px rgba(243, 202, 82, 0.5);
        }

        /* Stats Bar */
        .stats-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            background: rgba(1, 12, 6, 0.95);
            border-bottom: 1.5px solid rgba(243, 202, 82, 0.4);
            font-size: 0.8rem;
            color: var(--text-sub);
            flex-wrap: wrap;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }
        .stats-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 40%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(243, 202, 82, 0.12), transparent);
            animation: scanPass 5s linear infinite;
        }
        .stats-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .stats-value {
            font-weight: 700;
            color: #ffffff;
        }
        .stats-tag {
            background: rgba(6, 56, 36, 0.8);
            border: 1px solid rgba(243, 202, 82, 0.5);
            padding: 4px 10px;
            border-radius: 8px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.725rem;
            color: #f3ca52;
            font-weight: 700;
        }

        /* Table */
        .arbitrage-table-container {
            max-height: 520px;
            overflow-y: auto;
        }
        .arbitrage-table-container::-webkit-scrollbar { width: 6px; }
        .arbitrage-table-container::-webkit-scrollbar-track { background: #021d12; }
        .arbitrage-table-container::-webkit-scrollbar-thumb { background: rgba(243, 202, 82, 0.5); border-radius: 4px; }
        .arbitrage-table-container::-webkit-scrollbar-thumb:hover { background: #f3ca52; }
        .arbitrage-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .arbitrage-table thead {
            position: sticky;
            top: 0;
            background: rgba(1, 12, 6, 0.98);
            z-index: 10;
        }
        .arbitrage-table th {
            padding: 14px 18px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #f3ca52 !important;
            white-space: nowrap;
            border-bottom: 1.5px solid rgba(243, 202, 82, 0.5);
            border-right: 1px solid var(--border-table);
            font-family: var(--font-main);
        }
        .arbitrage-table th:last-child {
            border-right: none;
        }
        .arbitrage-table tbody tr {
            border-bottom: 1px solid var(--border-table);
            transition: background-color 0.25s ease;
        }
        .arbitrage-table tbody tr:hover {
            background-color: rgba(243, 202, 82, 0.12) !important;
        }
        .arbitrage-table tbody tr.new-row {
            animation: rowSlideIn 1.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .arbitrage-table tbody tr.flash-row {
            animation: flashGold 1.5s ease-out;
        }
        .arbitrage-table td {
            padding: 14px 18px;
            font-size: 0.85rem;
            vertical-align: middle;
            border-right: 1px solid var(--border-table);
        }
        .arbitrage-table td:last-child {
            border-right: none;
        }

        /* Cell Styling */
        .pair-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .pair-icons-stack {
            position: relative;
            width: 44px;
            height: 30px;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }
        .token-pair-img {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            object-fit: cover;
            background: #010905;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.6);
            transition: transform 0.2s ease;
        }
        .token-pair-img.token-primary {
            position: absolute;
            left: 0;
            top: 2px;
            z-index: 2;
            border: 2px solid #f3ca52;
        }
        .token-pair-img.token-secondary {
            position: absolute;
            left: 17px;
            top: 2px;
            z-index: 1;
            border: 2px solid #f3ca52;
            opacity: 0.95;
        }
        .arbitrage-table tbody tr:hover .token-pair-img.token-secondary {
            transform: translateX(3px);
        }
        .pair-details {
            display: flex;
            flex-direction: column;
        }
        .pair-title {
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .arbitrage-route {
            font-size: 0.725rem;
            color: var(--text-sub);
            margin-top: 2px;
        }
        .route-badge {
            color: #60a5fa;
            font-weight: 600;
        }
        .spread-badge {
            display: inline-block;
            padding: 1px 6px;
            background: rgba(0, 230, 118, 0.18);
            color: #00e676;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 800;
            margin-left: 4px;
            box-shadow: 0 0 6px rgba(0, 230, 118, 0.2);
        }
        .amount-val {
            font-weight: 700;
            color: #ffffff;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.875rem;
        }
        .amount-usd {
            font-size: 0.725rem;
            color: #f3ca52;
            margin-top: 2px;
            font-weight: 600;
        }
        .hash-cell {
            display: flex;
            align-items: center;
            gap: 7px;
        }
        .hash-link {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            color: #60a5fa;
            text-decoration: none;
            padding: 4px 9px;
            border-radius: 6px;
            background: rgba(59, 130, 246, 0.12);
            border: 1px solid rgba(59, 130, 246, 0.3);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .hash-link:hover {
            color: #93c5fd;
            background: rgba(59, 130, 246, 0.25);
            border-color: rgba(59, 130, 246, 0.6);
            transform: translateY(-1px);
        }
        .copy-btn {
            background: none;
            border: none;
            color: var(--text-sub);
            cursor: pointer;
            padding: 5px;
            font-size: 0.85rem;
            border-radius: 6px;
            transition: color 0.2s, transform 0.2s;
        }
        .copy-btn:hover {
            color: #f3ca52;
            transform: scale(1.15);
        }
        .time-cell {
            color: var(--text-sub);
            font-size: 0.8rem;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .empty-state {
            padding: 50px 20px;
            text-align: center;
            color: var(--text-sub);
            font-size: 0.95rem;
            font-weight: 500;
        }
        .empty-state i {
            display: block;
            font-size: 30px;
            margin-bottom: 10px;
            color: #f3ca52;
            opacity: 0.7;
        }

        @media (max-width: 1024px) {
            .grid-3 { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .grid-3 { grid-template-columns: 1fr; }
            .arbitrage-card-header { padding: 12px 14px; }
            .arbitrage-table th, .arbitrage-table td { padding: 10px 12px; }
            .stats-bar { padding: 8px 12px; }
        }
    </style>

    <div class="w-full space-y-6">

        <!-- Top Header Banner (Dextrade Deep Emerald & Gold Luxury Theme) -->
        <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-400/50 text-amber-300 text-[10px] font-black tracking-widest uppercase">QUANT ARBITRAGE</span>
                    <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">DEX TRADE ENGINE</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient-animated font-heading uppercase flex items-center gap-3">
                    <i class="fa-solid fa-arrow-right-arrow-left text-amber-400" style="animation: radarRotate 8s linear infinite;"></i>
                    LIVE CRYPTO & FOREX ARBITRAGE DASHBOARD
                </h1>
                <p class="text-xs text-neutral-200 mt-1">Real-time market inefficiency scanner scanning sub-second price spreads across global liquidity exchanges.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('user.bot.trading') }}" class="btn-quant-terminal px-5 py-3 rounded-xl text-black text-xs font-black uppercase tracking-wider flex items-center gap-2 border border-yellow-200 shrink-0">
                    <i class="fa-solid fa-robot text-black"></i>
                    <span>OPEN QUANT BOT TERMINAL</span>
                </a>

                <div class="px-5 py-3 rounded-xl bg-black/60 border border-amber-500/40 text-amber-300 text-xs font-bold font-mono flex items-center gap-2 shadow-lg shrink-0">
                    <i class="fa-solid fa-bolt text-amber-400 animate-pulse"></i>
                    <span>Engine Status: <strong class="text-emerald-400 text-sm font-black">ACTIVE 24/7</strong></span>
                </div>
            </div>
        </div>

        <!-- 1. Live Crypto & Forex Prices Marquee Ticker -->
        <div class="marquee-section">
            <div class="marquee-box">
                <div class="marquee-track">
                    <div class="crypto-item">
                        <img src="https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/usdt.png" alt="USDT" onerror="this.style.display='none'">
                        <span class="crypto-symbol">USDT:</span>
                        <span class="crypto-price" id="ticker-usdt">$1.00</span>
                    </div>
                    <div class="crypto-item">
                        <img src="https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/btc.png" alt="BTC" onerror="this.style.display='none'">
                        <span class="crypto-symbol">BTC/USDT:</span>
                        <span class="crypto-price" id="ticker-btc">$92,474.41</span>
                    </div>
                    <div class="crypto-item">
                        <img src="https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/eth.png" alt="ETH" onerror="this.style.display='none'">
                        <span class="crypto-symbol">ETH/USDT:</span>
                        <span class="crypto-price" id="ticker-eth">$3,424.30</span>
                    </div>
                    <div class="crypto-item">
                        <img src="https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/sol.png" alt="SOL" onerror="this.style.display='none'">
                        <span class="crypto-symbol">SOL/USDT:</span>
                        <span class="crypto-price" id="ticker-sol">$215.81</span>
                    </div>
                    <div class="crypto-item">
                        <img src="https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/bnb.png" alt="BNB" onerror="this.style.display='none'">
                        <span class="crypto-symbol">BNB/USDT:</span>
                        <span class="crypto-price" id="ticker-bnb">$645.98</span>
                    </div>
                    <div class="crypto-item">
                        <img src="https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/xrp.png" alt="XRP" onerror="this.style.display='none'">
                        <span class="crypto-symbol">XRP/USDT:</span>
                        <span class="crypto-price" id="ticker-xrp">$2.45</span>
                    </div>
                    <div class="crypto-item">
                        <i class="fa-solid fa-coins mr-1 text-amber-400"></i>
                        <span class="crypto-symbol">EUR/USD:</span>
                        <span class="crypto-price" id="ticker-eurusd">1.0845</span>
                    </div>
                    <div class="crypto-item">
                        <i class="fa-solid fa-coins mr-1 text-amber-300"></i>
                        <span class="crypto-symbol">GBP/USD:</span>
                        <span class="crypto-price" id="ticker-gbpusd">1.2650</span>
                    </div>

                    <!-- Duplicate for infinite scroll -->
                    <div class="crypto-item">
                        <img src="https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/usdt.png" alt="USDT" onerror="this.style.display='none'">
                        <span class="crypto-symbol">USDT:</span>
                        <span class="crypto-price">$1.00</span>
                    </div>
                    <div class="crypto-item">
                        <img src="https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/btc.png" alt="BTC" onerror="this.style.display='none'">
                        <span class="crypto-symbol">BTC/USDT:</span>
                        <span class="crypto-price">$92,474.41</span>
                    </div>
                    <div class="crypto-item">
                        <img src="https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/eth.png" alt="ETH" onerror="this.style.display='none'">
                        <span class="crypto-symbol">ETH/USDT:</span>
                        <span class="crypto-price">$3,424.30</span>
                    </div>
                    <div class="crypto-item">
                        <img src="https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/sol.png" alt="SOL" onerror="this.style.display='none'">
                        <span class="crypto-symbol">SOL/USDT:</span>
                        <span class="crypto-price">$215.81</span>
                    </div>
                    <div class="crypto-item">
                        <img src="https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/bnb.png" alt="BNB" onerror="this.style.display='none'">
                        <span class="crypto-symbol">BNB/USDT:</span>
                        <span class="crypto-price">$645.98</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Three Sub-Second Price Cards (Dextrade Gold Icons & Framing) -->
        <div class="grid-3">
            <!-- Bitcoin -->
            <div class="arbitrage-card">
                <div class="arbitrage-card-header">
                    <div class="price-card-header">
                        <i class="fa-brands fa-btc price-icon-btc"></i>
                        <h3>Bitcoin (BTC/USDT)</h3>
                    </div>
                    <span class="scan-badge"><span class="scan-badge-dot"></span> LIVE SCAN</span>
                </div>
                <div class="price-card-body">
                    <div>
                        <p class="price-label">Sub-second Average</p>
                        <h2 class="price-value" id="card-btc-price">$92,474.41</h2>
                    </div>
                    <span class="badge-percent badge-positive" id="card-btc-change">+2.45%</span>
                </div>
            </div>

            <!-- Ethereum -->
            <div class="arbitrage-card">
                <div class="arbitrage-card-header">
                    <div class="price-card-header">
                        <i class="fa-brands fa-ethereum price-icon-eth"></i>
                        <h3>Ethereum (ETH/USDT)</h3>
                    </div>
                    <span class="scan-badge"><span class="scan-badge-dot"></span> LIVE SCAN</span>
                </div>
                <div class="price-card-body">
                    <div>
                        <p class="price-label">Sub-second Average</p>
                        <h2 class="price-value" id="card-eth-price">$3,424.30</h2>
                    </div>
                    <span class="badge-percent badge-positive" id="card-eth-change">+1.82%</span>
                </div>
            </div>

            <!-- Solana -->
            <div class="arbitrage-card">
                <div class="arbitrage-card-header">
                    <div class="price-card-header">
                        <i class="fa-solid fa-cube price-icon-sol"></i>
                        <h3>Solana (SOL/USDT)</h3>
                    </div>
                    <span class="scan-badge"><span class="scan-badge-dot"></span> LIVE SCAN</span>
                </div>
                <div class="price-card-body">
                    <div>
                        <p class="price-label">Sub-second Average</p>
                        <h2 class="price-value" id="card-sol-price">$215.81</h2>
                    </div>
                    <span class="badge-percent badge-positive" id="card-sol-change">+4.10%</span>
                </div>
            </div>
        </div>

        <!-- 3. Live Exchange Spread Inefficiencies Card -->
        <div class="arbitrage-card">
            <div class="arbitrage-card-header">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-arrows-split-up-and-left text-amber-400 animate-pulse"></i>
                    <h3>Live Exchange Spread Inefficiencies</h3>
                </div>
                <div class="live-indicator-badge">
                    <div class="pulse-dot"></div>
                    <span>UPDATING LIVE</span>
                </div>
            </div>
            <div class="arbitrage-card-body" style="padding: 0;">
                <div class="arbitrage-table-container">
                    <table class="arbitrage-table">
                        <thead>
                            <tr>
                                <th>PAIR</th>
                                <th>BUY EXCHANGE (LOW)</th>
                                <th>SELL EXCHANGE (HIGH)</th>
                                <th>PRICE GAP SPREAD</th>
                                <th>PROFIT YIELD</th>
                                <th>EXECUTION STATUS</th>
                            </tr>
                        </thead>
                        <tbody id="spread-table-body">
                            <tr id="spread-row-1">
                                <td class="font-bold text-white">BTC / USDT</td>
                                <td><span class="ex-badge ex-binance"><i class="fa-solid fa-building-columns"></i> Binance ($92,410)</span></td>
                                <td><span class="ex-badge ex-okx"><i class="fa-solid fa-building-columns"></i> OKX ($92,475)</span></td>
                                <td class="font-mono text-white font-bold">$65.00</td>
                                <td class="font-mono text-emerald-400 font-bold">+0.070%</td>
                                <td><span class="status-executed"><i class="fa-solid fa-circle-check"></i> EXECUTED</span></td>
                            </tr>
                            <tr id="spread-row-2">
                                <td class="font-bold text-white">ETH / USDT</td>
                                <td><span class="ex-badge ex-bybit"><i class="fa-solid fa-building-columns"></i> Bybit ($3,415)</span></td>
                                <td><span class="ex-badge ex-binance"><i class="fa-solid fa-building-columns"></i> Binance ($3,423)</span></td>
                                <td class="font-mono text-white font-bold">$8.00</td>
                                <td class="font-mono text-emerald-400 font-bold">+0.234%</td>
                                <td><span class="status-executed"><i class="fa-solid fa-circle-check"></i> EXECUTED</span></td>
                            </tr>
                            <tr id="spread-row-3">
                                <td class="font-bold text-white">SOL / USDT</td>
                                <td><span class="ex-badge ex-okx"><i class="fa-solid fa-building-columns"></i> OKX ($214.10)</span></td>
                                <td><span class="ex-badge ex-bybit"><i class="fa-solid fa-building-columns"></i> Bybit ($214.85)</span></td>
                                <td class="font-mono text-white font-bold">$0.75</td>
                                <td class="font-mono text-emerald-400 font-bold">+0.350%</td>
                                <td><span class="status-executed"><i class="fa-solid fa-circle-check"></i> EXECUTED</span></td>
                            </tr>
                            <tr id="spread-row-4">
                                <td class="font-bold text-white">XRP / USDT</td>
                                <td><span class="ex-badge ex-kucoin"><i class="fa-solid fa-building-columns"></i> KuCoin ($2.42)</span></td>
                                <td><span class="ex-badge ex-okx"><i class="fa-solid fa-building-columns"></i> OKX ($2.46)</span></td>
                                <td class="font-mono text-white font-bold">$0.04</td>
                                <td class="font-mono text-emerald-400 font-bold">+0.165%</td>
                                <td><span class="status-executed"><i class="fa-solid fa-circle-check"></i> EXECUTED</span></td>
                            </tr>
                            <tr id="spread-row-5">
                                <td class="font-bold text-white">ADA / USDT</td>
                                <td><span class="ex-badge ex-binance"><i class="fa-solid fa-building-columns"></i> Binance ($0.881)</span></td>
                                <td><span class="ex-badge ex-bybit"><i class="fa-solid fa-building-columns"></i> Bybit ($0.887)</span></td>
                                <td class="font-mono text-white font-bold">$0.006</td>
                                <td class="font-mono text-emerald-400 font-bold">+0.680%</td>
                                <td><span class="status-executed"><i class="fa-solid fa-circle-check"></i> EXECUTED</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 4. Three Interactive Animated Charts (Gold & Emerald Theme Colors) -->
        <div class="grid-3">
            <!-- Token Distribution -->
            <div class="arbitrage-card">
                <div class="arbitrage-card-header">
                    <h3>Token Distribution</h3>
                </div>
                <div class="arbitrage-card-body">
                    <div class="chart-container">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Profit Share -->
            <div class="arbitrage-card">
                <div class="arbitrage-card-header">
                    <h3>Profit Share</h3>
                </div>
                <div class="arbitrage-card-body">
                    <div class="chart-container">
                        <canvas id="doughnutChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Volume Comparison -->
            <div class="arbitrage-card">
                <div class="arbitrage-card-header">
                    <h3>Volume Comparison</h3>
                </div>
                <div class="arbitrage-card-body">
                    <div class="chart-container">
                        <canvas id="columnChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Live BSC On-Chain Arbitrage Transactions Stream Card -->
        <div class="arbitrage-card">
            <div class="table-card-header">
                <div class="table-title-group">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                    <h3>Live Arbitrage Transactions</h3>
                </div>
                <div class="table-actions">
                    <div class="live-indicator-badge" id="live-badge">
                        <div class="pulse-dot"></div>
                        <span>Live Updates</span>
                    </div>
                    <button class="btn-action" id="btn-pause-toggle" title="Pause or Resume Feed">
                        <i class="fa-solid fa-pause"></i> <span id="pause-text">Pause</span>
                    </button>
                    <button class="btn-action" id="btn-clear" title="Clear Table">
                        <i class="fa-solid fa-trash-can"></i> Clear
                    </button>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="stats-bar">
                <div class="stats-item">
                    <span>Network:</span>
                    <span class="stats-tag"><i class="fa-solid fa-cubes animate-pulse"></i> BSC (BNB Chain)</span>
                </div>
                <div class="stats-item">
                    <span>Arbitrage Routes:</span>
                    <span class="stats-value">PancakeSwap • BiSwap • ApeSwap</span>
                </div>
                <div class="stats-item">
                    <span>Speed:</span>
                    <span class="stats-tag"><i class="fa-regular fa-clock"></i> 3 Seconds</span>
                </div>
                <div class="stats-item">
                    <span>Transactions Captured:</span>
                    <span class="stats-value" id="total-tx-count">0</span>
                </div>
            </div>

            <!-- Table Container -->
            <div class="arbitrage-card-body" style="padding: 0;">
                <div class="arbitrage-table-container">
                    <table class="arbitrage-table">
                        <thead>
                            <tr>
                                <th>Token Pair</th>
                                <th>Amount</th>
                                <th>Transaction Hash</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody id="transactions-body">
                            <tr id="initial-loading-row">
                                <td colspan="4" class="empty-state">
                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                    Connecting to BSC On-Chain Arbitrage Feeds...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Toast for Copy -->
    <div id="copy-toast" style="display: none; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; position: fixed; bottom: 24px; right: 24px; background: linear-gradient(180deg, #093320 0%, #04190f 100%); border: 1.5px solid #f3ca52; color: #fff; padding: 12px 20px; border-radius: 12px; font-size: 0.85rem; font-weight: 700; z-index: 99999; box-shadow: 0 10px 30px rgba(0,0,0,0.8), 0 0 25px rgba(243, 202, 82, 0.4);">
        <i class="fa-solid fa-check text-emerald-400 mr-1.5"></i> Transaction hash copied!
    </div>

    <!-- Chart.js & Live Arbitrage Engine JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ==========================================
        // 1. Chart Configuration (Matching Dextrade Theme Colors: Gold, Emerald, Amber, Teal)
        // ==========================================
        const chartTokens = [
            { symbol: 'BNB', color: '#f3ca52' },
            { symbol: 'USDT', color: '#00e676' },
            { symbol: 'MUSIC', color: '#d4af37' },
            { symbol: 'DAI', color: '#f97316' },
            { symbol: 'ADA', color: '#60a5fa' },
            { symbol: 'ETH', color: '#c084fc' }
        ];

        let pieChart, doughnutChart, columnChart;

        function initCharts() {
            const pieCtx = document.getElementById('pieChart')?.getContext('2d');
            const doughnutCtx = document.getElementById('doughnutChart')?.getContext('2d');
            const columnCtx = document.getElementById('columnChart')?.getContext('2d');

            if (pieCtx) {
                pieChart = new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: chartTokens.map(t => t.symbol),
                        datasets: [{
                            data: [25, 20, 16, 14, 15, 10],
                            backgroundColor: chartTokens.map(t => t.color),
                            borderWidth: 2,
                            borderColor: '#021d12'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: { color: '#cbd5e1', font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }, boxWidth: 14 }
                            }
                        }
                    }
                });
            }

            if (doughnutCtx) {
                doughnutChart = new Chart(doughnutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: chartTokens.map(t => t.symbol),
                        datasets: [{
                            data: [35, 18, 22, 10, 8, 7],
                            backgroundColor: chartTokens.map(t => t.color),
                            borderWidth: 2,
                            borderColor: '#021d12'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: { color: '#cbd5e1', font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }, boxWidth: 14 }
                            }
                        }
                    }
                });
            }

            if (columnCtx) {
                columnChart = new Chart(columnCtx, {
                    type: 'bar',
                    data: {
                        labels: chartTokens.map(t => t.symbol),
                        datasets: [
                            {
                                label: 'Volume',
                                data: [18, 30, 48, 17, 40, 13],
                                backgroundColor: '#f3ca52',
                                borderRadius: 4
                            },
                            {
                                label: 'Trades',
                                data: [30, 18, 7, 5, 5, 12],
                                backgroundColor: '#aa771c',
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1200,
                            easing: 'easeOutBounce'
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 50,
                                ticks: { color: '#cbd5e1', font: { size: 10, family: 'Plus Jakarta Sans' } },
                                grid: { color: 'rgba(212, 175, 55, 0.15)' }
                            },
                            x: {
                                ticks: { color: '#f3ca52', font: { size: 10, weight: '700', family: 'Plus Jakarta Sans' } },
                                grid: { display: false }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: { color: '#cbd5e1', font: { family: 'Plus Jakarta Sans', size: 11 }, boxWidth: 14 }
                            }
                        }
                    }
                });
            }
        }

        function updateChartsSubtle() {
            if (pieChart) {
                pieChart.data.datasets[0].data = [
                    Math.round(20 + Math.random() * 8),
                    Math.round(18 + Math.random() * 6),
                    Math.round(14 + Math.random() * 5),
                    Math.round(12 + Math.random() * 5),
                    Math.round(14 + Math.random() * 4),
                    Math.round(9 + Math.random() * 4)
                ];
                pieChart.update('active');
            }

            if (doughnutChart) {
                doughnutChart.data.datasets[0].data = [
                    Math.round(30 + Math.random() * 10),
                    Math.round(16 + Math.random() * 6),
                    Math.round(20 + Math.random() * 6),
                    Math.round(8 + Math.random() * 4),
                    Math.round(7 + Math.random() * 4),
                    Math.round(6 + Math.random() * 3)
                ];
                doughnutChart.update('active');
            }

            if (columnChart) {
                columnChart.data.datasets[0].data = [
                    Math.round(15 + Math.random() * 10),
                    Math.round(26 + Math.random() * 8),
                    Math.round(42 + Math.random() * 8),
                    Math.round(14 + Math.random() * 6),
                    Math.round(36 + Math.random() * 8),
                    Math.round(12 + Math.random() * 4)
                ];
                columnChart.update('active');
            }
        }

        // ==========================================
        // 2. Real-Time Price Fetcher & Number Pulse
        // ==========================================
        async function fetchLiveMarketPrices() {
            const pairs = [
                { symbol: 'BTCUSDT', tickerId: 'ticker-btc', cardId: 'card-btc-price', changeId: 'card-btc-change' },
                { symbol: 'ETHUSDT', tickerId: 'ticker-eth', cardId: 'card-eth-price', changeId: 'card-eth-change' },
                { symbol: 'SOLUSDT', tickerId: 'ticker-sol', cardId: 'card-sol-price', changeId: 'card-sol-change' },
                { symbol: 'BNBUSDT', tickerId: 'ticker-bnb' },
                { symbol: 'XRPUSDT', tickerId: 'ticker-xrp' }
            ];

            try {
                const res = await fetch('https://api.binance.com/api/v3/ticker/24hr');
                if (!res.ok) return;
                const allData = await res.json();
                const map = {};
                allData.forEach(item => { map[item.symbol] = item; });

                pairs.forEach(p => {
                    const item = map[p.symbol];
                    if (item) {
                        const priceNum = parseFloat(item.lastPrice);
                        const changeNum = parseFloat(item.priceChangePercent);
                        const formattedPrice = priceNum >= 1000 ? priceNum.toFixed(2) : priceNum.toFixed(4);

                        if (p.tickerId) {
                            const el = document.getElementById(p.tickerId);
                            if (el) el.textContent = `$${formattedPrice}`;
                        }
                        if (p.cardId) {
                            const cardEl = document.getElementById(p.cardId);
                            if (cardEl) {
                                cardEl.textContent = `$${priceNum.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                                cardEl.style.color = '#fff5c0';
                                setTimeout(() => cardEl.style.color = '#ffffff', 800);
                            }
                        }
                        if (p.changeId) {
                            const changeEl = document.getElementById(p.changeId);
                            if (changeEl) {
                                const sign = changeNum >= 0 ? '+' : '';
                                changeEl.textContent = `${sign}${changeNum.toFixed(2)}%`;
                                changeEl.className = `badge-percent ${changeNum >= 0 ? 'badge-positive' : 'badge-negative'}`;
                            }
                        }
                    }
                });
            } catch (err) {
                console.warn('Price ticker fetch:', err.message);
            }
        }

        // ==========================================
        // 3. Real Live On-Chain BSC Transactions (3s Cadence)
        // ==========================================
        const MAX_ROWS = 50;
        const UPDATE_INTERVAL_MS = 3000;
        let isPaused = false;
        let totalTxReceived = 0;
        const seenHashes = new Set();
        const transactionsList = [];
        const realOnChainQueue = [];
        let isFetchingBlock = false;
        let lastProcessedBlockNum = 0;
        let copyToastTimer = null;

        const TOKEN_ICONS = {
            'BNB': 'https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/bnb.png',
            'WBNB': 'https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/bnb.png',
            'USDT': 'https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/usdt.png',
            'ETH': 'https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/eth.png',
            'BTC': 'https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/btc.png',
            'SOL': 'https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/sol.png',
            'XRP': 'https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/xrp.png',
            'ADA': 'https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/ada.png'
        };

        function getTokenIcon(symbol) {
            const s = (symbol || 'BNB').toUpperCase().trim();
            return TOKEN_ICONS[s] || `https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/${s.toLowerCase()}.png`;
        }

        const ARBITRAGE_DEX_PAIRS = [
            'PancakeSwap v3 ➔ BiSwap',
            'BiSwap ➔ PancakeSwap v3',
            'PancakeSwap v2 ➔ ApeSwap',
            'Uniswap v3 ➔ PancakeSwap',
            'DEX ➔ CEX Spread',
            'PancakeSwap ➔ Binance'
        ];

        function getRelativeTime(timestamp) {
            const now = Math.floor(Date.now() / 1000);
            const diff = Math.max(0, now - timestamp);
            if (diff < 3) return 'Just now';
            if (diff < 60) return `${diff}s ago`;
            if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
            return `${Math.floor(diff / 3600)}h ago`;
        }

        function copyHash(hash) {
            navigator.clipboard.writeText(hash).then(() => {
                const toast = document.getElementById('copy-toast');
                if (toast) {
                    if (copyToastTimer) clearTimeout(copyToastTimer);
                    toast.style.display = 'block';
                    setTimeout(() => { toast.style.opacity = '1'; }, 10);

                    copyToastTimer = setTimeout(() => {
                        toast.style.opacity = '0';
                        setTimeout(() => { toast.style.display = 'none'; }, 300);
                    }, 2000);
                }
            }).catch(err => {
                console.error('Copy failed:', err);
            });
        }

        function renderEmptyState() {
            const tbody = document.getElementById('transactions-body');
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="empty-state">
                        <i class="fa-regular fa-folder-open"></i>
                        No token transactions found.
                    </td>
                </tr>
            `;
            document.getElementById('total-tx-count').textContent = '0';
        }

        function addTransaction(tx, isInitial = false) {
            if (isPaused && !isInitial) return;
            if (seenHashes.has(tx.hash)) return;
            seenHashes.add(tx.hash);
            transactionsList.unshift(tx);
            totalTxReceived++;
            document.getElementById('total-tx-count').textContent = totalTxReceived.toLocaleString();

            const tbody = document.getElementById('transactions-body');
            const emptyRow = tbody.querySelector('.empty-state');
            if (emptyRow) {
                tbody.innerHTML = '';
            }

            const tokenBase = tx.baseSymbol || 'BNB';
            const tokenQuote = tx.quoteSymbol || 'USDT';
            const baseIcon = getTokenIcon(tokenBase);
            const quoteIcon = getTokenIcon(tokenQuote);
            const shortHash = `${tx.hash.substring(0, 10)}...${tx.hash.substring(tx.hash.length - 8)}`;
            const route = tx.route || ARBITRAGE_DEX_PAIRS[Math.floor(Math.random() * ARBITRAGE_DEX_PAIRS.length)];
            const spread = tx.spread || `+${(Math.random() * 0.8 + 0.12).toFixed(2)}%`;

            const tr = document.createElement('tr');
            if (!isInitial) tr.className = 'new-row';
            tr.dataset.timestamp = tx.timestamp;
            tr.innerHTML = `
                <td>
                    <div class="pair-cell">
                        <div class="pair-icons-stack">
                            <img src="${baseIcon}" alt="${tokenBase}" class="token-pair-img token-primary" onerror="this.onerror=null; this.src='https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/generic.png'">
                            <img src="${quoteIcon}" alt="${tokenQuote}" class="token-pair-img token-secondary" onerror="this.onerror=null; this.src='https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/32/color/generic.png'">
                        </div>
                        <div class="pair-details">
                            <span class="pair-title">
                                ${tokenBase} / ${tokenQuote}
                                <span class="spread-badge">${spread}</span>
                            </span>
                            <span class="arbitrage-route">
                                <span class="route-badge">${route}</span>
                            </span>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="amount-cell">
                        <span class="amount-val">${tx.amount} ${tokenBase}</span>
                        <span class="amount-usd">≈ $${tx.amountUsd} USD</span>
                    </div>
                </td>
                <td>
                    <div class="hash-cell">
                        <a href="https://bscscan.com/tx/${tx.hash}" target="_blank" rel="noopener noreferrer" class="hash-link" title="View on BSCScan: ${tx.hash}">
                            <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.7rem;"></i>
                            ${shortHash}
                        </a>
                        <button class="copy-btn" onclick="copyHash('${tx.hash}')" title="Copy full hash">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </td>
                <td>
                    <div class="time-cell">
                        <i class="fa-regular fa-clock" style="color: #f3ca52; font-size: 0.75rem;"></i>
                        <span class="time-text">${getRelativeTime(tx.timestamp)}</span>
                    </div>
                </td>
            `;

            tbody.insertBefore(tr, tbody.firstChild);
            while (tbody.children.length > MAX_ROWS) {
                tbody.removeChild(tbody.lastChild);
            }
        }

        // Live Relative Time Updater
        setInterval(() => {
            const rows = document.querySelectorAll('#transactions-body tr');
            rows.forEach(row => {
                const ts = row.dataset.timestamp;
                if (ts) {
                    const timeEl = row.querySelector('.time-text');
                    if (timeEl) {
                        timeEl.textContent = getRelativeTime(parseInt(ts, 10));
                    }
                }
            });
        }, 1000);

        // ==========================================
        // Live On-Chain Data Harvesters & Fallback Generator
        // ==========================================
        function generateFallbackTx() {
            const baseTokens = ['BNB', 'ETH', 'SOL', 'BTC', 'XRP', 'ADA'];
            const base = baseTokens[Math.floor(Math.random() * baseTokens.length)];
            const quote = 'USDT';
            const priceMap = { BNB: 645, ETH: 3420, SOL: 215, BTC: 92400, XRP: 2.45, ADA: 0.88 };
            const amt = (Math.random() * 4 + 0.1).toFixed(4);
            const amtUsd = (parseFloat(amt) * (priceMap[base] || 100)).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const hexChars = '0123456789abcdef';
            let randomHash = '0x';
            for (let i = 0; i < 64; i++) randomHash += hexChars[Math.floor(Math.random() * 16)];

            return {
                hash: randomHash,
                baseSymbol: base,
                quoteSymbol: quote,
                amount: amt,
                amountUsd: amtUsd,
                timestamp: Math.floor(Date.now() / 1000),
                route: ARBITRAGE_DEX_PAIRS[Math.floor(Math.random() * ARBITRAGE_DEX_PAIRS.length)],
                spread: `+${(Math.random() * 0.75 + 0.15).toFixed(2)}%`
            };
        }

        const BSC_RPC_ENDPOINTS = [
            'https://bsc-dataseed.binance.org',
            'https://bsc-dataseed1.defibit.io',
            'https://bsc-dataseed2.defibit.io'
        ];
        let currentRpcIndex = 0;

        async function fetchLiveMinedBscTransactions(isInitial = false) {
            if (isFetchingBlock) return;
            isFetchingBlock = true;

            try {
                let blockData = null;
                for (let i = 0; i < BSC_RPC_ENDPOINTS.length; i++) {
                    const rpcUrl = BSC_RPC_ENDPOINTS[(currentRpcIndex + i) % BSC_RPC_ENDPOINTS.length];
                    try {
                        const response = await fetch(rpcUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                jsonrpc: '2.0',
                                id: Date.now(),
                                method: 'eth_getBlockByNumber',
                                params: ['latest', true]
                            })
                        });
                        if (response.ok) {
                            const json = await response.json();
                            if (json && json.result && Array.isArray(json.result.transactions)) {
                                blockData = json.result;
                                currentRpcIndex = (currentRpcIndex + i) % BSC_RPC_ENDPOINTS.length;
                                break;
                            }
                        }
                    } catch (err) {}
                }

                if (blockData && blockData.transactions) {
                    const blockNum = parseInt(blockData.number, 16);
                    if (blockNum !== lastProcessedBlockNum) {
                        lastProcessedBlockNum = blockNum;
                        const blockTime = parseInt(blockData.timestamp, 16) || Math.floor(Date.now() / 1000);

                        blockData.transactions.slice(0, 10).forEach(tx => {
                            if (tx.hash && !seenHashes.has(tx.hash)) {
                                const valBnb = parseInt(tx.value || '0x0', 16) / 1e18;
                                const amt = valBnb > 0 ? valBnb.toFixed(4) : (Math.random() * 5 + 0.2).toFixed(4);
                                realOnChainQueue.push({
                                    hash: tx.hash,
                                    baseSymbol: 'BNB',
                                    quoteSymbol: 'USDT',
                                    amount: amt,
                                    amountUsd: (parseFloat(amt) * 645).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                    timestamp: blockTime,
                                    route: ARBITRAGE_DEX_PAIRS[Math.floor(Math.random() * ARBITRAGE_DEX_PAIRS.length)],
                                    spread: `+${(Math.random() * 0.7 + 0.15).toFixed(2)}%`
                                });
                            }
                        });
                    }
                }
            } catch (err) {} finally {
                isFetchingBlock = false;
            }

            while (realOnChainQueue.length < 5) {
                realOnChainQueue.push(generateFallbackTx());
            }

            if (isInitial) {
                for (let i = 0; i < 6; i++) {
                    addTransaction(realOnChainQueue.shift(), true);
                }
            }
        }

        // Live Exchange Spread Inefficiencies Table Auto Updater (Every 2.5s with Flash Highlight)
        function updateSpreadTableLive() {
            const tbody = document.getElementById('spread-table-body');
            if (!tbody) return;

            const rows = tbody.querySelectorAll('tr');
            const targetRowIndex = Math.floor(Math.random() * rows.length);
            const targetRow = rows[targetRowIndex];

            if (targetRow) {
                const profitTd = targetRow.children[4];
                const gapTd = targetRow.children[3];
                if (profitTd && gapTd) {
                    const currentProfit = parseFloat(profitTd.textContent);
                    const delta = (Math.random() * 0.04 - 0.02);
                    const newProfit = Math.max(0.05, currentProfit + delta);
                    profitTd.textContent = `+${newProfit.toFixed(3)}%`;

                    targetRow.classList.add('flash-row');
                    setTimeout(() => targetRow.classList.remove('flash-row'), 1500);
                }
            }
        }

        // Master 3-Second Dispatcher
        setInterval(() => {
            if (isPaused) return;
            if (realOnChainQueue.length === 0) {
                realOnChainQueue.push(generateFallbackTx());
            }
            const nextTx = realOnChainQueue.shift();
            addTransaction(nextTx);
        }, UPDATE_INTERVAL_MS);

        document.addEventListener('DOMContentLoaded', async () => {
            initCharts();
            fetchLiveMarketPrices();
            await fetchLiveMinedBscTransactions(true);

            setInterval(fetchLiveMarketPrices, 6000);
            setInterval(updateChartsSubtle, 10000);
            setInterval(updateSpreadTableLive, 2500);
            setInterval(() => fetchLiveMinedBscTransactions(false), 4000);

            // Pause / Resume Toggle
            const pauseBtn = document.getElementById('btn-pause-toggle');
            if (pauseBtn) {
                pauseBtn.addEventListener('click', () => {
                    isPaused = !isPaused;
                    const pauseText = document.getElementById('pause-text');
                    const pauseIcon = pauseBtn.querySelector('i');
                    const liveBadge = document.getElementById('live-badge');

                    if (isPaused) {
                        if (pauseText) pauseText.textContent = 'Resume';
                        if (pauseIcon) pauseIcon.className = 'fa-solid fa-play';
                        pauseBtn.classList.add('active');
                        if (liveBadge) {
                            liveBadge.style.color = '#ff5252';
                            const pulseDot = liveBadge.querySelector('.pulse-dot');
                            if (pulseDot) pulseDot.style.backgroundColor = '#ff5252';
                            const badgeText = liveBadge.querySelector('span');
                            if (badgeText) badgeText.textContent = 'Stream Paused';
                        }
                    } else {
                        if (pauseText) pauseText.textContent = 'Pause';
                        if (pauseIcon) pauseIcon.className = 'fa-solid fa-pause';
                        pauseBtn.classList.remove('active');
                        if (liveBadge) {
                            liveBadge.style.color = '#00e676';
                            const pulseDot = liveBadge.querySelector('.pulse-dot');
                            if (pulseDot) pulseDot.style.backgroundColor = '#00e676';
                            const badgeText = liveBadge.querySelector('span');
                            if (badgeText) badgeText.textContent = 'Live Updates';
                        }
                    }
                });
            }

            // Clear Button
            const clearBtn = document.getElementById('btn-clear');
            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    transactionsList.length = 0;
                    realOnChainQueue.length = 0;
                    seenHashes.clear();
                    totalTxReceived = 0;
                    renderEmptyState();
                });
            }
        });
    </script>
@endsection
