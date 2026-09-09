<!doctype html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>DEX TRADE - Admin Control Panel</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon.png') }}?v=2" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon.png') }}?v=2" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicon.png') }}?v=2" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2" />
    <link href="{{ asset('css/index.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dextrade-theme.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
</head>

<body id="dashboard" class="relative overflow-x-hidden min-h-screen text-slate-100">
    <!-- Admin Sidebar -->
    @include('admin.layouts.sidebar')

    <!-- Main Content Area -->
    <div class="main-content min-h-screen flex flex-col transition-all duration-300">
        <!-- Admin Header -->
        @include('admin.layouts.header')

        <!-- Page Content -->
        <main class="flex-1 p-3 sm:p-6 lg:p-8 space-y-6 inner-content">
            @yield('content')
        </main>
    </div>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('js/app-validation.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (window.lucide) {
                window.lucide.createIcons();
            }

            // Initialize Modern Flatpickr JS Datepickers Globally
            if (typeof flatpickr === 'function') {
                flatpickr('.datepicker, .js-datepicker', {
                    dateFormat: 'Y-m-d',
                    theme: 'dark',
                    allowInput: true,
                    disableMobile: "true"
                });
            }

            // Universal Sidebar Controller (Desktop Collapse + Mobile Drawer)
            const sidebarToggles = document.querySelectorAll('.js-sidebar-toggle');
            const mobileBtns = document.querySelectorAll('.js-mobile-menu-toggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            // Restore Desktop Sidebar Preference
            if (localStorage.getItem('dextrade_sidebar_collapsed') === 'true' && window.innerWidth >= 1024) {
                document.body.classList.add('sidebar-collapsed');
            }

            sidebarToggles.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (window.innerWidth >= 1024) {
                        document.body.classList.toggle('sidebar-collapsed');
                        const isCollapsed = document.body.classList.contains('sidebar-collapsed');
                        localStorage.setItem('dextrade_sidebar_collapsed', isCollapsed ? 'true' : 'false');
                    } else {
                        if (sidebar) sidebar.classList.toggle('mobile-sidebar-open');
                        if (overlay) {
                            overlay.classList.toggle('hidden');
                            overlay.classList.toggle('opacity-100');
                        }
                    }
                });
            });

            mobileBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (sidebar) sidebar.classList.toggle('mobile-sidebar-open');
                    if (overlay) {
                        overlay.classList.toggle('hidden');
                        overlay.classList.toggle('opacity-100');
                    }
                });
            });

            if (overlay) {
                overlay.addEventListener('click', function() {
                    if (sidebar) sidebar.classList.remove('mobile-sidebar-open');
                    overlay.classList.add('hidden');
                    overlay.classList.remove('opacity-100');
                });
            }

            // Trigger Global Toasts for Session Flashes
            @if (session('success'))
                showToast('Success', "{{ session('success') }}", 'success');
            @endif
            @if (session('error'))
                showToast('Error', "{{ session('error') }}", 'error');
            @endif
            @if (session('info'))
                showToast('Info', "{{ session('info') }}", 'info');
            @endif
        });
    </script>
</body>

</html>