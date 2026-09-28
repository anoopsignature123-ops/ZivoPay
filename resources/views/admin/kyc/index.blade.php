@extends('admin.layouts.app')

@section('title', 'KYC Management & Document Verification')

@section('content')
    <style>
        .admin-card-glow {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.95) 0%, rgba(3, 28, 18, 0.95) 100%) !important;
            border: 2px solid rgba(16, 185, 129, 0.4) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.8), 0 0 20px rgba(16, 185, 129, 0.15) !important;
            transition: all 0.3s ease !important;
        }

        .admin-card-glow:hover {
            border-color: rgba(16, 185, 129, 0.8) !important;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.9), 0 0 30px rgba(16, 185, 129, 0.25) !important;
            transform: translateY(-2px);
        }
    </style>

    <div class="w-full space-y-6 font-sans relative">

        <!-- Ambient Emerald Radial Glow Decorator -->
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-[900px] h-[450px] bg-emerald-500/10 blur-[130px] pointer-events-none rounded-full"></div>

        <!-- Top Header Banner -->
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 p-6 sm:p-8 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl relative z-10">
            <div>
                <div class="text-[11px] font-black text-emerald-400 uppercase tracking-widest mb-1 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    ZIVO PAY CONTROL CENTER • KYC VERIFICATION
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white font-heading uppercase tracking-tight">USER KYC MANAGEMENT</h1>
                <p class="text-xs text-neutral-300 mt-1">Audit, verify, approve or reject member identity documents & banking details.</p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <div class="px-5 py-3 rounded-2xl bg-amber-500/20 border border-amber-500/50 text-amber-300 text-xs font-bold font-mono flex items-center gap-2 shadow-xl">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-400"></i>
                    <span>Pending KYCs: <strong class="text-amber-300 font-black text-base">{{ $stats['total_pending'] }}</strong></span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-xs font-bold flex items-center gap-2 relative z-10">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- STATS ROW: 4 Cards in 1 Single Row matching Admin Dashboard -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 relative z-10">

            <!-- 1. Pending Reviews -->
            <div class="p-5 rounded-3xl admin-card-glow space-y-2">
                <div class="flex items-center justify-between text-neutral-400 text-xs font-extrabold uppercase">
                    <span>PENDING REVIEWS</span>
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center border border-amber-500/30">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-amber-400 font-mono">{{ number_format($stats['total_pending']) }}</div>
                <p class="text-[11px] text-neutral-400">Awaiting Admin Action & Verification</p>
            </div>

            <!-- 2. Approved KYCs -->
            <div class="p-5 rounded-3xl admin-card-glow space-y-2">
                <div class="flex items-center justify-between text-neutral-400 text-xs font-extrabold uppercase">
                    <span>APPROVED KYCS</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-emerald-400 font-mono">{{ number_format($stats['total_approved']) }}</div>
                <p class="text-[11px] text-emerald-400 font-semibold">Verified & Active for 24x7 Payouts</p>
            </div>

            <!-- 3. Rejected KYCs -->
            <div class="p-5 rounded-3xl admin-card-glow space-y-2">
                <div class="flex items-center justify-between text-neutral-400 text-xs font-extrabold uppercase">
                    <span>REJECTED KYCS</span>
                    <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center border border-rose-500/30">
                        <i data-lucide="x-circle" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-rose-400 font-mono">{{ number_format($stats['total_rejected']) }}</div>
                <p class="text-[11px] text-neutral-400">Declined / Incomplete Submissions</p>
            </div>

            <!-- 4. Total Submissions -->
            <div class="p-5 rounded-3xl admin-card-glow space-y-2">
                <div class="flex items-center justify-between text-neutral-400 text-xs font-extrabold uppercase">
                    <span>TOTAL SUBMISSIONS</span>
                    <div class="w-9 h-9 rounded-xl bg-teal-500/20 text-teal-300 flex items-center justify-center border border-teal-500/30">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-white font-mono">{{ number_format($stats['total_count']) }}</div>
                <p class="text-[11px] text-neutral-400">All Time KYC Applications Received</p>
            </div>
        </div>

        <!-- Filter Form Bar (Exact horizontal flex row matching reports) -->
        <div class="bg-[#042718] p-4 sm:p-5 rounded-3xl border border-emerald-500/30 shadow-xl relative z-10">
            <form action="{{ route('admin.kyc.index') }}" method="GET" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Search Member / Doc / Bank</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Referral Code, Doc No, Account No..."
                            class="w-full pl-9 pr-3.5 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
                        <i data-lucide="search" class="w-4 h-4 text-emerald-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>
                </div>

                <div class="w-36 sm:w-40 shrink-0">
                    <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400 cursor-pointer">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="w-36 sm:w-40 shrink-0">
                    <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-3 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
                </div>

                <div class="w-36 sm:w-40 shrink-0">
                    <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-3 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400">
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs uppercase tracking-wider transition flex items-center justify-center gap-1.5 shadow-md">
                        <i data-lucide="filter" class="w-4 h-4"></i> Filter
                    </button>
                    <a href="{{ route('admin.kyc.index') }}" class="px-3.5 py-2.5 rounded-xl bg-[#01140c] border border-emerald-500/40 text-neutral-400 hover:text-white text-xs font-bold transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- KYC Submissions Table -->
        <div class="rounded-3xl bg-[#042718] border border-emerald-500/30 overflow-hidden shadow-2xl relative z-10">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-neutral-300">
                    <thead class="bg-[#01140c] text-emerald-400 font-extrabold uppercase tracking-wider border-b border-emerald-500/20 text-[10px]">
                        <tr>
                            <th class="px-4 py-3.5">Member Details</th>
                            <th class="px-4 py-3.5">Document Info</th>
                            <th class="px-4 py-3.5">Bank Payout Info</th>
                            <th class="px-4 py-3.5">Submitted On</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-4 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-500/10">
                        @forelse($kycs as $kyc)
                            <tr class="hover:bg-emerald-500/5 transition">
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 font-bold flex items-center justify-center shrink-0 border border-emerald-500/30">
                                            {{ strtoupper(substr($kyc->user->name ?? 'M', 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-white text-xs">{{ $kyc->user->name ?? 'N/A' }}</p>
                                            <p class="text-[10px] text-teal-400 font-mono">{{ $kyc->user->referral_code ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="font-bold text-white uppercase">{{ $kyc->document_type }}</p>
                                    <p class="text-[11px] font-mono text-emerald-300">{{ $kyc->document_number }}</p>
                                    <p class="text-[10px] text-neutral-400">Name: {{ $kyc->full_name }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="font-bold text-white">{{ $kyc->bank_name ?: 'N/A' }}</p>
                                    <p class="text-[11px] font-mono text-emerald-300">A/C: {{ $kyc->account_number ?: 'N/A' }}</p>
                                    <p class="text-[10px] font-mono text-neutral-400">IFSC: {{ $kyc->ifsc_code ?: 'N/A' }}</p>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap font-mono text-[11px]">
                                    {{ $kyc->created_at->format('d M Y, h:i A') }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if($kyc->status === 'approved')
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 font-bold text-[10px] uppercase">
                                            ✓ Approved
                                        </span>
                                    @elseif($kyc->status === 'pending')
                                        <span class="px-2.5 py-1 rounded-full bg-amber-500/20 border border-amber-500/50 text-amber-300 font-bold text-[10px] uppercase">
                                            ⏳ Pending
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full bg-rose-500/20 border border-rose-500/50 text-rose-300 font-bold text-[10px] uppercase">
                                            ✕ Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.kyc.show', $kyc->id) }}"
                                        class="px-3 py-1.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 border border-emerald-500/40 text-emerald-300 text-xs font-bold transition inline-flex items-center gap-1">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i> Audit Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-neutral-400">
                                    No KYC verification records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($kycs->hasPages())
                <div class="p-4 border-t border-emerald-500/20">
                    {{ $kycs->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
