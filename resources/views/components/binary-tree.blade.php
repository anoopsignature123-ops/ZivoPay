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
.tree-node-card-l2,
.tree-node-card-l3 {
    width: 140px;
    min-width: 140px;
    max-width: 140px;
    height: 148px;
    min-height: 148px;
    max-height: 148px;
    box-sizing: border-box;
    overflow: hidden !important;
    border: 2px solid rgba(243, 202, 82, 0.85) !important;
    border-radius: 0.85rem !important;
    background-color: rgba(8, 21, 16, 0.95) !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5) !important;
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
    <div class="w-full flex flex-col sm:flex-row items-center justify-between gap-3 p-3 sm:p-3.5 rounded-xl bg-gradient-to-b from-[#042115] to-[#010c07] border border-amber-500/40 shadow-md">
        <!-- Left Group: Title Badge & Legend -->
        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 text-center sm:text-left">
            <span class="px-2.5 py-1 rounded-full bg-amber-500/20 border border-amber-400/50 text-amber-300 font-black text-[11px] sm:text-xs uppercase tracking-wider flex items-center gap-1.5 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span>BINARY TREE</span>
            </span>
            <span class="hidden sm:inline text-neutral-600">|</span>
            <span class="text-amber-400 font-bold text-[11px] sm:text-xs flex items-center gap-1">👈 Left Branch</span>
            <span class="text-neutral-600">|</span>
            <span class="text-amber-400 font-bold text-[11px] sm:text-xs flex items-center gap-1">Right Branch 👉</span>
        </div>

        <!-- Right Group: Action Buttons & Zoom Controls -->
        <div class="flex items-center gap-2 shrink-0 flex-wrap justify-center">
            <!-- Mobile/Desktop Zoom Controls -->
            <div class="flex items-center gap-1 bg-black/80 p-1 rounded-xl border border-amber-500/40 shadow-sm">
                <button type="button" onclick="zoomTree(0.85)" class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-amber-500/20 text-amber-300 hover:bg-amber-500/40 flex items-center justify-center font-black font-mono text-xs sm:text-sm" title="Zoom Out">-</button>
                <button type="button" onclick="zoomTree(1)" class="px-1.5 h-6 sm:h-7 rounded-lg text-amber-300 font-mono font-bold text-[10px] sm:text-[11px] hover:bg-amber-500/20" title="Reset Zoom">100%</button>
                <button type="button" onclick="zoomTree(1.15)" class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-amber-500/20 text-amber-300 hover:bg-amber-500/40 flex items-center justify-center font-black font-mono text-xs sm:text-sm" title="Zoom In">+</button>
            </div>

            <button type="button" 
                    onclick="downloadTreeImage()" 
                    id="downloadTreeBtn"
                    class="px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-500 hover:from-amber-300 hover:to-amber-400 text-black font-black text-[11px] sm:text-xs uppercase tracking-wider flex items-center justify-center gap-1 transition shadow cursor-pointer whitespace-nowrap">
                <span>📸 Save</span>
            </button>

            <a href="{{ route($routePrefix . '.network.tree') }}" 
               class="px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl bg-black/90 hover:bg-black border border-amber-500/50 text-amber-300 font-bold text-[11px] sm:text-xs flex items-center justify-center gap-1 transition shadow whitespace-nowrap">
                <span>🎯 Recenter</span>
            </a>
        </div>
    </div>

    <!-- MAIN LEFT / RIGHT BINARY TREE GRAPH CANVAS -->
    <div id="treeCanvasContainer" class="w-full rounded-2xl bg-black/80 border border-amber-500/40 p-4 relative">
        
        <div class="genealogy-tree-wrapper">
            <div class="binary-tree-container">
                <ul class="binary-tree-container">
                    @include('components.binary-tree-node', ['node' => $root, 'level' => 0, 'maxLevel' => 3, 'path' => 'Root Node', 'routePrefix' => $routePrefix])
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
    let currentTreeScale = 1;

    function zoomTree(scale) {
        const container = document.querySelector('.binary-tree-container');
        if (!container) return;

        if (scale === 1) {
            currentTreeScale = 1;
        } else if (scale === 0.85) {
            currentTreeScale = Math.max(0.55, Math.round((currentTreeScale - 0.15) * 100) / 100);
        } else if (scale === 1.15) {
            currentTreeScale = Math.min(1.4, Math.round((currentTreeScale + 0.15) * 100) / 100);
        }

        container.style.transform = `scale(${currentTreeScale})`;
        container.style.transformOrigin = 'top center';
    }

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

    document.addEventListener('DOMContentLoaded', function() {
        const wrapper = document.querySelector('.genealogy-tree-wrapper');
        const container = document.querySelector('.binary-tree-container');
        if (wrapper && container) {
            const scrollLeft = (container.scrollWidth - wrapper.clientWidth) / 2;
            if (scrollLeft > 0) {
                wrapper.scrollLeft = scrollLeft;
            }
        }
    });
</script>
