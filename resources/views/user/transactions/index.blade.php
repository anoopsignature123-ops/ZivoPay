@extends('user.layouts.app')

@section('content')
    <div class="w-full space-y-6 font-sans">

        <!-- Top Header Banner (Matching User Management & Admin Flow Exactly) -->
        <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="pdf-num-badge">TL</span>
                    <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">DEX TRADE MEMBER
                        PORTAL</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient font-heading"> TRANSACTION HISTORY</h1>
                <p class="text-xs text-neutral-300 mt-1">Detailed history report of all credits, debits, deposits, package purchases,
                    and wallet activities.</p>
                </div>

            <!-- 2 Main Wallets Overview -->
            <div class="flex flex-wrap items-center gap-3">
                <div
                    class="px-5 py-3 rounded-xl bg-bg border border-amber-500/40 text-amber-300 text-xs font-bold font-mono flex items-center gap-2 shadow-lg shrink-0">
                    <i data-lucide="wallet" class="w-4 h-4 text-amber-400"></i>
                    <span>Deposit Wallet: <strong
                            class="text-emerald-400 text-sm font-black">${{ number_format($user->deposit_wallet, 2) }}</strong></span>
                </div>

                <div
                    class="px-5 py-3 rounded-xl bg-bg border border-amber-500/40 text-amber-300 text-xs font-bold font-mono flex items-center gap-2 shadow-lg shrink-0">
                    <i data-lucide="coins" class="w-4 h-4 text-amber-400"></i>
                    <span>Earning Wallet: <strong
                            class="text-emerald-400 text-sm font-black">${{ number_format($user->earning_wallet, 2) }}</strong></span>
                </div>
                </div>
                </div>

        <!-- TRANSACTIONS HISTORY CONTAINER -->
        <div class="bg-panel p-6 shadow-2xl rounded-2xl border border-amber-500/30 space-y-6">

            <!-- Filters & Search Bar -->
            <div
                class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-amber-500/20 pb-4">

                <!-- Quick Filter Tabs -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <a href="{{ route('user.transactions.index') }}"
                        class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ !request('wallet_type') && !request('type') ? 'bg-amber-500 text-black font-black shadow-md' : 'bg-bg border border-amber-500/30 text-neutral-300 hover:text-amber-400' }}">
                        All Transactions
                    </a>
                    <a href="{{ route('user.transactions.index', ['wallet_type' => 'deposit_wallet']) }}"
                        class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request('wallet_type') === 'deposit_wallet' ? 'bg-amber-500 text-black font-black shadow-md' : 'bg-bg border border-amber-500/30 text-neutral-300 hover:text-amber-400' }}">
                        💳 Deposit Wallet
                    </a>
                    <a href="{{ route('user.transactions.index', ['wallet_type' => 'earning_wallet']) }}"
                        class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ request('wallet_type') === 'earning_wallet' ? 'bg-amber-500 text-black font-black shadow-md' : 'bg-bg border border-amber-500/30 text-neutral-300 hover:text-amber-400' }}">
                        💰 Earning Wallet
                    </a>
                </div>

                <!-- Search Form (100% MATCHING REFERENCE UI CARD) -->
                <form action="{{ route('user.transactions.index') }}" method="GET"
                    class="flex flex-nowrap items-end gap-3 text-xs font-sans">
                    <div class="relative min-w-[200px]">
                        <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-amber-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Txn # or Remark..."
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-black/60 border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400">
                    </div>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-black font-black text-xs uppercase tracking-wider shadow-[0_0_15px_rgba(243,202,82,0.5)] transition flex items-center justify-center gap-1.5 shrink-0">
                        <i data-lucide="filter" class="w-3.5 h-3.5 text-black"></i> FILTER
                    </button>
                </form>
                </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead
                        class="bg-bg text-amber-400 uppercase text-xs font-bold font-heading tracking-wider border-b border-amber-500/30">
                        <tr>
                            <th class="p-4 rounded-l-xl">TXN NUMBER</th>
                            <th class="p-4">WALLET</th>
                            <th class="p-4">AMOUNT</th>
                            <th class="p-4">POST BALANCE</th>
                            <th class="p-4">DESCRIPTION / REMARK</th>
                            <th class="p-4">DATE & TIME</th>
                            <th class="p-4 rounded-r-xl">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-500/20 text-neutral-200">
                        @forelse($transactions as $txn)
                            <tr class="hover:bg-amber-500/10 transition">
                                <td class="p-4 font-mono font-bold text-amber-300 text-xs">
                                    {{ $txn->txn_number }}
                                </td>
                                <td class="p-4">
                                    <span
                                        class="px-2.5 py-1 rounded border text-[10px] font-black uppercase font-mono {{ $txn->wallet_type === 'deposit_wallet' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' }}">
                                        {{ $txn->wallet_type === 'deposit_wallet' ? 'DEPOSIT WALLET' : 'EARNING WALLET' }}
                                    </span>
                                </td>
                                <td
                                    class="p-4 font-mono font-black text-base {{ $txn->trx_type === '+' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $txn->trx_type }} ${{ number_format($txn->amount, 2) }}
                                </td>
                                <td class="p-4 font-mono text-xs text-white font-bold">
                                    ${{ number_format($txn->post_balance, 2) }}
                                </td>
                                <td class="p-4 text-xs text-neutral-300">
                                    <span class="max-w-[280px] inline-block truncate"
                                        title="{{ $txn->description }}">{{ $txn->description }}</span>
                                </td>
                                <td class="p-4">
                                    <div class="text-xs font-semibold text-white">
                                        {{ $txn->created_at ? $txn->created_at->format('M d, Y') : 'N/A' }}</div>
                                    <div class="text-[10px] text-neutral-400 font-mono">
                                        {{ $txn->created_at ? $txn->created_at->format('h:i A') : '' }}</div>
                                </td>
                                <td class="p-4">
                                    <span
                                        class="px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 text-[10px] font-black uppercase">
                                        {{ strtoupper($txn->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-neutral-400 text-sm">
                                    No financial transaction found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-4 border-t border-amber-500/20">
                {{ $transactions->links() }}
            </div>
            </div>

    </div>
@endsection
