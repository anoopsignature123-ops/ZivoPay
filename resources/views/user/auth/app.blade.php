<!doctype html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>@yield('title', 'DEX TRADE - Auth Portal')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon.png') }}?v=2" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon.png') }}?v=2" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicon.png') }}?v=2" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/index.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dextrade-theme.css') }}" rel="stylesheet">
    <style>
        body.ng-auth-bg {
            background-color: #020b06 !important;
            background-image: 
                linear-gradient(to bottom, rgba(2, 22, 13, 0.82), rgba(1, 10, 5, 0.92)),
                url('{{ asset("images/auth_bg.jpg") }}') !important;
            background-size: cover !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            background-attachment: fixed !important;
            min-height: 100vh;
        }

        .ng-auth-card-shadow {
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.85), 0 0 35px rgba(243, 202, 82, 0.35);
        }
    </style>
    @stack('styles')
</head>

<body class="ng-auth-bg flex items-center justify-center p-4 sm:p-6 py-12 relative overflow-y-auto text-slate-100 font-sans min-h-screen">

    <!-- High-Tech Ambient Glowing Orbs -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-amber-500/15 blur-[160px]"></div>
        <div class="absolute bottom-10 right-10 w-[450px] h-[450px] rounded-full bg-emerald-500/15 blur-[140px]"></div>
    </div>

    <!-- Main Auth Content Area (Centered Vertically & Horizontally) -->
    <div class="w-full max-w-md relative z-20 my-auto">
        @yield('content')
    </div>

    <script src="{{ asset('js/app-validation.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if ($errors->any())
                showToast('Authentication Error', "{{ $errors->first() }}", 'error');
            @endif
            @if (session('success'))
                showToast('Success', "{{ session('success') }}", 'success');
            @endif
            @if (session('info'))
                showToast('Info', "{{ session('info') }}", 'info');
            @endif
        });
    </script>
    @stack('scripts')
</body>

</html>
