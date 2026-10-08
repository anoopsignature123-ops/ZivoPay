@extends('admin.layouts.app')

@section('title', 'Static Content & WebView Pages')

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
                    ZIVO PAY CONTROL CENTER • WEBVIEWS & LEGAL PAGES
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white font-heading uppercase tracking-tight">STATIC CONTENT PAGES</h1>
                <p class="text-xs text-neutral-300 mt-1">Manage Terms & Conditions, Privacy Policy, Contact Us, and Mobile App WebView pages.</p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <div class="px-5 py-3 rounded-2xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-300 text-xs font-bold font-mono flex items-center gap-2 shadow-xl">
                    <i data-lucide="file-text" class="w-4 h-4 text-emerald-400"></i>
                    <span>Total Pages: <strong class="text-white font-black text-base">{{ $pages->count() }}</strong></span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2 relative z-10">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Pages Grid List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 relative z-10">
            @foreach($pages as $page)
                <div class="p-6 rounded-3xl admin-card-glow flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                {{ strtoupper($page->category) }}
                            </span>
                            @if($page->is_active)
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-400">Active</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-red-500/20 text-red-400">Disabled</span>
                            @endif
                        </div>

                        <h3 class="text-lg font-black text-white uppercase tracking-tight mb-1">{{ $page->title }}</h3>
                        <p class="text-xs font-mono text-emerald-400/80 mb-3">/page/{{ $page->slug }}</p>

                        <p class="text-xs text-neutral-400 line-clamp-3 leading-relaxed">
                            {{ strip_tags($page->content) }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-neutral-800/80 flex items-center justify-between gap-2">
                        <a href="{{ url('/page/'.$page->slug) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-neutral-800 hover:bg-neutral-700 text-neutral-300 text-xs font-bold transition flex items-center gap-1">
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Preview WebView
                        </a>

                        <a href="{{ route('admin.pages.edit', $page) }}" class="px-4 py-1.5 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/40 text-xs font-bold transition flex items-center gap-1">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit Content
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
@endsection
