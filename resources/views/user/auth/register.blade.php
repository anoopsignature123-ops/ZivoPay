@extends('user.auth.app')

@section('title', 'DEX TRADE - Member Registration')

@section('content')
    <div class="min-h-screen flex items-center justify-center p-4 relative font-sans">

        <!-- Background Decorator Overlay -->
        <div
            class="fixed inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-amber-500/10 via-bg/95 to-bg pointer-events-none">
        </div>

        @if(!isset($showModal) || !$showModal)
            <!-- Main Registration Form Container (Hidden when Modal is active) -->
            <div class="w-full max-w-lg space-y-6 relative z-10 my-8">

                <!-- Header Brand Logo -->
                <div class="text-center space-y-3">
                    <a href="{{ url('/') }}" class="inline-block">
                        <img src="{{ asset('images/dextrade_logo.png') }}" alt="DEX TRADE Logo" class="h-16 sm:h-20 w-auto mx-auto object-contain drop-shadow-[0_0_20px_rgba(243,202,82,0.8)] hover:scale-105 transition duration-300">
                    </a>
                </div>

                <!-- Form Card -->
                <div
                    class="ng-pkg-card p-6 sm:p-8 border-2 border-amber-400 shadow-[0_0_40px_rgba(0,0,0,0.9)] space-y-6 relative backdrop-blur-xl">
                    <div class="text-center space-y-1">
                        <span
                            class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-[10px] font-black uppercase tracking-widest border border-amber-500/40">
                            CREATE NEW ACCOUNT
                        </span>
                        <h2 class="text-2xl font-black text-white uppercase tracking-tight font-heading mt-2">MEMBER REGISTRATION</h2>
                        <p class="text-xs text-neutral-400">Join the premier Dex Trade investment ecosystem</p>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('user.register') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Sponsor ID Input & Live Verification Box -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-amber-400 uppercase">Sponsor Code / ID</label>
                                @if(isset($isLockedSponsor) && $isLockedSponsor)
                                    <span class="text-[10px] font-black text-amber-300 bg-amber-500/20 px-2 py-0.5 rounded border border-amber-500/40 uppercase flex items-center gap-1">
                                        🔒 Locked via Referral Link
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
                                    placeholder="Enter Sponsor Code (e.g. NGF-0967542)" 
                                    class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-sm focus:outline-none focus:border-amber-400 {{ (isset($isLockedSponsor) && $isLockedSponsor) ? 'opacity-85 cursor-not-allowed bg-amber-500/5' : '' }}">
                            </div>

                            <!-- Live Sponsor Info Card AJAX output -->
                            <div id="sponsorInfoBox" class="mt-2 hidden p-3 rounded-xl border text-xs font-medium transition-all">
                                <!-- Populated by JS -->
                            </div>
                        </div>

                        <!-- Placement Position (Left Leg vs Right Leg) -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-amber-400 uppercase">Binary Position / Leg *</label>
                                @if(isset($isLockedPosition) && $isLockedPosition)
                                    <span class="text-[10px] font-black text-emerald-300 bg-emerald-500/20 px-2 py-0.5 rounded border border-emerald-500/40 uppercase flex items-center gap-1">
                                        ⚡ Selected via Link ({{ strtoupper($position ?? 'LEFT') }})
                                    </span>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative flex items-center justify-center p-3 rounded-xl border border-amber-500/40 bg-bg cursor-pointer hover:border-amber-400 transition group">
                                    <input type="radio" name="position" value="left" {{ old('position', $position ?? 'left') === 'left' ? 'checked' : '' }} class="peer hidden">
                                    <div class="flex items-center gap-2 text-white text-xs font-extrabold uppercase peer-checked:text-amber-300">
                                        <div class="w-4 h-4 rounded-full border-2 border-neutral-500 flex items-center justify-center peer-checked:border-amber-400 peer-checked:bg-amber-400">
                                            <div class="w-1.5 h-1.5 rounded-full bg-black"></div>
                                        </div>
                                        <span>Left Leg (Team A)</span>
                                    </div>
                                </label>
                                <label class="relative flex items-center justify-center p-3 rounded-xl border border-amber-500/40 bg-bg cursor-pointer hover:border-amber-400 transition group">
                                    <input type="radio" name="position" value="right" {{ old('position', $position ?? 'left') === 'right' ? 'checked' : '' }} class="peer hidden">
                                    <div class="flex items-center gap-2 text-white text-xs font-extrabold uppercase peer-checked:text-amber-300">
                                        <div class="w-4 h-4 rounded-full border-2 border-neutral-500 flex items-center justify-center peer-checked:border-amber-400 peer-checked:bg-amber-400">
                                            <div class="w-1.5 h-1.5 rounded-full bg-black"></div>
                                        </div>
                                        <span>Right Leg (Team B)</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Full Name & Email -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-amber-400 uppercase mb-1.5">Full Name *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. John Doe"
                                    class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-sm focus:outline-none focus:border-amber-400">
                                </div>

                            <div>
                                <label class="block text-xs font-bold text-amber-400 uppercase mb-1.5">Email Address *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required placeholder="john@example.com"
                                    class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-sm focus:outline-none focus:border-amber-400">
                                </div>
                                </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-xs font-bold text-amber-400 uppercase mb-1.5">Mobile Phone *</label>
                            <input type="text" name="mobile" value="{{ old('mobile') }}" required placeholder="+1 234 567 890"
                                class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-sm focus:outline-none focus:border-amber-400">
                            </div>

                        <!-- Password & Confirm Password -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-amber-400 uppercase mb-1.5">Account Password *</label>
                                <div class="relative">
                                    <input type="password" id="regPassword" name="password" required placeholder="••••••••"
                                        class="w-full pl-4 pr-12 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-sm focus:outline-none focus:border-amber-400">

                                    <button type="button" onclick="togglePassVisibility('regPassword', 'regEyeOpen', 'regEyeClosed')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-amber-400 hover:text-amber-300 transition focus:outline-none"
                                        aria-label="Toggle Password Visibility">
                                        <svg id="regEyeOpen" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <svg id="regEyeClosed" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden text-amber-300" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                                            <line x1="2" y1="2" x2="22" y2="22" />
                                        </svg>
                                        </button>
                                        </div>
                                        </div>

                            <div>
                                <label class="block text-xs font-bold text-amber-400 uppercase mb-1.5">Confirm Password *</label>
                                <div class="relative">
                                    <input type="password" id="regPasswordConfirm" name="password_confirmation" required placeholder="••••••••"
                                        class="w-full pl-4 pr-12 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-sm focus:outline-none focus:border-amber-400">

                                    <button type="button" onclick="togglePassVisibility('regPasswordConfirm', 'regConfirmEyeOpen', 'regConfirmEyeClosed')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-amber-400 hover:text-amber-300 transition focus:outline-none"
                                        aria-label="Toggle Password Visibility">
                                        <svg id="regConfirmEyeOpen" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <svg id="regConfirmEyeClosed" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden text-amber-300"
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
                                <input type="checkbox" required checked class="w-4 h-4 rounded accent-amber-500">
                                <span>I agree to the <a href="javascript:void(0)" class="text-amber-400 font-bold hover:underline">Terms &
                                        Conditions</a> of Dex Trade.</span>
                                </label>
                                </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full py-4 rounded-xl bg-amber-500 text-black font-black text-sm uppercase tracking-wider shadow-xl hover:scale-102 transition flex items-center justify-center gap-2 mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-black" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <line x1="19" x2="19" y1="8" y2="14" />
                                <line x1="22" x2="16" y1="11" y2="11" />
                            </svg>
                            REGISTER & GET MEMBER ID
                        </button>
                        </form>

                    <div class="text-center pt-3 border-t border-amber-500/20 flex items-center justify-between text-xs">
                        <p class="text-neutral-400">
                            Already have an account?
                            <a href="{{ route('user.login') }}" class="text-amber-400 font-black hover:underline ml-1">LOG IN HERE</a>
                        </p>
                        <a href="{{ url('/') }}" class="text-neutral-300 font-bold hover:text-amber-400 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-amber-400" viewBox="0 0 24 24" fill="none"
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
            <div id="congratsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/95 backdrop-blur-xl">
                <div
                    class="w-full max-w-md ng-pkg-card p-6 sm:p-8 border-2 border-amber-400 shadow-[0_0_60px_rgba(243,202,82,0.5)] text-center space-y-5 animate-fadeInUp my-auto">

                    <!-- Trophy Badge -->
                    <div
                        class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg border-2 border-white">
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
                            class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-[10px] font-black uppercase tracking-widest border border-amber-500/40">
                            CONGRATULATIONS!
                        </span>
                        <h3 class="text-2xl font-black text-white uppercase tracking-tight mt-2 font-heading">REGISTRATION
                            SUCCESSFUL</h3>
                        <p class="text-xs text-neutral-300 mt-1">Welcome to Dex Trade. Please save your login details
                            below.</p>
                    </div>

                    <!-- Credentials Box (CLEAN & NO TRANSACTION PIN) -->
                    <div class="p-4 rounded-xl bg-black/80 border border-amber-500/40 text-left space-y-2 text-xs">
                        <div class="flex justify-between items-center pb-1.5 border-b border-amber-500/20">
                            <span class="text-neutral-400">Referral / Member Code:</span>
                            <span id="copyUserId"
                                class="font-black text-amber-400 text-sm tracking-wider">{{ $registeredUser['user_id'] }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-1.5 border-b border-amber-500/20">
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
                            class="w-full py-3 rounded-xl bg-amber-500/20 border border-amber-500/50 text-amber-300 font-bold text-xs uppercase tracking-wider hover:bg-amber-500/30 transition flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                            </svg>
                            Copy Member Details
                        </button>

                        <a href="{{ route('user.dashboard') }}"
                            class="w-full py-3.5 rounded-xl bg-amber-500 text-black font-black text-sm uppercase tracking-wider shadow-lg hover:scale-102 transition flex items-center justify-center gap-2">
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
                                    <span class="text-[10px] font-mono bg-black/50 px-2 py-1 rounded text-amber-400 border border-amber-500/30">${data.referral_code}</span>
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
                const text = `Dex Trade Member Credentials:\nMember ID: ${userId}\nSponsor ID: ${sponsorId}\nMember Name: ${name}`;
                navigator.clipboard.writeText(text).then(() => {
                    alert('Member details copied to clipboard!');
                });
            }
        </script>
@endsection
