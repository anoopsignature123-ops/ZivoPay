@extends('admin.layouts.app')

@php
$statusLabels = ['submitted' => 'Submitted', 'under_review' => 'Under review', 'approved' => 'Approved', 'rejected' => 'Rejected', 'disbursed' => 'Disbursed', 'completed' => 'Completed'];
$statusStyles = ['submitted' => 'text-sky-200 bg-sky-300/10 border-sky-300/15', 'under_review' => 'text-amber-200 bg-amber-300/10 border-amber-300/15', 'approved' => 'text-emerald-200 bg-emerald-300/10 border-emerald-300/15', 'rejected' => 'text-rose-200 bg-rose-300/10 border-rose-300/15', 'disbursed' => 'text-violet-200 bg-violet-300/10 border-violet-300/15', 'completed' => 'text-teal-200 bg-teal-300/10 border-teal-300/15'];
@endphp

@section('title', 'Loan Dashboard - ZIVO PAY')

@section('content')
    <main class="mx-auto w-full max-w-[1500px] space-y-6">
        <header
            class="flex flex-col justify-between gap-5 rounded-3xl border border-emerald-400/20 bg-gradient-to-br from-[#073622] via-[#05271b] to-[#03170f] p-6 sm:flex-row sm:items-center sm:p-8">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[.22em] text-emerald-300">Digital loan management</p>
                <h1 class="mt-2 text-2xl font-black text-white sm:text-3xl">Loan Dashboard</h1>
                <p class="mt-2 text-sm text-emerald-50/65">Review loan activity, customer submissions and category status.
                </p>
            </div>
            <div class="flex flex-wrap gap-2"><a href="{{ route('admin.loan.applications.index') }}"
                    class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-400 to-teal-300 px-4 text-sm font-black text-emerald-950"><i
                        data-lucide="files" class="h-4 w-4"></i> View applications</a><a
                    href="{{ route('admin.loan.reports.index') }}"
                    class="inline-flex min-h-11 items-center gap-2 rounded-xl border border-emerald-300/20 bg-emerald-300/[.06] px-4 text-sm font-bold text-emerald-100"><i
                        data-lucide="chart-no-axes-combined" class="h-4 w-4"></i> Reports</a></div>
        </header>

        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach([['All applications', $totalApplications, 'files', route('admin.loan.applications.index')], ['Submitted today', $todayApplications, 'calendar-days', route('admin.loan.applications.index', ['from' => today()->toDateString(), 'to' => today()->toDateString()])], ['Under review', $statusCounts['under_review'], 'scan-search', route('admin.loan.applications.index', ['status' => 'under_review'])], ['Approved', $statusCounts['approved'], 'badge-check', route('admin.loan.applications.index', ['status' => 'approved'])]] as [$label, $value, $icon, $url])
                <a href="{{ $url }}"
                    class="group flex items-center gap-4 rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-5 transition hover:border-emerald-300/30 hover:bg-emerald-300/[.04]"><span
                        class="grid h-12 w-12 place-items-center rounded-xl border border-emerald-300/15 bg-emerald-300/[.07] text-emerald-200"><i
                            data-lucide="{{ $icon }}" class="h-5 w-5"></i></span><span><span
                            class="block text-xs font-semibold text-neutral-400">{{ $label }}</span><span
                            class="mt-1 block text-2xl font-black text-white">{{ number_format($value) }}</span></span><i
                        data-lucide="arrow-up-right"
                        class="ml-auto h-4 w-4 text-neutral-600 transition group-hover:text-emerald-300"></i></a>
            @endforeach
        </section>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.7fr)_minmax(280px,.8fr)]">
            <section class="overflow-hidden rounded-2xl border border-emerald-300/15 bg-[#041b12]/90">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-emerald-300/10 px-5 py-4">
                    <div>
                        <h2 class="font-bold text-white">Recent loan applications</h2>
                        <p class="mt-1 text-xs text-neutral-400">Latest customer submissions</p>
                    </div><a href="{{ route('admin.loan.applications.index') }}"
                        class="text-xs font-bold text-emerald-300 hover:text-emerald-200">All applications →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px] text-left text-sm">
                        <thead
                            class="bg-emerald-300/[.04] text-[10px] font-black uppercase tracking-wider text-emerald-300">
                            <tr>
                                <th class="px-5 py-3">Application</th>
                                <th class="px-4 py-3">Applicant</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Open</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-emerald-300/[.07]">
                            @forelse($recentApplications as $application)
                                <tr class="transition hover:bg-emerald-300/[.025]">
                                    <td class="px-5 py-4"><a href="{{ route('admin.loan.applications.show', $application) }}"
                                            class="font-bold text-white hover:text-emerald-200">{{ $application->application_number }}</a>
                                        <p class="mt-0.5 text-xs text-neutral-500">
                                            {{ ($application->submitted_at ?? $application->created_at)?->format('d M Y, h:i A') }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <p class="font-semibold text-neutral-200">{{ $application->applicant_name }}</p>
                                        <p class="mt-0.5 text-xs text-emerald-300">Account:
                                            {{ $application->user?->name ?? 'Unlinked user' }}
                                            ( {{ $application->user?->referral_code ?? '—' }} )
                                        </p>
                                        <p class="mt-0.5 text-xs text-neutral-500">{{ $application->mobile }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-neutral-300">{{ $application->category?->name ?? '—' }}</td>
                                    <td class="px-4 py-4"><span
                                            class="rounded-full border px-2.5 py-1 text-[11px] font-bold {{ $statusStyles[$application->status] ?? 'border-slate-300/15 bg-slate-300/10 text-slate-200' }}">{{ $statusLabels[$application->status] ?? str($application->status)->headline() }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-right"><a
                                            href="{{ route('admin.loan.applications.show', $application) }}"
                                            aria-label="Review {{ $application->application_number }}"
                                            class="inline-flex min-h-9 items-center gap-2 rounded-lg border border-emerald-300/15 px-3 text-xs font-bold text-emerald-200 hover:bg-emerald-300/10"><i
                                                data-lucide="eye" class="h-4 w-4"></i> Review</a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-14 text-center"><span
                                            class="mx-auto grid h-12 w-12 place-items-center rounded-2xl border border-emerald-300/15 bg-emerald-300/[.05] text-emerald-200"><i
                                                data-lucide="inbox" class="h-5 w-5"></i></span>
                                        <p class="mt-3 font-bold text-white">No loan applications received yet</p>
                                        <p class="mt-1 text-sm text-neutral-400">Mobile API submissions will appear here.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <aside class="space-y-6">
                <section class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="font-bold text-white">Application status</h2>
                            <p class="mt-1 text-xs text-neutral-400">Current application distribution</p>
                        </div><i data-lucide="chart-pie" class="h-5 w-5 text-emerald-300"></i>
                    </div>
                    <div class="mt-4 space-y-3">
                        @foreach($statusLabels as $status => $label)
                            @php $count = $statusCounts[$status] ?? 0;
                            $percent = $totalApplications > 0 ? round(($count / $totalApplications) * 100) : 0; @endphp
                            <div>
                                <div class="mb-1.5 flex items-center justify-between text-xs"><span
                                        class="text-neutral-300">{{ $label }}</span><span
                                        class="font-bold text-white">{{ number_format($count) }} <span
                                            class="font-normal text-neutral-500">· {{ $percent }}%</span></span></div>
                                <div class="h-1.5 overflow-hidden rounded-full bg-white/[.07]">
                                    <div class="h-full rounded-full bg-emerald-400" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
                <section class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="font-bold text-white">Catalog overview</h2>
                            <p class="mt-1 text-xs text-neutral-400">Loan categories and availability</p>
                        </div><i data-lucide="boxes" class="h-5 w-5 text-emerald-300"></i>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-2"><a href="{{ route('admin.loan.categories.index') }}"
                            class="rounded-xl border border-white/[.07] p-3 text-center hover:border-emerald-300/20"><span
                                class="block text-xl font-black text-white">{{ number_format($totalCategories) }}</span><span
                                class="mt-1 block text-[10px] font-bold uppercase tracking-wide text-neutral-500">Categories</span></a><a
                            href="{{ route('admin.loan.categories.index', ['status' => 'active']) }}"
                            class="rounded-xl border border-white/[.07] p-3 text-center hover:border-emerald-300/20"><span
                                class="block text-xl font-black text-white">{{ number_format($activeCategories) }}</span><span
                                class="mt-1 block text-[10px] font-bold uppercase tracking-wide text-neutral-500">Active</span></a><a
                            href="{{ route('admin.loan.categories.index', ['status' => 'inactive']) }}"
                            class="rounded-xl border border-white/[.07] p-3 text-center hover:border-emerald-300/20"><span
                                class="block text-xl font-black text-white">{{ number_format($inactiveCategories) }}</span><span
                                class="mt-1 block text-[10px] font-bold uppercase tracking-wide text-neutral-500">Inactive</span></a>
                    </div><a href="{{ route('admin.loan.categories.index') }}"
                        class="mt-4 block text-xs font-bold text-emerald-300 hover:text-emerald-200">Manage loan categories
                        →</a>
                </section>
            </aside>
        </div>
    </main>
@endsection
