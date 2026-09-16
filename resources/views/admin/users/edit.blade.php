@extends('admin.layouts.app')

@section('content')
<div class="w-full space-y-6">
    <!-- Header Banner Full Width -->
    <div class="ng-banner-title p-6 sm:p-8 flex items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="pdf-num-badge">ED</span>
                <span class="text-xs text-emerald-400 font-extrabold tracking-[3px] uppercase">MEMBER MODIFICATION</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white font-heading">EDIT MEMBER: {{ $user->referral_code }}</h1>
            <p class="text-xs text-neutral-300 mt-1">Update profile information or reset password</p>
        </div>
        <a href="{{ route('admin.users') }}" class="px-4 py-2.5 rounded-xl bg-panel border border-emerald-500/40 text-emerald-300 font-bold text-xs uppercase tracking-wider hover:bg-emerald-500/20 transition flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Directory
        </a>
    </div>

    <!-- Form Card Compact Width Centered -->
    <div class="max-w-2xl mx-auto bg-panel p-6 sm:p-8 space-y-6 border border-emerald-500/30 rounded-2xl shadow-xl">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Reusable Form Partial -->
            @include('admin.users.form', ['user' => $user])

            <div class="pt-6 border-t border-emerald-500/20 flex items-center justify-end gap-3">
                <a href="{{ route('admin.users') }}" class="px-6 py-3.5 rounded-xl bg-neutral-900 border border-emerald-500/20 text-neutral-300 font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition">
                    CANCEL
                </a>
                <button type="submit" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-black text-xs uppercase tracking-wider shadow-lg hover:scale-102 transition flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4 text-white"></i>
                    UPDATE MEMBER DETAILS
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
