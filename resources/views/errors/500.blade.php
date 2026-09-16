<!doctype html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>500 - Server Disruption | ZIVO PAY</title>
    <link rel="icon" type="image/png" href="{{ asset('images/fav.png') }}?v=10" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/index.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dextrade-theme.css') }}" rel="stylesheet">
    <style>
        body.ng-error-bg {
            background-color: #020b06 !important;
            background-image: 
                linear-gradient(to bottom, rgba(2, 22, 13, 0.85), rgba(1, 10, 5, 0.93)),
                url('{{ asset("images/auth_bg.jpg") }}') !important;
            background-size: cover !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            background-attachment: fixed !important;
            min-height: 100vh;
        }

        .ng-giant-number {
            font-size: clamp(120px, 20vw, 260px) !important;
            line-height: 0.85 !important;
            letter-spacing: -4px !important;
            filter: drop-shadow(0 0 45px rgba(0, 230, 118, 0.55));
        }
    </style>
</head>

<body class="ng-error-bg flex flex-col justify-center items-center min-h-screen p-4 sm:p-8 text-slate-100 font-sans">

    <!-- Main Content Area -->
    <main class="w-full max-w-2xl my-auto text-center py-6 space-y-6">
        
        <!-- MASSIVE GIANT 500 NUMBER -->
        <div class="space-y-2 select-none">
            <h1 class="ng-giant-number font-black text-gold-gradient font-heading">
                500
            </h1>
            
            <div class="pt-2">
                <span class="px-4 py-1.5 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-black uppercase tracking-[3px] border border-emerald-500/40 shadow-inner inline-flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    HTTP ERROR 500 • SERVER EXCEPTION
                </span>
            </div>
        </div>

        <!-- Description -->
        <div class="space-y-3 max-w-lg mx-auto">
            <h2 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight font-heading">
                SERVER DISRUPTION DETECTED
            </h2>
            <p class="text-xs sm:text-sm text-neutral-300 leading-relaxed font-medium">
                Our trading engines experienced a temporary internal server exception. Please retry in a moment.
            </p>
            <div>
                <span class="text-xs font-mono text-emerald-400 bg-black/80 px-4 py-2 rounded-xl border border-emerald-500/30 inline-flex items-center gap-2 break-all max-w-full">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    {{ request()->url() }}
                </span>
            </div>
        </div>

        <!-- Compact Proportioned Action Buttons -->
        <div class="pt-4 max-w-md mx-auto flex flex-col sm:flex-row items-center justify-center gap-3">
            <button onclick="window.location.reload()" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-black text-xs uppercase tracking-wider hover:scale-105 transition shadow-lg flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                RETRY / REFRESH PAGE
            </button>

            <a href="{{ url('/') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-neutral-900 border border-emerald-500/40 text-emerald-400 font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition flex items-center justify-center gap-2 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                RETURN TO HOME
            </a>
        </div>

        <!-- Public Quick Links ONLY -->
        <div class="pt-6 border-t border-emerald-500/20 flex flex-wrap items-center justify-center gap-6 text-xs text-neutral-400 font-semibold">
            <a href="{{ route('user.login') }}" class="hover:text-emerald-400 transition flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Member Login
            </a>
            <a href="{{ route('user.register') }}" class="hover:text-emerald-400 transition flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Register Account
            </a>
        </div>

    </main>

    <!-- Footer -->
    <footer class="w-full max-w-5xl py-4 text-center border-t border-emerald-500/20">
        <p class="text-xs text-neutral-400 font-semibold font-mono">
            ZIVO PAY SERVER CLUSTER • ALL RIGHTS RESERVED © {{ date('Y') }}
        </p>
    </footer>

</body>

</html>
