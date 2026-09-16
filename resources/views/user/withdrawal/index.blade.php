@extends('user.layouts.app')

@section('title', 'ZIVO PAY - 24x7 Withdrawal Portal')

@section('content')
<div class="space-y-6 font-sans">

    <!-- Top Banner & Wallet Balance Header -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.25)] flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/40">
                    24X7 WITHDRAWAL PORTAL
                </span>
                <span class="text-xs text-emerald-400 font-black tracking-[2px] uppercase">ZIVO PAY PAYOUTS</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight mt-1 font-heading">24x7 INSTANT WITHDRAWALS</h1>
            <p class="text-xs text-neutral-300 mt-1">Withdraw your daily ROI earnings, level commissions, and referral income anytime 24 hours a day.</p>
        </div>

        <div class="p-4 rounded-2xl bg-[#02180f] border-2 border-emerald-400 text-center shrink-0 min-w-[220px] shadow-lg">
            <span class="block text-[10px] font-bold text-emerald-400 uppercase tracking-wider">Available Earning Wallet</span>
            <span class="text-3xl font-black text-white font-mono mt-0.5 block">₹{{ number_format($user->earning_wallet, 2) }}</span>
            <span class="text-[10px] text-emerald-400 font-semibold mt-1 block">Ready for 24x7 Payout</span>
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

    <!-- Withdrawal Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Approved Withdrawn -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between text-emerald-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Total Net Withdrawn</span>
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
            <h3 class="text-2xl font-black text-white font-mono">₹{{ number_format($stats['total_approved'], 2) }}</h3>
            <span class="text-[10px] text-emerald-400 font-semibold mt-1 block">Successfully Credited</span>
        </div>

        <!-- 2. Pending Requests Amount -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between text-teal-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Pending Approvals</span>
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <h3 class="text-2xl font-black text-teal-300 font-mono">₹{{ number_format($stats['total_pending'], 2) }}</h3>
            <span class="text-[10px] text-neutral-400 font-semibold mt-1 block">In Processing Queue</span>
        </div>

        <!-- 3. Total Rejected Amount -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between text-rose-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Rejected Requests</span>
                <i data-lucide="x-circle" class="w-5 h-5"></i>
            </div>
            <h3 class="text-2xl font-black text-rose-300 font-mono">₹{{ number_format($stats['total_rejected'], 2) }}</h3>
            <span class="text-[10px] text-neutral-400 font-semibold mt-1 block">Refunded to Wallet</span>
        </div>

        <!-- 4. Total Requests Count -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between text-emerald-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Total Payout Transactions</span>
                <i data-lucide="receipt" class="w-5 h-5"></i>
            </div>
            <h3 class="text-2xl font-black text-white font-mono">{{ $stats['total_count'] }}</h3>
            <span class="text-[10px] text-neutral-400 font-semibold mt-1 block">All Time Requests</span>
        </div>
    </div>

    <!-- Request Form & Guidelines Section (50% / 50% Equal Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <!-- Form Area (6 Cols - 50%) -->
        <div class="lg:col-span-6 p-6 sm:p-8 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl space-y-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-emerald-500/20 pb-4 mb-4">
                    <h2 class="text-lg font-black text-white uppercase tracking-tight flex items-center gap-2 font-heading">
                        <i data-lucide="send" class="w-5 h-5 text-emerald-400"></i> Submit 24x7 Payout Request
                    </h2>
                    <span class="text-[11px] px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 font-bold border border-emerald-500/30">Fast Processing</span>
                </div>

                <form action="{{ route('user.withdrawal.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Amount Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-emerald-400 uppercase">Withdrawal Amount (₹) *</label>
                            <span class="text-[11px] text-neutral-400">Min: <strong class="text-white">₹500</strong></span>
                        </div>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-400 font-bold text-lg font-mono">₹</span>
                            <input type="number" id="wthAmount" name="amount" min="500" max="{{ $user->earning_wallet }}" step="50" required value="{{ old('amount') }}" placeholder="e.g. 1000"
                                class="w-full pl-10 pr-20 py-3 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-mono font-bold text-base focus:outline-none focus:border-emerald-400 transition"
                                oninput="calculateFee(this.value)">
                            <button type="button" onclick="setMaxAmount({{ $user->earning_wallet }})" class="absolute right-3 top-1/2 -translate-y-1/2 px-2.5 py-1 rounded bg-emerald-500/20 hover:bg-emerald-500/40 text-emerald-300 font-bold text-[10px] border border-emerald-500/30 transition">
                                MAX
                            </button>
                        </div>
                        <p class="text-[11px] text-neutral-400 mt-1">Available Earning Wallet: <strong class="text-emerald-400">₹{{ number_format($user->earning_wallet, 2) }}</strong></p>
                    </div>

                    <!-- Payment Method Field -->
                    <div>
                        <label class="block text-xs font-bold text-emerald-400 uppercase mb-1">Select Payment Method *</label>
                        <select id="paymentMethod" name="payment_method" required class="w-full px-4 py-3 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400 transition cursor-pointer">
                            <option value="Bank Transfer" {{ old('payment_method') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer (IMPS / NEFT / RTGS)</option>
                            <option value="UPI" {{ old('payment_method') === 'UPI' ? 'selected' : '' }}>UPI (Google Pay / PhonePe / Paytm / BHIM)</option>
                            <option value="USDT (Crypto Wallet)" {{ old('payment_method') === 'USDT (Crypto Wallet)' ? 'selected' : '' }}>USDT TRC20 / BEP20 (Crypto Wallet)</option>
                        </select>
                    </div>

                    <!-- Account / Wallet Details Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-emerald-400 uppercase">Account / UPI / Wallet Address *</label>
                            @if($user->wallet_address)
                                <button type="button" onclick="autofillDetails('{{ addslashes($user->wallet_address) }}')" class="text-[10px] text-teal-300 hover:text-white font-bold underline flex items-center gap-1">
                                    <i data-lucide="zap" class="w-3 h-3 text-teal-300"></i> Autofill Profile Address
                                </button>
                            @endif
                        </div>
                        <textarea id="accountDetails" name="account_details" rows="3" required placeholder="Enter complete payment details e.g.&#10;Bank Name: HDFC Bank&#10;Account No: 1234567890&#10;IFSC Code: HDFC0001234&#10;Holder Name: John Doe"
                            class="w-full px-4 py-3 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400 transition">{{ old('account_details') }}</textarea>
                    </div>

                    <!-- Live Net Amount Payout Breakdown Box -->
                    <div class="p-4 rounded-2xl bg-[#02180f] border border-emerald-500/40 space-y-2 text-xs">
                        <div class="flex justify-between text-neutral-300">
                            <span>Gross Requested Amount:</span>
                            <strong id="displayGross" class="text-white font-mono font-bold">₹0.00</strong>
                        </div>
                        <div class="flex justify-between text-neutral-300">
                            <span>Admin Service Fee (5%):</span>
                            <strong id="displayFee" class="text-rose-400 font-mono font-bold">-₹0.00</strong>
                        </div>
                        <div class="border-t border-emerald-500/20 pt-2 flex justify-between text-sm">
                            <span class="font-bold text-emerald-400">Net Amount You Receive:</span>
                            <strong id="displayNet" class="text-emerald-400 font-mono font-black text-base">₹0.00</strong>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-black font-black uppercase tracking-wider text-xs hover:shadow-[0_0_25px_rgba(16,185,129,0.8)] transition flex items-center justify-center gap-2 shadow-lg">
                        <i data-lucide="check-circle" class="w-4 h-4"></i> Submit 24x7 Payout Request
                    </button>
                </form>
            </div>
        </div>

        <!-- Rules & Payout Guidelines (6 Cols - 50%) -->
        <div class="lg:col-span-6 p-6 sm:p-8 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl space-y-6 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center gap-2 border-b border-emerald-500/20 pb-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-white uppercase tracking-wider font-heading">24x7 Payout Rules</h3>
                        <p class="text-[11px] text-neutral-400">ZIVO PAY Instant Processing Policy</p>
                    </div>
                </div>

                <ul class="space-y-3.5 text-xs text-neutral-300">
                    <li class="flex items-start gap-2.5">
                        <div class="w-5 h-5 rounded bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-500/30">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        </div>
                        <span><strong>24x7 Availability:</strong> You can submit withdrawal requests 24 hours a day, 7 days a week.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <div class="w-5 h-5 rounded bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-500/30">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        </div>
                        <span><strong>Minimum Limit:</strong> Minimum single withdrawal request is <strong>₹500</strong>.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <div class="w-5 h-5 rounded bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-500/30">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        </div>
                        <span><strong>Service Charge:</strong> Flat <strong>5% admin fee</strong> deducted to cover gateway & banking transaction costs.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <div class="w-5 h-5 rounded bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-500/30">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        </div>
                        <span><strong>Multi-Channel Payout:</strong> Supported via Bank Account (IMPS/NEFT), UPI ID, or USDT Crypto Address.</span>
                    </li>
                </ul>
            </div>

            <!-- Profile Wallet Info Badge -->
            <div class="p-4 rounded-2xl bg-[#01140c] border border-emerald-500/30 space-y-1">
                <span class="text-[10px] text-neutral-400 uppercase font-bold block">Saved Withdrawal Profile:</span>
                <strong class="text-xs text-emerald-400 font-mono block truncate" title="{{ $user->wallet_address ?? 'Not Set' }}">
                    {{ $user->wallet_address ?? 'No default wallet address saved in profile.' }}
                </strong>
                <a href="{{ route('user.profile') }}" class="text-[10px] text-teal-300 hover:underline font-bold inline-block mt-1">
                    Update Profile Details &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Payout History Table -->
    <div class="p-6 rounded-3xl bg-[#042718] border-2 border-emerald-500/40 shadow-2xl space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-emerald-500/20 pb-4">
            <h2 class="text-lg font-black text-white uppercase tracking-tight flex items-center gap-2 font-heading">
                <i data-lucide="history" class="w-5 h-5 text-emerald-400"></i> My Withdrawal History
            </h2>

            <!-- Filter Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto max-w-full pb-1 sm:pb-0 shrink-0">
                <a href="{{ route('user.withdrawal.index') }}" class="whitespace-nowrap px-3 py-1.5 rounded-lg {{ !request('status') ? 'bg-emerald-500 text-black font-bold' : 'bg-emerald-500/10 text-neutral-300 hover:bg-emerald-500/20' }}">
                    All Requests
                </a>
                <a href="{{ route('user.withdrawal.index', ['status' => 'pending']) }}" class="whitespace-nowrap px-3 py-1.5 rounded-lg {{ request('status') === 'pending' ? 'bg-teal-500 text-black font-bold' : 'bg-emerald-500/10 text-neutral-300 hover:bg-emerald-500/20' }}">
                    Pending
                </a>
                <a href="{{ route('user.withdrawal.index', ['status' => 'approved']) }}" class="whitespace-nowrap px-3 py-1.5 rounded-lg {{ request('status') === 'approved' ? 'bg-emerald-500 text-black font-bold' : 'bg-emerald-500/10 text-neutral-300 hover:bg-emerald-500/20' }}">
                    Approved
                </a>
                <a href="{{ route('user.withdrawal.index', ['status' => 'rejected']) }}" class="whitespace-nowrap px-3 py-1.5 rounded-lg {{ request('status') === 'rejected' ? 'bg-rose-500 text-white font-bold' : 'bg-emerald-500/10 text-neutral-300 hover:bg-emerald-500/20' }}">
                    Rejected
                </a>
            </div>
        </div>

        @if($withdrawals->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-neutral-300 whitespace-nowrap">
                    <thead class="bg-[#02180f] text-emerald-400 uppercase text-[10px] font-bold border-b border-emerald-500/30">
                        <tr>
                            <th class="p-3.5">Trx ID</th>
                            <th class="p-3.5">Payment Method</th>
                            <th class="p-3.5">Account / Wallet Details</th>
                            <th class="p-3.5">Gross Amount</th>
                            <th class="p-3.5">Fee (5%)</th>
                            <th class="p-3.5">Net Payable</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5">Admin Remarks / UTR</th>
                            <th class="p-3.5 text-right">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-500/10">
                        @foreach($withdrawals as $wth)
                            <tr class="hover:bg-emerald-500/5 transition">
                                <td class="p-3.5 font-mono font-bold text-white">{{ $wth->trx_id }}</td>
                                <td class="p-3.5 font-semibold text-emerald-400">{{ $wth->payment_method }}</td>
                                <td class="p-3.5 font-medium text-neutral-200 max-w-xs truncate" title="{{ $wth->account_details }}">
                                    {{ $wth->account_details }}
                                </td>
                                <td class="p-3.5 font-mono font-bold text-white">₹{{ number_format($wth->amount, 2) }}</td>
                                <td class="p-3.5 font-mono text-rose-400">-₹{{ number_format($wth->charge, 2) }}</td>
                                <td class="p-3.5 font-mono font-bold text-emerald-400 text-sm">₹{{ number_format($wth->final_amount, 2) }}</td>
                                <td class="p-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase
                                        {{ $wth->status === 'approved' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 shadow-[0_0_10px_rgba(16,185,129,0.3)]' : '' }}
                                        {{ $wth->status === 'pending' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : '' }}
                                        {{ $wth->status === 'rejected' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/40' : '' }}">
                                        {{ $wth->status }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-neutral-300 max-w-xs truncate font-mono text-[11px]" title="{{ $wth->admin_remarks ?? 'N/A' }}">
                                    {{ $wth->admin_remarks ?? '-' }}
                                </td>
                                <td class="p-3.5 text-right text-neutral-400">{{ $wth->created_at ? $wth->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-4 border-t border-emerald-500/20">
                {{ $withdrawals->links() }}
            </div>
        @else
            <div class="p-8 text-center text-neutral-400 space-y-2">
                <i data-lucide="inbox" class="w-8 h-8 text-emerald-400/50 mx-auto"></i>
                <p>No withdrawal records found matching the selected status filter.</p>
            </div>
        @endif
    </div>

</div>

<script>
function calculateFee(val) {
    var amount = parseFloat(val) || 0;
    var fee = Math.round((amount * 0.05) * 100) / 100;
    var net = Math.round((amount - fee) * 100) / 100;

    document.getElementById('displayGross').innerText = '₹' + amount.toFixed(2);
    document.getElementById('displayFee').innerText = '-₹' + fee.toFixed(2);
    document.getElementById('displayNet').innerText = '₹' + net.toFixed(2);
}

function setMaxAmount(maxVal) {
    var el = document.getElementById('wthAmount');
    el.value = maxVal;
    calculateFee(maxVal);
}

function autofillDetails(details) {
    document.getElementById('accountDetails').value = details;
}
</script>
@endsection
