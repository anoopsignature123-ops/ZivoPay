@extends('admin.layouts.app')

@section('content')
    <div class="w-full space-y-6 font-sans">

        <!-- Top Header Banner -->
        <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="pdf-num-badge">TL</span>
                    <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">DEX TRADE AUDIT & REPORTS</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient font-heading">SYSTEM TRANSACTION HISTORY</h1>
                <p class="text-xs text-neutral-300 mt-1">Audit, track, and inspect all financial transactions, deposits, package purchases, and wallet credits.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="px-5 py-3 rounded-xl bg-bg border border-amber-500/40 text-amber-300 text-xs font-bold font-mono flex items-center gap-2 shadow-lg shrink-0">
                    <i data-lucide="arrow-down-left" class="w-4 h-4 text-emerald-400"></i>
                    <span>Total Credits: <strong class="text-emerald-400 text-sm font-black">${{ number_format($totalCredits, 2) }}</strong></span>
                </div>
                <div class="px-5 py-3 rounded-xl bg-bg border border-amber-500/40 text-amber-300 text-xs font-bold font-mono flex items-center gap-2 shadow-lg shrink-0">
                    <i data-lucide="arrow-up-right" class="w-4 h-4 text-rose-400"></i>
                    <span>Total Debits: <strong class="text-rose-400 text-sm font-black">${{ number_format($totalDebits, 2) }}</strong></span>
                </div>
            </div>
        </div>

        <!-- FILTER & SEARCH PANEL (MATCHING 100% REFERENCE UI CARD IMAGE) -->
        <div class="p-3.5 rounded-2xl bg-bg/80 border border-amber-500/40 shadow-lg">
            <form action="{{ route('admin.transactions.index') }}" method="GET" class="flex flex-nowrap items-end gap-3 w-full overflow-x-auto text-xs font-sans pb-1">

                <!-- 1. FROM DATE -->
                <div class="w-40 shrink-0">
                    <label class="block text-[10px] font-extrabold text-amber-400 uppercase tracking-wider mb-1">FROM DATE</label>
                    <div class="relative">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-amber-400"></i>
                        <input type="text" name="start_date" value="{{ request('start_date') }}" placeholder="YYYY-MM-DD" class="datepicker w-full pl-8 pr-3 py-2.5 rounded-xl bg-black/60 border border-amber-500/40 text-white font-mono text-xs focus:outline-none focus:border-amber-400">
                    </div>
                </div>

                <!-- 2. TO DATE -->
                <div class="w-40 shrink-0">
                    <label class="block text-[10px] font-extrabold text-amber-400 uppercase tracking-wider mb-1">TO DATE</label>
                    <div class="relative">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-amber-400"></i>
                        <input type="text" name="end_date" value="{{ request('end_date') }}" placeholder="YYYY-MM-DD" class="datepicker w-full pl-8 pr-3 py-2.5 rounded-xl bg-black/60 border border-amber-500/40 text-white font-mono text-xs focus:outline-none focus:border-amber-400">
                    </div>
                </div>

                <!-- 3. SELECT WALLET -->
                <div class="w-44 shrink-0">
                    <label class="block text-[10px] font-extrabold text-amber-400 uppercase tracking-wider mb-1">SELECT WALLET</label>
                    <select name="wallet_type" class="w-full px-3 py-2.5 rounded-xl bg-black/60 border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400 cursor-pointer">
                        <option value="">All System Wallets</option>
                        <option value="deposit_wallet" {{ request('wallet_type') === 'deposit_wallet' ? 'selected' : '' }}>💳 Deposit Wallet</option>
                        <option value="earning_wallet" {{ request('wallet_type') === 'earning_wallet' ? 'selected' : '' }}>💰 Earning Wallet</option>
                    </select>
                </div>

                <!-- 4. TRANSACTION TYPE -->
                <div class="w-44 shrink-0">
                    <label class="block text-[10px] font-extrabold text-amber-400 uppercase tracking-wider mb-1">TRANSACTION TYPE</label>
                    <select name="type" class="w-full px-3 py-2.5 rounded-xl bg-black/60 border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400 cursor-pointer">
                        <option value="">All Types</option>
                        <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>Deposit Approval</option>
                        <option value="package_purchase" {{ request('type') === 'package_purchase' ? 'selected' : '' }}>Package Purchase</option>
                        <option value="admin_add_fund" {{ request('type') === 'admin_add_fund' ? 'selected' : '' }}>Admin Direct Credit</option>
                        <option value="daily_roi" {{ request('type') === 'daily_roi' ? 'selected' : '' }}>Daily ROI Credit</option>
                        <option value="direct_commission" {{ request('type') === 'direct_commission' ? 'selected' : '' }}>Direct Commission</option>
                    </select>
                </div>

                <!-- 5. SEARCH MEMBER / TXN # (FLEX-1 TO FILL) -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[10px] font-extrabold text-amber-400 uppercase tracking-wider mb-1">SEARCH MEMBER / TXN #</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-amber-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, code, TXN-..." class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-black/60 border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400">
                    </div>
                </div>

                <!-- 6. FILTER & RESET BUTTONS -->
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-black font-black text-xs uppercase tracking-wider shadow-[0_0_15px_rgba(243,202,82,0.5)] transition flex items-center justify-center gap-1.5 shrink-0" title="Apply Filter">
                        <i data-lucide="filter" class="w-3.5 h-3.5 text-black"></i> FILTER
                    </button>
                    <a href="{{ route('admin.transactions.index') }}" class="py-2.5 px-4 rounded-xl bg-black/60 border border-white/60 text-white hover:bg-white/10 font-bold text-xs transition flex items-center justify-center shrink-0" title="Reset All Filters">
                        Reset
                    </a>
                </div>

            </form>
        </div>

        <!-- TRANSACTIONS LOGS TABLE -->
        <div class="bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30 space-y-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-bg text-amber-400 uppercase text-xs font-bold font-heading tracking-wider border-b border-amber-500/30">
                        <tr>
                            <th class="p-4 rounded-l-xl">TXN NUMBER</th>
                            <th class="p-4">MEMBER DETAILS</th>
                            <th class="p-4">WALLET TYPE</th>
                            <th class="p-4">AMOUNT ($)</th>
                            <th class="p-4">POST BALANCE</th>
                            <th class="p-4">DESCRIPTION / REMARK</th>
                            <th class="p-4">DATE & TIME</th>
                            <th class="p-4 rounded-r-xl">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-500/20 text-neutral-200">
                        @forelse($transactions as $txn)
                        <tr class="hover:bg-amber-500/10 transition">
                            <!-- Txn Number -->
                            <td class="p-4 font-mono font-bold text-amber-300 text-xs">
                                {{ $txn->txn_number }}
                            </td>

                            <!-- Member Details -->
                            <td class="p-4">
                                @if($txn->user)
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($txn->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-white text-xs">{{ $txn->user->name }}</div>
                                            <div class="text-[11px] text-amber-400 font-mono flex items-center gap-1">
                                                <span>{{ $txn->user->referral_code }}</span>
                                                <a href="{{ route('admin.users.show', $txn->user->id) }}" class="text-neutral-400 hover:text-amber-300" title="View Member Profile">
                                                    <i data-lucide="external-link" class="w-3 h-3"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-neutral-500 text-xs font-mono">Deleted User</span>
                                @endif
                            </td>

                            <!-- Wallet Type -->
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded border text-[10px] font-black uppercase font-mono {{ $txn->wallet_type === 'deposit_wallet' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' }}">
                                    {{ $txn->wallet_type === 'deposit_wallet' ? '💳 DEPOSIT WALLET' : '💰 EARNING WALLET' }}
                                </span>
                            </td>

                            <!-- Amount -->
                            <td class="p-4 font-mono font-black text-base {{ $txn->trx_type === '+' ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $txn->trx_type }} ${{ number_format($txn->amount, 2) }}
                            </td>

                            <!-- Post Balance -->
                            <td class="p-4 font-mono text-xs text-white font-bold">
                                ${{ number_format($txn->post_balance, 2) }}
                            </td>

                            <!-- Description / Remark -->
                            <td class="p-4 text-xs text-neutral-300">
                                <span class="max-w-[320px] inline-block truncate" title="{{ $txn->description }}">{{ $txn->description }}</span>
                            </td>

                            <!-- Date & Time -->
                            <td class="p-4">
                                <div class="text-xs font-semibold text-white">{{ $txn->created_at ? $txn->created_at->format('M d, Y') : 'N/A' }}</div>
                                <div class="text-[10px] text-neutral-400 font-mono">{{ $txn->created_at ? $txn->created_at->format('h:i A') : '' }}</div>
                            </td>

                            <!-- Status -->
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 text-[10px] font-black uppercase">
                                    {{ strtoupper($txn->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-neutral-400 text-sm font-semibold">
                                    No financial transaction found matching your specified filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pt-2 border-t border-amber-500/20">
                {{ $transactions->links() }}
            </div>
        </div>

    </div>
@endsection
