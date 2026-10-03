@extends('admin.layouts.app')

@section('title', 'Help & Support Tickets Control')

@section('content')
    <style>
        .admin-card-glow {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.95) 0%, rgba(3, 28, 18, 0.95) 100%) !important;
            border: 2px solid rgba(16, 185, 129, 0.4) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.8), 0 0 20px rgba(16, 185, 129, 0.15) !important;
            transition: all 0.3s ease !important;
        }

        .admin-card-glow:hover {
            border-color: rgba(16, 185, 129, 0.8) !important;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.9), 0 0 30px rgba(16, 185, 129, 0.25) !important;
            transform: translateY(-2px);
        }
    </style>

    <div class="w-full space-y-6 font-sans relative">

        <!-- Ambient Emerald Radial Glow Decorator -->
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-[900px] h-[450px] bg-emerald-500/10 blur-[130px] pointer-events-none rounded-full"></div>

        <!-- Top Header Banner -->
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 p-6 sm:p-8 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl relative z-10">
            <div>
                <div class="text-[11px] font-black text-emerald-400 uppercase tracking-widest mb-1 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    ZIVO PAY CONTROL CENTER • HELP & SUPPORT
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white font-heading uppercase tracking-tight">SUPPORT TICKETS MANAGEMENT</h1>
                <p class="text-xs text-neutral-300 mt-1">Review user helpdesk tickets, respond to queries, and update issue resolution status.</p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <div class="px-5 py-3 rounded-2xl bg-amber-500/20 border border-amber-500/50 text-amber-300 text-xs font-bold font-mono flex items-center gap-2 shadow-xl">
                    <i data-lucide="help-circle" class="w-4 h-4 text-amber-400"></i>
                    <span>Pending Tickets: <strong class="text-amber-300 font-black text-base">{{ $stats['pending'] }}</strong></span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2 relative z-10">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- STATS ROW: 4 Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 relative z-10">
            <div class="p-5 rounded-3xl admin-card-glow space-y-2">
                <div class="flex items-center justify-between text-neutral-400 text-xs font-extrabold uppercase">
                    <span>TOTAL TICKETS</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                        <i data-lucide="life-buoy" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-white font-mono tracking-tight">{{ number_format($stats['total']) }}</div>
                <div class="text-[11px] text-neutral-400 font-medium">All time support requests</div>
            </div>

            <div class="p-5 rounded-3xl admin-card-glow space-y-2">
                <div class="flex items-center justify-between text-neutral-400 text-xs font-extrabold uppercase">
                    <span>PENDING</span>
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center border border-amber-500/30">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-amber-400 font-mono tracking-tight">{{ number_format($stats['pending']) }}</div>
                <div class="text-[11px] text-amber-400/80 font-medium">Awaiting admin review</div>
            </div>

            <div class="p-5 rounded-3xl admin-card-glow space-y-2">
                <div class="flex items-center justify-between text-neutral-400 text-xs font-extrabold uppercase">
                    <span>RESOLVED</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-emerald-400 font-mono tracking-tight">{{ number_format($stats['resolved']) }}</div>
                <div class="text-[11px] text-emerald-400/80 font-medium">Successfully resolved tickets</div>
            </div>

            <div class="p-5 rounded-3xl admin-card-glow space-y-2">
                <div class="flex items-center justify-between text-neutral-400 text-xs font-extrabold uppercase">
                    <span>CLOSED</span>
                    <div class="w-9 h-9 rounded-xl bg-neutral-500/20 text-neutral-400 flex items-center justify-center border border-neutral-500/30">
                        <i data-lucide="archive" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-neutral-300 font-mono tracking-tight">{{ number_format($stats['closed']) }}</div>
                <div class="text-[11px] text-neutral-400 font-medium">Archived support cases</div>
            </div>
        </div>

        <!-- Filter & Search Section -->
        <div class="p-6 rounded-3xl bg-neutral-900/90 border border-emerald-500/20 shadow-2xl relative z-10">
            <form method="GET" action="{{ route('admin.support.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Search Ticket / User</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ticket ID, subject, mobile..."
                        class="w-full px-4 py-2.5 rounded-xl bg-neutral-950 border border-neutral-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Filter Category</label>
                    <select name="category" class="w-full px-4 py-2.5 rounded-xl bg-neutral-950 border border-neutral-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                        <option value="">All Categories</option>
                        <option value="recharge" {{ request('category') === 'recharge' ? 'selected' : '' }}>Recharge</option>
                        <option value="deposit" {{ request('category') === 'deposit' ? 'selected' : '' }}>Deposit</option>
                        <option value="withdrawal" {{ request('category') === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                        <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>Account</option>
                        <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>General</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Filter Status</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-xl bg-neutral-950 border border-neutral-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20">
                        <i data-lucide="filter" class="w-4 h-4"></i> Filter
                    </button>
                    <a href="{{ route('admin.support.index') }}" class="px-4 py-2.5 rounded-xl bg-neutral-800 hover:bg-neutral-700 text-neutral-300 text-xs font-bold transition">Reset</a>
                </div>
            </form>
        </div>

        <!-- Support Tickets Data Table -->
        <div class="rounded-3xl bg-neutral-900/90 border border-emerald-500/20 shadow-2xl overflow-hidden relative z-10">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-neutral-950/80 border-b border-neutral-800 text-[11px] font-black uppercase text-emerald-400 tracking-wider">
                            <th class="py-4 px-6">Ticket #</th>
                            <th class="py-4 px-6">Member</th>
                            <th class="py-4 px-6">Category</th>
                            <th class="py-4 px-6">Subject</th>
                            <th class="py-4 px-6">Priority</th>
                            <th class="py-4 px-6">Status</th>
                            <th class="py-4 px-6">Created At</th>
                            <th class="py-4 px-6 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-800/60 text-xs">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-neutral-800/40 transition">
                                <td class="py-4 px-6 font-mono font-bold text-white">{{ $ticket->ticket_number }}</td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-white">{{ $ticket->user->name ?? 'User #'.$ticket->user_id }}</div>
                                    <div class="text-[11px] text-emerald-400 font-mono">{{ $ticket->user->mobile ?? '' }}</div>
                                </td>
                                <td class="py-4 px-6 uppercase text-[11px] font-bold text-neutral-300">
                                    {{ str_replace('_', ' ', $ticket->category) }}
                                </td>
                                <td class="py-4 px-6 max-w-[220px] truncate text-neutral-200" title="{{ $ticket->subject }}">
                                    {{ $ticket->subject }}
                                </td>
                                <td class="py-4 px-6">
                                    @if(strtolower($ticket->priority) === 'high')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-red-500/20 text-red-400 border border-red-500/30">HIGH</span>
                                    @elseif(strtolower($ticket->priority) === 'medium')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-amber-500/20 text-amber-400 border border-amber-500/30">MEDIUM</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-blue-500/20 text-blue-400 border border-blue-500/30">LOW</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    @if(strtolower($ticket->status) === 'pending')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-amber-500/20 text-amber-400 border border-amber-500/30">PENDING</span>
                                    @elseif(strtolower($ticket->status) === 'resolved')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">RESOLVED</span>
                                    @elseif(strtolower($ticket->status) === 'in_progress')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-blue-500/20 text-blue-400 border border-blue-500/30">IN PROGRESS</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-neutral-800 text-neutral-400 border border-neutral-700">CLOSED</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-neutral-400 text-[11px]">
                                    {{ $ticket->created_at->format('d M Y, h:i A') }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('admin.support.show', $ticket) }}" class="px-3 py-1.5 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/40 text-[11px] font-bold transition inline-flex items-center gap-1">
                                        <i data-lucide="message-square" class="w-3.5 h-3.5"></i> Reply
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-neutral-400 text-xs">
                                    No support tickets found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tickets->hasPages())
                <div class="p-4 border-t border-neutral-800">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
