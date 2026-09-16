@extends('admin.layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Title Banner -->
    <div class="ng-banner-title p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="pdf-num-badge">AD</span>
                <span class="text-xs text-emerald-400 font-extrabold tracking-[3px] uppercase">SECURITY & SETTINGS</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white font-heading">ADMIN PROFILE & PASSWORD MANAGEMENT</h1>
            <p class="text-xs text-neutral-300 mt-1">Manage master administrator account settings and security credentials</p>
        </div>
    </div>

    <!-- 2 Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Manage Profile Form -->
        <div class="bg-panel p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-emerald-500/20">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400">
                    <i data-lucide="user-cog" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-white font-heading">UPDATE PROFILE DETAILS</h2>
                    <p class="text-xs text-neutral-400">Modify administrator name, contact email and phone number</p>
                </div>
            </div>

            <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Admin Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $admin->name) }}" required class="w-full px-4 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Master Email Address *</label>
                    <input type="email" name="email" value="{{ old('email', $admin->email) }}" required class="w-full px-4 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Mobile Phone *</label>
                    <input type="text" name="mobile" value="{{ old('mobile', $admin->mobile) }}" required class="w-full px-4 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Admin Referral Code (Read Only)</label>
                    <input type="text" value="{{ $admin->referral_code }}" readonly disabled class="w-full px-4 py-3 rounded-xl bg-neutral-900 border border-emerald-500/20 text-emerald-400 font-black text-sm opacity-80 cursor-not-allowed">
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-black text-sm uppercase tracking-wider shadow-lg hover:scale-102 transition flex items-center justify-center gap-2 mt-2">
                    <i data-lucide="save" class="w-4 h-4 text-white"></i>
                    SAVE PROFILE CHANGES
                </button>
            </form>
        </div>

        <!-- Change Password Form -->
        <div class="bg-panel p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-emerald-500/20">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400">
                    <i data-lucide="key-round" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-white font-heading">CHANGE MASTER PASSWORD</h2>
                    <p class="text-xs text-neutral-400">Update admin security credentials and authentication password</p>
                </div>
            </div>

            <form action="{{ route('admin.password.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Current Password *</label>
                    <div class="relative">
                        <input type="password" id="curPass" name="current_password" required placeholder="••••••••" class="w-full pl-4 pr-12 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">
                        <button type="button" onclick="togglePassVisibility('curPass', 'curEye1', 'curEye2')" class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-emerald-400 hover:text-emerald-300">
                            <svg id="curEye1" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg id="curEye2" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">New Password *</label>
                    <div class="relative">
                        <input type="password" id="newPass" name="password" required placeholder="••••••••" class="w-full pl-4 pr-12 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">
                        <button type="button" onclick="togglePassVisibility('newPass', 'newEye1', 'newEye2')" class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-emerald-400 hover:text-emerald-300">
                            <svg id="newEye1" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg id="newEye2" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase mb-1.5">Confirm New Password *</label>
                    <div class="relative">
                        <input type="password" id="confirmPass" name="password_confirmation" required placeholder="••••••••" class="w-full pl-4 pr-12 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-sm focus:outline-none focus:border-emerald-400">
                        <button type="button" onclick="togglePassVisibility('confirmPass', 'confEye1', 'confEye2')" class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-emerald-400 hover:text-emerald-300">
                            <svg id="confEye1" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg id="confEye2" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-black text-sm uppercase tracking-wider shadow-lg hover:scale-102 transition flex items-center justify-center gap-2 mt-2">
                    <i data-lucide="shield-check" class="w-4 h-4 text-white"></i>
                    UPDATE PASSWORD
                </button>
            </form>
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
@endsection
