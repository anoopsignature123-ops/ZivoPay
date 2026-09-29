@extends('admin.layouts.app')

@section('title', 'Deposit and P2P Transaction History - Admin ZIVO PAY')

@section('content')
<div class="w-full space-y-6 font-sans">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-slate-900/95 border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.3)] flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] uppercase border border-emerald-500/40">AUDIT LOG</span>
                <span class="text-xs text-emerald-400 font-black tracking-[3px] uppercase">ZIVO PAY SYSTEM REPORTS</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white font-heading uppercase">DEPOSIT AND P2P TRANSACTION HISTORY</h1>
            <p class="text-xs text-neutral-300 mt-1">Master audit report combining all user Deposits, Admin Wallet Credits, and P2P Member Transfers across the system.</p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <div class="px-4 py-2.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/40 text-right">
                <span class="text-[10px] uppercase tracking-wider text-emerald-400 font-bold block">Approved Deposits</span>
                <span class="text-xl sm:text-2xl font-black text-white font-mono">₹{{ number_format($totalDepositAmount, 2) }}</span>
            </div>
            <div class="px-4 py-2.5 rounded-2xl bg-purple-500/10 border border-purple-500/40 text-right">
                <span class="text-[10px] uppercase tracking-wider text-purple-300 font-bold block">P2P Transfer Volume</span>
                <span class="text-xl sm:text-2xl font-black text-purple-300 font-mono">₹{{ number_format($totalP2pAmount, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Filter Form Bar -->
    <div class="bg-slate-900/90 p-4 sm:p-5 rounded-3xl border border-emerald-500/30 shadow-xl">
        <form action="{{ route('admin.reports.deposit-p2p') }}" method="GET" class="flex flex-wrap items-end gap-3">
            <!-- FROM DATE -->
            <div class="w-36 sm:w-40 shrink-0">
                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-3 py-2.5 rounded-xl bg-bg border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
            </div>

            <!-- TO DATE -->
            <div class="w-36 sm:w-40 shrink-0">
                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-3 py-2.5 rounded-xl bg-bg border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
            </div>

            <!-- TRANSACTION CATEGORY TYPE -->
            <div class="w-44 sm:w-52 shrink-0">
                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Category Type</label>
                <select name="type" class="w-full px-3.5 py-2.5 rounded-xl bg-bg border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400 cursor-pointer">
                    <option value="">All Categories (Deposits & P2P)</option>
                    <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>User Self Deposit</option>
                    <option value="admin_credit" {{ request('type') == 'admin_credit' ? 'selected' : '' }}>Admin Direct Add Fund</option>
                    <option value="p2p" {{ request('type') == 'p2p' ? 'selected' : '' }}>P2P Member Transfer</option>
                </select>
            </div>

            <!-- STATUS -->
            <div class="w-36 sm:w-44 shrink-0">
                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Status</label>
                <select name="status" class="w-full px-3.5 py-2.5 rounded-xl bg-bg border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400 cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- SEARCH KEYWORD -->
            <div class="flex-1 min-w-[220px]">
                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Search Ref / Member / UTR</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ref ID, TRX ID, Member Name, Code, Email..." class="w-full pl-9 pr-3.5 py-2.5 rounded-xl bg-bg border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
                    <i data-lucide="search" class="w-4 h-4 text-emerald-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </div>

            <!-- FILTER BUTTONS -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs uppercase tracking-wider transition flex items-center justify-center gap-1.5 shadow-md cursor-pointer">
                    <i data-lucide="filter" class="w-4 h-4"></i> Filter
                </button>
                <a href="{{ route('admin.reports.deposit-p2p') }}" class="px-3.5 py-2.5 rounded-xl bg-bg border border-emerald-500/30 text-neutral-400 hover:text-white text-xs font-bold transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Main Report Table -->
    <div class="bg-slate-900/90 rounded-3xl border border-emerald-500/30 overflow-hidden shadow-2xl p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-emerald-500/20 pb-3">
            <h3 class="text-base font-black text-white uppercase font-heading flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-emerald-400"></i>
                Combined Audit Records Log
            </h3>
            <span class="text-xs font-mono font-bold text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/30">
                Total Records: {{ $paginatedRecords->total() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-[#02180f] text-emerald-400 font-extrabold uppercase tracking-wider border-b border-emerald-500/30">
                    <tr>
                        <th class="p-3.5">Ref ID & Date</th>
                        <th class="p-3.5">Member Details</th>
                        <th class="p-3.5">Category Type</th>
                        <th class="p-3.5">Details / Transfer Info</th>
                        <th class="p-3.5">Amount (₹)</th>
                        <th class="p-3.5">UTR / TRX Hash</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5">System Remark</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/10 text-neutral-200 font-medium">
                    @forelse($paginatedRecords as $row)
                        <tr class="hover:bg-emerald-500/5 transition">
                            <!-- Ref ID & Date -->
                            <td class="p-3.5">
                                <span class="font-mono font-bold text-white text-sm block">{{ $row['ref_code'] }}</span>
                                <span class="text-[10px] text-neutral-400 font-mono block">
                                    {{ $row['created_at'] ? $row['created_at']->format('d M Y, h:i A') : 'N/A' }}
                                </span>
                            </td>

                            <!-- Member Details -->
                            <td class="p-3.5">
                                @if(isset($row['user']) && $row['user'])
                                    <a href="{{ route('admin.users.show', $row['user']->id) }}" class="font-bold text-emerald-300 hover:underline block">
                                        {{ $row['user_name'] }}
                                    </a>
                                    <span class="text-[10px] text-neutral-400 font-mono block">Code: {{ $row['user_code'] }}</span>
                                @else
                                    <span class="text-neutral-400 font-bold">System Admin</span>
                                @endif
                            </td>

                            <!-- Category Type -->
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded {{ $row['badge_class'] }} text-[10px] font-black uppercase inline-block">
                                    {{ $row['category_label'] }}
                                </span>
                            </td>

                            <!-- Transfer Info -->
                            <td class="p-3.5 font-sans font-semibold text-neutral-200 text-xs">
                                {{ $row['party_detail'] }}
                            </td>

                            <!-- Amount -->
                            <td class="p-3.5 font-mono font-black text-sm text-emerald-400">
                                ₹{{ number_format($row['amount'], 2) }}
                            </td>

                            <!-- UTR / Hash -->
                            <td class="p-3.5 font-mono font-bold text-neutral-300 text-xs">
                                {{ $row['trx_hash'] }}
                            </td>

                            <!-- Status -->
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase {{ $row['status_badge'] }}">
                                    {{ $row['status'] }}
                                </span>
                            </td>

                            <!-- Remark -->
                            <td class="p-3.5 text-neutral-300 text-xs max-w-xs truncate" title="{{ $row['remark'] }}">
                                {{ $row['remark'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-neutral-400 font-bold">
                                No Deposit or P2P Transaction records found in system matching your filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($paginatedRecords->hasPages())
            <div class="pt-4 border-t border-emerald-500/20">
                {{ $paginatedRecords->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
