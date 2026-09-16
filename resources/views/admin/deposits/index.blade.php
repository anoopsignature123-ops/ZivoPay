@extends('admin.layouts.app')

@section('title', 'Manage User Add Fund Requests - Admin ZIVO PAY')

@section('content')
<div class="w-full space-y-6 font-sans">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.3)] flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] uppercase border border-emerald-500/40">DEPOSITS</span>
                <span class="text-xs text-emerald-400 font-black tracking-[3px] uppercase">ZIVO PAY GATEWAY</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white font-heading">USER ADD FUND MANAGEMENT</h1>
            <p class="text-xs text-neutral-300 mt-1">Audit, verify UTR/proof, and approve or reject user wallet deposit requests.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="px-5 py-3 rounded-2xl bg-[#02180f] border border-emerald-500/40 text-right">
                <span class="text-[10px] uppercase tracking-wider text-emerald-400 font-bold block">Total Approved Fund Volume</span>
                <span class="text-xl sm:text-2xl font-black text-white font-mono">₹{{ number_format($stats['total_approved'], 2) }}</span>
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

    <!-- Filter Bar -->
    <div class="bg-[#042718] p-5 rounded-3xl border border-emerald-500/30 shadow-xl">
        <form action="{{ route('admin.deposits.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Search User / Ref / UTR</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ref, UTR, Name, Code..." class="w-full px-3.5 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400" style="background-color: #01140c !important;">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Status</label>
                <select name="status" class="w-full px-3.5 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/30 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400" style="background-color: #01140c !important;">
                    <option value="" class="bg-[#042718]">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }} class="bg-[#042718]">Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }} class="bg-[#042718]">Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }} class="bg-[#042718]">Rejected</option>
                </select>
            </div>

            <div class="flex items-center gap-2 lg:col-span-2">
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs uppercase tracking-wider transition flex items-center justify-center gap-1.5 shadow-md">
                    <i data-lucide="search" class="w-4 h-4"></i> Search & Filter
                </button>
                <a href="{{ route('admin.deposits.index') }}" class="px-4 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/30 text-neutral-400 hover:text-white text-xs font-bold transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Deposits Table -->
    <div class="bg-[#042718] rounded-3xl border border-emerald-500/30 overflow-hidden shadow-2xl p-6 space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-[#02180f] text-emerald-400 font-extrabold uppercase tracking-wider border-b border-emerald-500/30">
                    <tr>
                        <th class="p-3.5">Ref ID & Date</th>
                        <th class="p-3.5">Member Details</th>
                        <th class="p-3.5">Method & UTR / Hash</th>
                        <th class="p-3.5">Amount (₹)</th>
                        <th class="p-3.5">Proof File</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5">Admin Remark / Note</th>
                        <th class="p-3.5 text-center">DIRECT ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/10 text-neutral-200 font-medium">
                    @forelse($deposits as $dep)
                        <tr class="hover:bg-emerald-500/5 transition">
                            <td class="p-3.5">
                                <span class="font-mono font-bold text-white text-sm block">{{ $dep->deposit_ref }}</span>
                                <span class="text-[10px] text-neutral-400 font-mono block">{{ $dep->created_at->format('d M Y, h:i A') }}</span>
                            </td>
                            <td class="p-3.5">
                                @if($dep->user)
                                    <div>
                                        <p class="font-bold text-white text-sm">{{ $dep->user->name }}</p>
                                        <p class="text-[11px] text-neutral-300 font-mono">{{ $dep->user->email }}</p>
                                        <span class="inline-block mt-0.5 px-2 py-0.2 rounded bg-emerald-500/20 text-emerald-400 font-mono text-[10px]">
                                            Code: {{ $dep->user->referral_code }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-neutral-500 italic">User Deleted</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 text-[10px] font-black uppercase border border-emerald-500/30 block w-max">
                                    {{ $dep->payment_method }}
                                </span>
                                <span class="text-xs font-mono font-bold text-emerald-400 mt-1 block truncate max-w-[150px]" title="{{ $dep->trx_hash }}">
                                    UTR: {{ $dep->trx_hash ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="p-3.5 font-mono font-bold text-emerald-400 text-sm">
                                ₹{{ number_format($dep->amount, 2) }}
                            </td>
                            <td class="p-3.5">
                                @if($dep->proof_file)
                                    <a href="{{ asset('storage/' . $dep->proof_file) }}" target="_blank" class="px-2.5 py-1 rounded-xl bg-teal-500/20 border border-teal-500/40 text-teal-300 text-[10px] font-bold hover:bg-teal-500 hover:text-black transition inline-flex items-center gap-1">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i> View Proof
                                    </a>
                                @else
                                    <span class="text-neutral-500 text-[10px] italic">No Proof Uploaded</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase 
                                    {{ $dep->status === 'approved' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : ($dep->status === 'pending' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40') }}">
                                    {{ $dep->status }}
                                </span>
                            </td>
                            <td class="p-3.5 text-neutral-300 text-xs max-w-xs truncate" title="{{ $dep->admin_remark ?? '-' }}">
                                {{ $dep->admin_remark ?? '-' }}
                            </td>
                            <td class="p-3.5 text-center">
                                @if($dep->status === 'pending')
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Approve Form -->
                                        <form action="{{ route('admin.deposits.approve', $dep) }}" method="POST" class="inline" onsubmit="return confirm('Approve deposit #{{ $dep->deposit_ref }} and credit ₹{{ number_format($dep->final_amount, 2) }} to {{ addslashes($dep->user->name ?? 'User') }}\'s Fund Wallet?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-full bg-gradient-to-r from-amber-300 via-yellow-400 to-amber-500 text-black font-extrabold text-xs flex items-center gap-1 shadow-[0_0_15px_rgba(245,158,11,0.5)] hover:scale-105 transition shrink-0 cursor-pointer">
                                                <i data-lucide="check-circle" class="w-4 h-4 text-black"></i>
                                                <span>Approve</span>
                                            </button>
                                        </form>

                                        <!-- Reject Form -->
                                        <form action="{{ route('admin.deposits.reject', $dep) }}" method="POST" class="inline" onsubmit="return confirm('Reject deposit #{{ $dep->deposit_ref }}?');">
                                            @csrf
                                            <button type="submit" class="w-9 h-8 rounded-2xl bg-[#02180f] border border-rose-500/60 text-rose-400 hover:bg-rose-500 hover:text-white font-bold transition flex items-center justify-center shadow-sm hover:scale-105 shrink-0 cursor-pointer">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-[10px] text-neutral-400 font-mono italic">Processed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-neutral-400 font-bold">No Add Fund requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-emerald-500/20">
            {{ $deposits->links() }}
        </div>
    </div>
</div>
@endsection
