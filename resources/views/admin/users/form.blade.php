<!-- Reusable User Management Form Partial -->
<div class="space-y-4">
    <!-- Full Name & Email -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-amber-400 uppercase mb-1.5">Member Full Name *</label>
            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required placeholder="e.g. John Doe"
                class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-sm focus:outline-none focus:border-amber-400">
        </div>

        <div>
            <label class="block text-xs font-bold text-amber-400 uppercase mb-1.5">Email Address *</label>
            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                placeholder="member@example.com"
                class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-sm focus:outline-none focus:border-amber-400">
        </div>
    </div>

    <!-- Phone & Sponsor Code -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-amber-400 uppercase mb-1.5">Mobile Phone *</label>
            <input type="text" name="mobile" value="{{ old('mobile', $user->mobile ?? '') }}" required placeholder="+1 234 567 890"
                class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-sm focus:outline-none focus:border-amber-400">
        </div>

        <div>
            <label class="block text-xs font-bold text-amber-400 uppercase mb-1.5">Sponsor Code / ID *</label>
            <input type="text" name="sponsor_code" value="{{ old('sponsor_code', $user->sponsor_code ?? 'DEX-0000001') }}" required
                placeholder="e.g. DEX-0000001"
                class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-sm focus:outline-none focus:border-amber-400">
        </div>
    </div>

    <!-- Binary Placement Position (Left Leg vs Right Leg) -->
    <div>
        <label class="block text-xs font-bold text-amber-400 uppercase mb-1.5">Binary Position / Leg Placement *</label>
        <select name="position" required class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-bold text-sm focus:outline-none focus:border-amber-400 cursor-pointer">
            <option value="left" {{ old('position', $user->position ?? 'left') === 'left' ? 'selected' : '' }}>Left Leg (Team A)</option>
            <option value="right" {{ old('position', $user->position ?? 'left') === 'right' ? 'selected' : '' }}>Right Leg (Team B)</option>
        </select>
    </div>

    <!-- Password & Password Confirmation -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
        <div>
            <label class="block text-xs font-bold text-amber-400 uppercase mb-1.5">
                {{ isset($user) ? 'New Password' : 'Account Password *' }}
            </label>
            <div class="relative">
                <input type="password" id="uPass" name="password" {{ isset($user) ? '' : 'required' }} placeholder="••••••••"
                    class="w-full pl-4 pr-12 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-sm focus:outline-none focus:border-amber-400">
                <button type="button" onclick="togglePassVisibility('uPass', 'uEye1', 'uEye2')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-amber-400 hover:text-amber-300">
                    <svg id="uEye1" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    <svg id="uEye2" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden text-amber-300" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                        <line x1="2" y1="2" x2="22" y2="22" />
                    </svg>
                </button>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-amber-400 uppercase mb-1.5">Confirm Password</label>
            <div class="relative">
                <input type="password" id="uConfirmPass" name="password_confirmation" {{ isset($user) ? '' : 'required' }}
                    placeholder="••••••••"
                    class="w-full pl-4 pr-12 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-sm focus:outline-none focus:border-amber-400">
                <button type="button" onclick="togglePassVisibility('uConfirmPass', 'ucEye1', 'ucEye2')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-amber-400 hover:text-amber-300">
                    <svg id="ucEye1" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    <svg id="ucEye2" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden text-amber-300" viewBox="0 0 24 24"
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
</script>