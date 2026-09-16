@extends('admin.layouts.app')

@section('title', 'ZIVO PAY - Admin 24x7 Withdrawal Approvals')

@section('content')
<div class="space-y-6 font-sans">

    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/40">
                    WITHDRAWAL MANAGEMENT
                </span>
                <span class="text-xs text-emerald-400 font-black tracking-[2px] uppercase">ZIVO PAY AUDIT</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight mt-1 font-heading">24X7 WITHDRAWAL APPROVALS QUEUE</h1>
            <p class="text-xs text-neutral-300 mt-1">Review, approve payouts with UTR references, or reject requests with automatic wallet refunds.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <span class="px-4 py-2 rounded-xl bg-[#02180f] border border-emerald-500/30 text-xs font-bold text-neutral-300">
                Total Requests: <strong class="text-emerald-400 font-mono">{{ $stats['total_requests'] }}</strong>
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-300 text-xs font-bold flex items-center gap-2 shadow-md">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400 shrink-0"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/20 border border-rose-500/50 text-rose-300 text-xs font-bold space-y-1 shadow-md">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400 shrink-0"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Stat Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Pending Queue Amount -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between text-teal-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Pending Approvals</span>
                <span class="px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300 font-mono font-bold text-[10px]">{{ $stats['pending_count'] }} Requests</span>
            </div>
            <h3 class="text-2xl font-black text-teal-300 font-mono">₹{{ number_format($stats['pending_amount'], 2) }}</h3>
            <span class="text-[10px] text-neutral-400 font-semibold mt-1 block">Needs Action</span>
        </div>

        <!-- 2. Approved Payouts Total -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between text-emerald-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Total Net Approved</span>
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
            <h3 class="text-2xl font-black text-emerald-400 font-mono">₹{{ number_format($stats['approved_amount'], 2) }}</h3>
            <span class="text-[10px] text-emerald-400 font-semibold mt-1 block">Paid Out to Members</span>
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
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="relative flex-1">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Trx ID, Member, Code..."
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400 transition">
                    </div>
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 font-bold text-xs hover:bg-emerald-500/30 transition shrink-0">
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
                        Approved Payouts
                    </a>
                    <a href="{{ route('admin.withdrawals', array_merge(request()->except('status'), ['status' => 'rejected'])) }}"
                        class="whitespace-nowrap px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'rejected' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/50 font-black shadow-md' : 'bg-[#01140c] border border-emerald-500/30 text-neutral-300 hover:text-rose-400' }}">
                        Rejected Requests
                    </a>
                </div>
            </div>
        </div>

        <!-- Approvals Queue Table -->
        @if($withdrawals->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-neutral-300 whitespace-nowrap">
                    <thead class="bg-[#02180f] text-emerald-400 uppercase text-[10px] font-bold border-b border-emerald-500/30">
                        <tr>
                            <th class="p-3.5">Trx ID & Date</th>
                            <th class="p-3.5">Member Details</th>
                            <th class="p-3.5">Method</th>
                            <th class="p-3.5">Account / Wallet Details</th>
                            <th class="p-3.5">Gross Amount</th>
                            <th class="p-3.5">Fee (5%)</th>
                            <th class="p-3.5">Net Payable</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5">Admin Remarks / UTR</th>
                            <th class="p-3.5 text-center">Actions Menu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-500/10">
                        @foreach($withdrawals as $wth)
                            <tr class="hover:bg-emerald-500/5 transition">
                                <td class="p-3.5">
                                    <span class="font-mono font-bold text-white block">{{ $wth->trx_id }}</span>
                                    <span class="text-[10px] text-neutral-400 block">{{ $wth->created_at ? $wth->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                                </td>
                                <td class="p-3.5">
                                    @if($wth->user)
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0 border border-emerald-500/30">
                                                <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.users.show', $wth->user->id) }}" class="font-bold text-white hover:text-emerald-400 hover:underline block">
                                                    {{ $wth->user->name }}
                                                </a>
                                                <span class="text-[10px] text-emerald-400 font-mono block">{{ $wth->user->referral_code }} • {{ $wth->user->email }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-neutral-500 italic">User Deleted</span>
                                    @endif
                                </td>
                                <td class="p-3.5 font-semibold text-emerald-300">{{ $wth->payment_method }}</td>
                                <td class="p-3.5 max-w-xs">
                                    <div class="flex items-center gap-1.5">
                                        <span class="truncate font-medium text-neutral-200 block text-xs" title="{{ $wth->account_details }}">
                                            {{ $wth->account_details }}
                                        </span>
                                        <button type="button" onclick="copyToClipboard('{{ addslashes($wth->account_details) }}', 'Account Details')"
                                            title="Copy Account Details"
                                            class="p-1 rounded bg-emerald-500/10 hover:bg-emerald-500/30 text-emerald-400 border border-emerald-500/30 transition shrink-0">
                                            <i data-lucide="copy" class="w-3 h-3"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="p-3.5 font-mono font-bold text-white">₹{{ number_format($wth->amount, 2) }}</td>
                                <td class="p-3.5 font-mono text-rose-400">-₹{{ number_format($wth->charge, 2) }}</td>
                                <td class="p-3.5 font-mono font-bold text-emerald-400 text-sm">₹{{ number_format($wth->final_amount, 2) }}</td>
                                <td class="p-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase
                                        {{ $wth->status === 'approved' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 shadow-[0_0_10px_rgba(16,185,129,0.3)]' : '' }}
                                        {{ $wth->status === 'pending' ? 'bg-teal-500/20 text-teal-300 border border-teal-500/40' : '' }}
                                        {{ $wth->status === 'rejected' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/40' : '' }}">
                                        {{ $wth->status }}
                                    </span>
                                </td>
                                <td class="p-3.5 font-mono text-[11px] text-neutral-300 max-w-xs truncate" title="{{ $wth->admin_remarks ?? '-' }}">
                                    {{ $wth->admin_remarks ?? '-' }}
                                </td>
                                <td class="p-3.5 text-center">
                                    @if($wth->status === 'pending')
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Approve Modal Open Button -->
                                            <button type="button"
                                                onclick="openApproveModal({{ $wth->id }}, '{{ $wth->trx_id }}', '{{ addslashes($wth->user ? $wth->user->name : 'Member') }}', '{{ number_format($wth->final_amount, 2) }}')"
                                                title="Approve Payout (₹{{ number_format($wth->final_amount, 2) }})"
                                                aria-label="Approve Payout"
                                                class="w-8 h-8 rounded-xl bg-emerald-500 text-black hover:bg-emerald-400 font-bold transition-all duration-200 flex items-center justify-center shadow-md hover:scale-110 shrink-0">
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                            </button>

                                            <!-- Reject Modal Open Button -->
                                            <button type="button"
                                                onclick="openRejectModal({{ $wth->id }}, '{{ $wth->trx_id }}', '{{ addslashes($wth->user ? $wth->user->name : 'Member') }}', '{{ number_format($wth->amount, 2) }}')"
                                                title="Reject Request & Refund Balance"
                                                aria-label="Reject Request"
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
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mx-auto border border-emerald-500/20">
                    <i data-lucide="inbox" class="w-6 h-6"></i>
                </div>
                <p class="text-sm font-semibold text-neutral-300">No withdrawal requests found matching the current search & status filters.</p>
            </div>
        @endif
    </div>

</div>

<!-- Approve Withdrawal Modal -->
<div id="approveModal" class="fixed inset-0 z-50 hidden bg-black/80 flex items-center justify-center p-4">
    <div class="w-full max-w-md p-6 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-2xl space-y-4 relative">
        <button type="button" onclick="closeApproveModal()" class="absolute top-4 right-4 text-neutral-400 hover:text-white">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <h3 class="text-lg font-black text-white uppercase tracking-tight flex items-center gap-2 font-heading">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400"></i> Approve Payout Request
        </h3>

        <form id="approveForm" action="" method="POST" class="space-y-4">
            @csrf
            <div>
                <span class="block text-xs font-bold text-neutral-400 uppercase">Target Member:</span>
                <strong id="approveUserName" class="text-sm font-bold text-emerald-400 block"></strong>
            </div>

            <div>
                <span class="block text-xs font-bold text-neutral-400 uppercase">Net Payable Amount:</span>
                <strong id="approveAmount" class="text-lg font-black text-white font-mono block"></strong>
            </div>

            <div>
                <label class="block text-xs font-bold text-emerald-400 uppercase mb-1">Bank UTR / Txn Reference No / Remark:</label>
                <input type="text" name="admin_remarks" placeholder="e.g. UTR: 425167890123 / IMPS Successful"
                    class="w-full px-4 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400">
            </div>

            <input type="hidden" name="status" value="approved">

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeApproveModal()" class="flex-1 py-2.5 rounded-xl bg-neutral-800 text-neutral-300 font-bold text-xs hover:bg-neutral-700 transition">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-emerald-500 text-black font-black text-xs uppercase hover:bg-emerald-400 transition shadow-lg">Confirm Approval</button>
            </div>
        </form>
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
                <span class="block text-xs font-bold text-neutral-400 uppercase">Refund Amount to Earning Wallet:</span>
                <strong id="rejectAmount" class="text-lg font-black text-white font-mono block"></strong>
            </div>

            <div>
                <label class="block text-xs font-bold text-rose-400 uppercase mb-1">Reason for Rejection *:</label>
                <input type="text" name="admin_remarks" required placeholder="e.g. Invalid Bank Account Number / Incorrect IFSC Code"
                    class="w-full px-4 py-2.5 rounded-xl bg-[#01140c] border border-rose-500/40 text-white font-semibold text-xs focus:outline-none focus:border-rose-400">
            </div>

            <input type="hidden" name="status" value="rejected">

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeRejectModal()" class="flex-1 py-2.5 rounded-xl bg-neutral-800 text-neutral-300 font-bold text-xs hover:bg-neutral-700 transition">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-rose-500 text-white font-black text-xs uppercase hover:bg-rose-600 transition shadow-lg">Confirm Rejection & Refund</button>
            </div>
        </form>
    </div>
</div>

<script>
function copyToClipboard(text, label) {
    navigator.clipboard.writeText(text).then(function() {
        alert(label + ' copied to clipboard!\n' + text);
    }).catch(function() {
        prompt('Copy ' + label + ':', text);
    });
}

function openApproveModal(id, trxId, name, amount) {
    document.getElementById('approveUserName').innerText = name + ' (' + trxId + ')';
    document.getElementById('approveAmount').innerText = '₹' + amount;
    document.getElementById('approveForm').action = '/admin/withdrawals/' + id + '/status';
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

function openRejectModal(id, trxId, name, amount) {
    document.getElementById('rejectUserName').innerText = name + ' (' + trxId + ')';
    document.getElementById('rejectAmount').innerText = '₹' + amount;
    document.getElementById('rejectForm').action = '/admin/withdrawals/' + id + '/status';
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endsection
