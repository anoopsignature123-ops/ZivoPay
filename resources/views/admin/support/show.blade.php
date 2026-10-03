@extends('admin.layouts.app')

@section('title', 'Ticket #' . $ticket->ticket_number . ' Detail')

@section('content')
    <div class="w-full max-w-5xl mx-auto space-y-6 font-sans relative">

        <!-- Top Navigation Header -->
        <div class="flex items-center justify-between p-6 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl">
            <div>
                <a href="{{ route('admin.support.index') }}" class="text-xs font-bold text-emerald-400 hover:underline flex items-center gap-1 mb-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Support Tickets
                </a>
                <h1 class="text-xl sm:text-2xl font-black text-white font-heading uppercase">TICKET #{{ $ticket->ticket_number }}</h1>
                <p class="text-xs text-neutral-300 mt-0.5">Submitted by {{ $ticket->user->name ?? 'User #'.$ticket->user_id }} on {{ $ticket->created_at->format('d M Y, h:i A') }}</p>
            </div>

            <div>
                @if(strtolower($ticket->status) === 'pending')
                    <span class="px-3 py-1.5 rounded-full text-xs font-black uppercase bg-amber-500/20 text-amber-400 border border-amber-500/30">PENDING</span>
                @elseif(strtolower($ticket->status) === 'resolved')
                    <span class="px-3 py-1.5 rounded-full text-xs font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">RESOLVED</span>
                @elseif(strtolower($ticket->status) === 'in_progress')
                    <span class="px-3 py-1.5 rounded-full text-xs font-black uppercase bg-blue-500/20 text-blue-400 border border-blue-500/30">IN PROGRESS</span>
                @else
                    <span class="px-3 py-1.5 rounded-full text-xs font-black uppercase bg-neutral-800 text-neutral-400 border border-neutral-700">CLOSED</span>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Ticket Details Card -->
        <div class="p-6 rounded-3xl bg-neutral-900/90 border border-emerald-500/20 shadow-2xl space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-neutral-800 pb-4">
                <div>
                    <span class="text-[10px] font-black uppercase text-emerald-400 tracking-wider block">Category</span>
                    <span class="text-sm font-bold text-white uppercase">{{ str_replace('_', ' ', $ticket->category) }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-emerald-400 tracking-wider block">Priority</span>
                    <span class="text-sm font-bold text-white uppercase">{{ $ticket->priority }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-emerald-400 tracking-wider block">Member Phone</span>
                    <span class="text-sm font-mono font-bold text-white">{{ $ticket->user->mobile ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-emerald-400 tracking-wider block">Member ID / Code</span>
                    <span class="text-sm font-mono font-bold text-emerald-400">{{ $ticket->user->referral_code ?? 'N/A' }}</span>
                </div>
            </div>

            <div>
                <h3 class="text-base font-bold text-white mb-2">{{ $ticket->subject }}</h3>
                <div class="p-4 rounded-2xl bg-neutral-950 border border-neutral-800 text-neutral-200 text-xs leading-relaxed whitespace-pre-line">
                    {{ $ticket->message }}
                </div>
            </div>
        </div>

        <!-- Previous Admin Reply (If Any) -->
        @if($ticket->admin_reply)
            <div class="p-6 rounded-3xl bg-emerald-950/40 border border-emerald-500/40 shadow-2xl space-y-3">
                <div class="flex items-center justify-between border-b border-emerald-500/30 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-xs">AD</div>
                        <div>
                            <p class="text-xs font-bold text-white">Admin Response</p>
                            <p class="text-[10px] text-emerald-400 font-mono">{{ $ticket->replied_at?->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-400">Replied</span>
                </div>
                <div class="text-xs text-emerald-100 leading-relaxed whitespace-pre-line">
                    {{ $ticket->admin_reply }}
                </div>
            </div>
        @endif

        <!-- Admin Response & Status Form -->
        <div class="p-6 rounded-3xl bg-neutral-900/90 border border-emerald-500/20 shadow-2xl space-y-4">
            <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="send" class="w-4 h-4 text-emerald-400"></i> Send Admin Response & Update Status
            </h3>

            <form method="POST" action="{{ route('admin.support.reply', $ticket) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Admin Reply Message</label>
                    <textarea name="admin_reply" rows="5" required placeholder="Type your response to the user here..."
                        class="w-full p-4 rounded-2xl bg-neutral-950 border border-neutral-800 text-white text-xs focus:border-emerald-500 focus:outline-none">{{ old('admin_reply', $ticket->admin_reply) }}</textarea>
                    @error('admin_reply')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                    <div>
                        <label class="block text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Update Ticket Status</label>
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl bg-neutral-950 border border-neutral-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                            <option value="pending" {{ old('status', $ticket->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status', $ticket->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ old('status', $ticket->status) === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ old('status', $ticket->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    <div class="sm:pt-6">
                        <button type="submit" class="w-full px-6 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold uppercase tracking-wider transition shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4"></i> Submit Response & Update Status
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
