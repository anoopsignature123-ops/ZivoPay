@extends('admin.layouts.app')

@section('title', 'ZIVO PAY - Admin 24x7 Withdrawal Approvals')

@section('content')
    <div class="space-y-6 font-sans">

        <!-- Header Banner -->
        <div
            class="p-6 sm:p-8 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span
                        class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/40">
                        WITHDRAWAL MANAGEMENT
                    </span>
                    <span class="text-xs text-emerald-400 font-black tracking-[2px] uppercase">ZIVO PAY AUDIT</span>
                    </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight mt-1 font-heading">24X7
                    WITHDRAWAL APPROVALS QUEUE</h1>
                <p class="text-xs text-neutral-300 mt-1">Approve INR withdrawal requests, send the saved net USDT amount
                    through the payout contract, then record its confirmed BSC transaction.</p>
                </div>

            <div class="flex flex-wrap items-center gap-3">
                <span class="px-4 py-2 rounded-xl bg-[#02180f] border border-emerald-500/30 text-xs font-bold text-neutral-300">
                    Total Requests: <strong class="text-emerald-400 font-mono">{{ $stats['total_requests'] }}</strong>
                </span>
                </div>
                </div>

        @if (session('success'))
            <div
                class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-300 text-xs font-bold flex items-center gap-2 shadow-md">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400 shrink-0"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if ($contractAddress !== '')
            <div class="rounded-2xl border border-sky-500/30 bg-sky-500/10 p-4 text-xs text-sky-100">
                USDT payout contract: <a class="font-mono text-sky-300 hover:underline"
                    href="https://bscscan.com/address/{{ urlencode($contractAddress) }}" target="_blank"
                    rel="noopener noreferrer">{{ $contractAddress }}</a>
            </div>
        @else
            <div class="rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4 text-xs text-amber-100">
                Payout contract is not configured. Set <code>USDT_PAYOUT_CONTRACT_ADDRESS</code> and
                <code>BSC_RPC_URL</code> in the server environment before processing payouts.
            </div>
        @endif

        @if ($errors->any())
            <div
                class="p-4 rounded-2xl bg-rose-500/20 border border-rose-500/50 text-rose-300 text-xs font-bold space-y-1 shadow-md">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400 shrink-0"></i>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Stat Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
            <!-- 1. Pending Queue Amount -->
            <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
                <div class="flex items-center justify-between text-teal-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Pending Approvals</span>
                    <span
                        class="px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300 font-mono font-bold text-[10px]">{{ $stats['pending_count'] }}
                        Requests</span>
                    </div>
                <h3 class="text-2xl font-black text-teal-300 font-mono">₹{{ number_format($stats['pending_amount'], 2) }}
                </h3>
                <span class="text-[10px] text-neutral-400 font-semibold mt-1 block">Needs Action</span>
                </div>

            <!-- 2. Approved Payouts Total -->
            <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
                <div class="flex items-center justify-between text-emerald-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Approved · Awaiting
                        Payout</span>
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                    </div>
                <h3 class="text-2xl font-black text-emerald-400 font-mono">
                    ₹{{ number_format($stats['approved_amount'], 2) }}</h3>
                <span class="text-[10px] text-emerald-400 font-semibold mt-1 block">Net INR reserved for payout</span>
                </div>

                <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
                    <div class="flex items-center justify-between text-emerald-400 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Completed Payouts</span>
                        <i data-lucide="badge-check" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-2xl font-black text-emerald-300 font-mono">
                        ₹{{ number_format($stats['completed_amount'], 2) }}</h3>
                    <span class="text-[10px] text-neutral-400 font-semibold mt-1 block">BSC transaction confirmed</span>
                </div>

            <!-- 3. Rejected Count -->
            <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
                <div class="flex items-center justify-between text-rose-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Rejected Requests</span>
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                </div>
                <h3 class="text-2xl font-black text-rose-300 font-mono">{{ $stats['rejected_count'] }}</h3>
                <span class="text-[10px] text-neutral-400 font-semibold mt-1 block">Refunded to User Wallet</span>
            </div>

            <!-- 4. Total Requests -->
            <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
                <div class="flex items-center justify-between text-emerald-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">All Payout Requests</span>
                    <i data-lucide="layers" class="w-5 h-5"></i>
                </div>
                <h3 class="text-2xl font-black text-white font-mono">{{ $stats['total_requests'] }}</h3>
                <span class="text-[10px] text-neutral-400 font-semibold mt-1 block">Lifetime Requests</span>
            </div>
            </div>

        <!-- Filter & Table Container -->
        <div class="p-6 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl space-y-6">
            <!-- Search & Status Filter Section -->
            <div class="space-y-4 border-b border-emerald-500/20 pb-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <!-- Search Form -->
                    <form action="{{ route('admin.withdrawals') }}" method="GET" class="flex items-center gap-2 w-full sm:w-80">
                        @if (request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        <div class="relative flex-1">
                            <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Trx ID, Member, Code..."
                                class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400 transition">
                            </div>
                        <button type="submit"
                            class="px-4 py-2.5 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 font-bold text-xs hover:bg-emerald-500/30 transition shrink-0">
                            Search
                        </button>
                        </form>

                    <!-- Status Filter Pills Container -->
                    <div class="flex items-center gap-2 overflow-x-auto max-w-full pb-1 sm:pb-0 shrink-0">
                        <a href="{{ route('admin.withdrawals', array_merge(request()->except('status'), ['status' => ''])) }}"
                            class="whitespace-nowrap px-4 py-2 rounded-xl text-xs font-bold transition {{ !request('status') ? 'bg-emerald-500 text-black font-black shadow-md' : 'bg-[#01140c] border border-emerald-500/30 text-neutral-300 hover:text-emerald-400' }}">
                            All Requests
                        </a>
                        <a href="{{ route('admin.withdrawals', array_merge(request()->except('status'), ['status' => 'pending'])) }}"
                            class="whitespace-nowrap px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'pending' ? 'bg-teal-500 text-black font-black shadow-md' : 'bg-[#01140c] border border-emerald-500/30 text-neutral-300 hover:text-teal-300' }}">
                            Pending Queue ({{ $stats['pending_count'] }})
                        </a>
                        <a href="{{ route('admin.withdrawals', array_merge(request()->except('status'), ['status' => 'approved'])) }}"
                            class="whitespace-nowrap px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'approved' ? 'bg-emerald-500 text-black font-black shadow-md' : 'bg-[#01140c] border border-emerald-500/30 text-neutral-300 hover:text-emerald-400' }}">
                            Approved · Awaiting Payout
                            </a>
                            <a href="{{ route('admin.withdrawals', array_merge(request()->except('status'), ['status' => 'completed'])) }}"
                                class="whitespace-nowrap px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'completed' ? 'bg-emerald-500 text-black font-black shadow-md' : 'bg-[#01140c] border border-emerald-500/30 text-neutral-300 hover:text-emerald-400' }}">
                                Completed
                            </a>
                            <a href="{{ route('admin.withdrawals', array_merge(request()->except('status'), ['status' => 'rejected'])) }}"
                                class="whitespace-nowrap px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'rejected' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/50 font-black shadow-md' : 'bg-[#01140c] border border-emerald-500/30 text-neutral-300 hover:text-rose-400' }}">
                                Rejected Requests
                            </a>
                            </div>
                            </div>
                            </div>

            @if (in_array(request('status'), ['pending', 'approved'], true))
                <div id="bulkActionsBar"
                    class="flex flex-wrap items-center gap-3 rounded-2xl border border-emerald-500/25 bg-[#01140c] p-4">
                    <label class="flex items-center gap-2 text-xs text-neutral-300">
                        <input type="checkbox" id="selectAll" onchange="toggleAll(this)" class="accent-emerald-500">
                        Select all on this page
                    </label>
                    <span class="text-xs text-neutral-400"><strong id="selectedCount" class="text-white">0</strong> selected
                        · Net payout: <strong id="selectedTotal" class="text-emerald-300">0.000000 USDT</strong></span>
                    @if (request('status') === 'pending')
                        <button type="button" onclick="bulkApprove()"
                            class="rounded-xl bg-emerald-500 px-4 py-2 text-xs font-black text-black hover:bg-emerald-400">Approve
                            selected</button>
                    @else
                        <button type="button" onclick="showPayoutModal()" @disabled($contractAddress === '')
                            class="rounded-xl bg-sky-500 px-4 py-2 text-xs font-black text-white hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-50">Pay
                            selected via contract</button>
                    @endif
                </div>
            @endif

            <!-- Approvals Queue Table -->
            @if ($withdrawals->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-neutral-300 whitespace-nowrap">
                        <thead
                            class="bg-[#02180f] text-emerald-400 uppercase text-[10px] font-bold border-b border-emerald-500/30">
                            <tr>
                                @if (in_array(request('status'), ['pending', 'approved'], true))
                                    <th class="p-3.5"><input type="checkbox" id="tableSelectAll"
                                            onchange="toggleAll(this)" class="accent-emerald-500"
                                            aria-label="Select all requests"></th>
                                @endif
                                <th class="p-3.5">Trx ID & Date</th>
                                <th class="p-3.5">Member Details</th>
                                <th class="p-3.5">Method</th>
                                <th class="p-3.5">Source Wallet / USDT address</th>
                                <th class="p-3.5">Requested (INR)</th>
                                <th class="p-3.5">Fee (5%)</th>
                                <th class="p-3.5">Net INR</th>
                                <th class="p-3.5">Send (USDT)</th>
                                <th class="p-3.5">Status</th>
                                <th class="p-3.5">Admin Remark / Tx Hash</th>
                                <th class="p-3.5 text-center">Actions Menu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-emerald-500/10">
                            @foreach ($withdrawals as $wth)
                                <tr class="hover:bg-emerald-500/5 transition">
                                    @if (in_array(request('status'), ['pending', 'approved'], true))
                                        <td class="p-3.5"><input type="checkbox"
                                                class="withdrawal-check accent-emerald-500" value="{{ $wth->id }}"
                                                data-address="{{ $wth->account_details }}"
                                                data-usdt="{{ $wth->usdt_amount }}" onchange="updateSelection()"
                                                aria-label="Select withdrawal {{ $wth->trx_id }}"
                                                @disabled($wth->usdt_amount === null || !preg_match('/^0x[a-fA-F0-9]{40}$/', (string) $wth->account_details))></td>
                                    @endif
                                    <td class="p-3.5">
                                        <span class="font-mono font-bold text-white block">{{ $wth->trx_id }}</span>
                                        <span
                                            class="text-[10px] text-neutral-400 block">{{ $wth->created_at ? $wth->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                                    </td>
                                    <td class="p-3.5">
                                        @if ($wth->user)
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0 border border-emerald-500/30">
                                                    <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                                </div>
                                                <div>
                                                    <a href="{{ route('admin.users.show', $wth->user->id) }}"
                                                        class="font-bold text-white hover:text-emerald-400 hover:underline block">
                                                        {{ $wth->user->name }}
                                                    </a>
                                                    <span class="text-[10px] text-emerald-400 font-mono block">{{ $wth->user->referral_code }}
                                                        • {{ $wth->user->email }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-neutral-500 italic">User Deleted</span>
                                        @endif
                                        </td>
                                        <td class="p-3.5 font-semibold text-emerald-300">{{ $wth->payment_method }}</td>
                                        <td class="p-3.5 max-w-xs">
                                        <div class="space-y-1 max-w-xs">
                                            <span
                                                class="block text-[10px] uppercase text-emerald-300">{{ $wth->source_wallet === 'deposit_wallet' ? 'Fund Wallet' : 'Earning Wallet' }}</span>
                                            <span class="truncate font-medium text-neutral-200 block text-xs"
                                                title="{{ $wth->account_details }}">
                                                {{ $wth->account_details }}
                                            </span>
                                            <button type="button" onclick="copyToClipboard(@js($wth->account_details), 'USDT address')" title="Copy Account Details"
                                                class="p-1 rounded bg-emerald-500/10 hover:bg-emerald-500/30 text-emerald-400 border border-emerald-500/30 transition shrink-0">
                                                <i data-lucide="copy" class="w-3 h-3"></i>
                                            </button>
                                            </div>
                                            </td>
                                    <td class="p-3.5 font-mono font-bold text-white">₹{{ number_format($wth->amount, 2) }}
                                    </td>
                                    <td class="p-3.5 font-mono text-rose-400">-₹{{ number_format($wth->charge, 2) }}</td>
                                    <td class="p-3.5 font-mono font-bold text-emerald-400 text-sm">
                                        ₹{{ number_format($wth->final_amount, 2) }}</td>
                                    <td class="p-3.5 font-mono font-black text-white">
                                        {{ $wth->usdt_amount !== null ? number_format((float) $wth->usdt_amount, 6) . ' USDT' : '—' }}
                                    </td>
                                    <td class="p-3.5">
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase
                                                                                {{ in_array($wth->status, ['approved', 'completed'], true) ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 shadow-[0_0_10px_rgba(16,185,129,0.3)]' : '' }}
                                        {{ $wth->status === 'pending' ? 'bg-teal-500/20 text-teal-300 border border-teal-500/40' : '' }}
                                        {{ $wth->status === 'rejected' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/40' : '' }}">
                                            {{ $wth->status === 'approved' ? 'Approved · Awaiting payout' : $wth->status }}
                                            </span>
                                            </td>
                                    <td class="p-3.5 font-mono text-[11px] text-neutral-300 max-w-xs truncate"
                                        title="{{ $wth->provider_reference ?? ($wth->admin_remarks ?? '-') }}">
                                        @if ($wth->provider_reference)
                                            <a href="https://bscscan.com/tx/{{ urlencode($wth->provider_reference) }}" target="_blank" rel="noopener noreferrer"
                                                class="text-emerald-300 hover:underline">View transaction</a>
                                        @else
                                            {{ $wth->admin_remarks ?? '-' }}
                                        @endif
                                    </td>
                                    <td class="p-3.5 text-center">
                                        @if (in_array($wth->status, ['pending', 'approved'], true))
                                            <div class="flex items-center justify-center gap-2">
                                                    <button type="button"
                                                        onclick="openRejectModal({{ $wth->id }}, @js($wth->trx_id), @js($wth->user ? $wth->user->name : 'Member'), '{{ number_format($wth->amount, 2) }}', @js($wth->source_wallet === 'deposit_wallet' ? 'Fund Wallet' : 'Earning Wallet'))"
                                                        title="Reject Request & Refund Balance" aria-label="Reject Request"
                                                        class="w-8 h-8 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-300 hover:bg-rose-500 hover:text-white font-bold transition-all duration-200 flex items-center justify-center shadow-sm hover:scale-110 shrink-0">
                                                        <i data-lucide="x" class="w-4 h-4"></i>
                                                    </button>
                                                </div>
                                        @else
                                            <span class="text-neutral-500 text-[11px] font-semibold italic">Processed</span>
                                        @endif
                                            </td>
                                            </tr>
                            @endforeach
                                            </tbody>
                                            </table>
                                            </div>

                <div class="pt-4 border-t border-emerald-500/20">
                    {{ $withdrawals->links() }}
                </div>
            @else
                    <div class="p-12 text-center text-neutral-400 space-y-3">
                        <div
                            class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mx-auto border border-emerald-500/20">
                            <i data-lucide="inbox" class="w-6 h-6"></i>
                        </div>
                        <p class="text-sm font-semibold text-neutral-300">No withdrawal requests found matching the current
                            search & status filters.</p>
                    </div>
                @endif
                </div>

    </div>

    <div id="payoutModal" class="fixed inset-0 z-50 hidden flex items-center justify-center overflow-y-auto bg-black/80 p-4" role="dialog" aria-modal="true" aria-labelledby="payoutModalTitle">
        <div class="my-auto max-h-[calc(100dvh-2rem)] w-full max-w-2xl space-y-4 overflow-y-auto rounded-3xl border border-sky-500/50 bg-[#042718] p-4 shadow-2xl sm:p-6">
            <div class="flex items-center justify-between">
                <h3 id="payoutModalTitle" class="text-lg font-black text-white">BSC USDT bulk payout</h3><button type="button"
                    onclick="closePayoutModal()" class="text-neutral-400">✕</button>
            </div>
            <p class="text-xs text-neutral-300">Review selected recipient addresses and net USDT amounts. MetaMask must use
                BNB Smart Chain (56), and the connected account must own the configured payout contract.</p>
            <div class="max-h-56 overflow-auto rounded-xl border border-emerald-500/20">
                <table class="w-full text-left text-xs">
                    <thead class="sticky top-0 bg-[#02180f] text-emerald-300">
                        <tr>
                            <th class="p-2">Address</th>
                            <th class="p-2 text-right">USDT</th>
                        </tr>
                    </thead>
                    <tbody id="payoutRows" class="divide-y divide-emerald-500/10"></tbody>
                </table>
            </div>
            <div id="payoutMessage" role="status"
                class="hidden rounded-xl border border-sky-500/30 bg-sky-500/10 p-3 text-xs text-sky-100"></div>
            <div class="flex justify-end gap-3"><button type="button" onclick="closePayoutModal()"
                    class="rounded-xl border border-neutral-600 px-4 py-2 text-xs text-neutral-200">Cancel</button><button
                    id="payoutButton" type="button" onclick="processPayout()"
                    class="rounded-xl bg-sky-500 px-4 py-2 text-xs font-black text-white">Connect MetaMask & pay</button>
            </div>
        </div>
        </div>

    <!-- Reject Withdrawal Modal -->
    <div id="rejectModal" class="fixed inset-0 z-50 hidden bg-black/80 flex items-center justify-center p-4">
        <div class="w-full max-w-md p-6 rounded-3xl bg-[#042718] border-2 border-rose-500/60 shadow-2xl space-y-4 relative">
            <button type="button" onclick="closeRejectModal()" class="absolute top-4 right-4 text-neutral-400 hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <h3 class="text-lg font-black text-rose-400 uppercase tracking-tight flex items-center gap-2 font-heading">
                <i data-lucide="x-circle" class="w-5 h-5 text-rose-400"></i> Reject Payout Request
            </h3>

            <form id="rejectForm" action="" method="POST" class="space-y-4">
                @csrf
                <div>
                    <span class="block text-xs font-bold text-neutral-400 uppercase">Target Member:</span>
                    <strong id="rejectUserName" class="text-sm font-bold text-rose-300 block"></strong>
                </div>

                <div>
                    <span class="block text-xs font-bold text-neutral-400 uppercase">Refund Amount to Original
                        Wallet:</span>
                    <strong id="rejectAmount" class="text-lg font-black text-white font-mono block"></strong>
                    <span id="rejectWallet" class="block text-xs text-emerald-300"></span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-rose-400 uppercase mb-1">Reason for Rejection *:</label>
                    <input type="text" name="admin_remarks" required placeholder="e.g. Invalid Bank Account Number / Incorrect IFSC Code"
                        class="w-full px-4 py-2.5 rounded-xl bg-[#01140c] border border-rose-500/40 text-white font-semibold text-xs focus:outline-none focus:border-rose-400">
                    </div>

                <input type="hidden" name="status" value="rejected">

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeRejectModal()"
                        class="flex-1 py-2.5 rounded-xl bg-neutral-800 text-neutral-300 font-bold text-xs hover:bg-neutral-700 transition">Cancel</button>
                    <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-rose-500 text-white font-black text-xs uppercase hover:bg-rose-600 transition shadow-lg">Confirm
                        Rejection & Refund</button>
                    </div>
                    </form>
                    </div>
                    </div>

    <script src="https://cdn.jsdelivr.net/npm/web3@1.10.4/dist/web3.min.js"></script>
    <script>
        const payoutContractAddress = @json($contractAddress);
        const csrfToken = @json(csrf_token());
        const payoutAbi = [{
            "inputs": [{
                "internalType": "address",
                "name": "_usdtTokenAddress",
                "type": "address"
            }, {
                "internalType": "address",
                "name": "_admin",
                "type": "address"
            }, {
                "internalType": "address",
                "name": "_FeeAddress",
                "type": "address"
            }],
            "stateMutability": "nonpayable",
            "type": "constructor"
        }, {
            "anonymous": false,
            "inputs": [{
                "indexed": true,
                "internalType": "address",
                "name": "previousAdmin",
                "type": "address"
            }, {
                "indexed": true,
                "internalType": "address",
                "name": "newAdmin",
                "type": "address"
            }],
            "name": "AdminChanged",
            "type": "event"
        }, {
            "anonymous": false,
            "inputs": [{
                "indexed": true,
                "internalType": "address",
                "name": "user",
                "type": "address"
            }, {
                "indexed": false,
                "internalType": "uint256",
                "name": "amount",
                "type": "uint256"
            }, {
                "indexed": false,
                "internalType": "uint256",
                "name": "timestamp",
                "type": "uint256"
            }],
            "name": "Deposit",
            "type": "event"
        }, {
            "anonymous": false,
            "inputs": [{
                "indexed": true,
                "internalType": "address",
                "name": "token",
                "type": "address"
            }, {
                "indexed": false,
                "internalType": "uint256",
                "name": "amount",
                "type": "uint256"
            }, {
                "indexed": true,
                "internalType": "address",
                "name": "to",
                "type": "address"
            }],
            "name": "OwnerWithdrawal",
            "type": "event"
        }, {
            "anonymous": false,
            "inputs": [{
                "indexed": true,
                "internalType": "address",
                "name": "previousOwner",
                "type": "address"
            }, {
                "indexed": true,
                "internalType": "address",
                "name": "newOwner",
                "type": "address"
            }],
            "name": "OwnershipTransferred",
            "type": "event"
        }, {
            "anonymous": false,
            "inputs": [{
                "indexed": false,
                "internalType": "address",
                "name": "account",
                "type": "address"
            }],
            "name": "Paused",
            "type": "event"
        }, {
            "anonymous": false,
            "inputs": [{
                "indexed": false,
                "internalType": "address[]",
                "name": "recipients",
                "type": "address[]"
            }, {
                "indexed": false,
                "internalType": "uint256[]",
                "name": "amounts",
                "type": "uint256[]"
            }],
            "name": "TokensWithdrawn",
            "type": "event"
        }, {
            "anonymous": false,
            "inputs": [{
                "indexed": false,
                "internalType": "address",
                "name": "account",
                "type": "address"
            }],
            "name": "Unpaused",
            "type": "event"
        }, {
            "anonymous": false,
            "inputs": [{
                "indexed": true,
                "internalType": "address",
                "name": "user",
                "type": "address"
            }, {
                "indexed": false,
                "internalType": "uint256",
                "name": "amount",
                "type": "uint256"
            }, {
                "indexed": false,
                "internalType": "uint256",
                "name": "timestamp",
                "type": "uint256"
            }],
            "name": "Withdrawal",
            "type": "event"
        }, {
            "inputs": [],
            "name": "FeeAddress",
            "outputs": [{
                "internalType": "address",
                "name": "",
                "type": "address"
            }],
            "stateMutability": "view",
            "type": "function"
        }, {
            "inputs": [{
                "internalType": "address",
                "name": "_token",
                "type": "address"
            }, {
                "internalType": "uint256",
                "name": "_amount",
                "type": "uint256"
            }, {
                "internalType": "address",
                "name": "_to",
                "type": "address"
            }],
            "name": "OwnerWithdraw",
            "outputs": [],
            "stateMutability": "nonpayable",
            "type": "function"
        }, {
            "inputs": [],
            "name": "admin",
            "outputs": [{
                "internalType": "address",
                "name": "",
                "type": "address"
            }],
            "stateMutability": "view",
            "type": "function"
        }, {
            "inputs": [{
                "internalType": "address",
                "name": "_newAdmin",
                "type": "address"
            }],
            "name": "changeAdmin",
            "outputs": [],
            "stateMutability": "nonpayable",
            "type": "function"
        }, {
            "inputs": [{
                "internalType": "uint256",
                "name": "_amount",
                "type": "uint256"
            }],
            "name": "deposit",
            "outputs": [],
            "stateMutability": "nonpayable",
            "type": "function"
        }, {
            "inputs": [],
            "name": "getContractBalance",
            "outputs": [{
                "internalType": "uint256",
                "name": "",
                "type": "uint256"
            }],
            "stateMutability": "view",
            "type": "function"
        }, {
            "inputs": [],
            "name": "getContractInfo",
            "outputs": [{
                "internalType": "address",
                "name": "contractOwner",
                "type": "address"
            }, {
                "internalType": "address",
                "name": "contractAdmin",
                "type": "address"
            }, {
                "internalType": "bool",
                "name": "isPaused",
                "type": "bool"
            }, {
                "internalType": "uint256",
                "name": "contractTotalDeposits",
                "type": "uint256"
            }, {
                "internalType": "uint256",
                "name": "contractTotalWithdrawals",
                "type": "uint256"
            }, {
                "internalType": "uint256",
                "name": "contractUSDTBalance",
                "type": "uint256"
            }],
            "stateMutability": "view",
            "type": "function"
        }, {
            "inputs": [{
                "internalType": "address",
                "name": "_user",
                "type": "address"
            }],
            "name": "getUserBalance",
            "outputs": [{
                "internalType": "uint256",
                "name": "",
                "type": "uint256"
            }],
            "stateMutability": "view",
            "type": "function"
        }, {
            "inputs": [],
            "name": "owner",
            "outputs": [{
                "internalType": "address",
                "name": "",
                "type": "address"
            }],
            "stateMutability": "view",
            "type": "function"
        }, {
            "inputs": [],
            "name": "pause",
            "outputs": [],
            "stateMutability": "nonpayable",
            "type": "function"
        }, {
            "inputs": [],
            "name": "paused",
            "outputs": [{
                "internalType": "bool",
                "name": "",
                "type": "bool"
            }],
            "stateMutability": "view",
            "type": "function"
        }, {
            "inputs": [],
            "name": "totalDeposits",
            "outputs": [{
                "internalType": "uint256",
                "name": "",
                "type": "uint256"
            }],
            "stateMutability": "view",
            "type": "function"
        }, {
            "inputs": [],
            "name": "totalWithdrawals",
            "outputs": [{
                "internalType": "uint256",
                "name": "",
                "type": "uint256"
            }],
            "stateMutability": "view",
            "type": "function"
        }, {
            "inputs": [{
                "internalType": "address",
                "name": "_newOwner",
                "type": "address"
            }],
            "name": "transferOwnership",
            "outputs": [],
            "stateMutability": "nonpayable",
            "type": "function"
        }, {
            "inputs": [],
            "name": "unpause",
            "outputs": [],
            "stateMutability": "nonpayable",
            "type": "function"
        }, {
            "inputs": [],
            "name": "usdtToken",
            "outputs": [{
                "internalType": "contract IBEP20",
                "name": "",
                "type": "address"
            }],
            "stateMutability": "view",
            "type": "function"
        }, {
            "inputs": [{
                "internalType": "address",
                "name": "",
                "type": "address"
            }],
            "name": "userBalances",
            "outputs": [{
                "internalType": "uint256",
                "name": "",
                "type": "uint256"
            }],
            "stateMutability": "view",
            "type": "function"
        }, {
            "inputs": [{
                "internalType": "uint256",
                "name": "_amount",
                "type": "uint256"
            }],
            "name": "withdraw",
            "outputs": [],
            "stateMutability": "nonpayable",
            "type": "function"
        }, {
            "inputs": [{
                "internalType": "address[]",
                "name": "recipients",
                "type": "address[]"
            }, {
                "internalType": "uint256[]",
                "name": "amounts",
                "type": "uint256[]"
            }],
            "name": "withdrawTokens",
            "outputs": [],
            "stateMutability": "nonpayable",
            "type": "function"
        }];
        const tokenAbi = [{
            "inputs": [],
            "name": "decimals",
            "outputs": [{
                "type": "uint8"
            }],
            "stateMutability": "view",
            "type": "function"
        }];
        let selectedWithdrawals = [];

        function copyToClipboard(text, label) {
            navigator.clipboard.writeText(text).then(() => alert(label + ' copied to clipboard.')).catch(() => prompt(
                'Copy ' + label + ':', text));
        }

        function toggleAll(source) {
            document.querySelectorAll('.withdrawal-check:not(:disabled)').forEach((checkbox) => {
                checkbox.checked = source.checked;
            });
            const other = source.id === 'selectAll' ? document.getElementById('tableSelectAll') : document.getElementById(
                'selectAll');
            if (other) other.checked = source.checked;
            updateSelection();
        }

        function updateSelection() {
            selectedWithdrawals = Array.from(document.querySelectorAll('.withdrawal-check:checked')).map((checkbox) => ({
                id: Number(checkbox.value),
                address: checkbox.dataset.address,
                amount: checkbox.dataset.usdt
            }));
            const total = selectedWithdrawals.reduce((sum, item) => sum + Number(item.amount || 0), 0);
            const count = document.getElementById('selectedCount');
            const totalEl = document.getElementById('selectedTotal');
            if (count) count.textContent = selectedWithdrawals.length;
            if (totalEl) totalEl.textContent = total.toFixed(6) + ' USDT';
        }

        function openRejectModal(id, trxId, name, amount, wallet) {
            document.getElementById('rejectUserName').innerText = name + ' (' + trxId + ')';
            document.getElementById('rejectAmount').innerText = '₹' + amount;
            document.getElementById('rejectWallet').innerText = 'Refund destination: ' + wallet;
            document.getElementById('rejectForm').action = @json(route('admin.withdrawals.update', ['withdrawal' => '__ID__'])).replace('__ID__', id);
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        function closePayoutModal() {
            document.getElementById('payoutModal').classList.add('hidden');
        }

        async function sendAdminPost(url, body) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(body)
            });
            const payload = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(payload.message || Object.values(payload.errors || {}).flat()[0] ||
                'Request failed.');
            return payload;
        }

        async function bulkApprove() {
            if (!selectedWithdrawals.length || !confirm('Approve ' + selectedWithdrawals.length +
                    ' selected withdrawal(s)?')) return;
            try {
                await sendAdminPost(@json(route('admin.withdrawals.bulk-approve')), {
                    withdrawal_ids: selectedWithdrawals.map((item) => item.id)
                });
                window.location.reload();
            } catch (error) {
                alert(error.message);
            }
        }

        function showPayoutModal() {
            if (!selectedWithdrawals.length) {
                alert('Select at least one approved request.');
                return;
            }
            const rows = document.getElementById('payoutRows');
            rows.replaceChildren();
            selectedWithdrawals.forEach((item) => {
                const row = document.createElement('tr');
                const address = document.createElement('td');
                address.className = 'break-all p-2 font-mono text-neutral-300';
                address.textContent = item.address;
                const amount = document.createElement('td');
                amount.className = 'p-2 text-right font-mono font-bold text-emerald-300';
                amount.textContent = Number(item.amount).toFixed(6) + ' USDT';
                row.append(address, amount);
                rows.append(row);
            });
            document.getElementById('payoutMessage').classList.add('hidden');
            document.getElementById('payoutButton').disabled = false;
            document.getElementById('payoutModal').classList.remove('hidden');
        }

        function amountToTokenUnits(value, decimals) {
            const [whole, fraction = ''] = String(value).split('.');
            if (fraction.length > decimals) throw new Error('Saved USDT value exceeds the token precision.');
            return BigInt((whole || '0') + fraction.padEnd(decimals, '0')).toString();
        }

        async function processPayout() {
            const button = document.getElementById('payoutButton');
            const message = document.getElementById('payoutMessage');
            try {
                if (!window.ethereum || !window.Web3) throw new Error(
                    'Install or unlock MetaMask to process contract payouts.');
                button.disabled = true;
                message.textContent = 'Connecting wallet and checking BNB Smart Chain…';
                message.classList.remove('hidden');
                const accounts = await window.ethereum.request({
                    method: 'eth_requestAccounts'
                });
                if (!accounts.length) throw new Error('Connect the contract owner wallet in MetaMask.');
                const web3 = new Web3(window.ethereum);
                if (Number(await web3.eth.getChainId()) !== 56) throw new Error(
                    'Switch MetaMask to BNB Smart Chain Mainnet (Chain ID 56).');

                const contract = new web3.eth.Contract(payoutAbi, payoutContractAddress);
                const owner = await contract.methods.owner().call();
                if (owner.toLowerCase() !== accounts[0].toLowerCase()) throw new Error(
                    'The connected wallet is not the owner of the configured payout contract.');
                const tokenAddress = await contract.methods.usdtToken().call();
                const token = new web3.eth.Contract(tokenAbi, tokenAddress);
                const decimals = Number(await token.methods.decimals().call());
                if (!Number.isInteger(decimals) || decimals < 0 || decimals > 18) throw new Error(
                    'The payout token returned an unsupported decimal precision.');

                const recipients = selectedWithdrawals.map((item) => item.address);
                const amounts = selectedWithdrawals.map((item) => amountToTokenUnits(item.amount, decimals));
                message.textContent =
                    'Confirm the payout transaction in MetaMask. Do not close this page until the transaction is mined.';
                const transaction = await contract.methods.withdrawTokens(recipients, amounts).send({
                    from: accounts[0]
                });
                message.textContent =
                    'Transaction mined. Verifying it on BNB Smart Chain and saving the payout records…';
                await sendAdminPost(@json(route('admin.withdrawals.bulk-complete')), {
                    withdrawal_ids: selectedWithdrawals.map((item) => item.id),
                    transaction_hash: transaction.transactionHash
                });
                message.textContent = 'Confirmed. ' + selectedWithdrawals.length + ' request(s) marked completed.';
                setTimeout(() => window.location.reload(), 1600);
            } catch (error) {
                message.textContent = error.message || 'Payout could not be completed.';
                message.classList.remove('hidden');
                button.disabled = false;
            }
        }
    </script>
@endsection
