@extends('admin.layouts.app')

@section('title', 'User Management Module - ZIVO PAY')

@section('content')
<div class="w-full space-y-6 font-sans">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.3)] flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] uppercase border border-emerald-500/40">UM</span>
                <span class="text-xs text-emerald-400 font-black tracking-[3px] uppercase">ZIVO PAY NETWORK</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white font-heading">USER MANAGEMENT MODULE</h1>
            <p class="text-xs text-neutral-300 mt-1">Manage registered members, referral links, sponsor trees, fund wallets, and account actions.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <a href="{{ route('admin.users.create') }}" class="px-5 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-black font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 transition flex items-center gap-2 shrink-0">
                <i data-lucide="user-plus" class="w-4 h-4 text-black"></i>
                ADD NEW USER
            </a>

            <!-- Search Form -->
            <form id="userSearchForm" action="{{ route('admin.users') }}" method="GET" class="flex items-center gap-2 flex-1 lg:flex-none">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <div class="relative flex-1 lg:w-72">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-400"></i>
                    <input type="text" id="liveSearchInput" name="search" value="{{ request('search') }}" placeholder="Search name, email, code..." class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400">
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 font-bold text-xs hover:bg-emerald-500/30 transition flex items-center gap-1.5 shrink-0">
                    Search
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Quick Status Filter Tabs & Table Container -->
    <div class="bg-[#042718] p-6 shadow-2xl rounded-3xl border border-emerald-500/30 space-y-6">
        <!-- Top Status Filter Tabs -->
        <div class="border-b border-emerald-500/20 pb-4 flex items-center justify-between flex-wrap gap-4">
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('admin.users', array_merge(request()->except('status'), ['status' => ''])) }}" class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition {{ !request('status') ? 'bg-emerald-500 text-black font-black shadow-md' : 'bg-[#01140c] border border-emerald-500/30 text-neutral-300 hover:text-emerald-400' }}">
                    All Members
                </a>
                <a href="{{ route('admin.users', array_merge(request()->except('status'), ['status' => 'active'])) }}" class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'active' ? 'bg-emerald-500 text-black font-black shadow-md' : 'bg-[#01140c] border border-emerald-500/30 text-neutral-300 hover:text-emerald-400' }}">
                    Active Members
                </a>
                <a href="{{ route('admin.users', array_merge(request()->except('status'), ['status' => 'inactive'])) }}" class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition {{ request('status') === 'inactive' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/50 font-black shadow-md' : 'bg-[#01140c] border border-emerald-500/30 text-neutral-300 hover:text-rose-400' }}">
                    Inactive Members
                </a>
            </div>

            <span class="text-xs text-neutral-400 font-mono">Total Members: <strong class="text-emerald-400">{{ $users->total() }}</strong></span>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-[#02180f] text-emerald-400 uppercase text-[10px] font-bold border-b border-emerald-500/30">
                    <tr>
                        <th class="p-3.5">User Details & Referral Links</th>
                        <th class="p-3.5">Sponsor Details</th>
                        <th class="p-3.5">Registration & Activation</th>
                        <th class="p-3.5">Fund & Earning Wallets</th>
                        <th class="p-3.5">Account Status</th>
                        <th class="p-3.5 text-center">DIRECT ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/10">
                    @forelse($users as $user)
                        <tr class="hover:bg-emerald-500/5 transition">
                            <!-- Column 1: User Details & Referral Links -->
                            <td class="p-3.5 max-w-xs">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 via-teal-600 to-emerald-800 text-white font-black text-sm flex items-center justify-center shadow-md shrink-0 border-2 border-emerald-400">
                                        <i data-lucide="user" class="w-5 h-5 text-white"></i>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="font-bold text-white text-sm flex items-center gap-1.5">
                                            {{ $user->name }}
                                            <span class="text-[10px] px-2 py-0.2 rounded bg-emerald-500/20 text-emerald-400 font-mono">{{ $user->referral_code }}</span>
                                        </div>
                                        <div class="text-[11px] text-neutral-300 font-mono">{{ $user->email }} • {{ $user->mobile ?? 'No Mobile' }}</div>

                                        <!-- Referral Link Copy Button -->
                                        <div class="flex items-center gap-1.5 pt-1">
                                            @php
                                                $refUrl = url('/user/register?sponsor='.$user->referral_code);
                                            @endphp
                                            <button type="button" onclick="copyLink('{{ $refUrl }}', 'Referral Link')" class="px-2 py-1 rounded bg-emerald-500/10 hover:bg-emerald-500/30 text-emerald-400 text-[10px] font-bold border border-emerald-500/30 transition flex items-center gap-1">
                                                <i data-lucide="copy" class="w-3 h-3"></i> Copy Link
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Column 2: Sponsor Details -->
                            <td class="p-3.5">
                                @if($user->sponsor)
                                    <span class="font-bold text-white block flex items-center gap-1">
                                        <i data-lucide="user-check" class="w-3.5 h-3.5 text-emerald-400"></i> {{ $user->sponsor->name }}
                                    </span>
                                    <span class="text-[11px] text-emerald-400 font-mono block">{{ $user->sponsor_code }}</span>
                                    <span class="text-[10px] text-neutral-400 block">{{ $user->sponsor->email }}</span>
                                @elseif($user->sponsor_code)
                                    <span class="font-bold text-emerald-400 font-mono block">{{ $user->sponsor_code }}</span>
                                @else
                                    <span class="text-neutral-500 italic">No Sponsor (Root)</span>
                                @endif
                            </td>

                            <!-- Column 3: Registration & Activation -->
                            <td class="p-3.5 space-y-1.5">
                                <div class="text-[11px]">
                                    <span class="text-neutral-400">Reg:</span>
                                    <strong class="text-white">{{ $user->created_at ? $user->created_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                                </div>
                                <div class="text-[11px]">
                                    <span class="text-neutral-400">Act:</span>
                                    <strong class="text-emerald-400">{{ $user->activated_at ? $user->activated_at->format('d M Y, h:i A') : 'Not Activated' }}</strong>
                                </div>
                                <div class="pt-0.5">
                                    @if($user->is_subscription_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-black border border-emerald-500/50 shadow-[0_0_15px_rgba(16,185,129,0.35)]">
                                            <span class="relative flex h-2 w-2">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                                            </span>
                                            ₹3,000 PACKAGE ACTIVE
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-500/20 text-rose-300 text-[10px] font-black border border-rose-500/50 shadow-[0_0_15px_rgba(244,63,94,0.35)]">
                                            <span class="relative flex h-2 w-2">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-500 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                            </span>
                                            NO PACKAGE TAKEN
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Column 4: Fund Wallet & Earning Wallet -->
                            <td class="p-3.5 space-y-1">
                                <div>
                                    <span class="text-[10px] text-neutral-400 uppercase block">Fund Wallet</span>
                                    <strong class="text-sm font-mono font-bold text-white">₹{{ number_format($user->deposit_wallet, 2) }}</strong>
                                </div>
                                <div>
                                    <span class="text-[10px] text-neutral-400 uppercase block">Earning Wallet</span>
                                    <strong class="text-sm font-mono font-bold text-emerald-400">₹{{ number_format($user->earning_wallet, 2) }}</strong>
                                </div>
                            </td>

                            <!-- Column 5: Status -->
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase {{ $user->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40' }}">
                                    {{ $user->status }}
                                </span>
                            </td>

                            <!-- Column 6: Direct Actions -->
                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Add Fund Glowing Golden Pill Button -->
                                    <button type="button" 
                                        onclick="openAddFundModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->referral_code }}')"
                                        title="Add / Deduct Fund ({{ $user->name }})"
                                        aria-label="Add / Deduct Fund"
                                        class="px-3.5 py-1.5 rounded-full bg-gradient-to-r from-amber-300 via-yellow-400 to-amber-500 text-black font-extrabold text-xs flex items-center gap-1.5 shadow-[0_0_15px_rgba(245,158,11,0.5)] hover:scale-105 transition-all duration-200 shrink-0 cursor-pointer">
                                        <i data-lucide="plus-circle" class="w-4 h-4 text-black"></i>
                                        <span>Add Fund</span>
                                    </button>

                                    <!-- Login as User (External Link Icon) -->
                                    <a href="{{ route('admin.users.impersonate', $user) }}" 
                                        title="Login as User ({{ $user->name }})"
                                        aria-label="Login as User"
                                        class="w-9 h-8 rounded-2xl bg-[#02180f] border border-amber-500/60 text-amber-300 hover:bg-amber-400 hover:text-black hover:border-amber-400 font-bold transition-all duration-200 flex items-center justify-center shadow-[0_0_10px_rgba(245,158,11,0.15)] hover:scale-105 shrink-0">
                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>

                                    <!-- Direct Members List (Users Icon + Count) -->
                                    <a href="{{ route('admin.users.show', $user) }}" 
                                        title="Direct Members List ({{ $user->directMembers->count() }})"
                                        aria-label="Direct Members List"
                                        class="px-2.5 py-1.5 rounded-2xl bg-[#02180f] border border-amber-500/60 text-amber-300 hover:bg-amber-400 hover:text-black hover:border-amber-400 font-bold font-mono text-xs transition-all duration-200 flex items-center gap-1 shadow-[0_0_10px_rgba(245,158,11,0.15)] hover:scale-105 shrink-0">
                                        <i data-lucide="users" class="w-4 h-4"></i>
                                        <span>{{ $user->directMembers->count() }}</span>
                                    </a>

                                    <!-- Network Tree View (Tree / Git Fork Icon) -->
                                    <a href="{{ route('admin.network.tree', ['search' => $user->referral_code]) }}" 
                                        title="View Network Tree ({{ $user->referral_code }})"
                                        aria-label="View Network Tree"
                                        class="w-9 h-8 rounded-2xl bg-[#02180f] border border-amber-500/60 text-amber-300 hover:bg-amber-400 hover:text-black hover:border-amber-400 font-bold transition-all duration-200 flex items-center justify-center shadow-[0_0_10px_rgba(245,158,11,0.15)] hover:scale-105 shrink-0">
                                        <i data-lucide="git-fork" class="w-4 h-4"></i>
                                    </a>

                                    <!-- View Member Details (Eye Icon) -->
                                    <a href="{{ route('admin.users.show', $user) }}" 
                                        title="View Member Profile ({{ $user->name }})"
                                        aria-label="View Member Profile"
                                        class="w-9 h-8 rounded-2xl bg-[#02180f] border border-amber-500/60 text-amber-300 hover:bg-amber-400 hover:text-black hover:border-amber-400 font-bold transition-all duration-200 flex items-center justify-center shadow-[0_0_10px_rgba(245,158,11,0.15)] hover:scale-105 shrink-0">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>

                                    <!-- Edit Member Profile (Pencil Icon) -->
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                        title="Edit Member Profile ({{ $user->name }})"
                                        aria-label="Edit Member Profile"
                                        class="w-9 h-8 rounded-2xl bg-[#02180f] border border-amber-500/60 text-amber-300 hover:bg-amber-400 hover:text-black hover:border-amber-400 font-bold transition-all duration-200 flex items-center justify-center shadow-[0_0_10px_rgba(245,158,11,0.15)] hover:scale-105 shrink-0">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-neutral-400">No members found matching the selected criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-emerald-500/20">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Admin Add / Deduct Fund Modal -->
<div id="addFundModal" class="fixed inset-0 z-50 hidden bg-black/85 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="w-full max-w-md p-6 rounded-3xl border-2 border-emerald-500/60 shadow-[0_0_50px_rgba(0,0,0,0.95)] space-y-4 relative text-white" style="background-color: #042718 !important; opacity: 1 !important;">
        <button type="button" onclick="closeAddFundModal()" class="absolute top-4 right-4 text-neutral-400 hover:text-white transition">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <h3 class="text-lg font-black text-white uppercase tracking-tight flex items-center gap-2">
            <i data-lucide="wallet" class="w-5 h-5 text-emerald-400"></i> Add / Deduct Wallet Fund
        </h3>

        <form id="addFundForm" action="" method="POST" class="space-y-4">
            @csrf
            <div>
                <span class="block text-xs font-bold text-neutral-400 uppercase">Target Member:</span>
                <strong id="modalUserName" class="text-sm font-bold text-emerald-400 block"></strong>
            </div>

            <div>
                <label class="block text-xs font-bold text-emerald-400 uppercase mb-1">Select Wallet:</label>
                <select name="wallet_type" required class="w-full px-4 py-2.5 rounded-xl border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400" style="background-color: #01140c !important;">
                    <option value="deposit_wallet" class="bg-[#042718] text-white">Fund Wallet (Deposit Wallet)</option>
                    <option value="earning_wallet" class="bg-[#042718] text-white">Earning Wallet</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-emerald-400 uppercase mb-1">Select Action:</label>
                <select name="action" required class="w-full px-4 py-2.5 rounded-xl border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400" style="background-color: #01140c !important;">
                    <option value="add" class="bg-[#042718] text-white">Add Fund (+ Credit)</option>
                    <option value="deduct" class="bg-[#042718] text-white">Deduct Fund (- Debit)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-emerald-400 uppercase mb-1">Amount (₹):</label>
                <input type="number" name="amount" min="0.01" step="0.01" required placeholder="Enter amount..." class="w-full px-4 py-2.5 rounded-xl border border-emerald-500/40 text-white font-mono font-bold text-sm focus:outline-none focus:border-emerald-400" style="background-color: #01140c !important;">
            </div>

            <div>
                <label class="block text-xs font-bold text-emerald-400 uppercase mb-1">Remark / Reason:</label>
                <input type="text" name="remark" placeholder="e.g. Deposit Credit / Manual Transfer..." class="w-full px-4 py-2.5 rounded-xl border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400" style="background-color: #01140c !important;">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeAddFundModal()" class="flex-1 py-2.5 rounded-xl bg-neutral-800 text-neutral-300 font-bold text-xs hover:bg-neutral-700 transition">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-emerald-500 text-black font-black text-xs uppercase hover:bg-emerald-400 transition shadow-lg">Confirm Action</button>
            </div>
        </form>
    </div>
</div>

<script>
function copyLink(url, label) {
    navigator.clipboard.writeText(url).then(function() {
        alert(label + ' copied to clipboard!\n' + url);
    }).catch(function() {
        prompt('Copy ' + label + ':', url);
    });
}

function openAddFundModal(userId, name, code) {
    document.getElementById('modalUserName').innerText = name + ' (' + code + ')';
    document.getElementById('addFundForm').action = '/admin/users/' + userId + '/add-fund';
    document.getElementById('addFundModal').classList.remove('hidden');
}

function closeAddFundModal() {
    document.getElementById('addFundModal').classList.add('hidden');
}
</script>
@endsection
