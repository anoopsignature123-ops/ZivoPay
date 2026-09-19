@extends('admin.layouts.app')

@section('title', 'Member Details & Profile Overview - ZIVO PAY')

@section('content')
<div class="w-full space-y-6 font-sans">
    
    <!-- Top Header Banner -->
    <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.users') }}" class="text-xs text-emerald-400 font-extrabold tracking-[3px] uppercase hover:underline flex items-center gap-1">
                    &larr; BACK TO MEMBER DIRECTORY
                </a>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white font-heading uppercase">MEMBER DETAILS: {{ $user->name }}</h1>
            <p class="text-xs text-neutral-300 mt-1">Full profile information, sponsor credentials, network tree stats, and wallet management.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.users.impersonate', $user) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-black text-xs uppercase tracking-wider shadow-lg flex items-center gap-2 transition">
                <i data-lucide="user-check" class="w-4 h-4 text-white"></i> LOGIN AS MEMBER
            </a>

            <a href="{{ route('admin.network.tree', ['code' => $user->referral_code]) }}" class="px-4 py-2.5 rounded-xl bg-teal-500/20 text-teal-300 border border-teal-500/40 hover:bg-teal-500/30 font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 transition">
                <i data-lucide="network" class="w-4 h-4 text-teal-400"></i> VIEW TEAM TREE
            </a>

            <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2.5 rounded-xl bg-black/80 border border-emerald-500/40 text-emerald-300 font-bold text-xs uppercase tracking-wider hover:bg-emerald-500/20 transition flex items-center gap-1.5">
                <i data-lucide="edit-3" class="w-4 h-4 text-emerald-400"></i> EDIT PROFILE
            </a>

            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider shadow-lg flex items-center gap-2 transition {{ $user->status === 'active' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500/30' }}">
                    <i data-lucide="power" class="w-4 h-4"></i> {{ $user->status === 'active' ? 'DEACTIVATE ACCOUNT' : 'ACTIVATE ACCOUNT' }}
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2 shadow-md">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Profile Overview Card -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl space-y-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-emerald-500/20">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 via-emerald-600 to-teal-800 text-white font-black text-2xl flex items-center justify-center shadow-xl border-2 border-emerald-300 shrink-0">
                    <span class="text-white font-black">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-black text-white font-heading">{{ $user->name }}</h2>
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider {{ $user->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-400 border border-rose-500/40' }}">
                            {{ strtoupper($user->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-neutral-300 font-mono mt-0.5">{{ $user->email }} • {{ $user->mobile }}</p>

                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="px-2.5 py-0.5 rounded bg-black/80 border border-emerald-500/40 text-emerald-400 font-mono font-bold text-[10px]">
                            REFERRAL CODE: {{ $user->referral_code }}
                        </span>

                        @if($user->sponsor)
                            <a href="{{ route('admin.users.show', $user->sponsor->id) }}" class="px-2.5 py-0.5 rounded bg-black/80 border border-teal-500/40 text-teal-300 font-mono font-bold text-[10px] hover:underline flex items-center gap-1">
                                SPONSOR: {{ $user->sponsor->name }} ({{ $user->sponsor_code }})
                            </a>
                        @else
                            <span class="px-2.5 py-0.5 rounded bg-black/80 border border-neutral-600 text-neutral-400 font-mono font-bold text-[10px]">
                                SPONSOR: {{ $user->sponsor_code ?? 'ROOT / SYSTEM' }}
                            </span>
                        @endif

                        @if($user->is_subscription_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-black border border-emerald-500/50 shadow-[0_0_15px_rgba(16,185,129,0.35)]">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                                </span>
                                ₹3,000 PACKAGE ACTIVE
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-300 text-[10px] font-black border border-rose-500/50 shadow-[0_0_15px_rgba(244,63,94,0.35)]">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-500 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                </span>
                                NO PACKAGE TAKEN
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-right text-xs text-neutral-300 space-y-1">
                <div><span class="text-neutral-400">Joined:</span> <strong class="text-white font-mono">{{ $user->created_at ? $user->created_at->format('M d, Y h:i A') : 'N/A' }}</strong></div>
                <div><span class="text-neutral-400">Activated:</span> <strong class="text-emerald-400 font-mono">{{ $user->subscription_activated_at ? $user->subscription_activated_at->format('M d, Y h:i A') : 'Inactive' }}</strong></div>
            </div>
        </div>

        <!-- Comprehensive Professional Stat Cards Grid (4 Cards Per Row on Desktop) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 font-sans">
            <!-- Deposit Wallet -->
            <div class="p-5 rounded-2xl bg-[#01140c] border border-emerald-500/30 hover:border-emerald-500/60 transition shadow-lg flex items-center justify-between">
                <div>
                    <span class="text-neutral-400 text-[11px] font-bold uppercase tracking-wider block">Deposit / Fund Wallet</span>
                    <strong class="text-teal-300 font-black text-xl font-mono block mt-1">${{ number_format((float)$user->deposit_wallet, 2) }}</strong>
                </div>
                <div class="w-11 h-11 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Earning Wallet -->
            <div class="p-5 rounded-2xl bg-[#01140c] border border-emerald-500/30 hover:border-emerald-500/60 transition shadow-lg flex items-center justify-between">
                <div>
                    <span class="text-neutral-400 text-[11px] font-bold uppercase tracking-wider block">Earning Wallet</span>
                    <strong class="text-emerald-400 font-black text-xl font-mono block mt-1">${{ number_format((float)$user->earning_wallet, 2) }}</strong>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center shrink-0">
                    <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Active Investment -->
            <div class="p-5 rounded-2xl bg-[#01140c] border border-emerald-500/30 hover:border-emerald-500/60 transition shadow-lg flex items-center justify-between">
                <div>
                    <span class="text-neutral-400 text-[11px] font-bold uppercase tracking-wider block">Active Package / Inv.</span>
                    <strong class="text-amber-400 font-black text-xl font-mono block mt-1">${{ number_format((float)$totalInvestmentAmount, 2) }}</strong>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Direct Members -->
            <div class="p-5 rounded-2xl bg-[#01140c] border border-emerald-500/30 hover:border-emerald-500/60 transition shadow-lg flex items-center justify-between">
                <div>
                    <span class="text-neutral-400 text-[11px] font-bold uppercase tracking-wider block">Direct Referrals</span>
                    <strong class="text-white font-black text-xl font-mono block mt-1">{{ $directCount }} Members</strong>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center shrink-0">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Total Team Size -->
            <div class="p-5 rounded-2xl bg-[#01140c] border border-emerald-500/30 hover:border-emerald-500/60 transition shadow-lg flex items-center justify-between">
                <div>
                    <span class="text-neutral-400 text-[11px] font-bold uppercase tracking-wider block">Total Downline Team</span>
                    <strong class="text-emerald-300 font-black text-xl font-mono block mt-1">{{ $totalTeamCount }} Downlines</strong>
                </div>
                <div class="w-11 h-11 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Level Income -->
            <div class="p-5 rounded-2xl bg-[#01140c] border border-emerald-500/30 hover:border-emerald-500/60 transition shadow-lg flex items-center justify-between">
                <div>
                    <span class="text-neutral-400 text-[11px] font-bold uppercase tracking-wider block">Level Income Earned</span>
                    <strong class="text-teal-300 font-black text-xl font-mono block mt-1">${{ number_format((float)$totalLevelIncome, 2) }}</strong>
                </div>
                <div class="w-11 h-11 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Direct Income -->
            <div class="p-5 rounded-2xl bg-[#01140c] border border-emerald-500/30 hover:border-emerald-500/60 transition shadow-lg flex items-center justify-between">
                <div>
                    <span class="text-neutral-400 text-[11px] font-bold uppercase tracking-wider block">Direct Referral Income</span>
                    <strong class="text-emerald-400 font-black text-xl font-mono block mt-1">${{ number_format((float)$totalDirectIncome, 2) }}</strong>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center shrink-0">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Total Withdrawn -->
            <div class="p-5 rounded-2xl bg-[#01140c] border border-emerald-500/30 hover:border-emerald-500/60 transition shadow-lg flex items-center justify-between">
                <div>
                    <span class="text-neutral-400 text-[11px] font-bold uppercase tracking-wider block">Approved Withdrawals</span>
                    <strong class="text-rose-400 font-black text-xl font-mono block mt-1">${{ number_format((float)$totalWithdrawn, 2) }}</strong>
                    @if($pendingWithdrawal > 0)
                        <span class="text-[10px] text-amber-300 font-bold block mt-0.5">Pending: ${{ number_format((float)$pendingWithdrawal, 2) }}</span>
                    @endif
                </div>
                <div class="w-11 h-11 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 flex items-center justify-center shrink-0">
                    <i data-lucide="arrow-up-right" class="w-5 h-5"></i>
                </div>
            </div>
        </div>

        <!-- Withdrawal Address & Referral Link Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-emerald-500/20">
            <div class="p-4 rounded-2xl bg-[#01140c] border border-emerald-500/30 space-y-1">
                <span class="text-neutral-400 text-[10px] uppercase font-sans font-bold block">USDT Withdrawal Wallet Address:</span>
                <div class="flex items-center justify-between gap-2">
                    <code class="text-emerald-300 font-mono text-xs truncate block" title="{{ $user->wallet_address ?? 'Not Set' }}">
                        {{ $user->wallet_address ?? 'NOT SET YET BY MEMBER' }}
                    </code>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-[#01140c] border border-emerald-500/30 space-y-1">
                <span class="text-neutral-400 text-[10px] uppercase font-sans font-bold block">Direct Registration Referral Link:</span>
                <div class="flex items-center justify-between gap-2">
                    <input type="text" readonly id="adminRefLink" value="{{ url('/user/register?sponsor='.$user->referral_code) }}" class="w-full bg-black/60 border border-emerald-500/30 px-3 py-1.5 rounded-lg text-emerald-400 font-mono text-xs focus:outline-none">
                    <button type="button" onclick="copyAdminRefLink()" class="px-3 py-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 font-bold text-xs uppercase tracking-wider transition border border-emerald-500/40 shrink-0">
                        Copy
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Add/Deduct Wallet Direct Controls -->
    <div class="p-6 rounded-3xl bg-[#042718] border border-emerald-500/30 space-y-4 shadow-xl">
        <div class="flex items-center gap-2 border-b border-emerald-500/20 pb-3">
            <i data-lucide="wallet-cards" class="w-5 h-5 text-emerald-400"></i>
            <h2 class="text-base font-black text-white uppercase tracking-tight font-heading">ADMIN DIRECT WALLET ADJUSTMENT</h2>
        </div>
        <p class="text-xs text-neutral-300">Credit Fund Wallet or Earning Wallet directly for member {{ $user->name }}</p>

        <form action="{{ route('admin.users.add-fund', $user) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Select Target Wallet *</label>
                <select name="wallet_type" required class="w-full px-4 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400" style="background-color: #01140c !important;">
                    <option value="deposit_wallet">Fund Wallet</option>
                    <option value="earning_wallet">Earning Wallet</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-emerald-400 uppercase mb-1">Amount ($) *</label>
                <input type="number" step="0.01" name="amount" required placeholder="100.00" class="w-full px-4 py-2.5 rounded-xl bg-black border border-emerald-500/40 text-white font-mono text-xs focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-emerald-400 uppercase mb-1">Admin Remark</label>
                <input type="text" name="remark" placeholder="e.g. Special Fund Addition" class="w-full px-4 py-2.5 rounded-xl bg-black border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none">
            </div>

            <div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-black text-xs uppercase tracking-wider shadow-lg transition">
                    Credit Fund
                </button>
            </div>
        </form>
    </div>

    <!-- Direct Referrals Table -->
    <div class="p-6 rounded-3xl bg-[#042718] border border-emerald-500/30 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-emerald-500/20 pb-3">
            <div class="flex items-center gap-2">
                <i data-lucide="users" class="w-5 h-5 text-emerald-400"></i>
                <h2 class="text-base font-black text-white uppercase tracking-tight font-heading">DIRECT REFERRALS ({{ $directCount }})</h2>
            </div>
        </div>

        @if($directsList->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-sans">
                    <thead>
                        <tr class="border-b border-emerald-500/20 text-emerald-400 text-[11px] font-bold uppercase tracking-wider">
                            <th class="py-3 px-3">Member Name</th>
                            <th class="py-3 px-3">Referral Code</th>
                            <th class="py-3 px-3">Email / Mobile</th>
                            <th class="py-3 px-3">Package Status</th>
                            <th class="py-3 px-3">Joined Date</th>
                            <th class="py-3 px-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-500/10 text-neutral-200">
                        @foreach($directsList as $direct)
                            <tr class="hover:bg-emerald-500/5 transition">
                                <td class="py-3 px-3 font-bold text-white flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($direct->name, 0, 1)) }}
                                    </div>
                                    <span>{{ $direct->name }}</span>
                                </td>
                                <td class="py-3 px-3 font-mono font-bold text-emerald-400">{{ $direct->referral_code }}</td>
                                <td class="py-3 px-3 font-mono text-neutral-300">{{ $direct->email }}</td>
                                <td class="py-3 px-3">
                                    @if($direct->is_subscription_active)
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-black border border-emerald-500/40">₹3,000 ACTIVE</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 text-[10px] font-black border border-rose-500/40">INACTIVE</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 font-mono text-neutral-400">{{ $direct->created_at ? $direct->created_at->format('M d, Y') : 'N/A' }}</td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ route('admin.users.show', $direct->id) }}" class="px-3 py-1 rounded bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 text-[11px] font-bold uppercase transition border border-emerald-500/40 inline-block">
                                        View Profile
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6 text-center text-xs text-neutral-400 bg-black/40 rounded-2xl border border-emerald-500/20">
                No direct referrals found for this member yet.
            </div>
        @endif
    </div>

    <!-- Recent Transactions Table -->
    <div class="p-6 rounded-3xl bg-[#042718] border border-emerald-500/30 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-emerald-500/20 pb-3">
            <div class="flex items-center gap-2">
                <i data-lucide="history" class="w-5 h-5 text-emerald-400"></i>
                <h2 class="text-base font-black text-white uppercase tracking-tight font-heading">RECENT MEMBER TRANSACTIONS</h2>
            </div>
        </div>

        @if($recentTransactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-sans">
                    <thead>
                        <tr class="border-b border-emerald-500/20 text-emerald-400 text-[11px] font-bold uppercase tracking-wider">
                            <th class="py-3 px-3">Trx ID</th>
                            <th class="py-3 px-3">Type</th>
                            <th class="py-3 px-3">Amount</th>
                            <th class="py-3 px-3">Wallet</th>
                            <th class="py-3 px-3">Description</th>
                            <th class="py-3 px-3 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-500/10 text-neutral-200">
                        @foreach($recentTransactions as $trx)
                            <tr class="hover:bg-emerald-500/5 transition">
                                <td class="py-3 px-3 font-mono font-bold text-white">{{ $trx->trx_id ?? 'TRX-'.$trx->id }}</td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border bg-emerald-500/20 text-emerald-300 border-emerald-500/40">
                                        {{ str_replace('_', ' ', strtoupper($trx->type)) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 font-mono font-bold {{ $trx->trx_type === '-' ? 'text-rose-400' : 'text-emerald-400' }}">
                                    {{ $trx->trx_type === '-' ? '-' : '+' }}${{ number_format((float)$trx->amount, 2) }}
                                </td>
                                <td class="py-3 px-3 font-mono text-neutral-300 uppercase">{{ str_replace('_', ' ', $trx->wallet_type ?? 'Wallet') }}</td>
                                <td class="py-3 px-3 text-neutral-300 truncate max-w-[250px]" title="{{ $trx->description }}">{{ $trx->description }}</td>
                                <td class="py-3 px-3 text-right font-mono text-neutral-400">{{ $trx->created_at ? $trx->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6 text-center text-xs text-neutral-400 bg-black/40 rounded-2xl border border-emerald-500/20">
                No transaction logs recorded for this member yet.
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    function copyAdminRefLink() {
        const copyText = document.getElementById("adminRefLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value).then(() => {
            alert("Member referral link copied to clipboard!");
        });
    }
</script>
@endpush
@endsection
