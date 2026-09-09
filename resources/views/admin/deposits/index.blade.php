@extends('admin.layouts.app')

@section('content')
    <div class="w-full space-y-6">
        <!-- Header Banner (Matching User Management Module Exactly) -->
        <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="pdf-num-badge">DM</span>
                    <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">DEX TRADE NETWORK</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient font-heading">MEMBER DEPOSIT HISTORY AUDIT</h1>
                <p class="text-xs text-neutral-300 mt-1">Audit USDT (BEP20) instant deposits credited to member deposit wallets.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-500/20 border border-rose-500/50 text-rose-300 text-xs font-bold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Main Container Panel (Matching reference image #03180e & gold border) -->
        <div class="bg-[#03180e] p-6 shadow-2xl rounded-2xl border-2 border-amber-500/50 space-y-6">

            <!-- Filter Bar with JS Datepicker & Search in 1 Single Row -->
            <div
                class="flex flex-col lg:flex-row items-stretch lg:items-end justify-between gap-4 border-b border-amber-500/20 pb-4">

                <!-- Title Label -->
                <div class="flex items-center gap-2 text-xs font-black text-amber-400 uppercase tracking-wider">
                    <i data-lucide="history" class="w-4 h-4 text-amber-400"></i>
                    <span>DEPOSIT HISTORY</span>
                </div>

                <!-- Date Range & Search Form -->
                <form action="{{ route('admin.deposits.index') }}" method="GET"
                    class="flex flex-nowrap items-end gap-3 overflow-x-auto text-xs font-sans pb-1">
                    <!-- 1. FROM DATE -->
                    <div class="w-36 shrink-0">
                        <label class="block text-[10px] font-extrabold text-amber-400 uppercase tracking-wider mb-1">FROM
                            DATE</label>
                        <div class="relative">
                            <i data-lucide="calendar"
                                class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-amber-400"></i>
                            <input type="text" name="start_date" value="{{ request('start_date') }}" placeholder="YYYY-MM-DD"
                                class="datepicker w-full pl-8 pr-3 py-2 rounded-xl bg-black/60 border border-amber-500/40 text-white font-mono text-xs focus:outline-none focus:border-amber-400">
                        </div>
                    </div>

                    <!-- 2. TO DATE -->
                    <div class="w-36 shrink-0">
                        <label class="block text-[10px] font-extrabold text-amber-400 uppercase tracking-wider mb-1">TO
                            DATE</label>
                        <div class="relative">
                            <i data-lucide="calendar"
                                class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-amber-400"></i>
                            <input type="text" name="end_date" value="{{ request('end_date') }}" placeholder="YYYY-MM-DD"
                                class="datepicker w-full pl-8 pr-3 py-2 rounded-xl bg-black/60 border border-amber-500/40 text-white font-mono text-xs focus:outline-none focus:border-amber-400">
                        </div>
                    </div>

                    <!-- 3. SEARCH TXN HASH / MEMBER -->
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-[10px] font-extrabold text-amber-400 uppercase tracking-wider mb-1">SEARCH
                            MEMBER / HASH</label>
                        <div class="relative">
                            <i data-lucide="search"
                                class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-amber-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Member name, code, hash..."
                                class="w-full pl-8 pr-3 py-2 rounded-xl bg-black/60 border border-amber-500/40 text-white font-mono text-xs focus:outline-none focus:border-amber-400">
                        </div>
                    </div>

                    <!-- 4. FILTER BUTTON -->
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="submit"
                            class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-black font-black text-xs uppercase tracking-wider shadow transition flex items-center justify-center gap-1.5 shrink-0">
                            <i data-lucide="filter" class="w-3.5 h-3.5 text-black"></i> FILTER
                        </button>
                        <a href="{{ route('admin.deposits.index') }}"
                            class="py-2 px-3 rounded-xl bg-bg border border-amber-500/30 text-neutral-300 hover:text-amber-400 font-bold text-xs transition flex items-center justify-center shrink-0">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- DEPOSIT HISTORY TABLE WITH EXACT 8 COLUMNS FROM REFERENCE DESIGN -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead
                        class="bg-black/90 text-amber-400 uppercase text-[11px] font-extrabold font-heading tracking-wider border-b border-amber-500/30">
                        <tr>
                            <th class="p-3.5 rounded-l-xl">TXN NUMBER</th>
                            <th class="p-3.5">MEMBER DETAILS</th>
                            <th class="p-3.5">WALLET TYPE</th>
                            <th class="p-3.5 text-right">AMOUNT ($)</th>
                            <th class="p-3.5 text-right">POST BALANCE</th>
                            <th class="p-3.5">DESCRIPTION / REMARK</th>
                            <th class="p-3.5">DATE & TIME</th>
                            <th class="p-3.5 text-center rounded-r-xl">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-500/20 text-neutral-200">
                        @forelse($deposits as $dep)
                            <tr class="hover:bg-amber-500/5 transition border-b border-amber-500/10">
                                <!-- 1. TXN NUMBER -->
                                <td class="p-3.5">
                                    <span
                                        class="font-mono font-bold text-amber-400 text-xs tracking-wider uppercase">{{ $dep->deposit_ref }}</span>
                                </td>

                                <!-- 2. MEMBER DETAILS -->
                                <td class="p-3.5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-amber-500 text-black font-black text-xs flex items-center justify-center shrink-0 shadow">
                                            {{ strtoupper(substr($dep->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.users.show', $dep->user->id ?? 1) }}"
                                                class="font-bold text-white hover:text-amber-300 transition text-xs block font-heading uppercase">
                                                {{ $dep->user->name ?? 'Unknown Member' }}
                                            </a>
                                            <a href="{{ route('admin.users.show', $dep->user->id ?? 1) }}"
                                                class="text-[11px] font-mono text-amber-400 hover:underline flex items-center gap-1 mt-0.5">
                                                <span>{{ $dep->user->referral_code ?? 'NGF-1954601' }}</span>
                                                <i data-lucide="arrow-up-right" class="w-3 h-3 text-amber-400"></i>
                                            </a>
                                        </div>
                                    </div>
                                </td>

                                <!-- 3. WALLET TYPE -->
                                <td class="p-3.5">
                                    <span
                                        class="px-2.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/40 text-amber-400 text-[11px] font-bold tracking-wide inline-flex items-center gap-1.5">
                                        💳 DEPOSIT WALLET
                                    </span>
                                </td>

                                <!-- 4. AMOUNT ($) -->
                                <td class="p-3.5 text-right font-mono font-bold text-emerald-400 text-xs sm:text-sm">
                                    + ${{ number_format($dep->amount, 2) }}
                                </td>

                                <!-- 5. POST BALANCE -->
                                <td class="p-3.5 text-right font-mono font-bold text-white text-xs sm:text-sm">
                                    ${{ number_format($dep->transaction->post_balance ?? $dep->user->deposit_wallet ?? 0, 2) }}
                                </td>

                                <!-- 6. DESCRIPTION / REMARK -->
                                <td class="p-3.5">
                                    <div class="text-xs text-neutral-200 font-sans">
                                        Deposit via {{ $dep->payment_gateway }}
                                    </div>
                                    @if($dep->admin_notes)
                                        <div class="text-[10px] font-sans text-amber-300/90 italic mt-0.5">
                                            Remark: {{ $dep->admin_notes }}
                                        </div>
                                    @endif
                                    @if($dep->txn_hash)
                                        <div class="text-[10px] font-mono text-amber-400/80 truncate max-w-[180px] mt-0.5"
                                            title="{{ $dep->txn_hash }}">
                                            Hash: {{ $dep->txn_hash }}
                                        </div>
                                    @endif
                                </td>

                                <!-- 7. DATE & TIME -->
                                <td class="p-3.5">
                                    <div class="text-xs font-sans text-white font-medium">
                                        {{ $dep->created_at ? $dep->created_at->format('M d, Y') : '-' }}
                                    </div>
                                    <div class="text-[10px] font-mono text-neutral-400">
                                        {{ $dep->created_at ? $dep->created_at->format('h:i A') : '-' }}
                                    </div>
                                </td>

                                <!-- 8. STATUS & ACTIONS -->
                                <td class="p-3.5 text-center">
                                    <div class="flex flex-col items-center gap-1.5">
                                        @if($dep->status === 'approved')
                                            <span
                                                class="px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/40 text-emerald-400 text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1">
                                                <i data-lucide="check-circle" class="w-3 h-3 text-emerald-400"></i> APPROVED
                                            </span>
                                        @elseif($dep->status === 'rejected')
                                            <span
                                                class="px-2.5 py-1 rounded-full bg-rose-500/10 border border-rose-500/40 text-rose-400 text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1">
                                                <i data-lucide="x-circle" class="w-3 h-3 text-rose-400"></i> REJECTED
                                            </span>
                                        @else
                                            <span
                                                class="px-2.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/40 text-amber-300 text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1">
                                                <i data-lucide="clock" class="w-3 h-3 text-amber-300"></i> PENDING
                                            </span>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <form action="{{ route('admin.deposits.approve', $dep->id) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-2 py-0.5 rounded bg-emerald-500 hover:bg-emerald-400 text-black font-black text-[10px] uppercase transition shadow"
                                                        title="Approve Deposit">
                                                        Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.deposits.reject', $dep->id) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-2 py-0.5 rounded bg-rose-500 hover:bg-rose-400 text-white font-black text-[10px] uppercase transition shadow"
                                                        title="Reject Deposit">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-neutral-400 text-sm font-semibold">
                                    No deposit records found in audit history.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-4 border-t border-amber-500/20">
                {{ $deposits->links() }}
            </div>
        </div>
    </div>
@endsection
