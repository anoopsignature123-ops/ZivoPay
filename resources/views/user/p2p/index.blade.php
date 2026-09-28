@extends('user.layouts.app')

@section('title', 'P2P Member Fund Transfer - ZIVO PAY')

@section('content')
    <div class="max-w-5xl mx-auto space-y-5 font-sans">

        <!-- Top Header Banner & Balances -->
        <div class="p-5 sm:p-6 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-[0_0_30px_rgba(16,185,129,0.25)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] uppercase tracking-wider border border-emerald-500/40">
                        PEER-TO-PEER NETWORK
                    </span>
                    <span class="text-[11px] text-emerald-400 font-black tracking-[2px] uppercase">ZIVO PAY TRANSFERS</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-white uppercase tracking-tight font-heading">P2P MEMBER FUND TRANSFER</h1>
                <p class="text-xs text-neutral-300 mt-0.5">Transfer funds instantly to any registered ZivoPay member using their Referral Code.</p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <div class="p-3 rounded-2xl bg-[#01140c] border border-emerald-500/40 text-center min-w-[130px]">
                    <span class="block text-[10px] font-bold text-emerald-400 uppercase">Fund Wallet</span>
                    <strong class="text-base font-mono text-white">₹{{ number_format($user->deposit_wallet, 2) }}</strong>
                </div>
                <div class="p-3 rounded-2xl bg-[#01140c] border border-emerald-500/40 text-center min-w-[130px]">
                    <span class="block text-[10px] font-bold text-teal-300 uppercase">Earning Wallet</span>
                    <strong class="text-base font-mono text-emerald-400">₹{{ number_format($user->earning_wallet, 2) }}</strong>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-300 text-xs font-bold flex items-center gap-2 shadow-md">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400 shrink-0"></i>
                <span>{{ session('success') }}</span>
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

        <!-- P2P Transfer Form & Info Hub (2 Equal Columns) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-stretch">

            <!-- Left: Transfer Form Card -->
            <div class="p-5 sm:p-6 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl space-y-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 border-b border-emerald-500/20 pb-3 mb-4">
                        <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                            <i data-lucide="send" class="w-4 h-4"></i>
                        </div>
                        <h2 class="text-xs font-black text-white uppercase tracking-wider font-heading">Send P2P Transfer</h2>
                    </div>

                    <form action="{{ route('user.p2p.store') }}" method="POST" id="p2pForm" class="space-y-3.5">
                        @csrf

                        <!-- Recipient Member Code Input with Live AJAX Lookup -->
                        <div>
                            <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1.5">Recipient Referral Code / Member ID *</label>
                            <div class="flex items-center gap-1.5 p-1 rounded-xl bg-[#01140c] border border-emerald-500/40 focus-within:border-emerald-400 transition">
                                <input type="text" name="receiver_code" id="receiverCodeInput" required value="{{ old('receiver_code') }}"
                                    placeholder="e.g. ZIVO-4133291"
                                    class="w-full bg-transparent px-3 py-2 text-white text-xs font-mono font-bold uppercase focus:outline-none tracking-wide">
                                <button type="button" id="verifyBtn" class="shrink-0 px-3.5 py-2 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 font-bold text-xs border border-emerald-500/40 transition whitespace-nowrap shadow-sm">
                                    Verify Member
                                </button>
                            </div>

                            <!-- AJAX Recipient Info Feedback Box (Compact) -->
                            <div id="recipientFeedback" class="hidden mt-2.5 p-3 rounded-xl text-xs border"></div>
                        </div>

                        <!-- Source Wallet Dropdown -->
                        <div>
                            <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1.5">Select Source Wallet *</label>
                            <select name="from_wallet" id="sourceWalletSelect" required onchange="updateWalletHint()"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400 transition cursor-pointer">
                                <option value="deposit_wallet" {{ old('from_wallet') === 'deposit_wallet' ? 'selected' : '' }}>
                                    Fund Wallet (Available: ₹{{ number_format($user->deposit_wallet, 2) }})
                                </option>
                                <option value="earning_wallet" {{ old('from_wallet', 'earning_wallet') === 'earning_wallet' ? 'selected' : '' }}>
                                    Earning Wallet (Available: ₹{{ number_format($user->earning_wallet, 2) }})
                                </option>
                            </select>
                            <span class="text-[10px] text-neutral-400 mt-1 block">Funds will be credited instantly into Recipient's <strong>Fund Wallet</strong>.</span>
                        </div>

                        <!-- Amount Input -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Transfer Amount (₹) *</label>
                                <span class="text-[10px] text-neutral-400">Min: <strong class="text-white">₹10</strong></span>
                            </div>
                            <div class="flex items-center gap-1 p-1 rounded-xl bg-[#01140c] border border-emerald-500/40 focus-within:border-emerald-400 transition">
                                <span class="pl-3 text-emerald-400 font-bold font-mono text-xs select-none">₹</span>
                                <input type="number" name="amount" id="transferAmountInput" min="10" step="1" required value="{{ old('amount') }}"
                                    placeholder="Enter amount"
                                    class="w-full bg-transparent px-2.5 py-2 text-white text-xs font-mono font-bold focus:outline-none">
                                <button type="button" onclick="setMaxAmount()" class="shrink-0 px-3 py-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 text-xs font-bold border border-emerald-500/30 transition whitespace-nowrap">
                                    MAX
                                </button>
                            </div>
                        </div>

                        <!-- Remarks / Note -->
                        <div>
                            <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1.5">Transfer Note / Remarks (Optional)</label>
                            <input type="text" name="remarks" value="{{ old('remarks') }}"
                                placeholder="e.g. Package activation fund, P2P payment..."
                                class="w-full px-4 py-3 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400 transition">
                        </div>

                        <!-- Submit Action Button -->
                        <div class="pt-2">
                            <button type="submit" onclick="return confirm('Confirm P2P Fund Transfer? Funds will be transferred instantly.');"
                                class="w-full py-3 px-6 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-black font-black text-xs uppercase tracking-wider shadow-lg hover:scale-[1.01] active:scale-95 transition inline-flex items-center justify-center gap-2">
                                <i data-lucide="send" class="w-4 h-4"></i>
                                <span>Execute Instant P2P Transfer</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right: P2P Rules & Instant Benefits Card -->
            <div class="p-5 sm:p-6 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl space-y-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 border-b border-emerald-500/20 pb-3 mb-4">
                        <div class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center border border-teal-500/30 shrink-0">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                        </div>
                        <h2 class="text-xs font-black text-white uppercase tracking-wider font-heading">P2P Transfer Rules & Benefits</h2>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="p-3 rounded-xl bg-[#01140c] border border-emerald-500/20 flex items-start gap-2.5">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5"></i>
                            <div>
                                <strong class="text-white font-bold block">Instant Real-Time Credit:</strong>
                                <span class="text-neutral-400 text-[11px]">Transferred funds are credited instantly into the recipient member's Fund Wallet without any admin delay.</span>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-[#01140c] border border-emerald-500/20 flex items-start gap-2.5">
                            <i data-lucide="percent" class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5"></i>
                            <div>
                                <strong class="text-white font-bold block">0% Transaction Fee:</strong>
                                <span class="text-neutral-400 text-[11px]">Peer-to-Peer member transfers carry 0% service charge. 100% of the transferred amount is received.</span>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-[#01140c] border border-emerald-500/20 flex items-start gap-2.5">
                            <i data-lucide="lock" class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5"></i>
                            <div>
                                <strong class="text-white font-bold block">Secure Member Verification:</strong>
                                <span class="text-neutral-400 text-[11px]">Always verify the recipient's name before submitting to prevent accidental transfers to wrong member IDs.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-[#01140c] border border-emerald-500/40 text-center space-y-1">
                    <span class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider">Your P2P Activity Summary</span>
                    <div class="flex items-center justify-around text-xs font-mono pt-1">
                        <div>
                            <span class="text-neutral-400 text-[10px] block">Total Sent:</span>
                            <strong class="text-rose-400">₹{{ number_format($stats['total_sent'], 2) }}</strong>
                        </div>
                        <div class="h-6 w-px bg-emerald-500/20"></div>
                        <div>
                            <span class="text-neutral-400 text-[10px] block">Total Received:</span>
                            <strong class="text-emerald-400">₹{{ number_format($stats['total_received'], 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- P2P Transfer History Ledger Table -->
        <div class="rounded-3xl bg-[#042718] border border-emerald-500/30 overflow-hidden shadow-2xl">
            <div class="p-4 sm:p-5 border-b border-emerald-500/20 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-[#01140c]/80">
                <div>
                    <h3 class="text-sm font-black text-white uppercase tracking-wider font-heading flex items-center gap-2">
                        <i data-lucide="history" class="w-4 h-4 text-emerald-400"></i> P2P Member Transfer History
                    </h3>
                    <p class="text-[11px] text-neutral-400 mt-0.5">Audit log of all P2P fund transfers sent and received by your account.</p>
                </div>

                <!-- Filter Type Tabs -->
                <div class="flex items-center gap-1.5 shrink-0 bg-[#042718] p-1 rounded-xl border border-emerald-500/30">
                    <a href="{{ route('user.p2p.index') }}" class="px-3 py-1 rounded-lg text-xs font-bold transition {{ !request('type') ? 'bg-emerald-500 text-black shadow' : 'text-neutral-400 hover:text-white' }}">
                        All ({{ $stats['total_count'] }})
                    </a>
                    <a href="{{ route('user.p2p.index', ['type' => 'sent']) }}" class="px-3 py-1 rounded-lg text-xs font-bold transition {{ request('type') === 'sent' ? 'bg-rose-500 text-white shadow' : 'text-neutral-400 hover:text-white' }}">
                        Sent
                    </a>
                    <a href="{{ route('user.p2p.index', ['type' => 'received']) }}" class="px-3 py-1 rounded-lg text-xs font-bold transition {{ request('type') === 'received' ? 'bg-emerald-500 text-black shadow' : 'text-neutral-400 hover:text-white' }}">
                        Received
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-neutral-300">
                    <thead class="bg-[#01140c] text-emerald-400 font-extrabold uppercase tracking-wider border-b border-emerald-500/20 text-[10px]">
                        <tr>
                            <th class="px-4 py-3">TRX ID</th>
                            <th class="px-4 py-3">Transfer Type</th>
                            <th class="px-4 py-3">Sender / Receiver</th>
                            <th class="px-4 py-3">Source Wallet</th>
                            <th class="px-4 py-3">Amount (₹)</th>
                            <th class="px-4 py-3">Remarks</th>
                            <th class="px-4 py-3 text-right">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-500/10">
                        @forelse($transfers as $trf)
                            @php
                                $isSent = ($trf->sender_id === $user->id);
                            @endphp
                            <tr class="hover:bg-emerald-500/5 transition">
                                <td class="px-4 py-3 font-mono font-bold text-white whitespace-nowrap">
                                    {{ $trf->trx_id }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($isSent)
                                        <span class="px-2.5 py-1 rounded-full bg-rose-500/20 border border-rose-500/40 text-rose-300 text-[10px] font-bold uppercase inline-flex items-center gap-1">
                                            <i data-lucide="arrow-up-right" class="w-3 h-3 text-rose-400"></i> Sent
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-[10px] font-bold uppercase inline-flex items-center gap-1">
                                            <i data-lucide="arrow-down-left" class="w-3 h-3 text-emerald-400"></i> Received
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($isSent)
                                        <p class="font-bold text-white text-xs">To: {{ $trf->receiver->name ?? 'N/A' }}</p>
                                        <p class="text-[10px] text-teal-400 font-mono">Code: {{ $trf->receiver->referral_code ?? 'N/A' }}</p>
                                    @else
                                        <p class="font-bold text-white text-xs">From: {{ $trf->sender->name ?? 'N/A' }}</p>
                                        <p class="text-[10px] text-teal-400 font-mono">Code: {{ $trf->sender->referral_code ?? 'N/A' }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-semibold text-neutral-300 capitalize">
                                    {{ str_replace('_', ' ', $trf->from_wallet) }}
                                </td>
                                <td class="px-4 py-3 font-mono font-black text-sm whitespace-nowrap">
                                    @if($isSent)
                                        <span class="text-rose-400">-₹{{ number_format($trf->amount, 2) }}</span>
                                    @else
                                        <span class="text-emerald-400">+₹{{ number_format($trf->amount, 2) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-neutral-400 text-[11px]">
                                    {{ $trf->remarks ?: '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right font-mono text-[11px] text-neutral-400">
                                    {{ $trf->created_at->format('d M Y, h:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-neutral-400">
                                    No P2P member transfer records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transfers->hasPages())
                <div class="p-4 border-t border-emerald-500/20">
                    {{ $transfers->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Live Recipient Verification JavaScript -->
    <script>
        const depBal = {{ (float) $user->deposit_wallet }};
        const earnBal = {{ (float) $user->earning_wallet }};

        function setMaxAmount() {
            const select = document.getElementById('sourceWalletSelect');
            const input = document.getElementById('transferAmountInput');
            if (select && input) {
                const bal = select.value === 'deposit_wallet' ? depBal : earnBal;
                input.value = Math.floor(bal);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const receiverInput = document.getElementById('receiverCodeInput');
            const verifyBtn = document.getElementById('verifyBtn');
            const feedbackBox = document.getElementById('recipientFeedback');

            function performMemberCheck() {
                const code = receiverInput.value.trim();
                if (!code) {
                    feedbackBox.classList.add('hidden');
                    return;
                }

                feedbackBox.className = "mt-2 p-2 rounded-xl text-xs space-y-0.5 border bg-[#01140c] border-emerald-500/30 text-neutral-300";
                feedbackBox.innerHTML = `<span>⏳ Verifying member details...</span>`;
                feedbackBox.classList.remove('hidden');

                fetch(`{{ route('user.p2p.check-member') }}?member_code=${encodeURIComponent(code)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            feedbackBox.className = "mt-2 p-2.5 rounded-xl text-xs border bg-emerald-950/80 border-emerald-500/60 text-emerald-300 space-y-0.5 shadow";
                            feedbackBox.innerHTML = `
                                <div class="flex items-center gap-1.5 font-bold text-white">
                                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
                                    <span>Recipient Verified: <strong>${data.name}</strong></span>
                                </div>
                                <div class="text-[10px] text-teal-300 font-mono">
                                    Code: ${data.referral_code} • Account: ${data.is_active ? 'Active Member (₹3,000)' : 'Free Member'}
                                </div>
                            `;
                            if (window.lucide) window.lucide.createIcons();
                        } else {
                            feedbackBox.className = "mt-2 p-2.5 rounded-xl text-xs border bg-rose-950/80 border-rose-500/60 text-rose-300 space-y-0.5 shadow";
                            feedbackBox.innerHTML = `
                                <div class="flex items-center gap-1.5 font-bold">
                                    <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-400"></i>
                                    <span>${data.message}</span>
                                </div>
                            `;
                            if (window.lucide) window.lucide.createIcons();
                        }
                    })
                    .catch(err => {
                        feedbackBox.className = "mt-2 p-2.5 rounded-xl text-xs border bg-rose-950/80 border-rose-500/60 text-rose-300";
                        feedbackBox.innerHTML = `<span>Error verifying member code.</span>`;
                    });
            }

            if (verifyBtn) verifyBtn.addEventListener('click', performMemberCheck);
            if (receiverInput) receiverInput.addEventListener('blur', performMemberCheck);
        });
    </script>
@endsection
