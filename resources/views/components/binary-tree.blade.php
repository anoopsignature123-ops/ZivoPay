@props(['treeData', 'routePrefix' => 'user'])

@php
    $root = $treeData['root'] ?? null;
    $directs = $treeData['directs'] ?? collect();
    $totalDirects = $treeData['total_directs'] ?? 0;
    $activeDirects = $treeData['active_directs'] ?? 0;

    // Helper closure to calculate user financial stats safely
    $getUserStats = function($u) {
        if (!$u) return [
            'sponsor_name' => 'SUPER ADMIN',
            'sponsor_code' => 'N/A',
            'active_invest' => '$0.00',
            'earning_wallet' => '$0.00',
            'daily_roi' => '$0.00',
            'direct_income' => '$0.00',
            'email' => 'N/A',
        ];

        $activeInvest = $u->userPackages ? $u->userPackages->where('status', 'active')->sum('invested_amount') : 0;
        $dailyRoi = $u->transactions ? $u->transactions->where('type', 'daily_roi')->sum('amount') : 0;
        $directInc = $u->transactions ? $u->transactions->where('type', 'direct_income')->sum('amount') : 0;
        
        return [
            'sponsor_name' => $u->sponsor ? $u->sponsor->name : ($u->sponsor_code ? $u->sponsor_code : 'No Sponsor'),
            'sponsor_code' => $u->sponsor_code ?? 'N/A',
            'active_invest' => '$' . number_format($activeInvest, 2),
            'earning_wallet' => '$' . number_format((float)($u->earning_wallet ?? 0), 2),
            'daily_roi' => '$' . number_format($dailyRoi, 2),
            'direct_income' => '$' . number_format($directInc, 2),
            'email' => $u->email ?? 'N/A',
        ];
    };

    $rootStats = $getUserStats($root);
@endphp

<style>
.genealogy-tree-wrapper {
    width: 100%;
    overflow-x: auto;
    padding: 1.5rem 0.5rem 2rem 0.5rem;
    -webkit-overflow-scrolling: touch;
}

@media (min-width: 768px) {
    .genealogy-tree-wrapper {
        padding: 5rem 1.5rem 3.5rem 1.5rem;
    }
}

.genealogy-tree {
    display: inline-block;
    min-width: 100%;
    text-align: center;
}

.genealogy-tree ul {
    padding-top: 16px;
    position: relative;
    transition: all 0.3s;
    display: flex;
    justify-content: center;
    margin: 0;
    padding-left: 0;
}

@media (min-width: 768px) {
    .genealogy-tree ul {
        padding-top: 24px;
    }
}

.genealogy-tree li {
    text-align: center;
    list-style-type: none;
    position: relative;
    padding: 16px 3px 0 3px;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
    align-items: center;
}

@media (min-width: 768px) {
    .genealogy-tree li {
        padding: 24px 8px 0 8px;
    }
}

/* Connectors using ::before & ::after */
.genealogy-tree li::before, .genealogy-tree li::after {
    content: '';
    position: absolute;
    top: 0;
    right: 50%;
    border-top: 2px solid #f3ca52;
    width: 50%;
    height: 16px;
}

@media (min-width: 768px) {
    .genealogy-tree li::before, .genealogy-tree li::after {
        height: 24px;
    }
}

.genealogy-tree li::after {
    right: auto;
    left: 50%;
    border-left: 2px solid #f3ca52;
}

/* Single child */
.genealogy-tree li:only-child::after, .genealogy-tree li:only-child::before {
    display: none;
}

.genealogy-tree li:only-child {
    padding-top: 0;
}

/* Remove left connector from first child and right connector from last child */
.genealogy-tree li:first-child::before, .genealogy-tree li:last-child::after {
    border: 0 none;
}

/* Round corners on connectors */
.genealogy-tree li:last-child::before {
    border-right: 2px solid #f3ca52;
    border-radius: 0 6px 0 0;
}

.genealogy-tree li:first-child::after {
    border-radius: 6px 0 0 0;
}

/* Vertical line from parent */
.genealogy-tree ul ul::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    border-left: 2px solid #f3ca52;
    width: 0;
    height: 16px;
}

@media (min-width: 768px) {
    .genealogy-tree ul ul::before {
        height: 24px;
    }
}

/* SLEEK UNCLIPPED DESKTOP TOOLTIP STYLING */
.node-card-wrapper {
    position: relative;
}

.node-tooltip {
    position: absolute;
    bottom: 108%;
    left: 50%;
    transform: translateX(-50%) translateY(-4px);
    background: linear-gradient(180deg, #051b11 0%, #010a06 100%);
    border: 2px solid #f59e0b;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.95), 0 0 35px rgba(245, 158, 11, 0.5);
    border-radius: 1.25rem;
    padding: 1.15rem;
    width: 300px;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: all 0.2s ease-in-out;
    z-index: 999999 !important;
    text-align: left;
}

/* Root Tooltip opens downwards to ensure 100% visibility */
.root-node-wrapper .node-tooltip {
    bottom: auto;
    top: 108%;
    transform: translateX(-50%) translateY(4px);
}

.node-card-wrapper:hover .node-tooltip {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

.node-card-wrapper:hover .node-tooltip:not(.root-tooltip) {
    transform: translateX(-50%) translateY(-10px);
}

.root-node-wrapper:hover .node-tooltip {
    transform: translateX(-50%) translateY(10px);
}

.gold-glowing-avatar {
    background: radial-gradient(circle at 35% 35%, #fff3a3 0%, #f59e0b 55%, #b45309 100%) !important;
    border: 2.5px solid #fef08a !important;
    box-shadow: 0 0 20px rgba(245, 158, 11, 0.9), 0 0 35px rgba(254, 240, 138, 0.5) !important;
    color: #000000 !important;
}
</style>

<div class="w-full space-y-6 select-none font-sans">

    <!-- TOP 4 GENEALOGY SUMMARY CARDS (Compact & Responsive Grid) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Card 1: USER NAME -->
        <div class="p-3.5 sm:p-4 rounded-2xl sm:rounded-3xl pdf-package-card relative overflow-hidden">
            <div class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-amber-400">ROOT USER NAME</div>
            <h3 class="text-base sm:text-xl font-black text-white font-heading mt-0.5 truncate">{{ $root ? $root->name : 'N/A' }}</h3>
        </div>

        <!-- Card 2: USER ID -->
        <div class="p-3.5 sm:p-4 rounded-2xl sm:rounded-3xl pdf-package-card relative overflow-hidden">
            <div class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-amber-400">REFERRAL CODE</div>
            <h3 class="text-base sm:text-xl font-black text-amber-300 font-mono tracking-wider mt-0.5">{{ $root ? $root->referral_code : 'N/A' }}</h3>
        </div>

        <!-- Card 3: TOTAL DIRECT MEMBERS -->
        <div class="p-3.5 sm:p-4 rounded-2xl sm:rounded-3xl pdf-package-card relative overflow-hidden">
            <div class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-emerald-400">TOTAL DIRECTS</div>
            <h3 class="text-base sm:text-xl font-black text-emerald-400 font-mono mt-0.5">{{ $totalDirects }} Members</h3>
        </div>

        <!-- Card 4: ACTIVE DIRECTS -->
        <div class="p-3.5 sm:p-4 rounded-2xl sm:rounded-3xl pdf-package-card relative overflow-hidden">
            <div class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-sky-400">ACTIVE DIRECTS</div>
            <h3 class="text-base sm:text-xl font-black text-sky-300 font-mono mt-0.5">{{ $activeDirects }} Active</h3>
        </div>
    </div>

    <!-- CANVAS HEADER TOOLBAR WITH DOWNLOAD IMAGE BUTTON -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 p-3.5 sm:p-4 rounded-2xl sm:rounded-3xl pdf-package-card">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
            <span class="text-xs font-black text-amber-300 uppercase tracking-wider font-heading">TEAM GENEALOGY TREE GRAPH</span>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <button type="button" 
                    onclick="downloadTreeImage()" 
                    id="downloadTreeBtn"
                    class="flex-1 sm:flex-none px-3.5 py-2 rounded-xl pdf-gold-ribbon font-black text-[11px] sm:text-xs flex items-center justify-center gap-1.5 transition shadow-md hover:scale-105 active:scale-95 cursor-pointer">
                <span>📸 Save Image</span>
            </button>

            <a href="{{ route($routePrefix . '.network.tree') }}" 
               class="flex-1 sm:flex-none px-3.5 py-2 rounded-xl bg-black/80 hover:bg-black border border-amber-500/40 text-amber-300 font-bold text-[11px] sm:text-xs flex items-center justify-center gap-1.5 transition shadow-md">
                <span>🎯 Recenter</span>
            </a>
        </div>
    </div>

    <!-- MAIN GENEALOGY TREE GRAPH CANVAS -->
    <div id="treeCanvasContainer" class="w-full rounded-2xl sm:rounded-3xl pdf-package-card relative">
        
        <div class="genealogy-tree-wrapper">
            <div class="genealogy-tree">
                <ul>
                    <li>
                        <!-- ROOT NODE CARD WITH FULL TOOLTIP -->
                        @if($root)
                            <div class="node-card-wrapper root-node-wrapper inline-block">
                                <!-- RICH HOVER TOOLTIP BOX FOR ROOT (OPENS BELOW ROOT CARD) -->
                                <div class="node-tooltip root-tooltip space-y-2 hidden md:block">
                                    <div class="font-black text-white border-b border-amber-500/40 pb-2 flex justify-between items-center">
                                        <span class="text-white font-black font-heading text-sm sm:text-base">{{ $root->name }}</span>
                                        <span class="text-xs text-amber-400 font-mono font-bold">{{ $root->referral_code }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-300 font-semibold">Sponsor:</span>
                                        <span class="text-white font-bold">{{ $rootStats['sponsor_name'] }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-300 font-semibold">Account Status:</span>
                                        <span class="font-black uppercase tracking-wider {{ $root->status === 'active' ? 'text-emerald-400' : 'text-rose-400' }}">{{ $root->status }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-300 font-semibold">Active Capital:</span>
                                        <span class="text-emerald-400 font-mono font-bold">{{ $rootStats['active_invest'] }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-300 font-semibold">Earning Wallet:</span>
                                        <span class="text-emerald-400 font-mono font-bold">{{ $rootStats['earning_wallet'] }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-300 font-semibold">Daily ROI Income:</span>
                                        <span class="text-amber-400 font-mono font-bold">{{ $rootStats['daily_roi'] }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-300 font-semibold">Direct Income:</span>
                                        <span class="text-amber-400 font-mono font-bold">{{ $rootStats['direct_income'] }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-300 font-semibold">Total Directs:</span>
                                        <span class="text-amber-400 font-mono font-bold">{{ $totalDirects }} Members</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs pt-1.5 border-t border-amber-500/20">
                                        <span class="text-slate-400 font-medium">Joined Date:</span>
                                        <span class="text-slate-200 font-mono font-bold">{{ $root->created_at ? $root->created_at->format('Y-m-d') : 'N/A' }}</span>
                                    </div>
                                </div>

                                <div class="p-2 sm:p-3.5 rounded-xl sm:rounded-2xl bg-[#02180e]/95 border-2 border-amber-400 shadow-[0_0_25px_rgba(243,202,82,0.35)] text-center w-32 sm:w-44 relative group cursor-pointer"
                                     onclick="openMobileModal('{{ addslashes($root->name) }}', '{{ $root->referral_code }}', '{{ addslashes($rootStats['sponsor_name']) }}', '{{ strtoupper($root->status) }}', '{{ $rootStats['active_invest'] }}', '{{ $rootStats['earning_wallet'] }}', '{{ $rootStats['daily_roi'] }}', '{{ $rootStats['direct_income'] }}', '{{ $totalDirects }}', '{{ $root->created_at ? $root->created_at->format('Y-m-d') : 'N/A' }}')">
                                    
                                    <!-- Circular Avatar with Status Dot -->
                                    <div class="relative w-10 h-10 sm:w-14 sm:h-14 mx-auto mb-1.5 sm:mb-2">
                                        <div class="w-full h-full rounded-full gold-glowing-avatar text-black font-black text-base sm:text-xl flex items-center justify-center font-heading">
                                            {{ strtoupper(substr($root->name, 0, 1)) }}
                                        </div>
                                        @if($root->status === 'active')
                                            <span class="w-3 h-3 sm:w-3.5 sm:h-3.5 rounded-full bg-emerald-400 border-2 border-slate-900 absolute top-0 right-0 shadow-[0_0_8px_#34d399]" title="Active User"></span>
                                        @else
                                            <span class="w-3 h-3 sm:w-3.5 sm:h-3.5 rounded-full bg-rose-500 border-2 border-slate-900 absolute top-0 right-0 shadow-[0_0_8px_#f43f5e]" title="Inactive User"></span>
                                        @endif
                                    </div>

                                    <!-- Name Pill -->
                                    <div class="px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full bg-black/90 border border-slate-700/80 text-[11px] sm:text-sm font-black text-white font-heading truncate max-w-[105px] sm:max-w-[150px] mx-auto mb-1 sm:mb-1.5 shadow-sm">
                                        {{ $root->name }}
                                    </div>

                                    <!-- Self ID -->
                                    <div class="text-[9.5px] sm:text-[11px] text-slate-300 font-mono font-medium">
                                        SelfID: <span class="text-white font-bold">{{ $root->referral_code }}</span>
                                    </div>

                                    <!-- Sponsor ID -->
                                    <div class="text-[9.5px] sm:text-[11px] text-amber-400 font-mono font-bold mt-0.5">
                                        SponsorID: <span class="text-amber-300">{{ $rootStats['sponsor_code'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- LEVEL 1: ALL DIRECT MEMBERS -->
                        @if($directs->count() > 0)
                            <ul>
                                @foreach($directs as $direct)
                                    @php $directStats = $getUserStats($direct); @endphp
                                    <li>
                                        <!-- DIRECT MEMBER CARD WITH RICH TOOLTIP -->
                                        <div class="node-card-wrapper inline-block">
                                            <!-- HOVER TOOLTIP BOX FOR DIRECT MEMBERS -->
                                            <div class="node-tooltip space-y-2 hidden md:block">
                                                <div class="font-black text-white border-b border-amber-500/40 pb-2 flex justify-between items-center">
                                                    <span class="text-white font-black font-heading text-sm sm:text-base">{{ $direct->name }}</span>
                                                    <span class="text-xs text-amber-400 font-mono font-bold">{{ $direct->referral_code }}</span>
                                                </div>
                                                <div class="flex justify-between items-center text-xs">
                                                    <span class="text-slate-300 font-semibold">Sponsor:</span>
                                                    <span class="text-white font-bold">{{ $root->name }}</span>
                                                </div>
                                                <div class="flex justify-between items-center text-xs">
                                                    <span class="text-slate-300 font-semibold">Account Status:</span>
                                                    <span class="font-black uppercase tracking-wider {{ $direct->status === 'active' ? 'text-emerald-400' : 'text-rose-400' }}">{{ $direct->status }}</span>
                                                </div>
                                                <div class="flex justify-between items-center text-xs">
                                                    <span class="text-slate-300 font-semibold">Active Capital:</span>
                                                    <span class="text-emerald-400 font-mono font-bold">{{ $directStats['active_invest'] }}</span>
                                                </div>
                                                <div class="flex justify-between items-center text-xs">
                                                    <span class="text-slate-300 font-semibold">Earning Wallet:</span>
                                                    <span class="text-emerald-400 font-mono font-bold">{{ $directStats['earning_wallet'] }}</span>
                                                </div>
                                                <div class="flex justify-between items-center text-xs">
                                                    <span class="text-slate-300 font-semibold">Daily ROI Income:</span>
                                                    <span class="text-amber-400 font-mono font-bold">{{ $directStats['daily_roi'] }}</span>
                                                </div>
                                                <div class="flex justify-between items-center text-xs">
                                                    <span class="text-slate-300 font-semibold">Direct Income:</span>
                                                    <span class="text-amber-400 font-mono font-bold">{{ $directStats['direct_income'] }}</span>
                                                </div>
                                                <div class="flex justify-between items-center text-xs">
                                                    <span class="text-slate-300 font-semibold">Direct Referrals:</span>
                                                    <span class="text-amber-400 font-mono font-bold">{{ $direct->direct_members_count ?? 0 }} Members</span>
                                                </div>
                                                <div class="flex justify-between items-center text-xs pt-1.5 border-t border-amber-500/20">
                                                    <span class="text-slate-400 font-medium">Joined Date:</span>
                                                    <span class="text-slate-200 font-mono font-bold">{{ $direct->created_at ? $direct->created_at->format('Y-m-d') : 'N/A' }}</span>
                                                </div>
                                                <div class="pt-1.5 border-t border-amber-500/20 text-[11px] text-amber-400 font-bold text-center">
                                                    🔍 Click card to inspect this branch
                                                </div>
                                            </div>

                                            <div class="p-2 sm:p-3 rounded-xl sm:rounded-2xl bg-[#02180e]/90 border border-amber-500/50 hover:border-amber-400 hover:scale-[1.04] transition-all text-center w-28 sm:w-40 shadow-xl inline-block cursor-pointer relative"
                                                 onclick="handleCardClick(event, '{{ route($routePrefix . '.network.tree', ['code' => $direct->referral_code]) }}', '{{ addslashes($direct->name) }}', '{{ $direct->referral_code }}', '{{ addslashes($root->name) }}', '{{ strtoupper($direct->status) }}', '{{ $directStats['active_invest'] }}', '{{ $directStats['earning_wallet'] }}', '{{ $directStats['daily_roi'] }}', '{{ $directStats['direct_income'] }}', '{{ $direct->direct_members_count ?? 0 }}', '{{ $direct->created_at ? $direct->created_at->format('Y-m-d') : 'N/A' }}')">
                                                
                                                <!-- Circular Avatar with Status Dot -->
                                                <div class="relative w-9 h-9 sm:w-12 sm:h-12 mx-auto mb-1 sm:mb-1.5">
                                                    <div class="w-full h-full rounded-full gold-glowing-avatar text-black font-black text-sm sm:text-lg flex items-center justify-center font-heading">
                                                        {{ strtoupper(substr($direct->name, 0, 1)) }}
                                                    </div>
                                                    @if($direct->status === 'active')
                                                        <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-emerald-400 border-2 border-slate-900 absolute top-0 right-0 shadow-[0_0_6px_#34d399]" title="Active"></span>
                                                    @else
                                                        <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-rose-500 border-2 border-slate-900 absolute top-0 right-0 shadow-[0_0_6px_#f43f5e]" title="Inactive"></span>
                                                    @endif
                                                </div>

                                                <!-- Name Pill -->
                                                <div class="px-2 py-0.5 sm:px-2.5 sm:py-0.5 rounded-full bg-black/90 border border-slate-700/80 text-[10px] sm:text-xs font-bold text-white truncate max-w-[95px] sm:max-w-[130px] mx-auto mb-1 shadow-sm">
                                                    {{ $direct->name }}
                                                </div>

                                                <!-- Self ID -->
                                                <div class="text-[9px] sm:text-[10px] text-slate-300 font-mono font-medium">
                                                    SelfID: <span class="text-white font-bold">{{ $direct->referral_code }}</span>
                                                </div>

                                                <!-- Sponsor ID -->
                                                <div class="text-[9px] sm:text-[10px] text-amber-400 font-mono font-bold mt-0.5">
                                                    SponsorID: <span class="text-amber-300">{{ $root->referral_code }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- LEVEL 2: SUB-CHILDREN -->
                                        @if(isset($direct->sub_children) && $direct->sub_children->count() > 0)
                                            <ul>
                                                @foreach($direct->sub_children->take(4) as $sub)
                                                    @php $subStats = $getUserStats($sub); @endphp
                                                    <li>
                                                        <div class="node-card-wrapper inline-block">
                                                            <!-- HOVER TOOLTIP BOX FOR SUB-CHILDREN -->
                                                            <div class="node-tooltip space-y-2 hidden md:block">
                                                                <div class="font-black text-white border-b border-amber-500/40 pb-2 flex justify-between items-center">
                                                                    <span class="text-white font-black font-heading text-sm sm:text-base">{{ $sub->name }}</span>
                                                                    <span class="text-xs text-amber-400 font-mono font-bold">{{ $sub->referral_code }}</span>
                                                                </div>
                                                                <div class="flex justify-between items-center text-xs">
                                                                    <span class="text-slate-300 font-semibold">Sponsor:</span>
                                                                    <span class="text-white font-bold">{{ $direct->name }}</span>
                                                                </div>
                                                                <div class="flex justify-between items-center text-xs">
                                                                    <span class="text-slate-300 font-semibold">Account Status:</span>
                                                                    <span class="font-black uppercase tracking-wider {{ $sub->status === 'active' ? 'text-emerald-400' : 'text-rose-400' }}">{{ $sub->status }}</span>
                                                                </div>
                                                                <div class="flex justify-between items-center text-xs">
                                                                    <span class="text-slate-300 font-semibold">Active Capital:</span>
                                                                    <span class="text-emerald-400 font-mono font-bold">{{ $subStats['active_invest'] }}</span>
                                                                </div>
                                                                <div class="flex justify-between items-center text-xs">
                                                                    <span class="text-slate-300 font-semibold">Earning Wallet:</span>
                                                                    <span class="text-emerald-400 font-mono font-bold">{{ $subStats['earning_wallet'] }}</span>
                                                                </div>
                                                                <div class="flex justify-between items-center text-xs">
                                                                    <span class="text-slate-300 font-semibold">Daily ROI Income:</span>
                                                                    <span class="text-amber-400 font-mono font-bold">{{ $subStats['daily_roi'] }}</span>
                                                                </div>
                                                                <div class="flex justify-between items-center text-xs">
                                                                    <span class="text-slate-300 font-semibold">Direct Income:</span>
                                                                    <span class="text-amber-400 font-mono font-bold">{{ $subStats['direct_income'] }}</span>
                                                                </div>
                                                                <div class="flex justify-between items-center text-xs">
                                                                    <span class="text-slate-300 font-semibold">Direct Referrals:</span>
                                                                    <span class="text-amber-400 font-mono font-bold">{{ $sub->direct_members_count ?? 0 }} Members</span>
                                                                </div>
                                                                <div class="flex justify-between items-center text-xs pt-1.5 border-t border-amber-500/20">
                                                                    <span class="text-slate-400 font-medium">Joined Date:</span>
                                                                    <span class="text-slate-200 font-mono font-bold">{{ $sub->created_at ? $sub->created_at->format('Y-m-d') : 'N/A' }}</span>
                                                                </div>
                                                                <div class="pt-1.5 border-t border-amber-500/20 text-[11px] text-amber-400 font-bold text-center">
                                                                    🔍 Click card to inspect this branch
                                                                </div>
                                                            </div>

                                                            <div class="p-1.5 sm:p-2.5 rounded-lg sm:rounded-xl bg-[#02180e]/90 border border-amber-500/40 hover:border-amber-400 hover:scale-[1.04] transition-all text-center w-24 sm:w-34 shadow-lg inline-block cursor-pointer relative"
                                                                 onclick="handleCardClick(event, '{{ route($routePrefix . '.network.tree', ['code' => $sub->referral_code]) }}', '{{ addslashes($sub->name) }}', '{{ $sub->referral_code }}', '{{ addslashes($direct->name) }}', '{{ strtoupper($sub->status) }}', '{{ $subStats['active_invest'] }}', '{{ $subStats['earning_wallet'] }}', '{{ $subStats['daily_roi'] }}', '{{ $subStats['direct_income'] }}', '{{ $sub->direct_members_count ?? 0 }}', '{{ $sub->created_at ? $sub->created_at->format('Y-m-d') : 'N/A' }}')">
                                                                
                                                                <!-- Circular Avatar with Status Dot -->
                                                                <div class="relative w-8 h-8 sm:w-10 sm:h-10 mx-auto mb-1">
                                                                    <div class="w-full h-full rounded-full gold-glowing-avatar text-black font-black text-xs sm:text-sm flex items-center justify-center font-heading">
                                                                        {{ strtoupper(substr($sub->name, 0, 1)) }}
                                                                    </div>
                                                                    @if($sub->status === 'active')
                                                                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-emerald-400 border-2 border-slate-900 absolute top-0 right-0 shadow-[0_0_5px_#34d399]" title="Active"></span>
                                                                    @else
                                                                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-rose-500 border-2 border-slate-900 absolute top-0 right-0 shadow-[0_0_5px_#f43f5e]" title="Inactive"></span>
                                                                    @endif
                                                                </div>

                                                                <!-- Name Pill -->
                                                                <div class="px-2 py-0.5 rounded-full bg-black/90 border border-slate-700/80 text-[10px] sm:text-[11px] font-bold text-white truncate max-w-[90px] sm:max-w-[110px] mx-auto mb-1 shadow-sm">
                                                                    {{ $sub->name }}
                                                                </div>

                                                                <!-- Self ID -->
                                                                <div class="text-[9px] sm:text-[9.5px] text-slate-300 font-mono font-medium">
                                                                    SelfID: <span class="text-white font-bold">{{ $sub->referral_code }}</span>
                                                                </div>

                                                                <!-- Sponsor ID -->
                                                                <div class="text-[9px] sm:text-[9.5px] text-amber-400 font-mono font-bold mt-0.5">
                                                                    SponsorID: <span class="text-amber-300">{{ $direct->referral_code }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>

<!-- DETAILED MOBILE MEMBER INFO MODAL OVERLAY -->
<div id="mobileMemberModal" style="display: none;" class="fixed inset-0 z-[99999] bg-black/85 backdrop-blur-sm items-center justify-center p-4">
    <div class="relative w-full max-w-xs sm:max-w-sm p-5 rounded-3xl border-2 border-amber-400 shadow-[0_0_50px_rgba(243,202,82,0.4)] text-left space-y-3 text-xs" style="background-color: #07120a !important;">
        
        <!-- Header -->
        <div class="flex justify-between items-center pb-2.5 border-b border-amber-500/30">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-full pdf-gold-badge text-black font-black text-xs flex items-center justify-center shadow">ID</span>
                <div>
                    <h3 id="mobileModalName" class="font-black text-white text-sm font-heading">Member Name</h3>
                    <p id="mobileModalCode" class="text-[11px] text-amber-400 font-mono">NGF-0000000</p>
                </div>
            </div>
            <button type="button" onclick="closeMobileMemberModal()" class="w-7 h-7 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-300 hover:bg-rose-500/40 flex items-center justify-center font-black text-sm transition">
                ✕
            </button>
        </div>

        <!-- Details Rows -->
        <div class="space-y-2 py-1">
            <div class="flex justify-between items-center py-1 border-b border-amber-500/10">
                <span class="text-neutral-400 font-medium">Sponsor:</span>
                <span id="mobileModalSponsor" class="font-bold text-amber-300 font-mono">ROOT</span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-amber-500/10">
                <span class="text-neutral-400 font-medium">Status:</span>
                <span id="mobileModalStatus" class="font-black text-emerald-400 uppercase">ACTIVE</span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-amber-500/10">
                <span class="text-neutral-400 font-medium">Active Capital:</span>
                <span id="mobileModalActiveInvest" class="font-mono text-emerald-400 font-bold">$0.00</span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-amber-500/10">
                <span class="text-neutral-400 font-medium">Earning Wallet:</span>
                <span id="mobileModalEarningWallet" class="font-mono text-emerald-400 font-bold">$0.00</span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-amber-500/10">
                <span class="text-neutral-400 font-medium">Daily ROI Income:</span>
                <span id="mobileModalDailyRoi" class="font-mono text-amber-400 font-bold">$0.00</span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-amber-500/10">
                <span class="text-neutral-400 font-medium">Direct Income:</span>
                <span id="mobileModalDirectIncome" class="font-mono text-amber-400 font-bold">$0.00</span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-amber-500/10">
                <span class="text-neutral-400 font-medium">Direct Referrals:</span>
                <span id="mobileModalDirects" class="font-bold text-amber-400 font-mono">0 Members</span>
            </div>
            <div class="flex justify-between items-center py-1">
                <span class="text-neutral-400 font-medium">Joined Date:</span>
                <span id="mobileModalJoined" class="font-mono text-neutral-200">2026-01-01</span>
            </div>
        </div>

        <div class="pt-2 flex flex-col gap-2">
            <a id="mobileModalNavBtn" href="#" class="w-full py-2.5 rounded-xl pdf-gold-ribbon text-black font-black text-xs uppercase tracking-wider text-center block shadow">
                🔍 Inspect This Branch Tree
            </a>
            <button type="button" onclick="closeMobileMemberModal()" class="w-full py-2 rounded-xl bg-black/80 border border-white/40 text-neutral-300 font-bold text-xs uppercase tracking-wider">
                Close
            </button>
        </div>
    </div>
</div>

<!-- HTML2CANVAS SCRIPT FOR 1-CLICK TREE IMAGE EXPORT -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    function downloadTreeImage() {
        const btn = document.getElementById('downloadTreeBtn');
        const container = document.getElementById('treeCanvasContainer');
        if (!container) return;

        const originalText = btn.innerHTML;
        btn.innerHTML = '<span>⏳ Saving PNG...</span>';
        btn.disabled = true;

        html2canvas(container, {
            backgroundColor: '#0b161e',
            scale: 2,
            useCORS: true,
            logging: false
        }).then(canvas => {
            const image = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            const userCode = "{{ $root->referral_code ?? 'TREE' }}";
            link.download = `Dex_Trade_Genealogy_Tree_${userCode}.png`;
            link.href = image;
            link.click();

            btn.innerHTML = originalText;
            btn.disabled = false;
        }).catch(err => {
            console.error(err);
            alert('Could not capture tree image. Please try again.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    function handleCardClick(event, navUrl, name, code, sponsor, status, activeInvest, earningWallet, dailyRoi, directIncome, directs, joined) {
        if (window.innerWidth < 768) {
            event.preventDefault();
            event.stopPropagation();
            openMobileModal(name, code, sponsor, status, activeInvest, earningWallet, dailyRoi, directIncome, directs, joined, navUrl);
        } else {
            window.location.href = navUrl;
        }
    }

    function openMobileModal(name, code, sponsor, status, activeInvest, earningWallet, dailyRoi, directIncome, directs, joined, navUrl = '#') {
        document.getElementById('mobileModalName').textContent = name;
        document.getElementById('mobileModalCode').textContent = code;
        document.getElementById('mobileModalSponsor').textContent = sponsor;
        document.getElementById('mobileModalStatus').textContent = status;
        document.getElementById('mobileModalActiveInvest').textContent = activeInvest;
        document.getElementById('mobileModalEarningWallet').textContent = earningWallet;
        document.getElementById('mobileModalDailyRoi').textContent = dailyRoi;
        document.getElementById('mobileModalDirectIncome').textContent = directIncome;
        document.getElementById('mobileModalDirects').textContent = directs + ' Members';
        document.getElementById('mobileModalJoined').textContent = joined;
        
        const navBtn = document.getElementById('mobileModalNavBtn');
        if (navBtn) {
            navBtn.href = navUrl !== '#' ? navUrl : "{{ route($routePrefix . '.network.tree') }}?code=" + code;
        }

        const modal = document.getElementById('mobileMemberModal');
        if (modal) modal.style.display = 'flex';
    }

    function closeMobileMemberModal() {
        const modal = document.getElementById('mobileMemberModal');
        if (modal) modal.style.display = 'none';
    }

    document.addEventListener('click', function(e) {
        const modal = document.getElementById('mobileMemberModal');
        if (modal && e.target === modal) {
            closeMobileMemberModal();
        }
    });
</script>
