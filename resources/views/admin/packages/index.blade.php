@extends('admin.layouts.app')

@section('title', 'Capital Package Management - ZIVO PAY Admin')

@section('content')
<div class="w-full space-y-6 font-sans">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-slate-900/95 border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.3)] flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] uppercase border border-emerald-500/40">PKGM</span>
                <span class="text-xs text-emerald-400 font-black tracking-[3px] uppercase">ZIVO PAY CAPITAL PLANS</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white font-heading">CAPITAL PACKAGE MANAGEMENT</h1>
            <p class="text-xs text-neutral-300 mt-1">Configure investment tiers, daily ROI returns, direct referral bonuses, and 15-level team income settings.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.packages.create') }}" class="px-5 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-black font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 transition flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-black"></i>
                ADD NEW PACKAGE
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Package List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($packages as $pkg)
            <div class="bg-slate-900/95 p-6 rounded-3xl border-2 {{ $pkg->status === 'active' ? 'border-emerald-500/50 shadow-[0_0_20px_rgba(16,185,129,0.15)]' : 'border-neutral-700 opacity-60' }} flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase {{ $pkg->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-neutral-800 text-neutral-400' }}">
                            {{ strtoupper($pkg->status) }}
                        </span>
                        <span class="text-xs font-mono font-bold text-neutral-400">#PKG-{{ $pkg->id }}</span>
                    </div>

                    <h3 class="text-lg font-black text-white uppercase tracking-tight">{{ $pkg->name }}</h3>
                    <p class="text-xs text-neutral-400 mt-1 line-clamp-2">{{ $pkg->description }}</p>

                    <div class="mt-4 p-4 rounded-2xl bg-slate-950/80 border border-emerald-500/20 space-y-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-neutral-400 font-medium">Investment Range</span>
                            <span class="text-emerald-400 font-bold font-mono">₹{{ number_format($pkg->min_amount) }} - ₹{{ number_format($pkg->max_amount) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-neutral-400 font-medium">Daily ROI Return</span>
                            <span class="text-emerald-300 font-black font-mono">{{ number_format($pkg->daily_roi_percentage, 2) }}% / day</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-neutral-400 font-medium">Duration</span>
                            <span class="text-white font-bold">{{ $pkg->duration_days }} Days</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-neutral-400 font-medium">Direct Level 1 Bonus</span>
                            <span class="text-emerald-400 font-bold">{{ number_format($pkg->direct_bonus_percentage, 2) }}%</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-neutral-400 font-medium">Level 2-15 Team Bonus</span>
                            <span class="text-emerald-400 font-bold">{{ number_format($pkg->level_income_percentage, 2) }}% / level</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-emerald-500/20 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.packages.edit', $pkg->id) }}" class="flex-1 py-2 px-3 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 font-bold text-xs hover:bg-emerald-500/30 text-center transition">
                        Edit Plan
                    </a>

                    <form action="{{ route('admin.packages.toggle-status', $pkg->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="py-2 px-3 rounded-xl bg-slate-800 border border-neutral-700 text-neutral-300 font-bold text-xs hover:bg-slate-700 transition">
                            {{ $pkg->status === 'active' ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.packages.destroy', $pkg->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this package?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-400 hover:bg-rose-500/30 transition">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full p-8 text-center text-neutral-400 font-bold bg-slate-900/90 rounded-3xl border border-emerald-500/30">
                No capital packages configured yet. Click "ADD NEW PACKAGE" to create your first package plan.
            </div>
        @endforelse
    </div>

    @if($packages->hasPages())
        <div class="p-4 bg-slate-900/90 rounded-2xl border border-emerald-500/20">
            {{ $packages->links() }}
        </div>
    @endif
</div>
@endsection
