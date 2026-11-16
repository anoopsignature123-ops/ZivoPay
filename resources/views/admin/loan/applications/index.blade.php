@extends('admin.layouts.app')

@php
    $statusLabels = ['submitted' => 'Submitted', 'under_review' => 'Under review', 'approved' => 'Approved', 'rejected' => 'Rejected', 'disbursed' => 'Disbursed', 'completed' => 'Completed'];
    $statusStyles = ['submitted' => 'text-sky-200 bg-sky-300/10 border-sky-300/15', 'under_review' => 'text-amber-200 bg-amber-300/10 border-amber-300/15', 'approved' => 'text-emerald-200 bg-emerald-300/10 border-emerald-300/15', 'rejected' => 'text-rose-200 bg-rose-300/10 border-rose-300/15', 'disbursed' => 'text-violet-200 bg-violet-300/10 border-violet-300/15', 'completed' => 'text-teal-200 bg-teal-300/10 border-teal-300/15'];
@endphp

@section('title', 'Loan Applications - ZIVO PAY')

@section('content')
    <main class="mx-auto w-full max-w-[1500px] space-y-6">
        <header
            class="flex flex-col justify-between gap-4 rounded-3xl border border-emerald-400/20 bg-gradient-to-br from-[#073622] via-[#05271b] to-[#03170f] p-6 sm:flex-row sm:items-center sm:p-8">
            <div>
                <a href="{{ route('admin.loan.dashboard') }}" class="text-xs font-bold text-emerald-300">← Loan
                    dashboard</a>
                <p class="mt-4 text-[10px] font-black uppercase tracking-[.22em] text-emerald-300">Customer submissions</p>
                <h1 class="mt-1 text-2xl font-black text-white sm:text-3xl">Loan Applications</h1>
                <p class="mt-2 text-sm text-emerald-50/65">Review applicant details, loan requirements and supporting
                    documents.</p>
                </div>
                <a href="{{ route('admin.loan.reports.index') }}"
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-emerald-300/20 bg-emerald-300/[.06] px-4 text-sm font-bold text-emerald-100"><i
                        data-lucide="chart-no-axes-combined" class="h-4 w-4"></i> Open reports</a>
                </header>

                @if(session('success'))
                    <div role="status" class="rounded-xl border border-emerald-300/20 bg-emerald-300/10 p-4 text-sm text-emerald-100">
                        {{ session('success') }}
                </div>@endif
                @if(session('error'))
                    <div role="alert" class="rounded-xl border border-rose-300/20 bg-rose-300/10 p-4 text-sm text-rose-100">
                        {{ session('error') }}
                </div>@endif

                <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($statusLabels as $status => $label)
                        <a href="{{ route('admin.loan.applications.index', ['status' => $status]) }}"
                            class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-4 transition hover:border-emerald-300/30">
                            <div class="flex items-center justify-between"><span
                                    class="text-xs font-semibold text-neutral-400">{{ $label }}</span><span
                                    class="h-2 w-2 rounded-full {{ $status === 'approved' || $status === 'completed' ? 'bg-emerald-300' : ($status === 'rejected' ? 'bg-rose-300' : ($status === 'under_review' ? 'bg-amber-300' : 'bg-sky-300')) }}"></span>
                            </div>
                            <p class="mt-2 text-2xl font-black text-white">{{ number_format($stats[$status] ?? 0) }}</p>
                        </a>
                    @endforeach
                        </section>

                <form method="GET" action="{{ route('admin.loan.applications.index') }}"
                    class="grid gap-3 rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-4 sm:grid-cols-2 xl:grid-cols-4">
                    <label class="relative block xl:col-span-2"><span class="sr-only">Search applications</span><i data-lucide="search"
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-500"></i><input
                            type="search" name="search" value="{{ $filters['search'] ?? '' }}"
                            placeholder="Application number, applicant, phone or email"
                            class="min-h-11 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] pl-10 pr-3 text-sm text-white placeholder:text-neutral-500 outline-none focus:border-emerald-300/50"></label>
                    <select name="status" aria-label="Status"
                        class="min-h-11 rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50">
                        <option value="">All statuses</option>@foreach($statusLabels as $status => $label)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="category_id" aria-label="Category"
                        class="min-h-11 rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50">
                        <option value="">All categories</option>@foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>{{ $category->name }}</option>@endforeach
                    </select>
                    <label><span class="sr-only">From date</span><input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
                            aria-label="From date"
                            class="min-h-11 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50"></label>
                    <label><span class="sr-only">To date</span><input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
                            aria-label="To date"
                            class="min-h-11 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50"></label>
                    <div class="flex gap-2"><button
                            class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl border border-emerald-300/25 bg-emerald-300/10 px-4 text-sm font-bold text-emerald-100 hover:bg-emerald-300/15"><i
                                data-lucide="list-filter" class="h-4 w-4"></i> Filter</button><a
                            href="{{ route('admin.loan.applications.index') }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl border border-white/10 px-4 text-sm font-bold text-neutral-300 hover:bg-white/5">Reset</a>
                    </div>
                </form>

                <section class="overflow-hidden rounded-2xl border border-emerald-300/15 bg-[#041b12]/90">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-emerald-300/10 px-5 py-4">
                        <div>
                            <h2 class="font-bold text-white">Application list</h2>
                            <p class="mt-1 text-xs text-neutral-400">{{ number_format($applications->total()) }} matching records
                            </p>
                        </div><a href="{{ route('admin.loan.reports.export', request()->query()) }}"
                            class="text-xs font-bold text-emerald-300 hover:text-emerald-200">Export CSV →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1000px] text-left text-sm">
                            <thead class="bg-emerald-300/[.04] text-[10px] font-black uppercase tracking-wider text-emerald-300">
                                <tr>
                                    <th class="px-5 py-3">Application</th>
                                    <th class="px-4 py-3">Applicant</th>
                                    <th class="px-4 py-3">Loan category</th>
                                    <th class="px-4 py-3">Loan request</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Received</th>
                                    <th class="px-5 py-3 text-right">Details</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-emerald-300/[.07]">
                                @forelse($applications as $application)
                                    <tr class="transition hover:bg-emerald-300/[.025]">
                                        <td class="px-5 py-4"><a href="{{ route('admin.loan.applications.show', $application) }}"
                                                class="font-bold text-white hover:text-emerald-200">{{ $application->application_number }}</a>
                                            <p class="mt-0.5 text-xs text-neutral-500">#{{ $application->id }}</p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="font-semibold text-neutral-200">
                                                {{ $application->applicant_name ?: 'Unknown applicant' }}
                                            </p>
                                            <p class="mt-0.5 text-xs text-emerald-300">Submitted by:
                                                {{ $application->user?->name ?? ($application->user_id ? 'User #' . $application->user_id : 'Unlinked user') }}
                                                · ID {{ $application->user_id ?? '—' }}
                                            </p>
                                            <p class="mt-0.5 text-xs text-neutral-500">{{ $application->mobile }} ·
                                                {{ $application->email ?: 'No email' }}
                                            </p>
                                        </td>
                                        <td class="px-4 py-4 text-neutral-200">
                                            {{ $application->category?->name ?? 'Category unavailable' }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="font-semibold text-neutral-200">
                                                ₹{{ number_format((float) $application->loan_amount, 2) }}</p>
                                            <p class="mt-0.5 text-xs text-neutral-500">{{ $application->tenure_years }} years ·
                                                {{ $application->preferred_lender }}
                                            </p>
                                        </td>
                                        <td class="px-4 py-4"><span
                                                class="rounded-full border px-2.5 py-1 text-[11px] font-bold {{ $statusStyles[$application->status] ?? 'border-slate-300/15 bg-slate-300/10 text-slate-200' }}">{{ $statusLabels[$application->status] ?? str($application->status)->headline() }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-xs text-neutral-300">
                                            {{ ($application->submitted_at ?? $application->created_at)?->format('d M Y, h:i A') ?? '—' }}
                                        </td>
                                        <td class="px-5 py-4 text-right"><a href="{{ route('admin.loan.applications.show', $application) }}"
                                                class="inline-flex min-h-9 items-center gap-2 rounded-lg border border-emerald-300/15 px-3 text-xs font-bold text-emerald-200 hover:bg-emerald-300/10"><i
                                                    data-lucide="eye" class="h-4 w-4"></i> Review</a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-5 py-16 text-center"><span
                                                class="mx-auto grid h-12 w-12 place-items-center rounded-2xl border border-emerald-300/15 bg-emerald-300/[.05] text-emerald-200"><i
                                                    data-lucide="inbox" class="h-5 w-5"></i></span>
                                            <p class="mt-3 font-bold text-white">No loan applications found</p>
                                            <p class="mt-1 text-sm text-neutral-400">Try changing the filters. Mobile submissions will
                                                appear here.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            </table>
                            </div>
                            @if($applications->hasPages())
                            <div class="border-t border-emerald-300/10 px-5 py-4">{{ $applications->links() }}</div>@endif
                            </section>
                            </main>
@endsection