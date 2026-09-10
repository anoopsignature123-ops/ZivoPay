@extends('admin.layouts.app')

@section('title', 'Audit Member: ' . $user->name)

@section('content')
    <div class="w-full space-y-6 font-sans">
        <!-- Header Banner Full Width -->
        <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="pdf-num-badge">👑</span>
                    <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">ADMIN MEMBER PROFILE AUDIT</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white font-heading">{{ $user->name }}</h1>
                <div class="flex items-center gap-3 text-xs text-neutral-300 mt-1 font-mono">
                    <span>Code: <strong class="text-amber-400 font-black">{{ $user->referral_code }}</strong></span>
                    <span>•</span>
                    <span>Sponsor: <strong class="text-white font-black">{{ $user->sponsor ? $user->sponsor->name . ' (' . $user->sponsor_code . ')' : ($user->sponsor_code ?? 'No Sponsor (N/A)') }}</strong></span>
                    <span>•</span>
                    <span>Registered: <strong class="text-neutral-300 font-semibold">{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</strong></span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button onclick="openAddFundModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->referral_code }}')" class="px-4 py-2 rounded-xl pdf-gold-ribbon font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 transition flex items-center gap-2 text-black">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-black font-black"></i> Direct Add Fund
                </button>
                <a href="{{ route('admin.users.impersonate', $user->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 font-bold text-xs uppercase tracking-wider hover:bg-emerald-500/30 transition flex items-center gap-2">
                    <i data-lucide="external-link" class="w-4 h-4"></i> Login as User
                </a>
                <a href="{{ route('admin.users.edit', $user->id) }}" class="px-4 py-2 rounded-xl bg-sky-500/20 border border-sky-500/40 text-sky-300 font-bold text-xs uppercase tracking-wider hover:bg-sky-500/30 transition flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-4 h-4"></i> Edit Member
                </a>
                <a href="{{ route('admin.users') }}" class="px-4 py-2 rounded-xl bg-black/80 border border-amber-500/40 text-amber-300 font-bold text-xs uppercase tracking-wider hover:bg-amber-500/20 transition flex items-center gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Audit List
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i> {{ session('success') }}
            </div>
        @endif

        <!-- 4 SUMMARY KPI CARDS (SINGLE ROW 4 COLUMNS) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <div class="p-3.5 sm:p-4 rounded-2xl pdf-package-card relative overflow-hidden">
                <div class="text-[11px] font-extrabold text-neutral-300 uppercase tracking-wider truncate">DEPOSIT WALLET</div>
                <h3 class="text-xl sm:text-2xl font-black text-emerald-400 font-mono mt-0.5">${{ number_format($user->deposit_wallet, 2) }}</h3>
                <span class="text-[10px] text-neutral-400 font-mono block mt-0.5">Available for investments</span>
            </div>

            <div class="p-3.5 sm:p-4 rounded-2xl pdf-package-card relative overflow-hidden">
                <div class="text-[11px] font-extrabold text-amber-400 uppercase tracking-wider truncate">EARNING WALLET</div>
                <h3 class="text-xl sm:text-2xl font-black text-amber-300 font-mono mt-0.5">${{ number_format($user->earning_wallet, 2) }}</h3>
                <span class="text-[10px] text-neutral-400 font-mono block mt-0.5">Available for withdrawals</span>
            </div>

            <div class="p-3.5 sm:p-4 rounded-2xl pdf-package-card relative overflow-hidden">
                <div class="text-[11px] font-extrabold text-sky-400 uppercase tracking-wider truncate">ACTIVE INVESTMENT</div>
                <h3 class="text-xl sm:text-2xl font-black text-sky-300 font-mono mt-0.5">${{ number_format($totalInvested, 2) }}</h3>
                <span class="text-[10px] text-neutral-400 font-mono block mt-0.5">Total active contracts</span>
            </div>

            <div class="p-3.5 sm:p-4 rounded-2xl pdf-package-card relative overflow-hidden">
                <div class="text-[11px] font-extrabold text-teal-400 uppercase tracking-wider truncate">TOTAL EARNINGS ACCUMULATED</div>
                <h3 class="text-xl sm:text-2xl font-black text-teal-300 font-mono mt-0.5">${{ number_format($totalEarnings, 2) }}</h3>
                <span class="text-[10px] text-neutral-400 font-mono block mt-0.5">All historical payouts</span>
            </div>
        </div>

        <!-- Member Details Grid: 2-Column Side-by-Side Layout (col-sm-6 col-sm-6) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full items-start">

            <!-- CARD 1: Full Member Information -->
            <div class="p-6 rounded-3xl pdf-package-card space-y-4">
                <h3 class="text-base font-black text-white flex items-center justify-between border-b border-amber-500/20 pb-3 font-heading uppercase">
                    <span class="flex items-center gap-2"><i data-lucide="user" class="w-5 h-5 text-amber-400"></i> Account & Profile Information</span>
                    @if($user->status === 'active')
                        <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/50 text-[10px] font-black uppercase">ACTIVE</span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/50 text-[10px] font-black uppercase">INACTIVE</span>
                    @endif
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between items-center py-1.5 border-b border-amber-500/10">
                        <span class="text-neutral-400 font-medium">Member Full Name:</span>
                        <span class="font-bold text-white text-sm">{{ $user->name }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1.5 border-b border-amber-500/10">
                        <span class="text-neutral-400 font-medium">Member Code:</span>
                        <span class="font-black text-amber-400 font-mono text-sm">{{ $user->referral_code }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1.5 border-b border-amber-500/10">
                        <span class="text-neutral-400 font-medium">Sponsor Code:</span>
                        <span class="font-bold text-white font-mono">{{ $user->sponsor_code ?? 'N/A (No Sponsor)' }}</span>
                    </div>

                    <div class="flex justify-between items-center py-1.5 border-b border-amber-500/10">
                        <span class="text-neutral-400 font-medium">Registered Email:</span>
                        <span class="font-bold text-white font-mono">{{ $user->email }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1.5 border-b border-amber-500/10">
                        <span class="text-neutral-400 font-medium">Mobile Phone:</span>
                        <span class="font-bold text-white font-mono">{{ $user->mobile ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1.5 border-b border-amber-500/10">
                        <span class="text-neutral-400 font-medium">Quant BOT Status:</span>
                        @if($user->is_bot_active)
                            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/60 text-[10px] font-black uppercase tracking-wider animate-pulse shadow-[0_0_12px_rgba(16,185,129,0.5)] flex items-center gap-1.5" title="Activated at: {{ $user->bot_activated_at?->format('d M Y H:i') }}">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                BOT ACTIVE ({{ $user->bot_activated_at?->format('M d, Y H:i') }})
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-[10px] font-bold uppercase tracking-wider">
                                BOT INACTIVE
                            </span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center py-1.5">
                        <span class="text-neutral-400 font-medium">Registration Date:</span>
                        <span class="font-bold text-neutral-300 font-mono">{{ $user->created_at ? $user->created_at->format('M d, Y h:i A') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- CARD 2: Network & Direct Referrals Quick Audit -->
            <div class="p-6 rounded-3xl pdf-package-card space-y-4">
                <h3 class="text-base font-black text-white flex items-center justify-between border-b border-amber-500/20 pb-3 font-heading uppercase">
                    <span class="flex items-center gap-2"><i data-lucide="share-2" class="w-5 h-5 text-emerald-400"></i> Referral & Network Information</span>
                    <span class="px-2.5 py-0.5 rounded bg-black/60 text-amber-300 border border-amber-500/30 text-[10px] font-mono font-bold">
                        {{ $directCount }} Direct Referrals
                    </span>
                </h3>

                <div class="space-y-3.5 text-xs">
                    <!-- QUICK NAVIGATION ACTION BUTTONS -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <a href="{{ route('admin.network.tree', ['code' => $user->referral_code]) }}" 
                           class="p-3 rounded-2xl bg-black/80 border border-amber-500/40 text-amber-300 font-bold text-xs flex items-center justify-between hover:bg-amber-500/20 transition group">
                            <span class="flex items-center gap-1.5 truncate">
                                <i data-lucide="git-merge" class="w-4 h-4 text-amber-400"></i> Binary Tree
                            </span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-neutral-400 group-hover:translate-x-0.5 transition"></i>
                        </a>

                        <a href="{{ route('admin.network.direct', ['search' => $user->referral_code]) }}" 
                           class="p-3 rounded-2xl bg-black/80 border border-sky-500/40 text-sky-300 font-bold text-xs flex items-center justify-between hover:bg-sky-500/20 transition group">
                            <span class="flex items-center gap-1.5 truncate">
                                <i data-lucide="users" class="w-4 h-4 text-sky-400"></i> Direct Team
                            </span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-neutral-400 group-hover:translate-x-0.5 transition"></i>
                        </a>

                        <a href="{{ route('admin.users.impersonate', $user->id) }}" target="_blank"
                           class="p-3 rounded-2xl bg-black/80 border border-emerald-500/40 text-emerald-300 font-bold text-xs flex items-center justify-between hover:bg-emerald-500/20 transition group">
                            <span class="flex items-center gap-1.5 truncate">
                                <i data-lucide="log-in" class="w-4 h-4 text-emerald-400"></i> Login to User
                            </span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-neutral-400 group-hover:translate-x-0.5 transition"></i>
                        </a>
                    </div>

                    <!-- OFFICIAL MEMBER REFERRAL LINKS BOX (LEFT & RIGHT) -->
                    <div class="space-y-3">
                        <div class="p-3 rounded-2xl bg-black/80 border border-amber-500/40 space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[10px] font-black text-amber-400 uppercase flex items-center gap-1 shrink-0">
                                    🔗 LEFT REFERRAL LINK (TEAM A)
                                </span>
                                <button onclick="copyLink('{{ url('/user/register?sponsor=' . $user->referral_code . '&position=left') }}', 'Left Referral Link copied!')" class="px-2.5 py-0.5 rounded-lg pdf-gold-ribbon font-black text-[9px] uppercase flex items-center gap-1 shrink-0 shadow text-black cursor-pointer">
                                    <i data-lucide="copy" class="w-3 h-3 text-black font-black"></i> Copy Left
                                </button>
                            </div>
                            <p class="text-[10px] text-amber-300 font-mono break-all font-bold">{{ url('/user/register?sponsor=' . $user->referral_code . '&position=left') }}</p>
                        </div>

                        <div class="p-3 rounded-2xl bg-black/80 border border-emerald-500/40 space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[10px] font-black text-emerald-400 uppercase flex items-center gap-1 shrink-0">
                                    🔗 RIGHT REFERRAL LINK (TEAM B)
                                </span>
                                <button onclick="copyLink('{{ url('/user/register?sponsor=' . $user->referral_code . '&position=right') }}', 'Right Referral Link copied!')" class="px-2.5 py-0.5 rounded-lg pdf-gold-ribbon font-black text-[9px] uppercase flex items-center gap-1 shrink-0 shadow text-black cursor-pointer">
                                    <i data-lucide="copy" class="w-3 h-3 text-black font-black"></i> Copy Right
                                </button>
                            </div>
                            <p class="text-[10px] text-emerald-300 font-mono break-all font-bold">{{ url('/user/register?sponsor=' . $user->referral_code . '&position=right') }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- 2-COLUMN SIDE-BY-SIDE TABLES GRID (col-sm-6 col-sm-6) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full items-start">

            <!-- CARD 1 (col-sm-6): ACTIVE INVESTMENT PACKAGES SECTION -->
            <div class="p-6 rounded-3xl pdf-package-card space-y-4 w-full">
                <div class="flex items-center justify-between border-b border-amber-500/20 pb-3">
                    <h3 class="text-base font-black text-white flex items-center gap-2 font-heading uppercase">
                        <i data-lucide="package-check" class="w-5 h-5 text-amber-400"></i> Active Investment Packages
                    </h3>
                    <span class="text-xs text-neutral-400 font-mono">Total Contracts: <strong class="text-amber-400 font-black">{{ $user->userPackages->count() }}</strong></span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs whitespace-nowrap">
                        <thead class="bg-black/80 text-amber-400 uppercase text-[10px] font-bold border-b border-amber-500/30">
                            <tr>
                                <th class="p-3">PACKAGE NAME</th>
                                <th class="p-3">INVESTED AMOUNT</th>
                                <th class="p-3">DAILY ROI %</th>
                                <th class="p-3">TOTAL 2X CAP</th>
                                <th class="p-3">PURCHASED ON</th>
                                <th class="p-3 text-right">STATUS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-500/10 text-neutral-300 font-medium">
                            @forelse($user->userPackages as $pkg)
                            <tr class="hover:bg-amber-500/10 transition">
                                <td class="p-3 font-bold text-white flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ $pkg->status === 'active' ? 'bg-emerald-400 animate-pulse' : 'bg-neutral-500' }}"></span>
                                    📦 {{ $pkg->package->name ?? 'Investment Package' }}
                                </td>
                                <td class="p-3 font-mono font-black text-emerald-400 text-sm">${{ number_format($pkg->invested_amount, 2) }}</td>
                                <td class="p-3 font-mono text-amber-300 font-bold">+{{ $pkg->daily_roi }}% / Day</td>
                                <td class="p-3 font-mono text-white font-bold">${{ number_format($pkg->total_return_amount, 2) }} (2X)</td>
                                <td class="p-3 font-mono text-neutral-300">{{ $pkg->purchased_at ? $pkg->purchased_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                <td class="p-3 text-right">
                                    @if($pkg->status === 'active')
                                        <span class="px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 text-[10px] font-black uppercase">ACTIVE</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded bg-neutral-800 text-neutral-400 border border-neutral-700 text-[10px] font-black uppercase">COMPLETED</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-neutral-400 text-xs">
                                    This member has not purchased any investment packages yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CARD 2 (col-sm-6): RECENT FINANCIAL TRANSACTIONS TABLE -->
            <div class="p-6 rounded-3xl pdf-package-card space-y-4 w-full">
                <div class="flex items-center justify-between border-b border-amber-500/20 pb-3">
                    <h3 class="text-base font-black text-white flex items-center gap-2 font-heading uppercase">
                        <i data-lucide="receipt" class="w-5 h-5 text-amber-400"></i> Recent Financial Transactions Audit
                    </h3>
                    <span class="text-xs text-neutral-400 font-mono">Showing Last 10 Transactions</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs whitespace-nowrap">
                        <thead class="bg-black/80 text-amber-400 uppercase text-[10px] font-bold border-b border-amber-500/30">
                            <tr>
                                <th class="p-3">TXN CODE</th>
                                <th class="p-3">TYPE</th>
                                <th class="p-3">DESCRIPTION / REMARK</th>
                                <th class="p-3">AMOUNT</th>
                                <th class="p-3">DATE & TIME</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-500/10 text-neutral-300 font-medium">
                            @forelse($user->transactions as $txn)
                            <tr class="hover:bg-amber-500/10 transition">
                                <td class="p-3 font-mono font-bold text-amber-400">{{ $txn->trx_id ?? 'TXN-' . $txn->id }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded font-mono text-[9px] font-black uppercase {{ $txn->type === 'credit' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40' }}">
                                        {{ strtoupper($txn->type) }}
                                    </span>
                                </td>
                                <td class="p-3 font-medium text-white">{{ $txn->description ?? 'Transaction' }}</td>
                                <td class="p-3 font-mono font-black text-sm {{ $txn->type === 'credit' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $txn->type === 'credit' ? '+' : '-' }}${{ number_format($txn->amount, 2) }}
                                </td>
                                <td class="p-3 font-mono text-neutral-400 text-[11px]">{{ $txn->created_at ? $txn->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-neutral-400 text-xs">
                                    No financial transactions logged for this member yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    <!-- DYNAMIC DIRECT ADD FUND MODAL (POPUP) -->
    <div id="addFundModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-panel border border-amber-500/40 rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-5 relative">
            <button onclick="closeAddFundModal()" class="absolute right-4 top-4 text-neutral-400 hover:text-white p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <div class="flex items-center gap-3 border-b border-amber-500/20 pb-3">
                <div class="w-10 h-10 rounded-full pdf-gold-badge text-black font-black text-sm flex items-center justify-center shadow-md shrink-0">
                    💰
                </div>
                <div>
                    <h3 class="text-base font-black text-white font-heading uppercase">DIRECT ADD FUND</h3>
                    <p class="text-xs text-neutral-400" id="modalUserMeta">Credit wallet balance directly from Admin</p>
                </div>
            </div>

            <form id="addFundForm" action="" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-amber-400 uppercase tracking-wider">Select Wallet</label>
                    <select name="wallet_type" class="w-full px-4 py-3 rounded-xl bg-black/90 border border-amber-500/40 text-amber-300 font-bold text-xs focus:outline-none focus:border-amber-400 cursor-pointer" required>
                        <option value="deposit_wallet">💳 Deposit Wallet (For Purchasing Packages)</option>
                        <option value="earning_wallet">💰 Earning Wallet (For Withdrawals / Earnings)</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-amber-400 uppercase tracking-wider">Amount ($ USD)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-amber-400 font-bold">$</span>
                        <input type="number" step="0.01" min="0.01" name="amount" placeholder="100.00" class="w-full pl-8 pr-4 py-3 rounded-xl bg-black/90 border border-amber-500/40 text-white font-mono font-bold text-xs focus:outline-none focus:border-amber-400" required>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-amber-400 uppercase tracking-wider">Transaction Remark / Note</label>
                    <input type="text" name="remark" value="Directly credited by Admin side" placeholder="Enter custom remark..." class="w-full px-4 py-3 rounded-xl bg-black/90 border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400" required>
                    <p class="text-[10px] text-neutral-400">This remark will be logged in the member's transaction history.</p>
                </div>

                <div class="pt-2 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeAddFundModal()" class="px-5 py-2.5 rounded-xl bg-black/80 border border-neutral-700 text-neutral-300 font-bold text-xs hover:bg-neutral-800 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl pdf-gold-ribbon text-xs font-black uppercase tracking-wider shadow-lg hover:scale-105 transition text-black">
                        Confirm & Credit Wallet
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddFundModal(userId, userName, userCode) {
            const modal = document.getElementById('addFundModal');
            const form = document.getElementById('addFundForm');
            const meta = document.getElementById('modalUserMeta');

            form.action = `/admin/users/${userId}/add-fund`;
            meta.innerText = `Crediting fund to: ${userName} (${userCode})`;
            modal.classList.remove('hidden');
        }

        function closeAddFundModal() {
            document.getElementById('addFundModal').classList.add('hidden');
        }

        function copyLink(text, message) {
            navigator.clipboard.writeText(text);
            if (typeof showToast === 'function') {
                showToast('Success', message, 'success');
            } else {
                alert(message);
            }
        }
    </script>
@endsection
