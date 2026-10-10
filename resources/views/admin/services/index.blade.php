@extends('admin.layouts.app')

@section('title', 'Manage App Services & Statuses - Admin ZIVO PAY')

@section('content')
<div class="w-full space-y-6 font-sans">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.3)] flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] uppercase border border-emerald-500/40">DASHBOARD CONTROL</span>
                <span class="text-xs text-emerald-400 font-black tracking-[3px] uppercase">SERVICE CONFIGURATION</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white font-heading">APP SERVICES & STATUS MANAGEMENT</h1>
            <p class="text-xs text-neutral-300 mt-1">Enable, disable, or toggle service statuses (Active vs Coming Soon) for the Mobile App Dashboard.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="px-5 py-3 rounded-2xl bg-[#02180f] border border-emerald-500/40 text-right">
                <span class="text-[10px] uppercase tracking-wider text-emerald-400 font-bold block">Active Services</span>
                <span class="text-xl sm:text-2xl font-black text-emerald-400 font-mono">{{ $stats['active'] }}</span>
            </div>
            <div class="px-5 py-3 rounded-2xl bg-[#02180f] border border-emerald-500/40 text-right">
                <span class="text-[10px] uppercase tracking-wider text-amber-400 font-bold block">Coming Soon Services</span>
                <span class="text-xl sm:text-2xl font-black text-amber-400 font-mono">{{ $stats['coming_soon'] }}</span>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-bold text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Services Table -->
    <div class="bg-[#042718] border border-emerald-500/40 rounded-3xl overflow-hidden shadow-2xl p-6">
        <div class="flex items-center justify-between mb-5 border-b border-emerald-500/20 pb-4">
            <div>
                <h2 class="text-lg font-black text-white uppercase tracking-wider">ALL MOBILE APP SERVICES</h2>
                <p class="text-xs text-neutral-400 mt-0.5">Toggle services to dynamically reflect on Mobile App Dashboard & REST API feed.</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 font-extrabold text-xs border border-emerald-500/40">
                Total Services: {{ $services->count() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-200">
                <thead class="bg-[#02180f] text-xs uppercase font-black tracking-wider text-emerald-400 border-b border-emerald-500/40">
                    <tr>
                        <th class="py-4 px-4">#</th>
                        <th class="py-4 px-4">Service Title</th>
                        <th class="py-4 px-4">Category</th>
                        <th class="py-4 px-4">System Key</th>
                        <th class="py-4 px-4 text-center">Current Status</th>
                        <th class="py-4 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/15 font-semibold">
                    @foreach($services as $index => $service)
                        <tr class="hover:bg-emerald-500/10 transition border-b border-emerald-500/15">
                            <td class="py-4 px-4 font-mono font-black text-neutral-400 text-xs">{{ $index + 1 }}</td>
                            <td class="py-4 px-4 font-black text-white">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-300 shrink-0 shadow-inner">
                                        <i data-lucide="{{ $service->icon ?? 'app-window' }}" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <span class="text-sm font-black text-white block tracking-tight">{{ $service->title }}</span>
                                        <span class="text-[11px] text-neutral-400 font-mono font-normal">ID: #{{ $service->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 rounded-xl bg-emerald-950/80 border border-emerald-500/40 font-bold text-xs text-emerald-300 inline-block uppercase tracking-wider shadow-sm">
                                    {{ ucfirst(str_replace('_', ' ', $service->category)) }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-lg bg-cyan-950/60 border border-cyan-500/40 font-mono text-xs text-cyan-300 font-black inline-block">
                                    {{ $service->key }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if($service->status === 'active' && $service->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/50 shadow-sm shadow-emerald-500/20">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                        ACTIVE
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black uppercase bg-amber-500/20 text-amber-300 border border-amber-500/50 shadow-sm shadow-amber-500/20">
                                        <i data-lucide="hourglass" class="w-3.5 h-3.5 text-amber-300"></i>
                                        COMING SOON
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right">
                                <form action="{{ route('admin.services.toggle-status', $service) }}" method="POST" class="inline-block">
                                    @csrf
                                    @if($service->status === 'active')
                                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-black uppercase transition border shadow-md bg-amber-500/20 hover:bg-amber-500/40 text-amber-300 border-amber-500/50 flex items-center gap-1.5 ml-auto">
                                            <i data-lucide="pause-circle" class="w-4 h-4"></i> Set Coming Soon
                                        </button>
                                    @else
                                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-black uppercase transition border shadow-md bg-emerald-500/20 hover:bg-emerald-500/40 text-emerald-300 border-emerald-500/50 flex items-center gap-1.5 ml-auto">
                                            <i data-lucide="play-circle" class="w-4 h-4"></i> Set Active
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
