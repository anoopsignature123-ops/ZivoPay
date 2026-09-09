@extends('admin.layouts.app')

@section('content')
<style>
.gold-3d-badge {
    background: linear-gradient(180deg, #fef08a 0%, #f59e0b 50%, #b45309 100%) !important;
    border: 2px solid #fef08a !important;
    box-shadow: 0 4px 15px rgba(243, 202, 82, 0.4), inset 0 2px 4px rgba(255, 255, 255, 0.6) !important;
}

/* Force Exactly 3 Cards Per Row on Screens >= 768px */
@media (min-width: 768px) {
    .grid-3-cards {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 1.5rem !important;
    }
}
</style>

<div class="w-full space-y-6">
    <!-- Header Banner (Matching User Management Module Exactly) -->
    <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="pdf-num-badge">PM</span>
                <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">DEX TRADE NETWORK</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient font-heading">PACKAGES MANAGEMENT MODULE</h1>
            <p class="text-xs text-neutral-300 mt-1">Configure investment tiers, daily ROI percentages, contract duration, and status.</p>
        </div>

        <!-- Right Side Header Controls -->
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <a href="{{ route('admin.packages.create') }}" class="px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-black font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 transition flex items-center gap-2 shrink-0">
                <i data-lucide="plus-circle" class="w-4 h-4 text-black"></i> ADD NEW PACKAGE
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Main Container Panel -->
    <div class="bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30 space-y-6">
        
        <!-- Filter Bar Header -->
        <div class="flex items-center justify-between border-b border-amber-500/20 pb-4">
            <div class="flex items-center gap-2.5">
                <span class="px-5 py-2.5 rounded-xl text-xs font-black bg-amber-500 text-black shadow-md flex items-center gap-2">
                    <i data-lucide="package" class="w-4 h-4"></i> System Packages ({{ $packages->count() }})
                </span>
            </div>
        </div>

        <!-- PACKAGES GRID CARDS (EXACTLY 3 CARDS PER ROW: .grid-3-cards) -->
        <div class="grid grid-cols-1 grid-3-cards gap-6">
            @foreach($packages as $pkg)
            <div class="p-6 rounded-3xl bg-bg border border-amber-500/40 space-y-4 relative overflow-hidden group flex flex-col justify-between hover:border-amber-400/80 transition shadow-xl">
                <div class="space-y-3">
                    
                    <!-- Top Row with 3D Gold Badge & Status Pill -->
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-2xl gold-3d-badge text-black flex items-center justify-center font-black text-lg shadow-md shrink-0">
                            @if($loop->index == 0)
                                <i data-lucide="coins" class="w-5 h-5"></i>
                            @elseif($loop->index == 1)
                                <i data-lucide="rocket" class="w-5 h-5"></i>
                            @elseif($loop->index == 2)
                                <i data-lucide="trending-up" class="w-5 h-5"></i>
                            @elseif($loop->index == 3)
                                <i data-lucide="globe" class="w-5 h-5"></i>
                            @else
                                <i data-lucide="trophy" class="w-5 h-5"></i>
                            @endif
                        </div>
                        <form action="{{ route('admin.packages.toggle-status', $pkg->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1 rounded-full text-[10px] font-black uppercase border transition {{ $pkg->status === 'active' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40 hover:bg-amber-500/30' : 'bg-rose-500/20 text-rose-300 border-rose-500/40 hover:bg-rose-500/30' }}">
                                {{ $pkg->status === 'active' ? 'ACTIVE NOW' : 'INACTIVE' }}
                            </button>
                        </form>
                    </div>

                    <!-- Package Title & Investment Range -->
                    <div>
                        <div class="text-xs font-extrabold text-neutral-300 uppercase tracking-wider">{{ $pkg->name }}</div>
                        <h3 class="text-2xl sm:text-3xl font-black text-gold-gradient font-mono mt-1">${{ number_format($pkg->min_amount, 0) }} - ${{ number_format($pkg->max_amount, 0) }}</h3>
                    </div>

                    <!-- Package Details Box -->
                    <div class="p-4 rounded-2xl bg-panel border border-amber-500/30 space-y-2 font-mono">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-neutral-400 font-sans font-bold">Daily ROI:</span>
                            <span class="text-amber-300 font-black text-sm">{{ number_format($pkg->daily_roi, 2) }}% / Day</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-neutral-400 font-sans font-bold">Duration:</span>
                            <span class="text-white font-black text-sm">{{ $pkg->duration_days }} Days</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-neutral-400 font-sans font-bold">Total Return:</span>
                            <span class="text-amber-300 font-black text-sm">{{ number_format($pkg->total_return_multiplier, 1) }}X Return</span>
                        </div>
                    </div>

                    <p class="text-[11px] text-neutral-400 leading-relaxed italic">
                        {{ $pkg->description ?? 'Official Dex Trade investment plan.' }}
                    </p>
                </div>

                <!-- Bottom Solid Gold Button -->
                <div class="pt-3 border-t border-amber-500/20">
                    <a href="{{ route('admin.packages.edit', $pkg->id) }}" class="w-full py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-black font-black text-xs uppercase tracking-wider shadow-lg hover:scale-[1.02] transition text-center flex items-center justify-center gap-1.5">
                        <i data-lucide="edit-3" class="w-4 h-4 text-black"></i> EDIT PACKAGE
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
