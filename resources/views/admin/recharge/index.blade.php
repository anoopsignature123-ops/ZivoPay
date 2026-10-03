@extends('admin.layouts.app')

@section('title', 'Manage Mobile & Utility Recharges - Admin ZIVO PAY')

@section('content')
<div class="w-full space-y-6 font-sans">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.3)] flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] uppercase border border-emerald-500/40">A1TOPUP API GATEWAY</span>
                <span class="text-xs text-emerald-400 font-black tracking-[3px] uppercase">UTILITY SERVICE</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white font-heading">RECHARGE & UTILITY MANAGEMENT</h1>
            <p class="text-xs text-neutral-300 mt-1">Audit, monitor live gateway API balance, sync statuses, and manage user recharges.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="px-5 py-3 rounded-2xl bg-[#02180f] border border-emerald-500/40 text-right">
                <span class="text-[10px] uppercase tracking-wider text-emerald-400 font-bold block">A1Topup Provider Balance</span>
                <span class="text-xl sm:text-2xl font-black text-emerald-400 font-mono">₹{{ number_format((float) ($providerBalance['balance'] ?? 0), 2) }}</span>
            </div>
            <div class="px-5 py-3 rounded-2xl bg-[#02180f] border border-emerald-500/40 text-right">
                <span class="text-[10px] uppercase tracking-wider text-emerald-400 font-bold block">Total Success Volume</span>
                <span class="text-xl sm:text-2xl font-black text-white font-mono">₹{{ number_format($stats['total_volume'], 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-500/20 border border-rose-500/50 text-rose-400 text-xs font-bold flex items-center gap-2">
            <i data-lucide="x-circle" class="w-4 h-4 text-rose-400"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- ZivoPay Theme 8 Stats Cards (Matching ZivoPay Admin Theme) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. TOTAL SUCCESS -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 relative shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-neutral-400 uppercase tracking-wider">TOTAL SUCCESS</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                </div>
            </div>
            <span class="text-2xl font-black font-mono text-emerald-400 block mt-2">{{ number_format($stats['total_success_count']) }}</span>
            <span class="text-[10px] text-neutral-400 block mt-1">Total Volume: ₹{{ number_format($stats['total_volume'], 2) }}</span>
        </div>

        <!-- 2. TOTAL FAILURE -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-rose-500/30 relative shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-neutral-400 uppercase tracking-wider">TOTAL FAILURE</span>
                <div class="w-8 h-8 rounded-xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-center text-rose-400">
                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                </div>
            </div>
            <span class="text-2xl font-black font-mono text-rose-400 block mt-2">{{ number_format($stats['total_failed_count']) }}</span>
            <span class="text-[10px] text-neutral-400 block mt-1">Failed / Rejected Recharges</span>
        </div>

        <!-- 3. TOTAL PENDING -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-amber-500/30 relative shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-neutral-400 uppercase tracking-wider">TOTAL PENDING</span>
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
                    <i data-lucide="hourglass" class="w-4 h-4"></i>
                </div>
            </div>
            <span class="text-2xl font-black font-mono text-amber-400 block mt-2">{{ number_format($stats['total_pending_count']) }}</span>
            <span class="text-[10px] text-neutral-400 block mt-1">Under Processing with Gateway</span>
        </div>

        <!-- 4. WALLET TOPUP -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-blue-500/30 relative shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-neutral-400 uppercase tracking-wider">WALLET TOPUP</span>
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-blue-400">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                </div>
            </div>
            <span class="text-2xl font-black font-mono text-white block mt-2">₹{{ number_format($stats['wallet_topup'], 2) }}</span>
            <span class="text-[10px] text-neutral-400 block mt-1">Total User Add Fund Volume</span>
        </div>

        <!-- 5. OPENING BALANCE -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 relative shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-neutral-400 uppercase tracking-wider">OPENING BALANCE</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
            </div>
            <span class="text-2xl font-black font-mono text-emerald-400 block mt-2">₹{{ number_format($stats['opening_balance'], 2) }}</span>
            <span class="text-[10px] text-neutral-400 block mt-1">Aggregate User Fund Wallet</span>
        </div>

        <!-- 6. RECHARGE DEBIT -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 relative shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-neutral-400 uppercase tracking-wider">RECHARGE DEBIT</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                    <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                </div>
            </div>
            <span class="text-2xl font-black font-mono text-white block mt-2">₹{{ number_format($stats['recharge_debit'], 2) }}</span>
            <span class="text-[10px] text-neutral-400 block mt-1">Total Recharge Amount Debited</span>
        </div>

        <!-- 7. REFUND CREDIT -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-cyan-500/30 relative shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-neutral-400 uppercase tracking-wider">REFUND CREDIT</span>
                <div class="w-8 h-8 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400">
                    <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                </div>
            </div>
            <span class="text-2xl font-black font-mono text-cyan-400 block mt-2">₹{{ number_format($stats['refund_credit'], 2) }}</span>
            <span class="text-[10px] text-neutral-400 block mt-1">Auto-Refund Credit Amount</span>
        </div>

        <!-- 8. PROFIT -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-purple-500/30 relative shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-neutral-400 uppercase tracking-wider">PROFIT</span>
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-purple-400">
                    <i data-lucide="coins" class="w-4 h-4"></i>
                </div>
            </div>
            <span class="text-2xl font-black font-mono text-purple-400 block mt-2">₹{{ number_format($stats['profit'], 2) }}</span>
            <span class="text-[10px] text-neutral-400 block mt-1">Estimated Commission Profit</span>
        </div>
    </div>

    <!-- Filter Bar (Exact Match with ZivoPay Theme Screenshot media_1790857591743.png) -->
    <div class="bg-[#042718] p-4 sm:p-5 rounded-3xl border border-emerald-500/40 shadow-[0_0_25px_rgba(16,185,129,0.15)]">
        <form action="{{ route('admin.recharges.index') }}" method="GET" class="flex flex-wrap items-end gap-3 sm:gap-4">
            <!-- SEARCH USER / TRX -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-1.5">SEARCH USER / TRX</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="TRX ID, User, Code..." class="w-full px-4 py-2.5 rounded-2xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold placeholder-neutral-500 focus:outline-none focus:border-emerald-400" style="background-color: #01140c !important;">
            </div>

            <!-- FROM DATE -->
            <div class="w-36 sm:w-40">
                <label class="block text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-1.5">FROM DATE</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-3.5 py-2.5 rounded-2xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400" style="background-color: #01140c !important;">
            </div>

            <!-- TO DATE -->
            <div class="w-36 sm:w-40">
                <label class="block text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-1.5">TO DATE</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-3.5 py-2.5 rounded-2xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400" style="background-color: #01140c !important;">
            </div>

            <!-- STATUS -->
            <div class="w-32">
                <label class="block text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-1.5">STATUS</label>
                <select name="status" class="w-full px-3 py-2.5 rounded-2xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400" style="background-color: #01140c !important;">
                    <option value="" class="bg-[#042718]">All Status</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }} class="bg-[#042718]">Success</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }} class="bg-[#042718]">Pending</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }} class="bg-[#042718]">Failed</option>
                    <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }} class="bg-[#042718]">Refunded</option>
                </select>
            </div>

            <!-- SERVICE TYPE -->
            <div class="w-32">
                <label class="block text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-1.5">SERVICE</label>
                <select name="service_type" class="w-full px-3 py-2.5 rounded-2xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400" style="background-color: #01140c !important;">
                    <option value="" class="bg-[#042718]">All Services</option>
                    <option value="mobile" {{ request('service_type') === 'mobile' ? 'selected' : '' }} class="bg-[#042718]">Mobile</option>
                    <option value="dth" {{ request('service_type') === 'dth' ? 'selected' : '' }} class="bg-[#042718]">DTH</option>
                    <option value="fastag" {{ request('service_type') === 'fastag' ? 'selected' : '' }} class="bg-[#042718]">FASTag</option>
                    <option value="electricity" {{ request('service_type') === 'electricity' ? 'selected' : '' }} class="bg-[#042718]">Electricity</option>
                    <option value="postpaid" {{ request('service_type') === 'postpaid' ? 'selected' : '' }} class="bg-[#042718]">Postpaid</option>
                </select>
            </div>

            <!-- BUTTONS -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-400 hover:from-emerald-400 hover:to-emerald-300 text-black font-black text-xs uppercase tracking-wider shadow-[0_0_15px_rgba(16,185,129,0.4)] transition">
                    FILTER
                </button>
                <a href="{{ route('admin.recharges.index') }}" class="px-4 py-2.5 rounded-2xl bg-[#01140c] hover:bg-neutral-800 border border-emerald-500/40 text-neutral-300 font-bold text-xs uppercase tracking-wider transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Recharge Ledger Table -->
    <div class="bg-[#042718] rounded-3xl border border-emerald-500/30 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-neutral-200">
                <thead class="bg-[#02180f] text-[10px] uppercase font-black tracking-wider text-emerald-400 border-b border-emerald-500/20">
                    <tr>
                        <th class="py-3.5 px-4">Order ID & Date</th>
                        <th class="py-3.5 px-4">User Details</th>
                        <th class="py-3.5 px-4">Service & Operator</th>
                        <th class="py-3.5 px-4">Number / ID</th>
                        <th class="py-3.5 px-4 text-right">Amount</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Gateway Ref (TxID / OpID)</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/10 font-semibold">
                    @forelse($recharges as $recharge)
                        <tr class="hover:bg-emerald-500/5 transition">
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-bold text-white block">{{ $recharge->order_id }}</span>
                                <span class="text-[10px] text-neutral-400 block mt-0.5">{{ $recharge->created_at->format('M d, Y H:i A') }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white">{{ $recharge->user->name ?? 'N/A' }}</div>
                                <span class="text-[10px] font-mono text-emerald-400">{{ $recharge->user->referral_code ?? '' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 text-[10px] uppercase font-black inline-block mb-1 border border-emerald-500/20">{{ strtoupper($recharge->service_type) }}</span>
                                <div class="text-white text-xs font-bold">{{ $recharge->operator_name ?? $recharge->operator_code }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-emerald-300">
                                {{ $recharge->number }}
                                @if($recharge->circle_code)
                                    <span class="text-[10px] text-neutral-400 block font-sans">Circle: {{ $recharge->circle_code }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-black text-white text-sm">
                                ₹{{ number_format((float)$recharge->amount, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if(strtolower($recharge->status) === 'success')
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 text-[10px] font-black uppercase">Success</span>
                                @elseif(strtolower($recharge->status) === 'pending')
                                    <span class="px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/40 text-[10px] font-black uppercase">Pending</span>
                                @elseif(strtolower($recharge->status) === 'refunded')
                                    <span class="px-2.5 py-1 rounded-full bg-cyan-500/20 text-cyan-400 border border-cyan-500/40 text-[10px] font-black uppercase">Refunded</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/40 text-[10px] font-black uppercase">Failed</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono text-[11px]">
                                @if($recharge->txid || $recharge->opid)
                                    <span class="text-emerald-300 block">TxID: {{ $recharge->txid ?? '-' }}</span>
                                    <span class="text-neutral-400 block text-[10px]">OpID: {{ $recharge->opid ?? '-' }}</span>
                                @else
                                    <span class="text-neutral-500">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Sync Status Button -->
                                    <form action="{{ route('admin.recharges.sync', $recharge->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="Sync Live Status" class="p-2 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/40 text-emerald-400 border border-emerald-500/40 transition">
                                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>

                                    <!-- Force Refund Button -->
                                    @if(in_array(strtolower($recharge->status), ['pending', 'failed']))
                                        <form action="{{ route('admin.recharges.refund', $recharge->id) }}" method="POST" onsubmit="return confirm('Refund ₹{{ number_format($recharge->amount, 2) }} back to user fund wallet?');">
                                            @csrf
                                            <button type="submit" title="Force Refund to Wallet" class="p-2 rounded-lg bg-cyan-500/20 hover:bg-cyan-500/40 text-cyan-400 border border-cyan-500/40 transition">
                                                <i data-lucide="undo-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-neutral-400 font-medium">
                                No recharge records found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recharges->hasPages())
            <div class="p-4 bg-[#02180f] border-t border-emerald-500/20">
                {{ $recharges->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
