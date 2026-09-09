@extends('user.layouts.app')

@section('title', 'Ticket #' . $ticket->ticket_number)

@section('content')
<div class="w-full space-y-6">

    <!-- Top Ticket Header Box (FULL WIDTH MATCHING ALL PAGES) -->
    <div class="p-6 sm:p-8 rounded-3xl ng-banner-title space-y-4 shadow-2xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-amber-500/20 pb-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-black text-amber-400 font-mono text-lg">{{ $ticket->ticket_number }}</span>
                    
                    <!-- Status Badge -->
                    @if($ticket->status === 'answered')
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/50 text-[10px] font-black uppercase animate-pulse">
                            ANSWERED BY SUPPORT
                        </span>
                    @elseif($ticket->status === 'open' || $ticket->status === 'user_reply')
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/50 text-[10px] font-black uppercase">
                            OPEN / WAITING
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full bg-neutral-800 text-neutral-400 border border-neutral-700 text-[10px] font-black uppercase">
                            RESOLVED / CLOSED
                        </span>
                    @endif
                </div>

                <h1 class="text-xl sm:text-2xl font-black text-white font-heading">{{ $ticket->subject }}</h1>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                @if($ticket->status !== 'closed')
                <form action="{{ route('user.tickets.close', $ticket->id) }}" method="POST" onsubmit="return confirm('Mark this ticket as closed/resolved?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-500/20 hover:bg-rose-500/40 border border-rose-500/40 text-rose-300 font-extrabold text-xs flex items-center gap-1.5 transition">
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i> Mark as Closed
                    </button>
                </form>
                @endif

                <a href="{{ route('user.tickets.index') }}" class="px-4 py-2 rounded-xl bg-black/80 hover:bg-black border border-amber-500/40 text-amber-300 font-bold text-xs flex items-center gap-1.5 transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to List
                </a>
            </div>
        </div>

        <!-- Ticket Meta Info Pills -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs font-mono">
            <div class="p-3 rounded-2xl bg-black/60 border border-amber-500/20">
                <span class="text-neutral-400 font-sans block text-[10px]">CATEGORY</span>
                <span class="font-black text-amber-300 uppercase">{{ strtoupper($ticket->category) }}</span>
            </div>
            <div class="p-3 rounded-2xl bg-black/60 border border-amber-500/20">
                <span class="text-neutral-400 font-sans block text-[10px]">PRIORITY</span>
                <span class="font-black text-amber-300 uppercase">{{ strtoupper($ticket->priority) }}</span>
            </div>
            <div class="p-3 rounded-2xl bg-black/60 border border-amber-500/20">
                <span class="text-neutral-400 font-sans block text-[10px]">CREATED DATE</span>
                <span class="font-bold text-white">{{ $ticket->created_at ? $ticket->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
            </div>
            <div class="p-3 rounded-2xl bg-black/60 border border-amber-500/20">
                <span class="text-neutral-400 font-sans block text-[10px]">LAST ACTIVITY</span>
                <span class="font-bold text-white">{{ $ticket->updated_at ? $ticket->updated_at->diffForHumans() : 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- CONVERSATION MESSAGES THREAD (CLEANLY PROPORTIONED CONTAINER) -->
    <div class="max-w-4xl mx-auto space-y-6">
        @foreach($ticket->messages as $msg)
            @if($msg->is_admin_reply)
                <!-- ADMIN REPLY CHAT BUBBLE (LEFT ALIGNED WITH GOLD BADGE) -->
                <div class="flex items-start gap-3 max-w-3xl">
                    <div class="w-10 h-10 rounded-2xl pdf-gold-badge text-black font-black text-xs flex items-center justify-center shrink-0 shadow-lg mt-1">
                        🎧
                    </div>
                    <div class="p-5 rounded-3xl pdf-package-card space-y-2 border-2 border-amber-400/80 shadow-2xl relative w-full">
                        <div class="flex items-center justify-between border-b border-amber-500/20 pb-2">
                            <span class="font-black text-amber-300 text-xs font-heading flex items-center gap-1.5">
                                <span>DEX TRADE SUPPORT TEAM</span>
                                <span class="px-2 py-0.5 rounded bg-amber-500 text-black text-[9px] font-black uppercase">Official</span>
                            </span>
                            <span class="text-[10px] text-neutral-400 font-mono">{{ $msg->created_at ? $msg->created_at->format('d M Y, h:i A') : '' }}</span>
                        </div>
                        <div class="text-neutral-100 text-sm leading-relaxed whitespace-pre-line font-sans">
                            {{ $msg->message }}
                        </div>

                        @if($msg->attachment)
                        <div class="pt-2 border-t border-amber-500/10">
                            <a href="{{ asset('storage/' . $msg->attachment) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-black/80 text-amber-300 border border-amber-500/40 text-xs font-bold font-mono hover:underline">
                                <i data-lucide="paperclip" class="w-3.5 h-3.5 text-amber-400"></i>
                                View Support Attachment
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- USER CHAT BUBBLE (RIGHT ALIGNED WITH EMERALD GLASS BORDER) -->
                <div class="flex items-start justify-end gap-3 max-w-3xl ml-auto">
                    <div class="p-5 rounded-3xl bg-black/90 border-2 border-emerald-500/60 space-y-2 shadow-xl relative w-full">
                        <div class="flex items-center justify-between border-b border-emerald-500/20 pb-2">
                            <span class="font-black text-emerald-400 text-xs font-heading">
                                YOU ({{ Auth::user()->name }})
                            </span>
                            <span class="text-[10px] text-neutral-400 font-mono">{{ $msg->created_at ? $msg->created_at->format('d M Y, h:i A') : '' }}</span>
                        </div>
                        <div class="text-neutral-100 text-sm leading-relaxed whitespace-pre-line font-sans">
                            {{ $msg->message }}
                        </div>

                        @if($msg->attachment)
                        <div class="pt-2 border-t border-emerald-500/10">
                            <a href="{{ asset('storage/' . $msg->attachment) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-950/60 text-emerald-300 border border-emerald-500/40 text-xs font-bold font-mono hover:underline">
                                <i data-lucide="paperclip" class="w-3.5 h-3.5 text-emerald-400"></i>
                                View Attachment
                            </a>
                        </div>
                        @endif
                    </div>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 border border-emerald-400/50 text-emerald-300 font-black text-xs flex items-center justify-center shrink-0 shadow-lg mt-1">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- POST REPLY FORM CONTAINER (If Ticket Open) -->
    @if($ticket->status !== 'closed')
    <div class="p-6 rounded-3xl pdf-package-card space-y-4">
        <h3 class="text-sm font-black text-white font-heading uppercase tracking-wide flex items-center gap-2">
            <i data-lucide="message-square-plus" class="w-4 h-4 text-amber-400"></i> Post Reply to Support
        </h3>

        <form action="{{ route('user.tickets.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <textarea name="message" rows="4" placeholder="Type your reply message here..." required class="w-full p-4 rounded-2xl bg-black/80 border border-amber-500/40 text-white font-medium text-sm focus:outline-none focus:border-amber-400 shadow-inner"></textarea>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <input type="file" name="attachment" accept="image/jpeg,image/png,image/jpg,application/pdf" class="p-2 rounded-xl bg-black/80 border border-amber-500/30 text-neutral-300 font-mono text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:pdf-gold-ribbon file:font-black file:text-[10px] file:uppercase file:cursor-pointer cursor-pointer">

                <button type="submit" class="px-6 py-3 rounded-2xl pdf-gold-ribbon font-black text-xs uppercase tracking-wider shadow-xl hover:scale-105 transition flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4 text-black"></i>
                    <span>Send Reply</span>
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="p-4 rounded-2xl bg-black/80 border border-neutral-700 text-center text-xs text-neutral-400 font-semibold">
        🔒 This support ticket has been closed. You can open a new ticket if you need further assistance.
    </div>
    @endif

    </div>

</div>
@endsection
