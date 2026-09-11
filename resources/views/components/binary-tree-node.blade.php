@props(['node' => null, 'level' => 0, 'maxLevel' => 3, 'path' => 'Root Node', 'routePrefix' => 'user'])

@php
    $isRoot = $level === 0;
    
    $sponsorName = $node?->sponsor?->name ?? 'N/A';
    $sponsorCode = $node?->sponsor_code ?? 'N/A';
    $activeInvest = '$' . number_format($node ? (float)$node->userPackages()->where('status', 'active')->sum('invested_amount') : 0, 2);
    $earningWallet = '$' . number_format($node ? (float)$node->earning_wallet : 0, 2);
    $dailyRoi = '$' . number_format($node ? (float)$node->transactions()->where('type', 'daily_roi')->sum('amount') : 0, 2);
    $directIncome = '$' . number_format($node ? (float)$node->transactions()->where('type', 'direct_commission')->sum('amount') : 0, 2);
    $directsCount = $node ? \App\Models\User::where('sponsor_code', $node->referral_code)->count() . ' Members' : '0 Members';
    $joinedDate = $node?->created_at ? $node->created_at->format('Y-m-d') : 'N/A';
@endphp

<li>
    @if ($node)
        <div class="node-card-wrapper inline-block {{ $isRoot ? 'root-node-wrapper' : '' }}">
            <!-- HOVER TOOLTIP (DESKTOP) -->
            <div class="node-tooltip space-y-2 hidden md:block">
                <div class="font-black text-white border-b border-amber-500/40 pb-2 flex justify-between items-center">
                    <span class="text-white font-black font-heading text-sm sm:text-base">{{ $node->name }}</span>
                    <span class="text-xs text-amber-400 font-mono font-bold">{{ $node->referral_code }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-300 font-semibold">Position:</span>
                    <span class="text-amber-300 font-black uppercase">{{ $path }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-300 font-semibold">Sponsor:</span>
                    <span class="text-white font-bold">{{ $sponsorName }} ({{ $sponsorCode }})</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-300 font-semibold">Account Status:</span>
                    <span class="font-black uppercase tracking-wider {{ $node->status === 'active' ? 'text-emerald-400' : 'text-rose-400' }}">{{ $node->status }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-300 font-semibold">Active Capital:</span>
                    <span class="text-emerald-400 font-mono font-bold">{{ $activeInvest }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-300 font-semibold">Earning Wallet:</span>
                    <span class="text-emerald-400 font-mono font-bold">{{ $earningWallet }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-300 font-semibold">Daily ROI Income:</span>
                    <span class="text-amber-400 font-mono font-bold">{{ $dailyRoi }}</span>
                </div>
                <div class="flex justify-between items-center text-xs pt-1.5 border-t border-amber-500/20 text-[11px] text-amber-400 font-bold text-center">
                    🔍 Click card to inspect Subtree
                </div>
            </div>

            <!-- NODE CARD FRAME -->
            <div class="tree-node-card-l1 p-2 rounded-xl bg-[#081510]/95 border-2 border-[#f3ca52] hover:border-amber-400 hover:scale-[1.04] transition-all text-center flex flex-col items-center justify-between shadow-lg cursor-pointer relative group overflow-hidden"
                 onclick="handleCardClick(event, '{{ route($routePrefix . '.network.tree', ['code' => $node->referral_code]) }}', '{{ addslashes($node->name) }}', '{{ $node->referral_code }}', '{{ addslashes($sponsorName) }}', '{{ strtoupper($node->status) }}', '{{ $activeInvest }}', '{{ $earningWallet }}', '{{ $dailyRoi }}', '{{ $directIncome }}', '{{ $directsCount }}', '{{ $joinedDate }}')">
                
                <!-- STATUS DOT AT TOP RIGHT OF CARD FRAME -->
                @if($node->status === 'active')
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 border border-slate-950 absolute top-2 right-2 shadow-[0_0_6px_#34d399]" title="Active User"></span>
                @else
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 border border-slate-950 absolute top-2 right-2 shadow-[0_0_6px_#f43f5e]" title="Inactive User"></span>
                @endif

                <!-- CIRCULAR AVATAR -->
                <div class="w-8 h-8 rounded-full border border-slate-600 bg-slate-900/90 text-white font-bold text-xs flex items-center justify-center mx-auto shrink-0 shadow-sm {{ $isRoot ? 'gold-glowing-avatar' : '' }}">
                    {{ strtoupper(substr($node->name, 0, 1)) }}
                </div>

                <!-- NAME PILL -->
                <div class="w-full px-2 py-0.5 rounded-full bg-slate-900/90 border border-slate-700/80 text-[11px] font-bold text-white font-heading truncate mx-auto text-center leading-tight">
                    {{ $node->name }}
                </div>

                <!-- SELF ID LINE -->
                <div class="w-full text-center px-1">
                    <span class="text-[10px] font-mono font-bold text-amber-300 tracking-tight block truncate">
                        SelfID: {{ $node->referral_code }}
                    </span>
                </div>

                <!-- SPONSOR ID SECTION -->
                <div class="w-full text-center leading-tight pb-0.5">
                    <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">SponsorID:</div>
                    <div class="text-[10px] text-amber-400 font-mono font-bold tracking-tight truncate">{{ $sponsorCode }}</div>
                </div>
            </div>
        </div>
    @else
        <!-- VACANT NODE CARD -->
        <div class="node-card-wrapper inline-block">
            <div class="tree-node-card-l1 p-2 rounded-xl bg-black/70 border-2 border-dashed border-amber-500/50 text-center flex flex-col items-center justify-between relative overflow-hidden">
                <span class="text-[9px] text-amber-300 font-bold block">{{ strtoupper($path) }}</span>
                <div class="w-6 h-6 mx-auto rounded-full border-2 border-dashed border-amber-400 text-amber-400 flex items-center justify-center font-bold text-xs">+</div>
                <div class="w-full px-1 py-0.5 rounded-full bg-black/90 border border-dashed border-amber-500/40 text-[10px] font-bold text-amber-300 truncate">VACANT</div>
                <div class="text-[9px] text-amber-400/70 font-mono font-bold">[ AVAILABLE ]</div>
            </div>
        </div>
    @endif

    @if ($level < $maxLevel)
        <ul>
            @include('components.binary-tree-node', ['node' => $node?->left_child, 'level' => $level + 1, 'maxLevel' => $maxLevel, 'path' => '👈 Left', 'routePrefix' => $routePrefix])
            @include('components.binary-tree-node', ['node' => $node?->right_child, 'level' => $level + 1, 'maxLevel' => $maxLevel, 'path' => 'Right 👉', 'routePrefix' => $routePrefix])
        </ul>
    @endif
</li>
