@props(['treeData', 'routePrefix' => 'user'])

@php
    $root = $treeData['root'] ?? null;
    $leftChild = $treeData['left_child'] ?? null;
    $rightChild = $treeData['right_child'] ?? null;

    $leftBusiness = $treeData['left_business'] ?? 0.00;
    $rightBusiness = $treeData['right_business'] ?? 0.00;
    $leftCount = $treeData['left_count'] ?? 0;
    $rightCount = $treeData['right_count'] ?? 0;
    $totalTeam = $treeData['total_team'] ?? 0;
    $totalBusiness = $treeData['total_business'] ?? 0.00;

    // Helper closure to calculate user financial stats safely
    $getUserStats = function($u) {
        if (!$u) return [
            'sponsor_name' => 'N/A',
            'sponsor_code' => 'N/A',
            'active_invest' => '$0.00',
            'earning_wallet' => '$0.00',
            'daily_roi' => '$0.00',
            'direct_income' => '$0.00',
            'email' => 'N/A',
            'directs_count' => 0,
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
            'directs_count' => \App\Models\User::where('sponsor_code', $u->referral_code)->count(),
        ];
    };

    $rootStats = $getUserStats($root);
@endphp

<style>
.genealogy-tree-wrapper {
    width: 100%;
    overflow-x: auto;
    padding: 1.5rem 0.5rem 2.5rem 0.5rem;
    -webkit-overflow-scrolling: touch;
}

@media (min-width: 768px) {
    .genealogy-tree-wrapper {
        padding: 3rem 1.5rem 3.5rem 1.5rem;
    }
}

.binary-tree-container {
    display: inline-block;
    min-width: 100%;
    text-align: center;
}

.binary-tree-container ul {
    padding-top: 20px;
    position: relative;
    transition: all 0.3s;
    display: flex;
    justify-content: center;
    margin: 0;
    padding-left: 0;
}

@media (min-width: 768px) {
    .binary-tree-container ul {
        padding-top: 26px;
    }
}

.binary-tree-container li {
    text-align: center;
    list-style-type: none;
    position: relative;
    padding: 20px 6px 0 6px;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
    align-items: center;
}

@media (min-width: 768px) {
    .binary-tree-container li {
        padding: 26px 12px 0 12px;
    }
}

/* Perfect Binary Tree Connector Lines (Dashed Yellow Lines matching Reference) */
.binary-tree-container li::before, .binary-tree-container li::after {
    content: '';
    position: absolute;
    top: 0;
    right: 50%;
    border-top: 1.5px dashed #f3ca52;
    width: 50%;
    height: 20px;
}

@media (min-width: 768px) {
    .binary-tree-container li::before, .binary-tree-container li::after {
        height: 26px;
    }
}

.binary-tree-container li::after {
    right: auto;
    left: 50%;
    border-left: 1.5px dashed #f3ca52;
}

.binary-tree-container li:only-child::after, .binary-tree-container li:only-child::before {
    display: none;
}

.binary-tree-container li:only-child {
    padding-top: 0;
}

.binary-tree-container li:first-child::before, .binary-tree-container li:last-child::after {
    border: 0 none;
}

.binary-tree-container li:last-child::before {
    border-right: 1.5px dashed #f3ca52;
    border-radius: 0 8px 0 0;
}

.binary-tree-container li:first-child::after {
    border-radius: 8px 0 0 0;
}

.binary-tree-container ul ul::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    border-left: 1.5px dashed #f3ca52;
    width: 0;
    height: 20px;
}

@media (min-width: 768px) {
    .binary-tree-container ul ul::before {
        height: 26px;
    }
}

/* Tooltip Hover Overlay */
.node-card-wrapper {
    position: relative;
}

.node-tooltip {
    position: absolute;
    bottom: 108%;
    left: 50%;
    transform: translateX(-50%) translateY(-4px);
    background: linear-gradient(180deg, #051b11 0%, #010a06 100%);
    border: 2px solid #f3ca52;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.95), 0 0 35px rgba(243, 202, 82, 0.45);
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

/* UNIFORM ELEGANT CARD STYLING MATCHING REFERENCE IMAGE EXCLUSIVELY */
.tree-node-card-root,
.tree-node-card-l1,
.tree-node-card-l2 {
    width: 135px;
    min-width: 135px;
    max-width: 135px;
    height: 148px;
    min-height: 148px;
    max-height: 148px;
    box-sizing: border-box;
    border: 2px solid rgba(243, 202, 82, 0.85) !important;
    border-radius: 0.85rem !important;
    background-color: rgba(8, 21, 16, 0.95) !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5) !important;
}
@media (min-width: 640px) {
    .tree-node-card-root,
    .tree-node-card-l1,
    .tree-node-card-l2 {
        width: 145px;
        min-width: 145px;
        max-width: 145px;
        height: 152px;
        min-height: 152px;
        max-height: 152px;
        box-sizing: border-box;
    }
}
</style>

<div class="w-full space-y-4 select-none font-sans">

    <!-- TOP 4 BINARY TEAM STATS CARDS (MATCHING REFERENCE IMAGE 3 LAYOUT WITH DEX TRADE THEME) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        <!-- Card 1: ROOT USER NAME -->
        <div class="p-3.5 rounded-xl bg-gradient-to-b from-[#063824] to-[#021d12] border border-amber-500/50 shadow-md flex flex-col justify-between">
            <h3 class="text-base sm:text-lg font-black text-amber-400 font-heading truncate">{{ $root ? $root->name : 'N/A' }}</h3>
            <span class="text-[9.5px] sm:text-[10.5px] font-black uppercase tracking-wider text-neutral-300 mt-1 block">USER NAME</span>
        </div>

        <!-- Card 2: ROOT USER ID -->
        <div class="p-3.5 rounded-xl bg-gradient-to-b from-[#063824] to-[#021d12] border border-amber-500/50 shadow-md flex flex-col justify-between">
            <h3 class="text-base sm:text-lg font-black text-amber-300 font-mono truncate">{{ $root ? $root->referral_code : 'N/A' }}</h3>
            <span class="text-[9.5px] sm:text-[10.5px] font-black uppercase tracking-wider text-neutral-300 mt-1 block">USER ID</span>
        </div>

        <!-- Card 3: LEFT BUSINESS -->
        <div class="p-3.5 rounded-xl bg-gradient-to-b from-[#063824] to-[#021d12] border border-amber-500/50 shadow-md flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <h3 class="text-base sm:text-lg font-black text-emerald-400 font-mono">${{ number_format($leftBusiness, 2) }}</h3>
                <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-[9px] font-mono font-bold">{{ $leftCount }} Members</span>
            </div>
            <span class="text-[9.5px] sm:text-[10.5px] font-black uppercase tracking-wider text-neutral-300 mt-1 block">👈 LEFT BUSINESS</span>
        </div>

        <!-- Card 4: RIGHT BUSINESS -->
        <div class="p-3.5 rounded-xl bg-gradient-to-b from-[#063824] to-[#021d12] border border-amber-500/50 shadow-md flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <h3 class="text-base sm:text-lg font-black text-emerald-400 font-mono">${{ number_format($rightBusiness, 2) }}</h3>
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[9px] font-mono font-bold">{{ $rightCount }} Members</span>
            </div>
            <span class="text-[9.5px] sm:text-[10.5px] font-black uppercase tracking-wider text-neutral-300 mt-1 block">RIGHT BUSINESS 👉</span>
        </div>

    </div>

    <!-- CANVAS HEADER TOOLBAR WITH DOWNLOAD IMAGE BUTTON & LEGEND -->
    <div class="w-full flex flex-row items-center justify-between gap-3 p-3.5 rounded-xl bg-gradient-to-b from-[#042115] to-[#010c07] border border-amber-500/40 shadow-md overflow-x-auto">
        <!-- Left Group: Title Badge & Legend -->
        <div class="flex items-center gap-2.5 shrink-0 whitespace-nowrap">
            <span class="px-3 py-1.5 rounded-full bg-amber-500/20 border border-amber-400/50 text-amber-300 font-black text-xs uppercase tracking-wider flex items-center gap-2 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span>BINARY TREE NAVIGATION</span>
            </span>
            <span class="text-neutral-600">|</span>
            <span class="text-amber-400 font-bold text-xs flex items-center gap-1">👈 Left Branch</span>
            <span class="text-neutral-600">|</span>
            <span class="text-amber-400 font-bold text-xs flex items-center gap-1">Right Branch 👉</span>
        </div>

        <!-- Right Group: Action Buttons -->
        <div class="flex items-center gap-2.5 shrink-0 whitespace-nowrap">
            <button type="button" 
                    onclick="downloadTreeImage()" 
                    id="downloadTreeBtn"
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-500 hover:from-amber-300 hover:to-amber-400 text-black font-black text-xs uppercase tracking-wider flex items-center justify-center gap-1.5 transition shadow cursor-pointer whitespace-nowrap">
                <span>📸 Save Image</span>
            </button>

            <a href="{{ route($routePrefix . '.network.tree') }}" 
               class="px-4 py-2 rounded-xl bg-black/90 hover:bg-black border border-amber-500/50 text-amber-300 font-bold text-xs flex items-center justify-center gap-1.5 transition shadow whitespace-nowrap">
                <span>🎯 Recenter Tree</span>
            </a>
        </div>
    </div>

    <!-- MAIN LEFT / RIGHT BINARY TREE GRAPH CANVAS -->
    <div id="treeCanvasContainer" class="w-full rounded-2xl bg-black/80 border border-amber-500/40 p-4 relative">
        
        <div class="genealogy-tree-wrapper">
            <div class="binary-tree-container">
                <ul>
                    <li>
                        <!-- LEVEL 0: ROOT NODE CARD -->
                        @if($root)
                            <div class="node-card-wrapper root-node-wrapper inline-block">
                                <!-- HOVER TOOLTIP FOR ROOT -->
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
                                        <span class="text-slate-300 font-semibold">Status:</span>
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
                                        <span class="text-slate-300 font-semibold">Left Team:</span>
                                        <span class="text-amber-400 font-mono font-bold">{{ $leftCount }} Members (${{ number_format($leftBusiness, 2) }})</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-300 font-semibold">Right Team:</span>
                                        <span class="text-amber-400 font-mono font-bold">{{ $rightCount }} Members (${{ number_format($rightBusiness, 2) }})</span>
                                    </div>
                                </div>

                                <div class="tree-node-card-root p-2.5 rounded-xl bg-[#081510]/95 border-2 border-[#f3ca52] hover:border-amber-400 hover:scale-[1.03] transition-all text-center flex flex-col items-center justify-between shadow-lg cursor-pointer relative group"
                                     onclick="openMobileModal('{{ addslashes($root->name) }}', '{{ $root->referral_code }}', '{{ addslashes($rootStats['sponsor_name']) }}', '{{ strtoupper($root->status) }}', '{{ $rootStats['active_invest'] }}', '{{ $rootStats['earning_wallet'] }}', '{{ $rootStats['daily_roi'] }}', '{{ $rootStats['direct_income'] }}', '{{ $leftCount }}L / {{ $rightCount }}R', '{{ $root->created_at ? $root->created_at->format('Y-m-d') : 'N/A' }}')">
                                    
                                    <!-- STATUS DOT AT TOP RIGHT OF CARD FRAME -->
                                    @if($root->status === 'active')
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 border border-slate-950 absolute top-2.5 right-2.5 shadow-[0_0_6px_#34d399]" title="Active User"></span>
                                    @else
                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 border border-slate-950 absolute top-2.5 right-2.5 shadow-[0_0_6px_#f43f5e]" title="Inactive User"></span>
                                    @endif

                                    <!-- CIRCULAR AVATAR -->
                                    <div class="w-8 h-8 rounded-full border border-slate-600 bg-slate-900/90 text-white font-black text-xs flex items-center justify-center mx-auto shadow-sm">
                                        {{ strtoupper(substr($root->name, 0, 1)) }}
                                    </div>

                                    <!-- NAME PILL -->
                                    <div class="w-full px-2 py-0.5 rounded-full bg-slate-900/90 border border-slate-700/80 text-[11px] font-bold text-white font-heading truncate mx-auto">
                                        {{ $root->name }}
                                    </div>

                                    <!-- SELF ID LINE -->
                                    <div class="text-[10px] text-slate-400 font-mono font-medium leading-tight whitespace-nowrap truncate w-full">
                                        SelfID: <span class="text-slate-200 font-semibold">{{ $root->referral_code }}</span>
                                    </div>

                                    <!-- SPONSOR ID SECTION -->
                                    <div class="w-full text-center leading-tight">
                                        <div class="text-[9.5px] text-amber-400 font-bold">SponsorID:</div>
                                        <div class="text-[10.5px] text-amber-300 font-mono font-bold tracking-wider truncate">{{ $rootStats['sponsor_code'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- LEVEL 1: LEFT CHILD vs RIGHT CHILD (STRICT 2 BRANCHES) -->
                        <ul>
                            <!-- 1. LEFT SIDE BRANCH -->
                            <li>
                                @if($leftChild)
                                    @php $leftStats = $getUserStats($leftChild); @endphp
                                    <div class="node-card-wrapper inline-block">
                                        <!-- HOVER TOOLTIP -->
                                        <div class="node-tooltip space-y-2 hidden md:block">
                                            <div class="font-black text-white border-b border-amber-500/40 pb-2 flex justify-between items-center">
                                                <span class="text-white font-black font-heading text-sm sm:text-base">{{ $leftChild->name }}</span>
                                                <span class="text-xs text-amber-400 font-mono font-bold">{{ $leftChild->referral_code }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-300 font-semibold">Position:</span>
                                                <span class="text-amber-300 font-black uppercase">👈 LEFT LEG</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-300 font-semibold">Sponsor:</span>
                                                <span class="text-white font-bold">{{ $root->name }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-300 font-semibold">Account Status:</span>
                                                <span class="font-black uppercase tracking-wider {{ $leftChild->status === 'active' ? 'text-emerald-400' : 'text-rose-400' }}">{{ $leftChild->status }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-300 font-semibold">Active Capital:</span>
                                                <span class="text-emerald-400 font-mono font-bold">{{ $leftStats['active_invest'] }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-300 font-semibold">Earning Wallet:</span>
                                                <span class="text-emerald-400 font-mono font-bold">{{ $leftStats['earning_wallet'] }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-300 font-semibold">Daily ROI Income:</span>
                                                <span class="text-amber-400 font-mono font-bold">{{ $leftStats['daily_roi'] }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs pt-1.5 border-t border-amber-500/20 text-[11px] text-amber-400 font-bold text-center">
                                                🔍 Click card to inspect Left Branch
                                            </div>
                                        </div>

                                        <div class="tree-node-card-l1 p-2.5 rounded-xl bg-[#081510]/95 border-2 border-[#f3ca52] hover:border-amber-400 hover:scale-[1.03] transition-all text-center flex flex-col items-center justify-between shadow-lg cursor-pointer relative group"
                                             onclick="handleCardClick(event, '{{ route($routePrefix . '.network.tree', ['code' => $leftChild->referral_code]) }}', '{{ addslashes($leftChild->name) }}', '{{ $leftChild->referral_code }}', '{{ addslashes($root->name) }}', '{{ strtoupper($leftChild->status) }}', '{{ $leftStats['active_invest'] }}', '{{ $leftStats['earning_wallet'] }}', '{{ $leftStats['daily_roi'] }}', '{{ $leftStats['direct_income'] }}', '{{ $leftStats['directs_count'] }}', '{{ $leftChild->created_at ? $leftChild->created_at->format('Y-m-d') : 'N/A' }}')">
                                            
                                            <!-- STATUS DOT AT TOP RIGHT OF CARD FRAME -->
                                            @if($leftChild->status === 'active')
                                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 border border-slate-950 absolute top-2.5 right-2.5 shadow-[0_0_6px_#34d399]" title="Active User"></span>
                                            @else
                                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 border border-slate-950 absolute top-2.5 right-2.5 shadow-[0_0_6px_#f43f5e]" title="Inactive User"></span>
                                            @endif

                                            <!-- CIRCULAR AVATAR -->
                                            <div class="w-8 h-8 rounded-full border border-slate-600 bg-slate-900/90 text-white font-black text-xs flex items-center justify-center mx-auto shadow-sm">
                                                {{ strtoupper(substr($leftChild->name, 0, 1)) }}
                                            </div>

                                            <!-- NAME PILL -->
                                            <div class="w-full px-2 py-0.5 rounded-full bg-slate-900/90 border border-slate-700/80 text-[11px] font-bold text-white font-heading truncate mx-auto">
                                                {{ $leftChild->name }}
                                            </div>

                                            <!-- SELF ID LINE -->
                                            <div class="text-[10px] text-slate-400 font-mono font-medium leading-tight whitespace-nowrap truncate w-full">
                                                SelfID: <span class="text-slate-200 font-semibold">{{ $leftChild->referral_code }}</span>
                                            </div>

                                            <!-- SPONSOR ID SECTION -->
                                            <div class="w-full text-center leading-tight">
                                                <div class="text-[9.5px] text-amber-400 font-bold">SponsorID:</div>
                                                <div class="text-[10.5px] text-amber-300 font-mono font-bold tracking-wider truncate">{{ $leftStats['sponsor_code'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- EMPTY LEFT SLOT CARD -->
                                    <div class="node-card-wrapper inline-block">
                                        <div class="node-tooltip space-y-1.5 hidden md:block">
                                            <div class="font-black text-amber-400 border-b border-amber-500/40 pb-1.5 flex justify-between items-center">
                                                <span class="font-heading text-xs uppercase">Vacant Position</span>
                                                <span class="text-[10px] text-emerald-400 font-mono font-bold">[ AVAILABLE ]</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-300 font-semibold">Position:</span>
                                                <span class="text-amber-300 font-bold font-mono">👈 LEFT LEG SLOT</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-300 font-semibold">Status:</span>
                                                <span class="text-emerald-400 font-bold">Open for Placement</span>
                                            </div>
                                            <div class="text-[10.5px] text-amber-300/90 italic pt-1 border-t border-amber-500/20">
                                                Direct referrals will be assigned to this branch leg.
                                            </div>
                                        </div>
                                        <div class="tree-node-card-l1 p-2 sm:p-2.5 rounded-2xl bg-black/70 border-2 border-dashed border-amber-500/50 text-center flex flex-col items-center justify-center relative">
                                            <div class="mb-1">
                                                <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-[8px] sm:text-[8.5px] font-black tracking-wider uppercase border border-amber-400/40">👈 LEFT SLOT</span>
                                            </div>
                                            <div class="w-9 h-9 sm:w-11 sm:h-11 mx-auto rounded-full border-2 border-dashed border-amber-400 text-amber-400 flex items-center justify-center font-bold text-sm sm:text-base mb-1 shadow-inner">
                                                +
                                            </div>
                                            <div class="w-full px-1.5 py-0.5 rounded-full bg-black/90 border border-dashed border-amber-500/40 text-[10px] sm:text-xs font-bold text-amber-300 truncate mb-0.5">
                                                VACANT SLOT
                                            </div>
                                            <div class="text-[8.5px] sm:text-[9.5px] text-amber-400/70 font-mono font-bold">
                                                [ AVAILABLE ]
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- LEVEL 2: LEFT GRANDCHILDREN (LEFT-LEFT & LEFT-RIGHT) -->
                                <ul>
                                    <!-- Left-Left Child -->
                                    <li>
                                        @if($leftChild && isset($leftChild->left_child) && $leftChild->left_child)
                                            @php $ll = $leftChild->left_child; $llStats = $getUserStats($ll); @endphp
                                            <div class="node-card-wrapper inline-block">
                                                <!-- HOVER TOOLTIP -->
                                                <div class="node-tooltip space-y-2 hidden md:block">
                                                    <div class="font-black text-white border-b border-amber-500/40 pb-2 flex justify-between items-center">
                                                        <span class="text-white font-black font-heading text-sm sm:text-base">{{ $ll->name }}</span>
                                                        <span class="text-xs text-amber-400 font-mono font-bold">{{ $ll->referral_code }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Position:</span>
                                                        <span class="text-amber-300 font-black uppercase">👈 L-LEFT LEG</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Sponsor:</span>
                                                        <span class="text-white font-bold">{{ $llStats['sponsor_name'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Account Status:</span>
                                                        <span class="font-black uppercase tracking-wider {{ $ll->status === 'active' ? 'text-emerald-400' : 'text-rose-400' }}">{{ $ll->status }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Active Capital:</span>
                                                        <span class="text-emerald-400 font-mono font-bold">{{ $llStats['active_invest'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Earning Wallet:</span>
                                                        <span class="text-emerald-400 font-mono font-bold">{{ $llStats['earning_wallet'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Daily ROI Income:</span>
                                                        <span class="text-amber-400 font-mono font-bold">{{ $llStats['daily_roi'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs pt-1.5 border-t border-amber-500/20 text-[11px] text-amber-400 font-bold text-center">
                                                        🔍 Click card to inspect Subtree
                                                    </div>
                                                </div>

                                                <div class="tree-node-card-l2 p-2.5 rounded-xl bg-[#081510]/95 border-2 border-[#f3ca52] hover:border-amber-400 hover:scale-[1.04] transition-all text-center flex flex-col items-center justify-between shadow-lg cursor-pointer relative group"
                                                     onclick="handleCardClick(event, '{{ route($routePrefix . '.network.tree', ['code' => $ll->referral_code]) }}', '{{ addslashes($ll->name) }}', '{{ $ll->referral_code }}', '{{ addslashes($llStats['sponsor_name']) }}', '{{ strtoupper($ll->status) }}', '{{ $llStats['active_invest'] }}', '{{ $llStats['earning_wallet'] }}', '{{ $llStats['daily_roi'] }}', '{{ $llStats['direct_income'] }}', '{{ $llStats['directs_count'] }}', '{{ $ll->created_at ? $ll->created_at->format('Y-m-d') : 'N/A' }}')">
                                                    
                                                    <!-- STATUS DOT AT TOP RIGHT OF CARD FRAME -->
                                                    @if($ll->status === 'active')
                                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 border border-slate-950 absolute top-2.5 right-2.5 shadow-[0_0_6px_#34d399]" title="Active User"></span>
                                                    @else
                                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 border border-slate-950 absolute top-2.5 right-2.5 shadow-[0_0_6px_#f43f5e]" title="Inactive User"></span>
                                                    @endif

                                                    <!-- CIRCULAR AVATAR -->
                                                    <div class="w-8 h-8 rounded-full border border-slate-600 bg-slate-900/90 text-white font-black text-xs flex items-center justify-center mx-auto shadow-sm">
                                                        {{ strtoupper(substr($ll->name, 0, 1)) }}
                                                    </div>

                                                    <!-- NAME PILL -->
                                                    <div class="w-full px-2 py-0.5 rounded-full bg-slate-900/90 border border-slate-700/80 text-[11px] font-bold text-white font-heading truncate mx-auto">
                                                        {{ $ll->name }}
                                                    </div>

                                                    <!-- SELF ID LINE -->
                                                    <div class="text-[10px] text-slate-400 font-mono font-medium leading-tight whitespace-nowrap truncate w-full">
                                                        SelfID: <span class="text-slate-200 font-semibold">{{ $ll->referral_code }}</span>
                                                    </div>

                                                    <!-- SPONSOR ID SECTION -->
                                                    <div class="w-full text-center leading-tight">
                                                        <div class="text-[9.5px] text-amber-400 font-bold">SponsorID:</div>
                                                        <div class="text-[10.5px] text-amber-300 font-mono font-bold tracking-wider truncate">{{ $llStats['sponsor_code'] }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="node-card-wrapper inline-block">
                                                <div class="node-tooltip space-y-1.5 hidden md:block">
                                                    <div class="font-black text-amber-400 border-b border-amber-500/40 pb-1.5 flex justify-between items-center">
                                                        <span class="font-heading text-xs uppercase">Vacant Slot</span>
                                                        <span class="text-[10px] text-emerald-400 font-mono font-bold">[ AVAILABLE ]</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Position:</span>
                                                        <span class="text-amber-300 font-bold font-mono">👈 L-LEFT</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Parent Node:</span>
                                                        <span class="text-white font-bold">{{ $leftChild ? $leftChild->name : 'N/A' }}</span>
                                                    </div>
                                                    <div class="text-[10.5px] text-amber-300/90 italic pt-1 border-t border-amber-500/20">
                                                        Open position for Left downline placement.
                                                    </div>
                                                </div>
                                                <div class="tree-node-card-l2 p-2 rounded-xl bg-black/70 border-2 border-dashed border-amber-500/50 text-center flex flex-col items-center justify-center relative">
                                                    <span class="text-[8px] text-amber-300 font-bold block mb-0.5">👈 L-LEFT</span>
                                                    <div class="w-7 h-7 sm:w-9 sm:h-9 mx-auto rounded-full border-2 border-dashed border-amber-400 text-amber-400 flex items-center justify-center font-bold text-xs mb-0.5">+</div>
                                                    <div class="w-full px-1 py-0.5 rounded-full bg-black/90 border border-dashed border-amber-500/40 text-[9px] sm:text-[10px] font-bold text-amber-300 truncate mb-0.5">VACANT</div>
                                                    <div class="text-[8px] sm:text-[9px] text-amber-400/70 font-mono font-bold">[ AVAILABLE ]</div>
                                                </div>
                                            </div>
                                        @endif
                                    </li>

                                    <!-- Left-Right Child -->
                                    <li>
                                        @if($leftChild && isset($leftChild->right_child) && $leftChild->right_child)
                                            @php $lr = $leftChild->right_child; $lrStats = $getUserStats($lr); @endphp
                                            <div class="node-card-wrapper inline-block">
                                                <!-- HOVER TOOLTIP -->
                                                <div class="node-tooltip space-y-2 hidden md:block">
                                                    <div class="font-black text-white border-b border-amber-500/40 pb-2 flex justify-between items-center">
                                                        <span class="text-white font-black font-heading text-sm sm:text-base">{{ $lr->name }}</span>
                                                        <span class="text-xs text-amber-400 font-mono font-bold">{{ $lr->referral_code }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Position:</span>
                                                        <span class="text-amber-300 font-black uppercase">L-RIGHT LEG 👉</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Sponsor:</span>
                                                        <span class="text-white font-bold">{{ $lrStats['sponsor_name'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Account Status:</span>
                                                        <span class="font-black uppercase tracking-wider {{ $lr->status === 'active' ? 'text-emerald-400' : 'text-rose-400' }}">{{ $lr->status }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Active Capital:</span>
                                                        <span class="text-emerald-400 font-mono font-bold">{{ $lrStats['active_invest'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Earning Wallet:</span>
                                                        <span class="text-emerald-400 font-mono font-bold">{{ $lrStats['earning_wallet'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Daily ROI Income:</span>
                                                        <span class="text-amber-400 font-mono font-bold">{{ $lrStats['daily_roi'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs pt-1.5 border-t border-amber-500/20 text-[11px] text-amber-400 font-bold text-center">
                                                        🔍 Click card to inspect Subtree
                                                    </div>
                                                </div>

                                                <div class="tree-node-card-l2 p-2.5 rounded-xl bg-[#081510]/95 border-2 border-[#f3ca52] hover:border-amber-400 hover:scale-[1.04] transition-all text-center flex flex-col items-center justify-between shadow-lg cursor-pointer relative group"
                                                     onclick="handleCardClick(event, '{{ route($routePrefix . '.network.tree', ['code' => $lr->referral_code]) }}', '{{ addslashes($lr->name) }}', '{{ $lr->referral_code }}', '{{ addslashes($lrStats['sponsor_name']) }}', '{{ strtoupper($lr->status) }}', '{{ $lrStats['active_invest'] }}', '{{ $lrStats['earning_wallet'] }}', '{{ $lrStats['daily_roi'] }}', '{{ $lrStats['direct_income'] }}', '{{ $lrStats['directs_count'] }}', '{{ $lr->created_at ? $lr->created_at->format('Y-m-d') : 'N/A' }}')">
                                                    
                                                    <!-- STATUS DOT AT TOP RIGHT OF CARD FRAME -->
                                                    @if($lr->status === 'active')
                                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 border border-slate-950 absolute top-2.5 right-2.5 shadow-[0_0_6px_#34d399]" title="Active User"></span>
                                                    @else
                                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 border border-slate-950 absolute top-2.5 right-2.5 shadow-[0_0_6px_#f43f5e]" title="Inactive User"></span>
                                                    @endif

                                                    <!-- CIRCULAR AVATAR -->
                                                    <div class="w-8 h-8 rounded-full border border-slate-600 bg-slate-900/90 text-white font-black text-xs flex items-center justify-center mx-auto shadow-sm">
                                                        {{ strtoupper(substr($lr->name, 0, 1)) }}
                                                    </div>

                                                    <!-- NAME PILL -->
                                                    <div class="w-full px-2 py-0.5 rounded-full bg-slate-900/90 border border-slate-700/80 text-[11px] font-bold text-white font-heading truncate mx-auto">
                                                        {{ $lr->name }}
                                                    </div>

                                                    <!-- SELF ID LINE -->
                                                    <div class="text-[10px] text-slate-400 font-mono font-medium leading-tight whitespace-nowrap truncate w-full">
                                                        SelfID: <span class="text-slate-200 font-semibold">{{ $lr->referral_code }}</span>
                                                    </div>

                                                    <!-- SPONSOR ID SECTION -->
                                                    <div class="w-full text-center leading-tight">
                                                        <div class="text-[9.5px] text-amber-400 font-bold">SponsorID:</div>
                                                        <div class="text-[10.5px] text-amber-300 font-mono font-bold tracking-wider truncate">{{ $lrStats['sponsor_code'] }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="node-card-wrapper inline-block">
                                                <div class="node-tooltip space-y-1.5 hidden md:block">
                                                    <div class="font-black text-amber-400 border-b border-amber-500/40 pb-1.5 flex justify-between items-center">
                                                        <span class="font-heading text-xs uppercase">Vacant Slot</span>
                                                        <span class="text-[10px] text-emerald-400 font-mono font-bold">[ AVAILABLE ]</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Position:</span>
                                                        <span class="text-amber-300 font-bold font-mono">L-RIGHT 👉</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Parent Node:</span>
                                                        <span class="text-white font-bold">{{ $leftChild ? $leftChild->name : 'N/A' }}</span>
                                                    </div>
                                                    <div class="text-[10.5px] text-amber-300/90 italic pt-1 border-t border-amber-500/20">
                                                        Open position for Left branch right leg placement.
                                                    </div>
                                                </div>
                                                <div class="tree-node-card-l2 p-2 rounded-xl bg-black/70 border-2 border-dashed border-amber-500/50 text-center flex flex-col items-center justify-center relative">
                                                    <span class="text-[8px] text-amber-300 font-bold block mb-0.5">L-RIGHT 👉</span>
                                                    <div class="w-7 h-7 sm:w-9 sm:h-9 mx-auto rounded-full border-2 border-dashed border-amber-400 text-amber-400 flex items-center justify-center font-bold text-xs mb-0.5">+</div>
                                                    <div class="w-full px-1 py-0.5 rounded-full bg-black/90 border border-dashed border-amber-500/40 text-[9px] sm:text-[10px] font-bold text-amber-300 truncate mb-0.5">VACANT</div>
                                                    <div class="text-[8px] sm:text-[9px] text-amber-400/70 font-mono font-bold">[ AVAILABLE ]</div>
                                                </div>
                                            </div>
                                        @endif
                                    </li>
                                </ul>
                            </li>

                            <!-- 2. RIGHT SIDE BRANCH -->
                            <li>
                                @if($rightChild)
                                    @php $rightStats = $getUserStats($rightChild); @endphp
                                    <div class="node-card-wrapper inline-block">
                                        <!-- HOVER TOOLTIP -->
                                        <div class="node-tooltip space-y-2 hidden md:block">
                                            <div class="font-black text-white border-b border-amber-500/40 pb-2 flex justify-between items-center">
                                                <span class="text-white font-black font-heading text-sm sm:text-base">{{ $rightChild->name }}</span>
                                                <span class="text-xs text-amber-400 font-mono font-bold">{{ $rightChild->referral_code }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-300 font-semibold">Position:</span>
                                                <span class="text-amber-300 font-black uppercase">RIGHT LEG 👉</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-300 font-semibold">Sponsor:</span>
                                                <span class="text-white font-bold">{{ $root->name }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-300 font-semibold">Account Status:</span>
                                                <span class="font-black uppercase tracking-wider {{ $rightChild->status === 'active' ? 'text-emerald-400' : 'text-rose-400' }}">{{ $rightChild->status }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-300 font-semibold">Active Capital:</span>
                                                <span class="text-emerald-400 font-mono font-bold">{{ $rightStats['active_invest'] }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-300 font-semibold">Earning Wallet:</span>
                                                <span class="text-emerald-400 font-mono font-bold">{{ $rightStats['earning_wallet'] }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-300 font-semibold">Daily ROI Income:</span>
                                                <span class="text-amber-400 font-mono font-bold">{{ $rightStats['daily_roi'] }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs pt-1.5 border-t border-amber-500/20 text-[11px] text-amber-400 font-bold text-center">
                                                🔍 Click card to inspect Right Branch
                                            </div>
                                        </div>

                                        <div class="tree-node-card-l1 p-2.5 rounded-xl bg-[#081510]/95 border-2 border-[#f3ca52] hover:border-amber-400 hover:scale-[1.03] transition-all text-center flex flex-col items-center justify-between shadow-lg cursor-pointer relative group"
                                             onclick="handleCardClick(event, '{{ route($routePrefix . '.network.tree', ['code' => $rightChild->referral_code]) }}', '{{ addslashes($rightChild->name) }}', '{{ $rightChild->referral_code }}', '{{ addslashes($root->name) }}', '{{ strtoupper($rightChild->status) }}', '{{ $rightStats['active_invest'] }}', '{{ $rightStats['earning_wallet'] }}', '{{ $rightStats['daily_roi'] }}', '{{ $rightStats['direct_income'] }}', '{{ $rightStats['directs_count'] }}', '{{ $rightChild->created_at ? $rightChild->created_at->format('Y-m-d') : 'N/A' }}')">
                                            
                                            <!-- STATUS DOT AT TOP RIGHT OF CARD FRAME -->
                                            @if($rightChild->status === 'active')
                                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 border border-slate-950 absolute top-2.5 right-2.5 shadow-[0_0_6px_#34d399]" title="Active User"></span>
                                            @else
                                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 border border-slate-950 absolute top-2.5 right-2.5 shadow-[0_0_6px_#f43f5e]" title="Inactive User"></span>
                                            @endif

                                            <!-- CIRCULAR AVATAR -->
                                            <div class="w-8 h-8 rounded-full border border-slate-600 bg-slate-900/90 text-white font-black text-xs flex items-center justify-center mx-auto shadow-sm">
                                                {{ strtoupper(substr($rightChild->name, 0, 1)) }}
                                            </div>

                                            <!-- NAME PILL -->
                                            <div class="w-full px-2 py-0.5 rounded-full bg-slate-900/90 border border-slate-700/80 text-[11px] font-bold text-white font-heading truncate mx-auto">
                                                {{ $rightChild->name }}
                                            </div>

                                            <!-- SELF ID LINE -->
                                            <div class="text-[10px] text-slate-400 font-mono font-medium leading-tight whitespace-nowrap truncate w-full">
                                                SelfID: <span class="text-slate-200 font-semibold">{{ $rightChild->referral_code }}</span>
                                            </div>

                                            <!-- SPONSOR ID SECTION -->
                                            <div class="w-full text-center leading-tight">
                                                <div class="text-[9.5px] text-amber-400 font-bold">SponsorID:</div>
                                                <div class="text-[10.5px] text-amber-300 font-mono font-bold tracking-wider truncate">{{ $rightStats['sponsor_code'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- EMPTY RIGHT SLOT CARD -->
                                    <div class="node-card-wrapper inline-block">
                                        <div class="node-tooltip space-y-1.5 hidden md:block">
                                            <div class="font-black text-amber-400 border-b border-amber-500/40 pb-1.5 flex justify-between items-center">
                                                <span class="font-heading text-xs uppercase">Vacant Position</span>
                                                <span class="text-[10px] text-emerald-400 font-mono font-bold">[ AVAILABLE ]</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-300 font-semibold">Position:</span>
                                                <span class="text-amber-300 font-bold font-mono">RIGHT LEG SLOT 👉</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-300 font-semibold">Status:</span>
                                                <span class="text-emerald-400 font-bold">Open for Placement</span>
                                            </div>
                                            <div class="text-[10.5px] text-amber-300/90 italic pt-1 border-t border-amber-500/20">
                                                Direct referrals will be assigned to this branch leg.
                                            </div>
                                        </div>
                                        <div class="tree-node-card-l1 p-2 sm:p-2.5 rounded-2xl bg-black/70 border-2 border-dashed border-amber-500/50 text-center flex flex-col items-center justify-center relative">
                                            <div class="mb-1">
                                                <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-[8px] sm:text-[8.5px] font-black tracking-wider uppercase border border-amber-400/40">RIGHT SLOT 👉</span>
                                            </div>
                                            <div class="w-9 h-9 sm:w-11 sm:h-11 mx-auto rounded-full border-2 border-dashed border-amber-400 text-amber-400 flex items-center justify-center font-bold text-sm sm:text-base mb-1 shadow-inner">
                                                +
                                            </div>
                                            <div class="w-full px-1.5 py-0.5 rounded-full bg-black/90 border border-dashed border-amber-500/40 text-[10px] sm:text-xs font-bold text-amber-300 truncate mb-0.5">
                                                VACANT SLOT
                                            </div>
                                            <div class="text-[8.5px] sm:text-[9.5px] text-amber-400/70 font-mono font-bold">
                                                [ AVAILABLE ]
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- LEVEL 2: RIGHT GRANDCHILDREN (RIGHT-LEFT & RIGHT-RIGHT) -->
                                <ul>
                                    <!-- Right-Left Child -->
                                    <li>
                                        @if($rightChild && isset($rightChild->left_child) && $rightChild->left_child)
                                            @php $rl = $rightChild->left_child; $rlStats = $getUserStats($rl); @endphp
                                            <div class="node-card-wrapper inline-block">
                                                <!-- HOVER TOOLTIP -->
                                                <div class="node-tooltip space-y-2 hidden md:block">
                                                    <div class="font-black text-white border-b border-amber-500/40 pb-2 flex justify-between items-center">
                                                        <span class="text-white font-black font-heading text-sm sm:text-base">{{ $rl->name }}</span>
                                                        <span class="text-xs text-amber-400 font-mono font-bold">{{ $rl->referral_code }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Position:</span>
                                                        <span class="text-amber-300 font-black uppercase">👈 R-LEFT LEG</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Sponsor:</span>
                                                        <span class="text-white font-bold">{{ $rlStats['sponsor_name'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Account Status:</span>
                                                        <span class="font-black uppercase tracking-wider {{ $rl->status === 'active' ? 'text-emerald-400' : 'text-rose-400' }}">{{ $rl->status }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Active Capital:</span>
                                                        <span class="text-emerald-400 font-mono font-bold">{{ $rlStats['active_invest'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Earning Wallet:</span>
                                                        <span class="text-emerald-400 font-mono font-bold">{{ $rlStats['earning_wallet'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Daily ROI Income:</span>
                                                        <span class="text-amber-400 font-mono font-bold">{{ $rlStats['daily_roi'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs pt-1.5 border-t border-amber-500/20 text-[11px] text-amber-400 font-bold text-center">
                                                        🔍 Click card to inspect Subtree
                                                    </div>
                                                </div>

                                                <div class="tree-node-card-l2 p-2.5 rounded-xl bg-[#081510]/95 border-2 border-[#f3ca52] hover:border-amber-400 hover:scale-[1.04] transition-all text-center flex flex-col items-center justify-between shadow-lg cursor-pointer relative group"
                                                     onclick="handleCardClick(event, '{{ route($routePrefix . '.network.tree', ['code' => $rl->referral_code]) }}', '{{ addslashes($rl->name) }}', '{{ $rl->referral_code }}', '{{ addslashes($rlStats['sponsor_name']) }}', '{{ strtoupper($rl->status) }}', '{{ $rlStats['active_invest'] }}', '{{ $rlStats['earning_wallet'] }}', '{{ $rlStats['daily_roi'] }}', '{{ $rlStats['direct_income'] }}', '{{ $rlStats['directs_count'] }}', '{{ $rl->created_at ? $rl->created_at->format('Y-m-d') : 'N/A' }}')">
                                                    
                                                    <!-- STATUS DOT AT TOP RIGHT OF CARD FRAME -->
                                                    @if($rl->status === 'active')
                                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 border border-slate-950 absolute top-2.5 right-2.5 shadow-[0_0_6px_#34d399]" title="Active User"></span>
                                                    @else
                                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 border border-slate-950 absolute top-2.5 right-2.5 shadow-[0_0_6px_#f43f5e]" title="Inactive User"></span>
                                                    @endif

                                                    <!-- CIRCULAR AVATAR -->
                                                    <div class="w-8 h-8 rounded-full border border-slate-600 bg-slate-900/90 text-white font-black text-xs flex items-center justify-center mx-auto shadow-sm">
                                                        {{ strtoupper(substr($rl->name, 0, 1)) }}
                                                    </div>

                                                    <!-- NAME PILL -->
                                                    <div class="w-full px-2 py-0.5 rounded-full bg-slate-900/90 border border-slate-700/80 text-[11px] font-bold text-white font-heading truncate mx-auto">
                                                        {{ $rl->name }}
                                                    </div>

                                                    <!-- SELF ID LINE -->
                                                    <div class="text-[10px] text-slate-400 font-mono font-medium leading-tight whitespace-nowrap truncate w-full">
                                                        SelfID: <span class="text-slate-200 font-semibold">{{ $rl->referral_code }}</span>
                                                    </div>

                                                    <!-- SPONSOR ID SECTION -->
                                                    <div class="w-full text-center leading-tight">
                                                        <div class="text-[9.5px] text-amber-400 font-bold">SponsorID:</div>
                                                        <div class="text-[10.5px] text-amber-300 font-mono font-bold tracking-wider truncate">{{ $rlStats['sponsor_code'] }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="node-card-wrapper inline-block">
                                                <div class="node-tooltip space-y-1.5 hidden md:block">
                                                    <div class="font-black text-amber-400 border-b border-amber-500/40 pb-1.5 flex justify-between items-center">
                                                        <span class="font-heading text-xs uppercase">Vacant Slot</span>
                                                        <span class="text-[10px] text-emerald-400 font-mono font-bold">[ AVAILABLE ]</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Position:</span>
                                                        <span class="text-amber-300 font-bold font-mono">👈 R-LEFT</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Parent Node:</span>
                                                        <span class="text-white font-bold">{{ $rightChild ? $rightChild->name : 'N/A' }}</span>
                                                    </div>
                                                    <div class="text-[10.5px] text-amber-300/90 italic pt-1 border-t border-amber-500/20">
                                                        Open position for Right branch left leg placement.
                                                    </div>
                                                </div>
                                                <div class="tree-node-card-l2 p-2 rounded-xl bg-black/70 border-2 border-dashed border-amber-500/50 text-center flex flex-col items-center justify-center relative">
                                                    <span class="text-[8px] text-amber-300 font-bold block mb-0.5">👈 R-LEFT</span>
                                                    <div class="w-7 h-7 sm:w-9 sm:h-9 mx-auto rounded-full border-2 border-dashed border-amber-400 text-amber-400 flex items-center justify-center font-bold text-xs mb-0.5">+</div>
                                                    <div class="w-full px-1 py-0.5 rounded-full bg-black/90 border border-dashed border-amber-500/40 text-[9px] sm:text-[10px] font-bold text-amber-300 truncate mb-0.5">VACANT</div>
                                                    <div class="text-[8px] sm:text-[9px] text-amber-400/70 font-mono font-bold">[ AVAILABLE ]</div>
                                                </div>
                                            </div>
                                        @endif
                                    </li>

                                    <!-- Right-Right Child -->
                                    <li>
                                        @if($rightChild && isset($rightChild->right_child) && $rightChild->right_child)
                                            @php $rr = $rightChild->right_child; $rrStats = $getUserStats($rr); @endphp
                                            <div class="node-card-wrapper inline-block">
                                                <!-- HOVER TOOLTIP -->
                                                <div class="node-tooltip space-y-2 hidden md:block">
                                                    <div class="font-black text-white border-b border-amber-500/40 pb-2 flex justify-between items-center">
                                                        <span class="text-white font-black font-heading text-sm sm:text-base">{{ $rr->name }}</span>
                                                        <span class="text-xs text-amber-400 font-mono font-bold">{{ $rr->referral_code }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Position:</span>
                                                        <span class="text-amber-300 font-black uppercase">R-RIGHT LEG 👉</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Sponsor:</span>
                                                        <span class="text-white font-bold">{{ $rrStats['sponsor_name'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Account Status:</span>
                                                        <span class="font-black uppercase tracking-wider {{ $rr->status === 'active' ? 'text-emerald-400' : 'text-rose-400' }}">{{ $rr->status }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Active Capital:</span>
                                                        <span class="text-emerald-400 font-mono font-bold">{{ $rrStats['active_invest'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Earning Wallet:</span>
                                                        <span class="text-emerald-400 font-mono font-bold">{{ $rrStats['earning_wallet'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Daily ROI Income:</span>
                                                        <span class="text-amber-400 font-mono font-bold">{{ $rrStats['daily_roi'] }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs pt-1.5 border-t border-amber-500/20 text-[11px] text-amber-400 font-bold text-center">
                                                        🔍 Click card to inspect Subtree
                                                    </div>
                                                </div>

                                                <div class="tree-node-card-l2 p-2.5 rounded-xl bg-[#081510]/95 border-2 border-[#f3ca52] hover:border-amber-400 hover:scale-[1.04] transition-all text-center flex flex-col items-center justify-between shadow-lg cursor-pointer relative group"
                                                     onclick="handleCardClick(event, '{{ route($routePrefix . '.network.tree', ['code' => $rr->referral_code]) }}', '{{ addslashes($rr->name) }}', '{{ $rr->referral_code }}', '{{ addslashes($rrStats['sponsor_name']) }}', '{{ strtoupper($rr->status) }}', '{{ $rrStats['active_invest'] }}', '{{ $rrStats['earning_wallet'] }}', '{{ $rrStats['daily_roi'] }}', '{{ $rrStats['direct_income'] }}', '{{ $rrStats['directs_count'] }}', '{{ $rr->created_at ? $rr->created_at->format('Y-m-d') : 'N/A' }}')">
                                                    
                                                    <!-- STATUS DOT AT TOP RIGHT OF CARD FRAME -->
                                                    @if($rr->status === 'active')
                                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 border border-slate-950 absolute top-2.5 right-2.5 shadow-[0_0_6px_#34d399]" title="Active User"></span>
                                                    @else
                                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 border border-slate-950 absolute top-2.5 right-2.5 shadow-[0_0_6px_#f43f5e]" title="Inactive User"></span>
                                                    @endif

                                                    <!-- CIRCULAR AVATAR -->
                                                    <div class="w-8 h-8 rounded-full border border-slate-600 bg-slate-900/90 text-white font-black text-xs flex items-center justify-center mx-auto shadow-sm">
                                                        {{ strtoupper(substr($rr->name, 0, 1)) }}
                                                    </div>

                                                    <!-- NAME PILL -->
                                                    <div class="w-full px-2 py-0.5 rounded-full bg-slate-900/90 border border-slate-700/80 text-[11px] font-bold text-white font-heading truncate mx-auto">
                                                        {{ $rr->name }}
                                                    </div>

                                                    <!-- SELF ID LINE -->
                                                    <div class="text-[10px] text-slate-400 font-mono font-medium leading-tight whitespace-nowrap truncate w-full">
                                                        SelfID: <span class="text-slate-200 font-semibold">{{ $rr->referral_code }}</span>
                                                    </div>

                                                    <!-- SPONSOR ID SECTION -->
                                                    <div class="w-full text-center leading-tight">
                                                        <div class="text-[9.5px] text-amber-400 font-bold">SponsorID:</div>
                                                        <div class="text-[10.5px] text-amber-300 font-mono font-bold tracking-wider truncate">{{ $rrStats['sponsor_code'] }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="node-card-wrapper inline-block">
                                                <div class="node-tooltip space-y-1.5 hidden md:block">
                                                    <div class="font-black text-amber-400 border-b border-amber-500/40 pb-1.5 flex justify-between items-center">
                                                        <span class="font-heading text-xs uppercase">Vacant Slot</span>
                                                        <span class="text-[10px] text-emerald-400 font-mono font-bold">[ AVAILABLE ]</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Position:</span>
                                                        <span class="text-amber-300 font-bold font-mono">R-RIGHT 👉</span>
                                                    </div>
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-slate-300 font-semibold">Parent Node:</span>
                                                        <span class="text-white font-bold">{{ $rightChild ? $rightChild->name : 'N/A' }}</span>
                                                    </div>
                                                    <div class="text-[10.5px] text-amber-300/90 italic pt-1 border-t border-amber-500/20">
                                                        Open position for Right branch right leg placement.
                                                    </div>
                                                </div>
                                                <div class="tree-node-card-l2 p-2 rounded-xl bg-black/70 border-2 border-dashed border-amber-500/50 text-center flex flex-col items-center justify-center relative">
                                                    <span class="text-[8px] text-amber-300 font-bold block mb-0.5">R-RIGHT 👉</span>
                                                    <div class="w-7 h-7 sm:w-9 sm:h-9 mx-auto rounded-full border-2 border-dashed border-amber-400 text-amber-400 flex items-center justify-center font-bold text-xs mb-0.5">+</div>
                                                    <div class="w-full px-1 py-0.5 rounded-full bg-black/90 border border-dashed border-amber-500/40 text-[9px] sm:text-[10px] font-bold text-amber-300 truncate mb-0.5">VACANT</div>
                                                    <div class="text-[8px] sm:text-[9px] text-amber-400/70 font-mono font-bold">[ AVAILABLE ]</div>
                                                </div>
                                            </div>
                                        @endif
                                    </li>
                                </ul>
                            </li>
                        </ul>
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
                    <p id="mobileModalCode" class="text-[11px] text-amber-400 font-mono">0000000</p>
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
                <span class="text-neutral-400 font-medium">Downline Count:</span>
                <span id="mobileModalDirects" class="font-bold text-amber-400 font-mono">0 Members</span>
            </div>
            <div class="flex justify-between items-center py-1">
                <span class="text-neutral-400 font-medium">Joined Date:</span>
                <span id="mobileModalJoined" class="font-mono text-neutral-200">2026-01-01</span>
            </div>
        </div>

        <div class="pt-2 flex flex-col gap-2">
            <a id="mobileModalNavBtn" href="#" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-500 text-black font-black text-xs uppercase tracking-wider text-center block shadow">
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
            backgroundColor: '#010905',
            scale: 2,
            useCORS: true,
            logging: false
        }).then(canvas => {
            const image = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            const userCode = "{{ $root->referral_code ?? 'TREE' }}";
            link.download = `Dex_Trade_Binary_Tree_${userCode}.png`;
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
        document.getElementById('mobileModalDirects').textContent = directs;
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
