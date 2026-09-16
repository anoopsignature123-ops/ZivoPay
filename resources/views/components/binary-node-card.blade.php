@props(['node', 'role' => '', 'routePrefix' => 'user', 'isSmall' => false])

@php
    $leftStats = $node->left_leg_stats;
    $rightStats = $node->right_leg_stats;
@endphp

<div class="relative group-node inline-block text-center">
    
    <!-- 1. DESKTOP ONLY HOVER TOOLTIP CARD -->
    <div class="desktop-node-tooltip hidden md:block absolute bottom-[108%] left-1/2 -translate-x-1/2 mb-2 w-72 sm:w-80 p-4 rounded-3xl border-2 border-emerald-400 shadow-[0_20px_50px_rgba(0,0,0,0.95)] text-left space-y-2 text-xs"
         style="background: linear-gradient(180deg, #063824 0%, #021d12 50%, #000000 100%) !important; z-index: 99999 !important;">
        
        <!-- Tooltip Header -->
        <div class="flex justify-between items-center pb-2 border-b border-emerald-500/30">
            <span class="text-neutral-300 font-extrabold uppercase text-[10px]">Full Name</span>
            <span class="font-black text-white font-heading text-sm">{{ $node->name }}</span>
        </div>

        <div class="flex justify-between items-center pb-1 border-b border-emerald-500/20">
            <span class="text-neutral-400 font-semibold">Self ID Code</span>
            <span class="font-black text-emerald-400 font-mono">{{ $node->referral_code }}</span>
        </div>

        <div class="flex justify-between items-center pb-1 border-b border-emerald-500/20">
            <span class="text-neutral-400 font-semibold">Sponsor ID Code</span>
            <span class="font-black text-emerald-300 font-mono">{{ $node->sponsor_code ?? 'N/A' }}</span>
        </div>

        <div class="flex justify-between items-center pb-1 border-b border-emerald-500/20">
            <span class="text-neutral-400 font-semibold">Sponsor Name</span>
            <span class="font-bold text-teal-300">{{ $node->sponsor ? $node->sponsor->name : ($node->sponsor_code ? $node->sponsor_code : 'No Sponsor') }}</span>
        </div>

        <div class="flex justify-between items-center pb-1 border-b border-emerald-500/20">
            <span class="text-neutral-400 font-semibold">Account Status</span>
            <span class="font-black px-2 py-0.5 rounded text-[9px] uppercase tracking-wider {{ $node->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40' }}">
                {{ $node->status === 'active' ? 'ACTIVE MEMBER' : 'INACTIVE' }}
            </span>
        </div>

        <!-- Team A Members Pill -->
        <div class="p-2 rounded-xl bg-black/60 border border-emerald-500/40 flex justify-between items-center text-[11px]">
            <span class="font-extrabold text-emerald-400 uppercase">Team A Network</span>
            <span class="font-black text-emerald-300 font-mono">{{ $leftStats['active'] }} Act / {{ $leftStats['inactive'] }} Inact</span>
        </div>

        <!-- Team B Members Pill -->
        <div class="p-2 rounded-xl bg-black/60 border border-teal-500/40 flex justify-between items-center text-[11px]">
            <span class="font-extrabold text-teal-400 uppercase">Team B Network</span>
            <span class="font-black text-teal-300 font-mono">{{ $rightStats['active'] }} Act / {{ $rightStats['inactive'] }} Inact</span>
        </div>

        <div class="flex justify-between items-center pt-1 border-t border-emerald-500/20">
            <span class="text-neutral-400 font-semibold">Team A Volume</span>
            <span class="font-black text-emerald-400 font-mono text-sm">{{ $leftStats['business'] }}</span>
        </div>

        <div class="flex justify-between items-center">
            <span class="text-neutral-400 font-semibold">Team B Volume</span>
            <span class="font-black text-teal-300 font-mono text-sm">{{ $rightStats['business'] }}</span>
        </div>

        <!-- Arrow Pointer at Bottom of Tooltip Card -->
        <div class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-emerald-500"></div>
    </div>

    <!-- MAIN TREE NODE CARD -->
    <div class="relative inline-block">
        
        <!-- 2. MOBILE ONLY (i) INFO BADGE BUTTON -->
        <button type="button" 
                onclick="openMobileMemberModal(event, {{ json_encode([
                    'name' => $node->name,
                    'code' => $node->referral_code,
                    'sponsor_code' => $node->sponsor_code ?? 'N/A',
                    'sponsor_name' => $node->sponsor ? $node->sponsor->name : ($node->sponsor_code ? $node->sponsor_code : 'No Sponsor'),
                    'package' => $node->status === 'active' ? 'ACTIVE MEMBER' : 'INACTIVE',
                    'left_members' => $leftStats['active'].' Act, '.$leftStats['inactive'].' Inact',
                    'right_members' => $rightStats['active'].' Act, '.$rightStats['inactive'].' Inact',
                    'left_business' => $leftStats['business'],
                    'right_business' => $rightStats['business'],
                ]) }})"
                class="flex md:hidden absolute top-2 right-2 w-5 h-5 rounded-full bg-emerald-600 text-white font-black text-[10px] items-center justify-center z-30 shadow-md cursor-pointer transition hover:scale-110" 
                title="Tap for Info">
            i
        </button>

        <a href="{{ route($routePrefix . '.network.tree', ['code' => $node->referral_code]) }}" 
           class="block p-3.5 rounded-3xl bg-panel border-2 border-emerald-500/80 text-center {{ $isSmall ? 'w-32 p-2.5' : 'w-44' }} transition-all duration-300 hover:scale-105">
            
            <!-- CIRCULAR INITIAL AVATAR WITH LIVE GREEN DOT -->
            <div class="relative {{ $isSmall ? 'w-9 h-9' : 'w-11 h-11' }} mx-auto mb-2">
                <div class="w-full h-full rounded-full bg-emerald-600 text-white font-black {{ $isSmall ? 'text-xs' : 'text-base' }} flex items-center justify-center shadow-lg">
                    {{ strtoupper(substr($node->name, 0, 1)) }}
                </div>
                <!-- Live Active Status Dot -->
                <span class="absolute top-0 right-0 {{ $isSmall ? 'w-2.5 h-2.5' : 'w-3 h-3' }} rounded-full {{ $node->status === 'active' ? 'bg-emerald-400 ring-2 ring-black animate-pulse' : 'bg-rose-500 ring-2 ring-black' }}"></span>
            </div>

            <!-- NAME PILL CONTAINER -->
            <div class="px-2.5 py-0.5 rounded-full bg-black/80 border border-emerald-500/40 mb-1">
                <span class="font-black text-white {{ $isSmall ? 'text-[10px]' : 'text-xs' }} block truncate font-heading group-hover/node:text-emerald-300 transition">{{ $node->name }}</span>
            </div>

            <!-- SELF ID & SPONSOR ID TEXT -->
            <div class="{{ $isSmall ? 'text-[9px]' : 'text-[10px]' }} text-neutral-300 font-mono">SelfID: <span class="text-white font-bold">{{ $node->referral_code }}</span></div>
            <div class="{{ $isSmall ? 'text-[9px]' : 'text-[10px]' }} text-emerald-400 font-mono font-bold mt-0.5">SponsorID: {{ $node->sponsor_code ?? 'N/A' }}</div>
        </a>
    </div>
</div>

