@extends('user.layouts.app')

@section('title', 'My Support Tickets')

@section('content')
<div class="w-full space-y-6">

    <!-- Top Header Banner (Matching PDF Deep Emerald & Gold Theme) -->
    <div class="ng-banner-title p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="pdf-num-badge">🎫</span>
                <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">CUSTOMER HELP DESK</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white font-heading">MY SUPPORT TICKETS</h1>
            <p class="text-xs text-neutral-300 mt-1">Submit support requests, track status, and chat directly with Dex Trade support.</p>
        </div>

        <a href="{{ route('user.tickets.create') }}" class="px-6 py-3.5 rounded-full pdf-gold-ribbon font-black text-xs uppercase tracking-wider flex items-center gap-2 shadow-xl hover:scale-105 transition text-black">
            <i data-lucide="plus-circle" class="w-4 h-4 text-black font-black"></i>
            <span class="text-black font-black">Open New Ticket</span>
        </a>
    </div>

    <!-- 3 KPI SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('user.tickets.index') }}" class="p-5 rounded-3xl pdf-package-card relative overflow-hidden group block">
            <div class="text-xs font-extrabold text-amber-400 uppercase tracking-wider">OPEN TICKETS</div>
            <h3 class="text-3xl font-black text-white font-heading mt-1">{{ number_format($openCount) }}</h3>
        </a>

        <a href="{{ route('user.tickets.index', ['status' => 'answered']) }}" class="p-5 rounded-3xl pdf-package-card relative overflow-hidden group block">
            <div class="text-xs font-extrabold text-emerald-400 uppercase tracking-wider">ANSWERED BY SUPPORT</div>
            <h3 class="text-3xl font-black text-emerald-400 font-heading mt-1">{{ number_format($answeredCount) }}</h3>
        </a>

        <a href="{{ route('user.tickets.index', ['status' => 'closed']) }}" class="p-5 rounded-3xl pdf-package-card relative overflow-hidden group block">
            <div class="text-xs font-extrabold text-neutral-400 uppercase tracking-wider">RESOLVED / CLOSED</div>
            <h3 class="text-3xl font-black text-neutral-400 font-heading mt-1">{{ number_format($closedCount) }}</h3>
        </a>
    </div>

    <!-- MAIN TICKET LIST TABLE CONTAINER (MATCHING SCREENSHOT 100%) -->
    <div class="p-6 rounded-3xl pdf-package-card space-y-6">
        
        <!-- Filter Pills & Search Form (100% MATCHING USER SCREENSHOT) -->
        <form action="{{ route('user.tickets.index') }}" method="GET" class="flex flex-col lg:flex-row items-center justify-between gap-4 pb-4 border-b border-amber-500/20">
            
            <!-- Left Side: Rounded Outline Filter Pills -->
            <div class="flex items-center gap-2.5 overflow-x-auto w-full lg:w-auto shrink-0 pb-1 lg:pb-0">
                <a href="{{ route('user.tickets.index') }}" class="px-5 py-2 rounded-full text-xs font-black transition whitespace-nowrap {{ !request('status') ? 'bg-amber-400 text-black shadow-md' : 'bg-black/60 text-amber-300 border border-amber-500/50 hover:bg-amber-500/20' }}">
                    All ({{ $openCount + $answeredCount + $closedCount }})
                </a>
                <a href="{{ route('user.tickets.index', ['status' => 'open']) }}" class="px-5 py-2 rounded-full text-xs font-black transition whitespace-nowrap {{ request('status') === 'open' ? 'bg-amber-400 text-black shadow-md' : 'bg-black/60 text-amber-300 border border-amber-500/50 hover:bg-amber-500/20' }}">
                    Open ({{ $openCount }})
                </a>
                <a href="{{ route('user.tickets.index', ['status' => 'answered']) }}" class="px-5 py-2 rounded-full text-xs font-black transition whitespace-nowrap {{ request('status') === 'answered' ? 'bg-amber-400 text-black shadow-md' : 'bg-black/60 text-amber-300 border border-amber-500/50 hover:bg-amber-500/20' }}">
                    Answered ({{ $answeredCount }})
                </a>
                <a href="{{ route('user.tickets.index', ['status' => 'closed']) }}" class="px-5 py-2 rounded-full text-xs font-black transition whitespace-nowrap {{ request('status') === 'closed' ? 'bg-amber-400 text-black shadow-md' : 'bg-black/60 text-amber-300 border border-amber-500/50 hover:bg-amber-500/20' }}">
                    Closed ({{ $closedCount }})
                </a>
            </div>

            <!-- Right Side: Category Dropdown & Search Input Pill -->
            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto shrink-0 justify-end">
                <select name="category" onchange="this.form.submit()" class="px-4 py-2 rounded-full bg-black border border-amber-500/50 text-white text-xs font-bold focus:outline-none cursor-pointer">
                    <option value="">All Categories</option>
                    <option value="deposit" {{ request('category') === 'deposit' ? 'selected' : '' }}>Deposit</option>
                    <option value="withdrawal" {{ request('category') === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                    <option value="package" {{ request('category') === 'package' ? 'selected' : '' }}>Package</option>
                    <option value="network" {{ request('category') === 'network' ? 'selected' : '' }}>Network</option>
                    <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>Account</option>
                    <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Other</option>
                </select>

                <div class="relative w-full sm:w-64">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-amber-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ticket #, subject..." class="w-full pl-9 pr-4 py-2 rounded-full bg-black border border-amber-500/50 text-white font-semibold text-xs focus:outline-none focus:border-amber-400">
                </div>
            </div>

        </form>

        <!-- Tickets Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-black/80 text-amber-400 uppercase text-[10px] font-bold border-b border-amber-500/30">
                    <tr>
                        <th class="p-3 min-w-[140px]">TICKET CODE</th>
                        <th class="p-3 min-w-[220px]">SUBJECT & CATEGORY</th>
                        <th class="p-3">PRIORITY</th>
                        <th class="p-3">STATUS</th>
                        <th class="p-3">LAST UPDATED</th>
                        <th class="p-3 text-right">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-500/10 text-neutral-200">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-amber-500/10 transition group">
                        
                        <!-- Ticket Code -->
                        <td class="p-3 font-mono font-black text-amber-400 text-sm">
                            <a href="{{ route('user.tickets.show', $ticket->id) }}" class="hover:underline">
                                {{ $ticket->ticket_number }}
                            </a>
                        </td>

                        <!-- Subject & Category -->
                        <td class="p-3">
                            <div>
                                <a href="{{ route('user.tickets.show', $ticket->id) }}" class="font-bold text-white text-xs hover:text-amber-300 transition block">
                                    {{ Str::limit($ticket->subject, 45) }}
                                </a>
                                <span class="text-[9px] text-amber-400 font-mono uppercase bg-black/60 px-2 py-0.5 rounded border border-amber-500/20">
                                    Category: {{ strtoupper($ticket->category) }}
                                </span>
                            </div>
                        </td>

                        <!-- Priority -->
                        <td class="p-3">
                            @if($ticket->priority === 'urgent')
                                <span class="px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/40 text-[9px] font-black uppercase">URGENT</span>
                            @elseif($ticket->priority === 'high')
                                <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/40 text-[9px] font-black uppercase">HIGH</span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-sky-500/20 text-sky-300 border border-sky-500/40 text-[9px] font-black uppercase">{{ strtoupper($ticket->priority) }}</span>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="p-3">
                            @if($ticket->status === 'answered')
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/50 text-[9px] font-black uppercase animate-pulse">
                                    ANSWERED BY SUPPORT
                                </span>
                            @elseif($ticket->status === 'open' || $ticket->status === 'user_reply')
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/50 text-[9px] font-black uppercase">
                                    OPEN
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full bg-neutral-800 text-neutral-400 border border-neutral-700 text-[9px] font-black uppercase">
                                    RESOLVED / CLOSED
                                </span>
                            @endif
                        </td>

                        <!-- Last Updated -->
                        <td class="p-3 font-mono text-[11px] text-neutral-400">
                            {{ $ticket->updated_at ? $ticket->updated_at->diffForHumans() : 'N/A' }}
                        </td>

                        <!-- Action -->
                        <td class="p-3 text-right">
                            <a href="{{ route('user.tickets.show', $ticket->id) }}" class="px-3 py-1.5 rounded-lg pdf-gold-ribbon text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1 shadow">
                                <span>View Chat</span>
                                <i data-lucide="chevron-right" class="w-3 h-3 text-black"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-neutral-400 font-medium">
                            No support tickets found in system.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tickets->hasPages())
        <div class="pt-4 border-t border-amber-500/20">
            {{ $tickets->links() }}
        </div>
        @endif

    </div>

</div>
@endsection
