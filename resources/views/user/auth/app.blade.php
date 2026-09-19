<!doctype html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>@yield('title', 'ZIVO PAY - Auth Portal')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/fav.png') }}?v=10" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/fav.png') }}?v=10" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/fav.png') }}?v=10" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=10" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/index.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dextrade-theme.css') }}" rel="stylesheet">
    <style>
        body.ng-auth-bg {
            background-color: #021c10 !important;
            background-image: 
                linear-gradient(to bottom, rgba(2, 28, 16, 0.25), rgba(1, 15, 9, 0.45)),
                url('{{ asset("images/zivo_auth_bg.jpg") }}?v=6') !important;
            background-size: cover !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            background-attachment: fixed !important;
            min-height: 100vh;
        }

        .ng-auth-card-shadow {
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.85), 0 0 35px rgba(16, 185, 129, 0.35);
        }
    </style>
    @stack('styles')
</head>

<body class="ng-auth-bg min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 py-6 sm:py-10 relative overflow-y-auto text-slate-100 font-sans">

    <!-- Live Animated Background & Ambient Aurora Engine -->
    <div class="live-auth-bg-container"></div>
    <div class="aurora-glow-orb-1"></div>
    <div class="aurora-glow-orb-2"></div>

    <!-- Main Auth Content Area (Centered Vertically & Horizontally) -->
    <div class="w-full @yield('card_width', 'max-w-md sm:max-w-lg') relative z-20 my-auto py-2">
        @yield('content')
    </div>

    <script src="{{ asset('js/app-validation.js') }}"></script>
    <script src="{{ asset('js/pull-to-refresh.js') }}"></script>
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
