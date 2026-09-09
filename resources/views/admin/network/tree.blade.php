@extends('admin.layouts.app')

@section('content')
<div class="w-full space-y-6">
    <!-- Header Banner -->
    <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="pdf-num-badge">TT</span>
                <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">DEX TRADE NETWORK</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient font-heading">BINARY TEAM TREE VIEW</h1>
            <p class="text-xs text-neutral-300 mt-1">Interactive binary placement tree node hierarchy.</p>
        </div>

        <div class="flex items-center gap-3 w-full lg:w-auto">
            <form action="{{ route('admin.network.tree') }}" method="GET" class="flex items-center gap-2 w-full lg:w-auto">
                <div class="relative flex-1 lg:w-64">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-amber-400"></i>
                    <input type="text" name="code" value="{{ request('code') }}" placeholder="Search referral code..." class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400">
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-amber-500 text-black font-black text-xs uppercase tracking-wider hover:bg-amber-400 transition shrink-0">
                    Find Tree Node
                </button>
            </form>
        </div>
    </div>

    <!-- Binary Tree Container Box -->
    <div class="bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30 space-y-6">
        <div class="flex items-center justify-between border-b border-amber-500/20 pb-4">
            <div class="flex items-center gap-2">
                <i data-lucide="git-merge" class="w-5 h-5 text-amber-400"></i>
                <span class="text-sm font-bold text-white uppercase">Root Node:</span>
                <span class="text-sm font-black text-amber-400 font-mono">{{ $rootUser->name }} ({{ $rootUser->referral_code }})</span>
            </div>

            <a href="{{ route('admin.network.tree') }}" class="px-3 py-1.5 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/40 hover:bg-amber-500/40 transition text-xs font-bold flex items-center gap-1">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Recenter Root Tree
            </a>
        </div>

        <!-- Render Reusable Binary Tree Component -->
        <x-binary-tree :treeData="$treeData" routePrefix="admin" />
    </div>
</div>
@endsection
