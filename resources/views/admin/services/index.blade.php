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
        <h2 class="text-lg font-black text-white uppercase tracking-wider mb-4">All Mobile App Services</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-200">
                <thead class="bg-[#02180f] text-xs uppercase font-extrabold text-emerald-400 border-b border-emerald-500/40">
                    <tr>
                        <th class="py-4 px-4">#</th>
                        <th class="py-4 px-4">Service Title</th>
                        <th class="py-4 px-4">Category</th>
                        <th class="py-4 px-4">Key</th>
                        <th class="py-4 px-4">Current Status</th>
                        <th class="py-4 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/20">
                    @foreach($services as $index => $service)
                        <tr class="hover:bg-emerald-500/5 transition">
                            <td class="py-4 px-4 font-mono font-bold text-neutral-400">{{ $index + 1 }}</td>
                            <td class="py-4 px-4 font-bold text-white flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400 font-mono text-xs">
                                    {{ strtoupper(substr($service->key, 0, 2)) }}
                                </span>
                                {{ $service->title }}
                            </td>
                            <td class="py-4 px-4 font-mono text-xs text-neutral-300">
                                <span class="px-2.5 py-1 rounded-lg bg-neutral-800 border border-neutral-700 font-bold text-neutral-300">
                                    {{ ucfirst(str_replace('_', ' ', $service->category)) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 font-mono text-xs text-emerald-400 font-bold">{{ $service->key }}</td>
                            <td class="py-4 px-4">
                                @if($service->status === 'active' && $service->is_active)
                                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                                        ● ACTIVE
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-amber-500/20 text-amber-400 border border-amber-500/40">
                                        ⏱ COMING SOON
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right">
                                <form action="{{ route('admin.services.toggle-status', $service) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold font-mono uppercase transition border shadow-md {{ $service->status === 'active' ? 'bg-amber-500/20 hover:bg-amber-500/30 text-amber-400 border-amber-500/40' : 'bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 border-emerald-500/40' }}">
                                        {{ $service->status === 'active' ? 'Set Coming Soon' : 'Set Active' }}
                                    </button>
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
