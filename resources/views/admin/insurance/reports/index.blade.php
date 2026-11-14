@extends('admin.layouts.app')

@php
    $statusLabels = ['draft' => 'Draft', 'submitted' => 'Submitted', 'under_review' => 'Under review', 'approved' => 'Approved', 'rejected' => 'Rejected'];
    $statusStyles = ['draft' => 'text-slate-200 bg-slate-300/10 border-slate-300/15', 'submitted' => 'text-sky-200 bg-sky-300/10 border-sky-300/15', 'under_review' => 'text-amber-200 bg-amber-300/10 border-amber-300/15', 'approved' => 'text-emerald-200 bg-emerald-300/10 border-emerald-300/15', 'rejected' => 'text-rose-200 bg-rose-300/10 border-rose-300/15'];
@endphp

@section('title', 'Insurance Reports - ZIVO PAY')

@section('content')
<main class="mx-auto w-full max-w-[1500px] space-y-6">
    <header class="no-print flex flex-col justify-between gap-5 rounded-3xl border border-emerald-400/20 bg-gradient-to-br from-[#073622] via-[#05271b] to-[#03170f] p-6 sm:flex-row sm:items-center sm:p-8"><div><a href="{{ route('admin.insurance.dashboard') }}" class="text-xs font-bold text-emerald-300">← Insurance dashboard</a><p class="mt-4 text-[10px] font-black uppercase tracking-[.22em] text-emerald-300">Insights & exports</p><h1 class="mt-1 text-2xl font-black text-white sm:text-3xl">Insurance Reports</h1><p class="mt-2 text-sm text-emerald-50/65">Filter applications by date, category, provider, product and status.</p></div><div class="flex flex-wrap gap-2"><button type="button" onclick="window.print()" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-emerald-300/20 bg-emerald-300/[.06] px-4 text-sm font-bold text-emerald-100"><i data-lucide="printer" class="h-4 w-4"></i> Print / Save PDF</button><a href="{{ route('admin.insurance.reports.export', $filters) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-400 to-teal-300 px-4 text-sm font-black text-emerald-950"><i data-lucide="download" class="h-4 w-4"></i> Export Excel CSV</a></div></header>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5"><article class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-4"><p class="text-xs font-semibold text-neutral-400">Matching applications</p><p class="mt-2 text-2xl font-black text-white">{{ number_format($total) }}</p></article>@foreach($statusLabels as $status => $label)<article class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-4"><p class="text-xs font-semibold text-neutral-400">{{ $label }}</p><p class="mt-2 text-2xl font-black text-white">{{ number_format((int)($statusCounts[$status] ?? 0)) }}</p></article>@endforeach</section>

    <form method="GET" action="{{ route('admin.insurance.reports.index') }}" class="no-print grid gap-3 rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-4 sm:grid-cols-2 xl:grid-cols-4">
        <label class="relative block xl:col-span-2"><span class="sr-only">Search reports</span><i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-500"></i><input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Application number, customer, phone or email" class="min-h-11 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] pl-10 pr-3 text-sm text-white placeholder:text-neutral-500 outline-none focus:border-emerald-300/50"></label>
        <select name="status" aria-label="Status" class="min-h-11 rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50"><option value="">All statuses</option>@foreach($statusLabels as $status => $label)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $label }}</option>@endforeach</select>
        <select name="category_id" aria-label="Category" class="min-h-11 rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string)($filters['category_id'] ?? '') === (string)$category->id)>{{ $category->name }}</option>@endforeach</select>
        <select name="company_id" aria-label="Company" class="min-h-11 rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50"><option value="">All companies</option>@foreach($companies as $company)<option value="{{ $company->id }}" @selected((string)($filters['company_id'] ?? '') === (string)$company->id)>{{ $company->name }}</option>@endforeach</select>
        <select name="product_id" aria-label="Product" class="min-h-11 rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50"><option value="">All products</option>@foreach($products as $product)<option value="{{ $product->id }}" @selected((string)($filters['product_id'] ?? '') === (string)$product->id)>{{ $product->name }}</option>@endforeach</select>
        <label class="block"><span class="sr-only">From date</span><input type="date" name="from" value="{{ $filters['from'] ?? '' }}" aria-label="From date" class="min-h-11 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50"></label>
        <label class="block"><span class="sr-only">To date</span><input type="date" name="to" value="{{ $filters['to'] ?? '' }}" aria-label="To date" class="min-h-11 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50"></label>
        <div class="flex gap-2"><button class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl border border-emerald-300/25 bg-emerald-300/10 px-4 text-sm font-bold text-emerald-100 hover:bg-emerald-300/15"><i data-lucide="list-filter" class="h-4 w-4"></i> Apply</button><a href="{{ route('admin.insurance.reports.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-white/10 px-4 text-sm font-bold text-neutral-300 hover:bg-white/5">Reset</a></div>
    </form>

    <section class="overflow-hidden rounded-2xl border border-emerald-300/15 bg-[#041b12]/90"><div class="flex items-center justify-between gap-3 border-b border-emerald-300/10 px-5 py-4"><div><h2 class="font-bold text-white">Application report</h2><p class="mt-1 text-xs text-neutral-400">{{ number_format($applications->total()) }} rows match these filters</p></div><span class="rounded-full border border-emerald-300/15 bg-emerald-300/[.06] px-3 py-1 text-xs font-bold text-emerald-200">Submission date range</span></div>
        <div class="overflow-x-auto"><table class="w-full min-w-[900px] text-left text-sm"><thead class="bg-emerald-300/[.04] text-[10px] font-black uppercase tracking-wider text-emerald-300"><tr><th class="px-5 py-3">Application</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Category / company</th><th class="px-4 py-3">Product</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Submitted</th><th class="px-5 py-3 text-right">Open</th></tr></thead><tbody class="divide-y divide-emerald-300/[.07]">
            @forelse($applications as $application)
                <tr class="hover:bg-emerald-300/[.025]"><td class="px-5 py-4 font-bold text-white">{{ $application->application_number }}</td><td class="px-4 py-4 text-neutral-200">{{ $application->name ?: '—' }}<p class="mt-0.5 text-xs font-normal text-neutral-500">{{ $application->phone ?: '—' }}</p></td><td class="px-4 py-4 text-neutral-300">{{ $application->category?->name ?? '—' }}<p class="mt-0.5 text-xs text-neutral-500">{{ $application->company?->name ?? '—' }}</p></td><td class="px-4 py-4 text-neutral-300">{{ $application->product?->name ?? '—' }}</td><td class="px-4 py-4"><span class="rounded-full border px-2.5 py-1 text-[11px] font-bold {{ $statusStyles[$application->status] ?? $statusStyles['draft'] }}">{{ $statusLabels[$application->status] ?? str($application->status)->headline() }}</span></td><td class="px-4 py-4 text-xs text-neutral-300">{{ ($application->submitted_at ?? $application->created_at)?->format('d M Y') ?? '—' }}</td><td class="px-5 py-4 text-right"><a href="{{ route('admin.insurance.applications.show', $application) }}" class="inline-flex min-h-9 items-center gap-2 rounded-lg border border-emerald-300/15 px-3 text-xs font-bold text-emerald-200 hover:bg-emerald-300/10"><i data-lucide="eye" class="h-4 w-4"></i> View</a></td></tr>
            @empty
                <tr><td colspan="7" class="px-5 py-14 text-center text-sm text-neutral-400">No application data matches this report.</td></tr>
            @endforelse
        </tbody></table></div>
        @if($applications->hasPages())<div class="border-t border-emerald-300/10 px-5 py-4">{{ $applications->links() }}</div>@endif
    </section>
</main>
@endsection

@push('scripts')
<style>
    @media print {
        body, main { background: #fff !important; color: #111 !important; }
        #sidebar, .main-content > header, .live-bg-container, .aurora-glow-orb-1, .aurora-glow-orb-2, .no-print { display: none !important; }
        .main-content { margin: 0 !important; padding: 0 !important; }
        section, article { break-inside: avoid; border-color: #d1d5db !important; background: #fff !important; box-shadow: none !important; }
        h1, h2, p, span, th, td, a { color: #111 !important; }
        table { min-width: 0 !important; width: 100% !important; }
        .overflow-x-auto { overflow: visible !important; }
    }
</style>
@endpush
