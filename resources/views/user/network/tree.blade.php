@extends('user.layouts.app')

@section('content')
<div class="w-full space-y-6">
    <!-- Header Banner -->
    <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="pdf-num-badge">TT</span>
                <span class="text-xs text-emerald-400 font-extrabold tracking-[3px] uppercase">MY NETWORK</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-emerald-400 font-heading">MY DOWNLINE TEAM TREE</h1>
            <p class="text-xs text-neutral-300 mt-1">Interactive downline team structure hierarchy for your referral team.</p>
        </div>

        <div class="flex items-center gap-3 w-full lg:w-auto">
            <form action="{{ route('user.network.tree') }}" method="GET" class="flex items-center gap-2 w-full lg:w-auto">
                <div class="relative flex-1 lg:w-64">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-400"></i>
                    <input type="text" name="code" value="{{ request('code') }}" placeholder="Search referral code..." class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400">
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-black text-xs uppercase tracking-wider hover:from-emerald-400 hover:to-teal-500 transition shrink-0">
                    Find Tree Node
                </button>
            </form>
        </div>
    </div>

    <!-- Binary Tree Container Box -->
    <div class="bg-panel p-4 sm:p-6 shadow-2xl rounded-2xl border border-emerald-500/30 space-y-5">
        <!-- Render Reusable Binary Tree Component -->
        <x-binary-tree :treeData="$treeData" routePrefix="user" />
    </div>
</div>
@endsection
