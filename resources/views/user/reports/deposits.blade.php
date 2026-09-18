@extends('user.layouts.app')

@section('title', 'Add Fund & Fund Wallet History - ZIVO PAY')

@section('content')
<div class="max-w-6xl mx-auto space-y-5 font-sans">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.25)] flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[11px] font-black uppercase tracking-widest border border-emerald-500/40">
                FUND WALLET AUDIT LOG
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight mt-1 font-heading">ADD FUND & FUND WALLET HISTORY</h1>
            <p class="text-xs text-neutral-300 mt-1">Detailed audit report of all Fund Wallet deposits, UTR references, payment methods, and credit statuses.</p>
        </div>

        <div class="px-5 py-3 rounded-2xl bg-[#02180f] border-2 border-emerald-400 text-right shrink-0">
            <span class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-wider block">Available Fund Wallet</span>
            <span class="text-2xl font-black text-white font-mono">₹{{ number_format($user->deposit_wallet, 2) }}</span>
        </div>
    </div>

    <!-- Stats Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between text-emerald-400 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Total Approved Funds</span>
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-400"></i>
            </div>
            <h3 class="text-2xl font-black text-white font-mono">₹{{ number_format($totalApproved, 2) }}</h3>
            <span class="text-[10px] text-emerald-400 font-semibold mt-1 block">Credited to Fund Wallet</span>
        </div>

        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between text-amber-400 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Total Pending Approvals</span>
                <i data-lucide="clock" class="w-5 h-5 text-amber-400"></i>
            </div>
            <h3 class="text-2xl font-black text-amber-300 font-mono">₹{{ number_format($totalPending, 2) }}</h3>
            <span class="text-[10px] text-neutral-400 font-semibold mt-1 block">Verification Queue</span>
        </div>

        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between text-emerald-400 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Total Transactions</span>
                <i data-lucide="receipt" class="w-5 h-5 text-emerald-400"></i>
            </div>
            <h3 class="text-2xl font-black text-white font-mono">{{ $totalCount }}</h3>
            <span class="text-[10px] text-neutral-400 font-semibold mt-1 block">All Time Requests</span>
        </div>
    </div>

    <!-- Search & Filter Form Bar -->
    <div class="p-4 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-xl">
        <form action="{{ route('user.reports.deposits') }}" method="GET" class="zivo-filter-bar">
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

            <!-- STATUS -->
            <div class="zivo-filter-field-select">
                <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">STATUS</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400 transition cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- SEARCH REF / UTR / METHOD -->
            <div class="zivo-filter-field-search">
                <label class="block text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1">SEARCH REF / UTR / METHOD</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-emerald-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ref ID, UTR Number, UPI..."
                        class="w-full pl-9 pr-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="zivo-filter-actions">
                <button type="submit" class="py-2 px-5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-black font-black uppercase tracking-wider text-xs hover:shadow-[0_0_20px_rgba(16,185,129,0.7)] transition flex items-center justify-center gap-1.5 shadow cursor-pointer">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i> FILTER
                </button>
                <a href="{{ route('user.reports.deposits') }}" class="py-2 px-3.5 rounded-xl bg-black/60 border border-emerald-500/30 text-neutral-300 font-bold text-xs hover:text-white hover:border-emerald-400 transition text-center shrink-0">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Deposit History Report Table -->
    <div class="bg-[#042718] rounded-3xl border border-emerald-500/30 overflow-hidden shadow-2xl p-6 space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-[#02180f] text-emerald-400 font-extrabold uppercase tracking-wider border-b border-emerald-500/30">
                    <tr>
                        <th class="p-3.5">Ref ID & Date</th>
                        <th class="p-3.5">Payment Method</th>
                        <th class="p-3.5">Amount (₹)</th>
                        <th class="p-3.5">UTR / Reference Hash</th>
                        <th class="p-3.5">Proof Screenshot</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5">System Remark / Note</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/10 text-neutral-200 font-medium">
                    @forelse($deposits as $deposit)
                        <tr class="hover:bg-emerald-500/5 transition">
                            <td class="p-3.5">
                                <span class="font-mono font-bold text-white text-sm block">{{ $deposit->deposit_ref }}</span>
                                <span class="text-[10px] text-neutral-400 font-mono block">{{ $deposit->created_at ? $deposit->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-300 text-[10px] font-black uppercase border border-emerald-500/30 inline-block">
                                    {{ $deposit->payment_method }}
                                </span>
                            </td>
                            <td class="p-3.5 font-mono font-bold text-emerald-400 text-sm">
                                ₹{{ number_format($deposit->amount, 2) }}
                            </td>
                            <td class="p-3.5 font-mono font-bold text-emerald-300 text-xs">
                                {{ $deposit->trx_hash ?? 'N/A' }}
                            </td>
                            <td class="p-3.5">
                                @if($deposit->proof_file)
                                    <a href="{{ asset('storage/' . $deposit->proof_file) }}" target="_blank" class="px-2.5 py-1 rounded-xl bg-teal-500/20 border border-teal-500/40 text-teal-300 text-[10px] font-bold hover:bg-teal-500 hover:text-black transition inline-flex items-center gap-1">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i> View Proof
                                    </a>
                                @else
                                    <span class="text-neutral-500 text-[10px] italic">No Proof Uploaded</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase 
                                    {{ $deposit->status === 'approved' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 shadow-[0_0_10px_rgba(16,185,129,0.3)]' : ($deposit->status === 'pending' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40') }}">
                                    {{ $deposit->status }}
                                </span>
                            </td>
                            <td class="p-3.5 text-neutral-300 text-xs max-w-xs truncate" title="{{ $deposit->admin_remark ?? '-' }}">
                                {{ $deposit->admin_remark ?? 'Direct Instant Credit' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-neutral-400 font-bold">
                                No deposit history records found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($deposits->hasPages())
            <div class="pt-4 border-t border-emerald-500/20">
                {{ $deposits->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
