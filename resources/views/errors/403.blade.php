<!doctype html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>403 - Access Restricted | ZIVO PAY</title>
    <link rel="icon" type="image/png" href="{{ asset('images/fav.png') }}?v=10" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/index.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dextrade-theme.css') }}" rel="stylesheet">
    <style>
        body.ng-error-bg {
            background-color: #020b06 !important;
            background-image: 
                linear-gradient(to bottom, rgba(20, 4, 10, 0.88), rgba(8, 1, 4, 0.94)),
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
            filter: drop-shadow(0 0 45px rgba(255, 82, 82, 0.55));
        }
    </style>
</head>

<body class="ng-error-bg flex flex-col justify-center items-center min-h-screen p-4 sm:p-8 text-slate-100 font-sans">

    <!-- Main Content Area -->
    <main class="w-full max-w-2xl my-auto text-center py-6 space-y-6">
        
        <!-- MASSIVE GIANT 403 NUMBER -->
        <div class="space-y-2 select-none">
            <h1 class="ng-giant-number font-black text-gold-gradient font-heading">
                403
            </h1>
            
            <div class="pt-2">
                <span class="px-4 py-1.5 rounded-full bg-rose-500/20 text-rose-300 text-xs font-black uppercase tracking-[3px] border border-rose-500/40 shadow-inner inline-flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-400 animate-ping"></span>
                    HTTP ERROR 403 • ACCESS RESTRICTED
                </span>
            </div>
        </div>

        <!-- Description -->
        <div class="space-y-3 max-w-lg mx-auto">
            <h2 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight font-heading">
                FORBIDDEN ENDPOINT
            </h2>
            <p class="text-xs sm:text-sm text-neutral-300 leading-relaxed font-medium">
                You do not possess security permissions to view this endpoint.
            </p>
            <div>
                <span class="text-xs font-mono text-rose-400 bg-black/80 px-4 py-2 rounded-xl border border-rose-500/30 inline-flex items-center gap-2 break-all max-w-full">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    {{ request()->url() }}
                </span>
            </div>
        </div>

        <!-- Compact Proportioned Action Buttons -->
        <div class="pt-4 max-w-md mx-auto flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ url('/') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-black text-xs uppercase tracking-wider hover:scale-105 transition shadow-lg flex items-center justify-center gap-2 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                RETURN TO HOME
            </a>

            @auth
                <a href="{{ route('user.dashboard') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-neutral-900 border border-emerald-500/40 text-emerald-400 font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition flex items-center justify-center gap-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    MEMBER DASHBOARD
                </a>
            @else
                <a href="{{ route('user.login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-neutral-900 border border-emerald-500/40 text-emerald-400 font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition flex items-center justify-center gap-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    MEMBER LOGIN
                </a>
            @endauth
        </div>

        <!-- Public Quick Links ONLY -->
        <div class="pt-6 border-t border-rose-500/20 flex flex-wrap items-center justify-center gap-6 text-xs text-neutral-400 font-semibold">
            <a href="{{ route('user.login') }}" class="hover:text-emerald-400 transition flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Member Login
            </a>
            <a href="{{ route('user.register') }}" class="hover:text-emerald-400 transition flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Register Account
            </a>
        </div>

    </main>

    <!-- Footer -->
    <footer class="w-full max-w-5xl py-4 text-center border-t border-rose-500/20">
        <p class="text-xs text-neutral-400 font-semibold font-mono">
            ZIVO PAY SECURITY GATEWAY • ALL RIGHTS RESERVED © {{ date('Y') }}
        </p>
    </footer>

</body>

</html>
