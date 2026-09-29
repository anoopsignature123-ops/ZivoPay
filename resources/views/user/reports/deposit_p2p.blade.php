@extends('user.layouts.app')

@section('title', 'Deposit and P2P Transaction History - ZIVO PAY')

@section('content')
<div class="max-w-6xl mx-auto space-y-5 font-sans">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.25)] flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[11px] font-black uppercase tracking-widest border border-emerald-500/40">
                COMBINED AUDIT LEDGER ⚡
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight mt-1 font-heading">
                DEPOSIT AND P2P TRANSACTION HISTORY
            </h1>
            <p class="text-xs text-neutral-300 mt-1">
                Unified report of all your Fund Wallet Deposits, Admin Direct Credits, and P2P Member-to-Member Transfers.
            </p>
        </div>

        <div class="px-5 py-3 rounded-2xl bg-[#02180f] border-2 border-emerald-400 text-right shrink-0">
            <span class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-wider block">Available Fund Wallet</span>
            <span class="text-2xl font-black text-white font-mono">₹{{ number_format($user->deposit_wallet, 2) }}</span>
        </div>
    </div>

    <!-- Stats Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <!-- Approved Deposits -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between text-emerald-400 mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-neutral-400">Total Deposits Credited</span>
                <i data-lucide="arrow-down-left" class="w-5 h-5 text-emerald-400"></i>
            </div>
            <h3 class="text-2xl font-black text-white font-mono">₹{{ number_format($totalDepositAmount, 2) }}</h3>
            <span class="text-[10px] text-emerald-400 font-semibold mt-1 block">Approved & Admin Credits</span>
        </div>

        <!-- P2P Sent -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between text-amber-400 mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-neutral-400">Total P2P Sent</span>
                <i data-lucide="arrow-up-right" class="w-5 h-5 text-amber-400"></i>
            </div>
            <h3 class="text-2xl font-black text-amber-300 font-mono">₹{{ number_format($totalP2pSentAmount, 2) }}</h3>
            <span class="text-[10px] text-amber-400 font-semibold mt-1 block">Transferred to Members</span>
        </div>

        <!-- P2P Received -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between text-teal-300 mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-neutral-400">Total P2P Received</span>
                <i data-lucide="arrow-down-right" class="w-5 h-5 text-teal-300"></i>
            </div>
            <h3 class="text-2xl font-black text-teal-300 font-mono">₹{{ number_format($totalP2pReceivedAmount, 2) }}</h3>
            <span class="text-[10px] text-teal-400 font-semibold mt-1 block">Received from Members</span>
        </div>

        <!-- Total Records Count -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between text-emerald-400 mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-neutral-400">Total Transactions</span>
                <i data-lucide="receipt" class="w-5 h-5 text-emerald-400"></i>
            </div>
            <h3 class="text-2xl font-black text-white font-mono">{{ $totalCount }}</h3>
            <span class="text-[10px] text-neutral-400 font-semibold mt-1 block">Combined Total Records</span>
        </div>
    </div>

    <!-- Comprehensive Filter Bar -->
    <div class="p-4 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-xl">
        <form action="{{ route('user.reports.deposit-p2p') }}" method="GET" class="zivo-filter-bar">
            <!-- FROM DATE -->
            <div class="zivo-filter-field-date">
                <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">FROM DATE</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}"
                    class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
            </div>

            <!-- TO DATE -->
            <div class="zivo-filter-field-date">
                <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">TO DATE</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}"
                    class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
            </div>

            <!-- TRANSACTION TYPE -->
            <div class="zivo-filter-field-select">
                <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">TYPE / CATEGORY</label>
                <select name="type" class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400 transition cursor-pointer">
                    <option value="">All Types (Deposits & P2P)</option>
                    <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>Self Deposit / Gateway</option>
                    <option value="admin_credit" {{ request('type') === 'admin_credit' ? 'selected' : '' }}>Admin Add Fund</option>
                    <option value="p2p_sent" {{ request('type') === 'p2p_sent' ? 'selected' : '' }}>P2P Sent</option>
                    <option value="p2p_received" {{ request('type') === 'p2p_received' ? 'selected' : '' }}>P2P Received</option>
                </select>
            </div>

            <!-- STATUS -->
            <div class="zivo-filter-field-select">
                <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">STATUS</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400 transition cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- SEARCH KEYWORD -->
            <div class="zivo-filter-field-search">
                <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">SEARCH REF / UTR / MEMBER</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-emerald-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ref Code, TRX ID, UTR, Member..."
                        class="w-full pl-9 pr-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="zivo-filter-actions">
                <button type="submit" class="py-2 px-5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-black font-black uppercase tracking-wider text-xs hover:shadow-[0_0_20px_rgba(16,185,129,0.7)] transition flex items-center justify-center gap-1.5 shadow cursor-pointer">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i> FILTER
                </button>
                <a href="{{ route('user.reports.deposit-p2p') }}" class="py-2 px-3.5 rounded-xl bg-black/60 border border-emerald-500/30 text-neutral-300 font-bold text-xs hover:text-white hover:border-emerald-400 transition text-center shrink-0">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Report Table Section -->
    <div class="bg-[#042718] rounded-3xl border border-emerald-500/30 overflow-hidden shadow-2xl p-6 space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-[#02180f] text-emerald-400 font-extrabold uppercase tracking-wider border-b border-emerald-500/30">
                    <tr>
                        <th class="p-3.5">Ref Code & Date</th>
                        <th class="p-3.5">Category Type</th>
                        <th class="p-3.5">Party / Payment Method Details</th>
                        <th class="p-3.5">Amount (₹)</th>
                        <th class="p-3.5">UTR / TRX Hash</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5">Remarks / System Note</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/10 text-neutral-200 font-medium">
                    @forelse($paginatedRecords as $row)
                        <tr class="hover:bg-emerald-500/5 transition">
                            <!-- Ref Code & Date -->
                            <td class="p-3.5">
                                <span class="font-mono font-bold text-white text-sm block">{{ $row['ref_code'] }}</span>
                                <span class="text-[10px] text-neutral-400 font-mono block">
                                    {{ $row['created_at'] ? $row['created_at']->format('d M Y, h:i A') : 'N/A' }}
                                </span>
                            </td>

                            <!-- Category Type -->
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded {{ $row['badge_class'] }} text-[10px] font-black uppercase inline-block">
                                    {{ $row['category_label'] }}
                                </span>
                            </td>

                            <!-- Party / Payment Details -->
                            <td class="p-3.5 font-sans font-semibold text-neutral-200 text-xs">
                                {{ $row['party_detail'] }}
                            </td>

                            <!-- Amount -->
                            <td class="p-3.5 font-mono font-black text-sm {{ $row['category'] === 'p2p_sent' ? 'text-amber-300' : 'text-emerald-400' }}">
                                {{ $row['category'] === 'p2p_sent' ? '-' : '+' }}₹{{ number_format($row['amount'], 2) }}
                            </td>

                            <!-- UTR / TRX Hash -->
                            <td class="p-3.5 font-mono font-bold text-neutral-300 text-xs">
                                {{ $row['trx_hash'] }}
                            </td>

                            <!-- Status -->
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase {{ $row['status_badge'] }}">
                                    {{ $row['status'] }}
                                </span>
                            </td>

                            <!-- Remarks -->
                            <td class="p-3.5 text-neutral-300 text-xs max-w-xs truncate" title="{{ $row['remark'] }}">
                                {{ $row['remark'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-neutral-400 font-bold">
                                No Deposit or P2P Transaction records found matching your filters.
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
