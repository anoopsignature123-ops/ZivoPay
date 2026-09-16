@extends('user.auth.app')

@section('title', 'ZIVO PAY - Member Registration')

@section('content')
    <div class="min-h-screen flex items-center justify-center p-4 relative font-sans">

        <!-- Background Decorator Overlay -->
        <div
            class="fixed inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-emerald-500/15 via-bg/95 to-bg pointer-events-none">
        </div>

        @if(!isset($showModal) || !$showModal)
            <!-- Main Registration Form Container (Hidden when Modal is active) -->
            <div class="w-full max-w-lg space-y-6 relative z-10 my-8">

                <!-- Header Brand Logo -->
                <div class="text-center">
                    <a href="{{ url('/') }}" class="inline-block group">
                        <div class="flex items-center justify-center p-3 sm:p-4 rounded-2xl bg-[#042718] border border-emerald-500/40 shadow-2xl">
                            <img src="{{ asset('images/logo_full.png') }}?v=1000" alt="ZIVO PAY" class="h-16 sm:h-20 w-auto max-w-[240px] sm:max-w-[280px] object-contain drop-shadow-[0_4px_20px_rgba(16,185,129,0.7)]">
                        </div>
                    </a>
                </div>

                <!-- Form Card -->
                <div class="p-6 sm:p-8 border border-emerald-500/30 shadow-2xl space-y-6 relative bg-[#042718] rounded-3xl">
                    <div class="text-center space-y-1">
                        @if(Auth::check())
                            <span class="px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 text-[10px] font-black uppercase tracking-widest border border-teal-500/40">
                                DOWNLINE REGISTRATION MODE
                            </span>
                            <h2 class="text-2xl font-black text-white uppercase tracking-tight font-heading mt-2">REGISTER NEW DOWNLINE MEMBER</h2>
                            <p class="text-xs text-neutral-300">Register new member sponsored under your network ({{ Auth::user()->referral_code }})</p>
                        @else
                            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-black uppercase tracking-widest border border-emerald-500/40">
                                CREATE NEW ACCOUNT
                            </span>
                            <h2 class="text-2xl font-black text-white uppercase tracking-tight font-heading mt-2">MEMBER REGISTRATION</h2>
                            <p class="text-xs text-neutral-300">Join Zivo E-Commerce & Financial Services Ecosystem</p>
                        @endif
                    </div>

                    <!-- Global Validation Error Messages Alert -->
                    @if($errors->any())
                        <div class="p-4 rounded-2xl bg-rose-500/20 border border-rose-500/50 text-rose-300 text-xs font-bold space-y-1 shadow-md">
                            @foreach($errors->all() as $error)
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                    <span>{{ $error }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Form -->
                    <form action="{{ route('user.register') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Sponsor ID Input & Live Verification Box -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-emerald-400 uppercase">Sponsor Code / ID *</label>
                                @if(isset($isLockedSponsor) && $isLockedSponsor)
                                    <span class="text-[10px] font-black text-emerald-300 bg-emerald-500/20 px-2 py-0.5 rounded border border-emerald-500/40 uppercase flex items-center gap-1">
                                        🔒 Locked Sponsor
                                    </span>
                                @endif
                            </div>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="sponsorInput"
                                    name="sponsor_id" 
                                    value="{{ old('sponsor_id', $sponsor ?? '') }}" 
                                    {{ (isset($isLockedSponsor) && $isLockedSponsor) ? 'readonly' : '' }}
                                    placeholder="Enter Sponsor Code (e.g. ZIVO-0000001)" 
                                    class="w-full px-4 py-3 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400 {{ (isset($isLockedSponsor) && $isLockedSponsor) ? 'opacity-85 cursor-not-allowed bg-emerald-500/10' : '' }}">
                            </div>
                            @error('sponsor_id')
                                <p class="text-[11px] text-rose-400 font-bold mt-1">{{ $message }}</p>
                            @enderror

                            <!-- Live Sponsor Info Card AJAX output -->
                            <div id="sponsorInfoBox" class="mt-2 hidden p-3 rounded-xl border text-xs font-medium transition-all">
                                <!-- Populated by JS -->
                            </div>
                        </div>

                        <!-- Full Name & Email -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Full Name *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. John Doe"
                                    class="w-full px-4 py-3 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">
                                @error('name')
                                    <p class="text-[11px] text-rose-400 font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Email Address *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required placeholder="john@example.com"
                                    class="w-full px-4 py-3 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">
                                @error('email')
                                    <p class="text-[11px] text-rose-400 font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Mobile Phone *</label>
                            <input type="text" name="mobile" value="{{ old('mobile') }}" required placeholder="e.g. +91 9876543210"
                                class="w-full px-4 py-3 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">
                            @error('mobile')
                                <p class="text-[11px] text-rose-400 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password & Confirm Password -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Account Password *</label>
                                <div class="relative">
                                    <input type="password" id="regPassword" name="password" required placeholder="••••••••"
                                        class="w-full pl-4 pr-12 py-3 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">

                                    <button type="button" onclick="togglePassVisibility('regPassword', 'regEyeOpen', 'regEyeClosed')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-emerald-400 hover:text-emerald-300 transition focus:outline-none"
                                        style="position: absolute !important; top: 50% !important; right: 0.75rem !important; transform: translateY(-50%) !important;"
                                        aria-label="Toggle Password Visibility">
                                        <svg id="regEyeOpen" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <svg id="regEyeClosed" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden text-emerald-300" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                                            <line x1="2" y1="2" x2="22" y2="22" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="text-[11px] text-rose-400 font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Confirm Password *</label>
                                <div class="relative">
                                    <input type="password" id="regPasswordConfirm" name="password_confirmation" required placeholder="••••••••"
                                        class="w-full pl-4 pr-12 py-3 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">

                                    <button type="button" onclick="togglePassVisibility('regPasswordConfirm', 'regConfirmEyeOpen', 'regConfirmEyeClosed')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-emerald-400 hover:text-emerald-300 transition focus:outline-none"
                                        style="position: absolute !important; top: 50% !important; right: 0.75rem !important; transform: translateY(-50%) !important;"
                                        aria-label="Toggle Password Visibility">
                                        <svg id="regConfirmEyeOpen" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <svg id="regConfirmEyeClosed" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden text-emerald-300"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                                            <line x1="2" y1="2" x2="22" y2="22" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Terms Checkbox -->
                        <div class="pt-1">
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-neutral-300">
                                <input type="checkbox" required checked class="w-4 h-4 rounded accent-emerald-500">
                                <span>I agree to the <a href="javascript:void(0)" class="text-emerald-400 font-bold hover:underline">Terms &
                                        Conditions</a> of ZIVO PAY.</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-black text-sm uppercase tracking-wider shadow-xl hover:from-emerald-400 hover:to-teal-500 transition flex items-center justify-center gap-2 mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <line x1="19" x2="19" y1="8" y2="14" />
                                <line x1="22" x2="16" y1="11" y2="11" />
                            </svg>
                            REGISTER & GET MEMBER ID
                        </button>
                    </form>

                    <div class="text-center pt-3 border-t border-emerald-500/20 flex items-center justify-between text-xs">
                        @if(Auth::check())
                            <a href="{{ route('user.dashboard') }}" class="text-emerald-400 font-black hover:underline flex items-center gap-1">
                                &larr; Return to Dashboard
                            </a>
                        @else
                            <p class="text-neutral-400">
                                Already have an account?
                                <a href="{{ route('user.login') }}" class="text-emerald-400 font-black hover:underline ml-1">LOG IN HERE</a>
                            </p>
                        @endif
                        <a href="{{ url('/') }}" class="text-neutral-300 font-bold hover:text-emerald-400 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-400" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                <polyline points="9 22 9 12 15 12 15 22" />
                            </svg>
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- CONGRATULATIONS SUCCESS MODAL POPUP (NO BACKGROUND FORM CLUTTER) -->
        @if(isset($showModal) && $showModal && isset($registeredUser))
            <div id="congratsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/95">
                <div
                    class="w-full max-w-md p-6 sm:p-8 border border-emerald-500/50 shadow-[0_0_60px_rgba(16,185,129,0.5)] text-center space-y-5 animate-fadeInUp my-auto bg-[#042718] rounded-3xl">

                    <!-- Trophy Badge -->
                    <div
                        class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center shadow-lg border-2 border-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-black" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5">
                            <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6" />
                            <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18" />
                            <path d="M4 22h16" />
                            <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22" />
                            <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22" />
                            <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z" />
                        </svg>
                    </div>

                    <div>
                        <span
                            class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-black uppercase tracking-widest border border-emerald-500/40">
                            CONGRATULATIONS!
                        </span>
                        <h3 class="text-2xl font-black text-white uppercase tracking-tight mt-2 font-heading">REGISTRATION
                            SUCCESSFUL</h3>
                        <p class="text-xs text-neutral-300 mt-1">Welcome to ZIVO PAY. Please save your login details
                            below.</p>
                    </div>

                    <!-- Credentials Box -->
                    <div class="p-4 rounded-xl bg-[#01140c] border border-emerald-500/40 text-left space-y-2 text-xs">
                        <div class="flex justify-between items-center pb-1.5 border-b border-emerald-500/20">
                            <span class="text-neutral-400">Referral / Member Code:</span>
                            <span id="copyUserId"
                                class="font-black text-emerald-400 text-sm tracking-wider">{{ $registeredUser['user_id'] }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-1.5 border-b border-emerald-500/20">
                            <span class="text-neutral-400">Sponsor Code:</span>
                            <span class="font-bold text-white">{{ $registeredUser['sponsor_id'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-400">Member Name:</span>
                            <span class="font-bold text-white">{{ $registeredUser['name'] }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-2 pt-1">
                        <button
                            onclick="copyDetails('{{ $registeredUser['user_id'] }}', '{{ $registeredUser['sponsor_id'] }}', '{{ $registeredUser['name'] }}')"
                            class="w-full py-3 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-300 font-bold text-xs uppercase tracking-wider hover:bg-emerald-500/30 transition flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                            </svg>
                            Copy Member Details
                        </button>

                        <a href="{{ route('user.dashboard') }}"
                            class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-black text-sm uppercase tracking-wider shadow-lg hover:from-emerald-400 hover:to-teal-500 transition flex items-center justify-center gap-2">
                            PROCEED TO DASHBOARD
                        </a>
                    </div>

                </div>
            </div>
        @endif

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sponsorInput = document.getElementById('sponsorInput');
            if (sponsorInput) {
                const verifySponsor = () => {
                    const code = sponsorInput.value.trim();
                    const box = document.getElementById('sponsorInfoBox');

                    if (!code) {
                        if (box) box.classList.add('hidden');
                        return;
                    }

                    fetch(`{{ route('user.check-sponsor') }}?code=${encodeURIComponent(code)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (!box) return;
                            box.classList.remove('hidden');
                            if (data.success) {
                                box.className = 'mt-2 p-3 rounded-xl border bg-emerald-500/10 border-emerald-500/40 text-emerald-400 text-xs font-semibold flex items-center justify-between';
                                box.innerHTML = `
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                        <div>
                                            <span class="font-black uppercase tracking-wider text-white block">✓ VERIFIED SPONSOR</span>
                                            <span class="text-emerald-300 font-bold">${data.name}</span>
                                            <span class="text-neutral-400 font-mono text-[11px] block">${data.email}</span>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-mono bg-black/50 px-2 py-1 rounded text-emerald-400 border border-emerald-500/30">${data.referral_code}</span>
                                `;
                            } else {
                                box.className = 'mt-2 p-3 rounded-xl border bg-rose-500/10 border-rose-500/40 text-rose-300 text-xs font-semibold flex items-center gap-2';
                                box.innerHTML = `
                                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                    <span>${data.message || 'Invalid Sponsor Code! User not found.'}</span>
                                `;
                            }
                        })
                        .catch(() => {
                            if (box) box.classList.add('hidden');
                        });
                };

                sponsorInput.addEventListener('input', verifySponsor);
                sponsorInput.addEventListener('change', verifySponsor);
                verifySponsor();
            }
        });

        function togglePassVisibility(inputId, openId, closedId) {
            const pass = document.getElementById(inputId);
            const openSvg = document.getElementById(openId);
            const closedSvg = document.getElementById(closedId);

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

        function copyDetails(userId, sponsorId, name) {
            const text = `ZIVO PAY Member Credentials:\nMember ID: ${userId}\nSponsor ID: ${sponsorId}\nMember Name: ${name}`;
            navigator.clipboard.writeText(text).then(() => {
                alert('Member details copied to clipboard!');
            });
        }
    </script>
@endsection
