<!-- Reusable User Management Form Partial -->
<div class="space-y-4">
    <!-- Full Name & Email -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Member Full Name *</label>
            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required placeholder="e.g. John Doe"
                class="w-full px-4 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">
        </div>

        <div>
            <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Email Address *</label>
            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                placeholder="member@example.com"
                class="w-full px-4 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">
        </div>
    </div>

    <!-- Phone & Sponsor Code -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Mobile Phone *</label>
            <input type="text" name="mobile" value="{{ old('mobile', $user->mobile ?? '') }}" required placeholder="+1 234 567 890"
                class="w-full px-4 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">
        </div>

        <div>
            <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Sponsor Code / ID *</label>
            <input type="text" id="adminSponsorInput" name="sponsor_code" value="{{ old('sponsor_code', $user->sponsor_code ?? '') }}" required
                placeholder="Enter Sponsor Code (e.g. ZIVO-0000001)"
                class="w-full px-4 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">
            <div id="adminSponsorInfoBox" class="mt-2 hidden p-3 rounded-xl border text-xs font-medium transition-all"></div>
        </div>
    </div>



    <!-- Password & Password Confirmation -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
        <div>
            <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">
                {{ isset($user) ? 'New Password' : 'Account Password *' }}
            </label>
            <div class="relative">
                <input type="password" id="uPass" name="password" {{ isset($user) ? '' : 'required' }} placeholder="••••••••"
                    class="w-full pl-4 pr-12 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">
                <button type="button" onclick="togglePassVisibility('uPass', 'uEye1', 'uEye2')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-emerald-400 hover:text-emerald-300">
                    <svg id="uEye1" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    <svg id="uEye2" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden text-emerald-300" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                        <line x1="2" y1="2" x2="22" y2="22" />
                    </svg>
                </button>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Confirm Password</label>
            <div class="relative">
                <input type="password" id="uConfirmPass" name="password_confirmation" {{ isset($user) ? '' : 'required' }}
                    placeholder="••••••••"
                    class="w-full pl-4 pr-12 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">
                <button type="button" onclick="togglePassVisibility('uConfirmPass', 'ucEye1', 'ucEye2')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-emerald-400 hover:text-emerald-300">
                    <svg id="ucEye1" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    <svg id="ucEye2" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden text-emerald-300" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                        <line x1="2" y1="2" x2="22" y2="22" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
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

    document.addEventListener('DOMContentLoaded', function () {
        const sponsorInput = document.getElementById('adminSponsorInput');
        if (sponsorInput) {
            const verifySponsor = () => {
                const code = sponsorInput.value.trim();
                const box = document.getElementById('adminSponsorInfoBox');

                if (!code) {
                    if (box) {
                        box.classList.add('hidden');
                        box.innerHTML = '';
                    }
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
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 font-bold text-xs flex items-center justify-center shrink-0">
                                        ${(data.name || 'S').charAt(0).toUpperCase()}
                                    </div>
                                    <div>
                                        <span class="font-black uppercase tracking-wider text-white block">✓ VERIFIED SPONSOR</span>
                                        <span class="text-emerald-300 font-bold">${data.name}</span>
                                        <span class="text-neutral-400 font-mono text-[11px] block">${data.email}</span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-mono bg-black/60 px-2.5 py-1 rounded-lg text-emerald-400 border border-emerald-500/30 font-bold">${data.referral_code}</span>
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
            if (sponsorInput.value.trim()) {
                verifySponsor();
            }
        }
    });
</script>