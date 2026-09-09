@extends('admin.layouts.app')

@section('content')
<style>
.gold-3d-badge {
    background: linear-gradient(180deg, #fef08a 0%, #f59e0b 50%, #b45309 100%) !important;
    border: 2px solid #fef08a !important;
    box-shadow: 0 4px 15px rgba(243, 202, 82, 0.4), inset 0 2px 4px rgba(255, 255, 255, 0.6) !important;
}
</style>

<div class="w-full space-y-6">
    <!-- Header Banner -->
    <div class="ng-banner-title p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="pdf-num-badge">AP</span>
                <span class="text-xs text-amber-400 font-extrabold tracking-[3px] uppercase">DEX TRADE NETWORK</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gold-gradient font-heading">ADD NEW PACKAGE</h1>
            <p class="text-xs text-neutral-300 mt-1">Configure a new investment tier for Dex Trade members.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.packages.index') }}" class="px-5 py-3 rounded-xl bg-bg border border-amber-500/40 text-amber-300 font-bold text-xs hover:bg-amber-500/20 transition flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Packages
            </a>
        </div>
    </div>

    <!-- 2-Column Split: Form (Left) & Live Preview (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- LEFT: Form Input Container -->
        <div class="lg:col-span-2 bg-panel p-6 sm:p-8 shadow-2xl rounded-2xl border border-amber-500/30">
            <form action="{{ route('admin.packages.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-amber-400 uppercase tracking-wider">Package Name</label>
                    <input type="text" name="name" id="inputName" value="{{ old('name') }}" placeholder="e.g. PACKAGE 6" class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-amber-400 uppercase tracking-wider">Minimum Amount ($)</label>
                        <input type="number" step="0.01" name="min_amount" id="inputMin" value="{{ old('min_amount') }}" placeholder="10.00" class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400" required>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-amber-400 uppercase tracking-wider">Maximum Amount ($)</label>
                        <input type="number" step="0.01" name="max_amount" id="inputMax" value="{{ old('max_amount') }}" placeholder="500.00" class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-amber-400 uppercase tracking-wider">Daily ROI (%)</label>
                        <input type="number" step="0.01" name="daily_roi" id="inputRoi" value="{{ old('daily_roi') }}" placeholder="0.75" class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400" required>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-amber-400 uppercase tracking-wider">Duration (Days)</label>
                        <input type="number" name="duration_days" id="inputDuration" value="{{ old('duration_days', 200) }}" placeholder="200" class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400" required>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-amber-400 uppercase tracking-wider">Return Multiplier</label>
                        <input type="number" step="0.1" name="total_return_multiplier" id="inputMultiplier" value="{{ old('total_return_multiplier', 2.0) }}" placeholder="2.0" class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400" required>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-amber-400 uppercase tracking-wider">Status</label>
                    <select name="status" id="inputStatus" class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-amber-400 uppercase tracking-wider">Description</label>
                    <textarea name="description" id="inputDesc" rows="3" placeholder="Package description..." class="w-full px-4 py-3 rounded-xl bg-bg border border-amber-500/40 text-white font-semibold text-xs focus:outline-none focus:border-amber-400">{{ old('description') }}</textarea>
                </div>

                <div class="pt-4 border-t border-amber-500/20 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.packages.index') }}" class="px-6 py-3 rounded-xl bg-bg border border-amber-500/30 text-neutral-300 font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition">
                        CANCEL
                    </a>
                    <button type="submit" class="px-8 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-black font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 transition flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4 text-black"></i> CREATE PACKAGE
                    </button>
                </div>
            </form>
        </div>

        <!-- RIGHT: Live Package Card Preview -->
        <div class="space-y-4">
            <div class="text-xs font-black text-amber-400 uppercase tracking-widest flex items-center gap-2">
                <i data-lucide="eye" class="w-4 h-4 text-amber-400"></i> LIVE CARD PREVIEW
            </div>

            <div class="p-6 rounded-3xl bg-bg border border-amber-500/40 space-y-4 relative overflow-hidden shadow-2xl">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-2xl gold-3d-badge text-black flex items-center justify-center font-black text-lg shadow-md shrink-0">
                            <i data-lucide="rocket" class="w-5 h-5"></i>
                        </div>
                        <span id="previewStatus" class="px-3 py-1 rounded-full text-[10px] font-black uppercase border border-amber-500/40 bg-amber-500/20 text-amber-300">
                            ACTIVE
                        </span>
                    </div>

                    <div>
                        <div id="previewName" class="text-xs font-extrabold text-neutral-300 uppercase tracking-wider">NEW PACKAGE</div>
                        <h3 id="previewRange" class="text-2xl font-black text-gold-gradient font-mono mt-1">$10 - $500</h3>
                    </div>

                    <div class="p-4 rounded-2xl bg-panel border border-amber-500/30 space-y-2 font-mono">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-neutral-400 font-sans font-bold">Daily ROI:</span>
                            <span id="previewRoi" class="text-amber-300 font-black text-sm">0.75% / Day</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-neutral-400 font-sans font-bold">Duration:</span>
                            <span id="previewDuration" class="text-white font-black text-sm">200 Days</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-neutral-400 font-sans font-bold">Total Return:</span>
                            <span id="previewMultiplier" class="text-amber-300 font-black text-sm">0.0X Return</span>
                        </div>
                    </div>

                    <p id="previewDesc" class="text-[11px] text-neutral-400 leading-relaxed italic">
                        Official Dex Trade investment plan.
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-between text-xs text-neutral-400">
                    <span>Status</span>
                    <span id="previewStatus" class="font-bold text-emerald-400 uppercase">ACTIVE</span>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    const inputName = document.getElementById('name');
    const inputMin = document.getElementById('min_amount');
    const inputMax = document.getElementById('max_amount');
    const inputRoi = document.getElementById('daily_roi_percent');
    const inputDuration = document.getElementById('duration_days');
    const inputMultiplier = document.getElementById('capping_multiplier');
    const inputStatus = document.getElementById('status');
    const inputDesc = document.getElementById('description');

    const previewName = document.getElementById('previewName');
    const previewMin = document.getElementById('previewMin');
    const previewMax = document.getElementById('previewMax');
    const previewRoi = document.getElementById('previewRoi');
    const previewDuration = document.getElementById('previewDuration');
    const previewMultiplier = document.getElementById('previewMultiplier');
    const previewStatus = document.getElementById('previewStatus');
    const previewDesc = document.getElementById('previewDesc');

    function updatePreview() {
        if (previewName) previewName.innerText = inputName.value || 'Package Title';
        if (previewMin) previewMin.innerText = '$' + (parseFloat(inputMin.value)||0).toFixed(2);
        if (previewMax) previewMax.innerText = '$' + (parseFloat(inputMax.value)||0).toFixed(2);
        if (previewRoi) previewRoi.innerText = (parseFloat(inputRoi.value)||0).toFixed(2) + '% Daily';
        if (previewDuration) previewDuration.innerText = (inputDuration.value||0) + ' Days';
        if (previewMultiplier) previewMultiplier.innerText = (parseFloat(inputMultiplier.value)||0).toFixed(1) + 'X Return';
        if (previewStatus) previewStatus.innerText = (inputStatus.value || 'active').toUpperCase();
        if (previewDesc) previewDesc.innerText = inputDesc.value || 'Official Dex Trade investment plan.';
    }

    [inputName, inputMin, inputMax, inputRoi, inputDuration, inputMultiplier, inputStatus, inputDesc].forEach(el => {
        if (el) el.addEventListener('input', updatePreview);
        if (el) el.addEventListener('change', updatePreview);
    });
});
</script>
@endsection
