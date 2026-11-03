@extends('admin.layouts.app')

@section('title', 'Edit Capital Package - ZIVO PAY Admin')

@section('content')
@php
    $storedDescription = (string) $package->description;
    $hasLegacyIncomeRules = str_contains(strtolower($storedDescription), 'direct') || str_contains(strtolower($storedDescription), 'level 2-15');
    $defaultDescription = $hasLegacyIncomeRules
        ? 'Fixed Package ROI: 8% monthly for 25 cycles. ROI-on-ROI eligibility requires an activated account for at least 24 hours.'
        : $storedDescription;
@endphp
<div class="w-full space-y-6 font-sans max-w-4xl mx-auto">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-slate-900/95 border-2 border-emerald-500/60 shadow-[0_0_35px_rgba(16,185,129,0.3)] flex items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] uppercase border border-emerald-500/40">EDIT PLAN</span>
                <span class="text-xs text-emerald-400 font-black tracking-[3px] uppercase">ZIVO PAY CONFIGURATION</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white font-heading">EDIT CAPITAL PACKAGE: {{ strtoupper($package->name) }}</h1>
            <p class="text-xs text-neutral-300 mt-1">Update package amount limits. Fixed Package ROI is locked at 8% monthly for 25 cycles.</p>
        </div>

        <a href="{{ route('admin.packages.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 border border-emerald-500/30 text-neutral-300 text-xs font-bold hover:text-white transition">
            Back to Packages
        </a>
    </div>

    <!-- Form Container -->
    <div class="bg-slate-900/90 p-6 sm:p-8 rounded-3xl border border-emerald-500/30 shadow-2xl">
        <form action="{{ route('admin.packages.update', $package->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Package Name -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-emerald-400 uppercase mb-2">Package Plan Name</label>
                    <input type="text" name="name" value="{{ old('name', $package->name) }}" class="w-full px-4 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400" required>
                    @error('name') <span class="text-rose-400 text-[11px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Minimum Investment Amount -->
                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase mb-2">Minimum Amount (₹)</label>
                    <input type="number" step="0.01" name="min_amount" value="{{ old('min_amount', $package->min_amount) }}" class="w-full px-4 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400" required>
                    @error('min_amount') <span class="text-rose-400 text-[11px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Maximum Investment Amount -->
                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase mb-2">Maximum Amount (₹)</label>
                    <input type="number" step="0.01" name="max_amount" value="{{ old('max_amount', $package->max_amount) }}" class="w-full px-4 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400" required>
                    @error('max_amount') <span class="text-rose-400 text-[11px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2 rounded-xl border border-emerald-500/30 bg-emerald-500/5 p-4 text-sm text-emerald-200">
                    Fixed Package ROI: <strong>8% monthly × 25 cycles</strong>. This system rule is fixed and is not edited per tier.
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400" required>
                        <option value="active" {{ old('status', $package->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $package->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <span class="text-rose-400 text-[11px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-emerald-400 uppercase mb-2">Plan Description & Conditions</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl bg-bg border border-emerald-500/40 text-white font-semibold text-xs focus:outline-none focus:border-emerald-400">{{ old('description', $defaultDescription) }}</textarea>
                    @error('description') <span class="text-rose-400 text-[11px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-4 border-t border-emerald-500/20 flex items-center justify-end gap-3">
                <a href="{{ route('admin.packages.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 text-neutral-300 font-bold text-xs hover:text-white transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-black font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 transition">
                    Update Capital Package
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
