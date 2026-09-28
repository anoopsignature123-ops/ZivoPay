@extends('admin.layouts.app')

@section('title', 'Audit KYC Application - ' . $kyc->full_name)

@section('content')
    <div class="max-w-6xl mx-auto space-y-5 font-sans relative">

        <!-- Top Header Banner -->
        <div class="p-5 sm:p-6 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-[0_0_30px_rgba(16,185,129,0.25)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.kyc.index') }}" class="text-xs text-emerald-400 font-extrabold inline-flex items-center gap-1 mb-1 hover:underline">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to KYC List
                </a>
                <h1 class="text-xl sm:text-2xl font-black text-white font-heading uppercase">KYC APPLICATION AUDIT</h1>
                <p class="text-xs text-neutral-300 mt-0.5">Applicant: <strong class="text-white">{{ $kyc->full_name }}</strong> ({{ $kyc->user->referral_code }})</p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                @if($kyc->status === 'approved')
                    <span class="px-4 py-2 rounded-2xl bg-emerald-500/20 border-2 border-emerald-500 text-emerald-400 font-extrabold text-xs uppercase flex items-center gap-1.5 shadow-md">
                        <i data-lucide="check-circle" class="w-4 h-4"></i> KYC Approved
                    </span>
                @elseif($kyc->status === 'pending')
                    <span class="px-4 py-2 rounded-2xl bg-amber-500/20 border-2 border-amber-500 text-amber-300 font-extrabold text-xs uppercase flex items-center gap-1.5 shadow-md">
                        <i data-lucide="clock" class="w-4 h-4"></i> Pending Admin Action
                    </span>
                @else
                    <span class="px-4 py-2 rounded-2xl bg-rose-500/20 border-2 border-rose-500 text-rose-300 font-extrabold text-xs uppercase flex items-center gap-1.5 shadow-md">
                        <i data-lucide="x-circle" class="w-4 h-4"></i> KYC Rejected
                    </span>
                @endif
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

        <!-- VERIFICATION DECISION PANEL (Compact Row Card Container) -->
        <div class="p-5 sm:p-6 rounded-3xl bg-[#042718] border-2 border-emerald-500/50 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-emerald-500/20 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                    </div>
                    <h2 class="text-xs font-black text-white uppercase tracking-wider font-heading">Verification Decision Actions</h2>
                </div>
                @if($kyc->reviewed_at)
                    <span class="text-[11px] text-neutral-400 font-mono">Last Reviewed: {{ $kyc->reviewed_at->format('d M Y, h:i A') }}</span>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-start">
                <!-- Left Option: Approve Action -->
                <div class="p-4 rounded-2xl bg-[#01140c] border border-emerald-500/30 space-y-3 flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-black text-[10px] uppercase border border-emerald-500/30">Option A</span>
                            <h3 class="text-xs font-extrabold text-white uppercase">Approve KYC Verification</h3>
                        </div>
                        <p class="text-[11px] text-neutral-400">Approving will mark this member's KYC status as verified and unlock instant 24x7 withdrawal payouts.</p>
                    </div>

                    <form action="{{ route('admin.kyc.approve', $kyc->id) }}" method="POST" class="pt-2">
                        @csrf
                        <button type="submit" onclick="return confirm('Are you sure you want to APPROVE this user KYC application?');"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-black font-black text-xs uppercase tracking-wider shadow-md hover:scale-[1.02] active:scale-95 transition flex items-center gap-2">
                            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                            <span>Approve KYC Application</span>
                        </button>
                    </form>
                </div>

                <!-- Right Option: Reject Action -->
                <div class="p-4 rounded-2xl bg-[#01140c] border border-rose-500/30 space-y-3">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2 py-0.5 rounded bg-rose-500/20 text-rose-400 font-black text-[10px] uppercase border border-rose-500/30">Option B</span>
                        <h3 class="text-xs font-extrabold text-white uppercase">Reject KYC Application</h3>
                    </div>

                    <form action="{{ route('admin.kyc.reject', $kyc->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-bold text-rose-300 uppercase tracking-wider mb-1">Rejection Reason *</label>
                            <textarea name="rejection_reason" required rows="2"
                                placeholder="Explain reason (e.g. Blurred document photo, name mismatch...)"
                                class="w-full px-3 py-2 rounded-xl bg-neutral-900 border border-rose-500/40 text-white text-xs focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-400 transition">{{ old('rejection_reason', $kyc->rejection_reason) }}</textarea>
                        </div>

                        <button type="submit" onclick="return confirm('Are you sure you want to REJECT this KYC application?');"
                            class="px-5 py-2 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 border border-rose-500/50 text-rose-300 font-bold text-xs transition flex items-center gap-1.5">
                            <i data-lucide="x-circle" class="w-4 h-4 text-rose-400"></i>
                            <span>Reject KYC Application</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- MEMBER DETAILS & DOCUMENTS GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- 1. Member Account Profile Card -->
            <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-xl space-y-3">
                <div class="flex items-center gap-2 border-b border-emerald-500/20 pb-2.5">
                    <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                    </div>
                    <h2 class="text-xs font-black text-emerald-400 uppercase tracking-wider font-heading">Member Profile</h2>
                </div>

                <div class="grid grid-cols-2 gap-2.5 text-xs">
                    <div class="p-2.5 rounded-xl bg-[#01140c] border border-emerald-500/20">
                        <span class="text-neutral-400 block text-[10px] uppercase font-bold">Member Name</span>
                        <strong class="text-white font-bold text-xs">{{ $kyc->user->name }}</strong>
                    </div>
                    <div class="p-2.5 rounded-xl bg-[#01140c] border border-emerald-500/20">
                        <span class="text-neutral-400 block text-[10px] uppercase font-bold">Referral ID</span>
                        <strong class="text-teal-300 font-mono font-bold text-xs">{{ $kyc->user->referral_code }}</strong>
                    </div>
                    <div class="p-2.5 rounded-xl bg-[#01140c] border border-emerald-500/20">
                        <span class="text-neutral-400 block text-[10px] uppercase font-bold">Mobile Number</span>
                        <strong class="text-white font-mono text-xs">{{ $kyc->user->mobile ?: 'N/A' }}</strong>
                    </div>
                    <div class="p-2.5 rounded-xl bg-[#01140c] border border-emerald-500/20">
                        <span class="text-neutral-400 block text-[10px] uppercase font-bold">Email Address</span>
                        <strong class="text-white font-mono text-xs truncate block">{{ $kyc->user->email }}</strong>
                    </div>
                    <div class="p-2.5 rounded-xl bg-[#01140c] border border-emerald-500/20">
                        <span class="text-neutral-400 block text-[10px] uppercase font-bold">Package Status</span>
                        <span class="{{ $kyc->user->is_subscription_active ? 'text-emerald-400' : 'text-amber-400' }} font-bold text-xs">
                            {{ $kyc->user->is_subscription_active ? 'Active (₹3,000)' : 'Unsubscribed' }}
                        </span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-[#01140c] border border-emerald-500/20">
                        <span class="text-neutral-400 block text-[10px] uppercase font-bold">Submitted Date</span>
                        <span class="text-neutral-300 font-mono text-xs">{{ $kyc->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Document & Banking Card -->
            <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-xl space-y-3">
                <div class="flex items-center gap-2 border-b border-emerald-500/20 pb-2.5">
                    <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                    </div>
                    <h2 class="text-xs font-black text-emerald-400 uppercase tracking-wider font-heading">Document & Banking Credentials</h2>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="p-2.5 rounded-xl bg-[#01140c] border border-emerald-500/20 flex justify-between items-center">
                        <span class="text-neutral-400 text-[10px] uppercase font-bold">Document Type & No:</span>
                        <strong class="text-white uppercase font-bold">{{ $kyc->document_type }} - <span class="text-emerald-300 font-mono">{{ $kyc->document_number }}</span></strong>
                    </div>
                    <div class="p-2.5 rounded-xl bg-[#01140c] border border-emerald-500/20 flex justify-between items-center">
                        <span class="text-neutral-400 text-[10px] uppercase font-bold">Name on Document:</span>
                        <strong class="text-white font-bold">{{ $kyc->full_name }}</strong>
                    </div>
                    <div class="p-2.5 rounded-xl bg-[#01140c] border border-emerald-500/20 flex justify-between items-center">
                        <span class="text-neutral-400 text-[10px] uppercase font-bold">Bank Name & A/C:</span>
                        <strong class="text-white font-bold">{{ $kyc->bank_name ?: 'N/A' }} (<span class="text-emerald-300 font-mono">{{ $kyc->account_number ?: 'N/A' }}</span>)</strong>
                    </div>
                    <div class="p-2.5 rounded-xl bg-[#01140c] border border-emerald-500/20 flex justify-between items-center">
                        <span class="text-neutral-400 text-[10px] uppercase font-bold">IFSC Code & Holder:</span>
                        <strong class="text-white font-bold"><span class="font-mono uppercase text-emerald-300">{{ $kyc->ifsc_code ?: 'N/A' }}</span> ({{ $kyc->account_holder ?: 'N/A' }})</strong>
                    </div>
                    <div class="p-2.5 rounded-xl bg-[#01140c] border border-emerald-500/20 flex justify-between items-center">
                        <span class="text-neutral-400 text-[10px] uppercase font-bold">UPI ID:</span>
                        <strong class="text-teal-300 font-mono text-[11px]">{{ $kyc->upi_id ?: 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Document Proof Upload Copies -->
        <div class="p-5 rounded-2xl bg-[#042718] border border-emerald-500/30 shadow-xl space-y-3">
            <div class="flex items-center gap-2 border-b border-emerald-500/20 pb-2.5">
                <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                    <i data-lucide="image" class="w-3.5 h-3.5"></i>
                </div>
                <h2 class="text-xs font-black text-emerald-400 uppercase tracking-wider font-heading">Uploaded Document Proof Copies</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Front Copy -->
                <div class="p-3 rounded-xl bg-[#01140c] border border-emerald-500/30 space-y-2">
                    <span class="text-[10px] font-bold text-emerald-300 uppercase block">Front Copy of Document</span>
                    @if($kyc->front_image)
                        <div class="w-full h-48 rounded-lg overflow-hidden bg-black/80 border border-emerald-500/40 relative group">
                            <img src="{{ asset($kyc->front_image) }}" alt="Front ID Copy" class="w-full h-full object-contain">
                            <a href="{{ asset($kyc->front_image) }}" target="_blank"
                                class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition gap-1">
                                <i data-lucide="external-link" class="w-4 h-4"></i> View Original Document
                            </a>
                        </div>
                    @else
                        <p class="text-xs text-neutral-400 p-4 text-center">No front image uploaded.</p>
                    @endif
                </div>

                <!-- Back Copy -->
                <div class="p-3 rounded-xl bg-[#01140c] border border-emerald-500/30 space-y-2">
                    <span class="text-[10px] font-bold text-emerald-300 uppercase block">Back Copy of Document / Address Proof</span>
                    @if($kyc->back_image)
                        <div class="w-full h-48 rounded-lg overflow-hidden bg-black/80 border border-emerald-500/40 relative group">
                            <img src="{{ asset($kyc->back_image) }}" alt="Back ID Copy" class="w-full h-full object-contain">
                            <a href="{{ asset($kyc->back_image) }}" target="_blank"
                                class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition gap-1">
                                <i data-lucide="external-link" class="w-4 h-4"></i> View Original Document
                            </a>
                        </div>
                    @else
                        <p class="text-xs text-neutral-400 p-4 text-center">No back image copy uploaded.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection
