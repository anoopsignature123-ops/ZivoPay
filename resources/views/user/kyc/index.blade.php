@extends('user.layouts.app')

@section('title', 'KYC Document Verification & Payout Details')

@section('content')
    <div class="max-w-4xl mx-auto space-y-4 font-sans">

        <!-- Top Header Banner -->
        <div class="p-5 rounded-3xl bg-[#042718] border-2 border-emerald-500/60 shadow-[0_0_25px_rgba(16,185,129,0.2)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] uppercase tracking-wider border border-emerald-500/40">
                        VERIFICATION PORTAL
                    </span>
                    <span class="text-[11px] text-emerald-400 font-black tracking-[2px] uppercase">ZIVO PAY SECURITY</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-white uppercase tracking-tight font-heading">KYC DOCUMENT VERIFICATION</h1>
                <p class="text-xs text-neutral-300 mt-0.5">Submit identity documents and bank payout credentials for 24x7 withdrawal access.</p>
            </div>

            <div class="shrink-0">
                @if($user->kyc_status === 'approved')
                    <span class="px-4 py-2 rounded-2xl bg-emerald-500/20 border-2 border-emerald-500 text-emerald-400 text-xs font-bold font-mono inline-flex items-center gap-1.5 shadow-md">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i> KYC VERIFIED
                    </span>
                @elseif($user->kyc_status === 'pending')
                    <span class="px-4 py-2 rounded-2xl bg-amber-500/20 border-2 border-amber-500 text-amber-300 text-xs font-bold font-mono inline-flex items-center gap-1.5 shadow-md">
                        <i data-lucide="clock" class="w-4 h-4 text-amber-400"></i> UNDER REVIEW
                    </span>
                @elseif($user->kyc_status === 'rejected')
                    <span class="px-4 py-2 rounded-2xl bg-rose-500/20 border-2 border-rose-500 text-rose-300 text-xs font-bold font-mono inline-flex items-center gap-1.5 shadow-md">
                        <i data-lucide="x-circle" class="w-4 h-4 text-rose-400"></i> REJECTED
                    </span>
                @else
                    <span class="px-4 py-2 rounded-2xl bg-neutral-900 border-2 border-amber-500/50 text-amber-300 text-xs font-bold font-mono inline-flex items-center gap-1.5 shadow-md">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-400"></i> UNVERIFIED
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

        <!-- Status Alert Context Cards -->
        @if($user->kyc_status === 'approved')
            <div class="p-4 rounded-2xl bg-emerald-950/40 border border-emerald-500/50 text-emerald-300 text-xs space-y-1 shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/40">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-white text-sm font-heading">KYC Verification Completed</h3>
                        <p class="text-[11px] text-emerald-200/90">Your account is fully verified. 24x7 Payout Withdrawals are enabled.</p>
                    </div>
                </div>
            </div>
        @elseif($user->kyc_status === 'pending')
            <div class="p-4 rounded-2xl bg-amber-950/40 border border-amber-500/50 text-amber-200 text-xs space-y-1 shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 border border-amber-500/40">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-white text-sm font-heading">KYC Verification Under Review</h3>
                        <p class="text-[11px] text-amber-200/90">Your application and uploaded files are being verified by admin. Processing takes up to 24h.</p>
                    </div>
                </div>
            </div>
        @elseif($user->kyc_status === 'rejected')
            <div class="p-4 rounded-2xl bg-rose-950/40 border border-rose-500/50 text-rose-200 text-xs space-y-1 shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/40">
                        <i data-lucide="alert-octagon" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-white text-sm font-heading">Previous KYC Rejected</h3>
                        <p class="text-[11px] text-rose-300 font-mono mt-0.5"><strong>Reason:</strong> {{ $kyc->rejection_reason ?? 'Document details or images were incomplete.' }}</p>
                        <p class="text-[10px] text-rose-200/80">Please update your document information below and resubmit.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Compact KYC Form Card -->
        <div class="p-5 sm:p-6 rounded-3xl bg-[#042718] border border-emerald-500/30 shadow-2xl space-y-5">
            <form action="{{ route('user.kyc.store') }}" method="POST" enctype="multipart/form-data" id="kycForm" class="space-y-5">
                @csrf

                <!-- Section 1: Identity Information -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 border-b border-emerald-500/20 pb-2.5">
                        <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                            <i data-lucide="file-badge" class="w-3.5 h-3.5"></i>
                        </div>
                        <h2 class="text-xs font-black text-white uppercase tracking-wider font-heading">1. Personal Identity Documents</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        <!-- Document Type -->
                        <div>
                            <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Document Type *</label>
                            <select name="document_type" id="docTypeSelect" required {{ $user->kyc_status === 'pending' ? 'disabled' : '' }}
                                class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400 transition cursor-pointer">
                                <option value="aadhaar" {{ old('document_type', $kyc->document_type ?? '') === 'aadhaar' ? 'selected' : '' }}>Aadhaar Card</option>
                                <option value="pan" {{ old('document_type', $kyc->document_type ?? '') === 'pan' ? 'selected' : '' }}>PAN Card</option>
                                <option value="passport" {{ old('document_type', $kyc->document_type ?? '') === 'passport' ? 'selected' : '' }}>Passport</option>
                                <option value="voter_id" {{ old('document_type', $kyc->document_type ?? '') === 'voter_id' ? 'selected' : '' }}>Voter ID Card</option>
                            </select>
                        </div>

                        <!-- Full Name as per ID -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Full Name (As per Document) *</label>
                                <span id="fullNameValid" class="text-[10px] text-emerald-400 font-bold hidden">✓ Valid</span>
                            </div>
                            <input type="text" name="full_name" id="fullNameInput" required value="{{ old('full_name', $kyc->full_name ?? $user->name) }}" {{ $user->kyc_status === 'pending' ? 'disabled' : '' }}
                                placeholder="Enter full name as printed on ID"
                                class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400 transition">
                        </div>

                        <!-- Document Number -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider" id="docNumLabel">Document Number *</label>
                                <span id="docNumValid" class="text-[10px] text-emerald-400 font-bold hidden">✓ Format Valid</span>
                            </div>
                            <input type="text" name="document_number" id="docNumInput" required value="{{ old('document_number', $kyc->document_number ?? '') }}" {{ $user->kyc_status === 'pending' ? 'disabled' : '' }}
                                placeholder="e.g. 12-digit Aadhaar / 10-char PAN"
                                class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono font-bold focus:outline-none focus:border-emerald-400 transition">
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Date of Birth</label>
                            <input type="date" name="dob" value="{{ old('dob', isset($kyc->dob) ? $kyc->dob->format('Y-m-d') : '') }}" {{ $user->kyc_status === 'pending' ? 'disabled' : '' }}
                                class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400 transition">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Bank Account Details -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 border-b border-emerald-500/20 pb-2.5">
                        <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                            <i data-lucide="building-2" class="w-3.5 h-3.5"></i>
                        </div>
                        <h2 class="text-xs font-black text-white uppercase tracking-wider font-heading">2. Bank Account & Payout Details</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        <!-- Bank Name -->
                        <div>
                            <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Bank Name *</label>
                            <input type="text" name="bank_name" id="bankNameInput" required value="{{ old('bank_name', $kyc->bank_name ?? '') }}" {{ $user->kyc_status === 'pending' ? 'disabled' : '' }}
                                placeholder="e.g. State Bank of India, HDFC Bank"
                                class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400 transition">
                        </div>

                        <!-- Account Number -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Bank Account Number *</label>
                                <span id="accNumValid" class="text-[10px] text-emerald-400 font-bold hidden">✓ Valid</span>
                            </div>
                            <input type="text" name="account_number" id="accNumInput" required value="{{ old('account_number', $kyc->account_number ?? '') }}" {{ $user->kyc_status === 'pending' ? 'disabled' : '' }}
                                placeholder="Enter account number"
                                class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono font-bold focus:outline-none focus:border-emerald-400 transition">
                        </div>

                        <!-- IFSC Code -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Bank IFSC Code *</label>
                                <span id="ifscValid" class="text-[10px] text-emerald-400 font-bold hidden">✓ Valid IFSC</span>
                            </div>
                            <input type="text" name="ifsc_code" id="ifscInput" required value="{{ old('ifsc_code', $kyc->ifsc_code ?? '') }}" {{ $user->kyc_status === 'pending' ? 'disabled' : '' }}
                                placeholder="e.g. SBIN0001234"
                                class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono font-bold uppercase focus:outline-none focus:border-emerald-400 transition">
                        </div>

                        <!-- Account Holder Name -->
                        <div>
                            <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Account Holder Name *</label>
                            <input type="text" name="account_holder" required value="{{ old('account_holder', $kyc->account_holder ?? $user->name) }}" {{ $user->kyc_status === 'pending' ? 'disabled' : '' }}
                                placeholder="Enter account holder name"
                                class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-semibold focus:outline-none focus:border-emerald-400 transition">
                        </div>

                        <!-- UPI ID -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">UPI ID / PhonePe / Paytm Number (Optional)</label>
                            <input type="text" name="upi_id" value="{{ old('upi_id', $kyc->upi_id ?? $user->wallet_address) }}" {{ $user->kyc_status === 'pending' ? 'disabled' : '' }}
                                placeholder="e.g. name@upi or 9876543210@paytm"
                                class="w-full px-3 py-2 rounded-xl bg-[#01140c] border border-emerald-500/40 text-white text-xs font-mono focus:outline-none focus:border-emerald-400 transition">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Document Uploads with Instant Live Preview -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 border-b border-emerald-500/20 pb-2.5">
                        <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                            <i data-lucide="image" class="w-3.5 h-3.5"></i>
                        </div>
                        <h2 class="text-xs font-black text-white uppercase tracking-wider font-heading">3. Upload ID Proof Images & Live Preview</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Front Image Field -->
                        <div class="p-3.5 rounded-2xl bg-[#02180f] border border-emerald-500/30 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Front Image of Document *</label>
                                <span class="text-[10px] text-neutral-400">Max 5MB</span>
                            </div>

                            <input type="file" name="front_image" id="frontImageInput" accept="image/*,.pdf" {{ $user->kyc_status === 'pending' ? 'disabled' : '' }}
                                class="w-full text-xs text-neutral-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-500/20 file:text-emerald-400 hover:file:bg-emerald-500/30 cursor-pointer">

                            <!-- Existing File Preview -->
                            @if(isset($kyc->front_image) && $kyc->front_image)
                                <div class="space-y-1" id="frontExistingPreview">
                                    <span class="text-[10px] font-bold text-emerald-300 uppercase block">Current Uploaded Document:</span>
                                    <div class="relative w-full h-32 rounded-xl overflow-hidden border border-emerald-500/40 bg-black/60 group">
                                        <img src="{{ asset($kyc->front_image) }}" alt="Front ID" class="w-full h-full object-contain">
                                        <a href="{{ asset($kyc->front_image) }}" target="_blank" class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition gap-1">
                                            <i data-lucide="external-link" class="w-4 h-4"></i> View Original
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <!-- Instant JS Live Preview Box -->
                            <div id="frontLivePreviewBox" class="hidden space-y-2 p-2.5 rounded-xl bg-emerald-950/60 border border-emerald-500/50">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-emerald-400 uppercase flex items-center gap-1">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Selected File Preview:
                                    </span>
                                    <span id="frontFileSize" class="text-[10px] text-teal-300 font-mono"></span>
                                </div>
                                <div class="w-full h-32 rounded-lg overflow-hidden bg-black/80 border border-emerald-500/30 flex items-center justify-center">
                                    <img id="frontLiveImg" src="" alt="Live Front Preview" class="w-full h-full object-contain">
                                </div>
                                <p id="frontFileName" class="text-[10px] font-mono text-neutral-300 truncate"></p>
                            </div>
                        </div>

                        <!-- Back Image Field -->
                        <div class="p-3.5 rounded-2xl bg-[#02180f] border border-emerald-500/30 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Back Image / Address Proof (Optional)</label>
                                <span class="text-[10px] text-neutral-400">Max 5MB</span>
                            </div>

                            <input type="file" name="back_image" id="backImageInput" accept="image/*,.pdf" {{ $user->kyc_status === 'pending' ? 'disabled' : '' }}
                                class="w-full text-xs text-neutral-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-500/20 file:text-emerald-400 hover:file:bg-emerald-500/30 cursor-pointer">

                            <!-- Existing File Preview -->
                            @if(isset($kyc->back_image) && $kyc->back_image)
                                <div class="space-y-1" id="backExistingPreview">
                                    <span class="text-[10px] font-bold text-emerald-300 uppercase block">Current Uploaded Document:</span>
                                    <div class="relative w-full h-32 rounded-xl overflow-hidden border border-emerald-500/40 bg-black/60 group">
                                        <img src="{{ asset($kyc->back_image) }}" alt="Back ID" class="w-full h-full object-contain">
                                        <a href="{{ asset($kyc->back_image) }}" target="_blank" class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition gap-1">
                                            <i data-lucide="external-link" class="w-4 h-4"></i> View Original
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <!-- Instant JS Live Preview Box -->
                            <div id="backLivePreviewBox" class="hidden space-y-2 p-2.5 rounded-xl bg-emerald-950/60 border border-emerald-500/50">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-emerald-400 uppercase flex items-center gap-1">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Selected File Preview:
                                    </span>
                                    <span id="backFileSize" class="text-[10px] text-teal-300 font-mono"></span>
                                </div>
                                <div class="w-full h-32 rounded-lg overflow-hidden bg-black/80 border border-emerald-500/30 flex items-center justify-center">
                                    <img id="backLiveImg" src="" alt="Live Back Preview" class="w-full h-full object-contain">
                                </div>
                                <p id="backFileName" class="text-[10px] font-mono text-neutral-300 truncate"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Action Controls (Sleek Compact Button) -->
                <div class="pt-3 border-t border-emerald-500/20 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <a href="{{ route('user.withdrawal.index') }}" class="text-xs font-bold text-neutral-400 hover:text-white flex items-center gap-1 transition">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Back to Withdrawal Portal
                    </a>

                    @if($user->kyc_status === 'pending')
                        <button type="button" disabled
                            class="px-5 py-2.5 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-300 font-bold text-xs cursor-not-allowed flex items-center justify-center gap-2">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i> Application Under Review
                        </button>
                    @else
                        <button type="submit" id="submitBtn"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-black font-black text-xs uppercase tracking-wider shadow-md hover:scale-[1.02] active:scale-95 transition flex items-center justify-center gap-2">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                            <span>{{ $user->kyc_status === 'rejected' ? 'Resubmit KYC Documents' : ($user->kyc_status === 'approved' ? 'Update KYC Details' : 'Submit KYC for Verification') }}</span>
                        </button>
                    @endif
                </div>
            </form>
        </div>

    </div>

    <!-- Client-side Real-time Validation & File Preview JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Instant Document Image Live Preview Handlers
            const frontInput = document.getElementById('frontImageInput');
            const frontBox = document.getElementById('frontLivePreviewBox');
            const frontImg = document.getElementById('frontLiveImg');
            const frontName = document.getElementById('frontFileName');
            const frontSize = document.getElementById('frontFileSize');
            const frontExisting = document.getElementById('frontExistingPreview');

            if (frontInput) {
                frontInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(evt) {
                            frontImg.src = evt.target.result;
                            frontName.textContent = file.name;
                            frontSize.textContent = (file.size / 1024 > 1024) 
                                ? (file.size / (1024 * 1024)).toFixed(2) + ' MB' 
                                : (file.size / 1024).toFixed(1) + ' KB';
                            frontBox.classList.remove('hidden');
                            if (frontExisting) frontExisting.classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            const backInput = document.getElementById('backImageInput');
            const backBox = document.getElementById('backLivePreviewBox');
            const backImg = document.getElementById('backLiveImg');
            const backName = document.getElementById('backFileName');
            const backSize = document.getElementById('backFileSize');
            const backExisting = document.getElementById('backExistingPreview');

            if (backInput) {
                backInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(evt) {
                            backImg.src = evt.target.result;
                            backName.textContent = file.name;
                            backSize.textContent = (file.size / 1024 > 1024) 
                                ? (file.size / (1024 * 1024)).toFixed(2) + ' MB' 
                                : (file.size / 1024).toFixed(1) + ' KB';
                            backBox.classList.remove('hidden');
                            if (backExisting) backExisting.classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // 2. Real-time Field Validations
            const docSelect = document.getElementById('docTypeSelect');
            const docNumInput = document.getElementById('docNumInput');
            const docNumValid = document.getElementById('docNumValid');
            const ifscInput = document.getElementById('ifscInput');
            const ifscValid = document.getElementById('ifscValid');
            const accNumInput = document.getElementById('accNumInput');
            const accNumValid = document.getElementById('accNumValid');
            const fullNameInput = document.getElementById('fullNameInput');
            const fullNameValid = document.getElementById('fullNameValid');

            // Dynamic Placeholder per Document Type
            function updateDocTypePlaceholder() {
                if (!docSelect || !docNumInput) return;
                const type = docSelect.value;
                if (type === 'aadhaar') {
                    docNumInput.placeholder = "e.g. 1234 5678 9012 (12 Digits)";
                } else if (type === 'pan') {
                    docNumInput.placeholder = "e.g. ABCDE1234F (10 Characters)";
                } else if (type === 'passport') {
                    docNumInput.placeholder = "e.g. A1234567 (Passport No)";
                } else {
                    docNumInput.placeholder = "e.g. ABC1234567 (Voter ID)";
                }
                validateDocNumber();
            }

            if (docSelect) {
                docSelect.addEventListener('change', updateDocTypePlaceholder);
                updateDocTypePlaceholder();
            }

            // Document Number Live Validation
            function validateDocNumber() {
                if (!docNumInput) return;
                const val = docNumInput.value.trim();
                const type = docSelect ? docSelect.value : 'aadhaar';
                let isValid = false;

                if (type === 'aadhaar') {
                    const cleanNum = val.replace(/\s+/g, '');
                    isValid = /^\d{12}$/.test(cleanNum);
                } else if (type === 'pan') {
                    isValid = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i.test(val);
                } else {
                    isValid = val.length >= 6;
                }

                if (isValid) {
                    docNumValid.classList.remove('hidden');
                    docNumInput.classList.remove('border-rose-500/50');
                    docNumInput.classList.add('border-emerald-400');
                } else {
                    docNumValid.classList.add('hidden');
                    docNumInput.classList.remove('border-emerald-400');
                }
            }

            if (docNumInput) {
                docNumInput.addEventListener('input', validateDocNumber);
            }

            // IFSC Code Auto-uppercase & Validation
            if (ifscInput) {
                ifscInput.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                    const isValid = /^[A-Z]{4}0[A-Z0-9]{6}$/.test(this.value.trim());
                    if (isValid) {
                        ifscValid.classList.remove('hidden');
                        this.classList.add('border-emerald-400');
                    } else {
                        ifscValid.classList.add('hidden');
                        this.classList.remove('border-emerald-400');
                    }
                });
            }

            // Account Number Live Validation
            if (accNumInput) {
                accNumInput.addEventListener('input', function() {
                    const val = this.value.trim();
                    if (val.length >= 6 && /^\d+$/.test(val)) {
                        accNumValid.classList.remove('hidden');
                        this.classList.add('border-emerald-400');
                    } else {
                        accNumValid.classList.add('hidden');
                        this.classList.remove('border-emerald-400');
                    }
                });
            }

            // Full Name Live Validation
            if (fullNameInput) {
                fullNameInput.addEventListener('input', function() {
                    if (this.value.trim().length >= 3) {
                        fullNameValid.classList.remove('hidden');
                        this.classList.add('border-emerald-400');
                    } else {
                        fullNameValid.classList.add('hidden');
                        this.classList.remove('border-emerald-400');
                    }
                });
            }
        });
    </script>
@endsection
