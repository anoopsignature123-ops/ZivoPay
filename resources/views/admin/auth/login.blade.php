@extends('user.auth.app')

@section('title', 'ZIVO PAY - Admin Portal Login')

@section('content')
        <!-- Admin Login Form Card with Embedded Brand Logo -->
        <div
            class="ng-pkg-card p-6 sm:p-8 border-2 border-emerald-500/60 ng-auth-card-shadow space-y-6 relative bg-[#042718] rounded-3xl">
            <div class="text-center space-y-3">
                <a href="{{ url('/') }}" class="inline-block group mb-1">
                    <img src="{{ asset('images/logo.png') }}?v=1000" alt="ZIVO PAY"
                        class="h-16 sm:h-20 w-auto max-w-[240px] sm:max-w-[280px] mx-auto object-contain drop-shadow-[0_4px_20px_rgba(16,185,129,0.7)] group-hover:scale-105 transition-transform duration-200">
                </a>

    </div>

    <!-- Form -->
    <form action="{{ route('admin.login') }}" method="POST" class="space-y-4">
        @csrf
        <!-- Email Field -->
        <div>
            <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Email:</label>
            <div class="relative">
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-400 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="16" x="2" y="4" rx="2" />
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                    </svg>
                </div>
                <input type="text" name="email" value="{{ old('email') }}" required placeholder="Enter email..."
                    class="w-full pl-11 pr-4 py-3 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">
            </div>
        </div>

        <!-- Password Field -->
        <div>
            <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Password:</label>
            <div class="relative">
                <div
                    class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 flex items-center justify-center text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="5" y="10" width="14" height="11" rx="2" />
                        <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                    </svg>
                </div>

                <input type="password" id="adminPassword" name="password" required placeholder="Enter password..."
                    class="w-full pl-11 pr-12 py-3 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">

                <button type="button" onclick="togglePassword()"
                    class="absolute right-3.5 top-1/2 -translate-y-1/2 z-10 p-1 flex items-center justify-center text-emerald-400 hover:text-emerald-300 transition focus:outline-none cursor-pointer"
                    style="position: absolute !important; right: 0.875rem !important; left: auto !important; top: 70% !important; transform: translateY(-50%) !important;"
                    aria-label="Toggle Password Visibility">
                    <svg id="eyeIconOpen" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    <svg id="eyeIconClosed" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden text-emerald-300"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                        <line x1="2" y1="2" x2="22" y2="22" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Options -->
        <div class="flex items-center justify-between text-xs pt-1">
            <label class="flex items-center gap-2 cursor-pointer text-neutral-300">
                <input type="checkbox" name="remember" checked class="w-4 h-4 rounded accent-emerald-500">
                <span>Remember me</span>
            </label>
            <a href="javascript:void(0)" onclick="alert('Password reset link sent to admin email.');"
                class="text-emerald-400 font-bold hover:underline">Forgot password?</a>
        </div>

        <!-- Login Button -->
        <button type="submit"
            class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-black text-sm uppercase tracking-wider shadow-xl hover:from-emerald-400 hover:to-teal-500 transition flex items-center justify-center gap-2 mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path
                    d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                <path d="m9 12 2 2 4-4" />
            </svg>
            LOGIN TO ADMIN PANEL
        </button>
    </form>

            <div class="text-center pt-3 border-t border-emerald-500/20">
                <a href="{{ url('/') }}"
                    class="text-neutral-300 font-bold hover:text-emerald-400 inline-flex items-center gap-1.5 text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-400" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                    Back to Home Page
                </a>
            </div>
        </div>
@endsection

@push('scripts')
    <script>
        function togglePassword() {
            const pass = document.getElementById('adminPassword');
            const openSvg = document.getElementById('eyeIconOpen');
            const closedSvg = document.getElementById('eyeIconClosed');

            if (pass.type === 'password') {
                pass.type = 'text';
                openSvg.classList.add('hidden');
                closedSvg.classList.remove('hidden');
            } else {
                pass.type = 'password';
                openSvg.classList.remove('hidden');
                closedSvg.classList.add('hidden');
            }
        }
    </script>
@endpush
