@extends('admin.layouts.app')

@php
$statusLabels = ['submitted' => 'Submitted', 'under_review' => 'Under review', 'approved' => 'Approved', 'rejected' => 'Rejected', 'disbursed' => 'Disbursed', 'completed' => 'Completed'];
$statusStyles = ['submitted' => 'border-sky-300/15 bg-sky-300/10 text-sky-200', 'under_review' => 'border-amber-300/15 bg-amber-300/10 text-amber-200', 'approved' => 'border-emerald-300/15 bg-emerald-300/10 text-emerald-200', 'rejected' => 'border-rose-300/15 bg-rose-300/10 text-rose-200', 'disbursed' => 'border-violet-300/15 bg-violet-300/10 text-violet-200', 'completed' => 'border-teal-300/15 bg-teal-300/10 text-teal-200'];
$applicantFields = [
    'Applicant full name' => $application->applicant_name,
    'Submitted by account' => $application->user?->name ?? 'Unlinked user',
    'Referral ID' => $application->user?->referral_code ?? '—',
    'Date of birth' => $application->date_of_birth?->format('d M Y'),
    'Mobile number' => $application->mobile,
    'Email address' => $application->email,
    'Residential address' => $application->address,
    'City' => $application->city,
    'State' => $application->state,
    'PIN code' => $application->pin_code,
    'PAN number' => $application->pan_number,
    'Aadhaar number' => $application->aadhaar_number,
];
$employmentFields = [
    'Employment type' => str($application->employment_type)->replace('_', ' ')->headline(),
    'Monthly income' => $application->monthly_income ? '₹' . number_format((float) $application->monthly_income, 2) : null,
    'Employer / business' => $application->employer_name,
    'Existing monthly EMI' => $application->existing_monthly_emi !== null ? '₹' . number_format((float) $application->existing_monthly_emi, 2) : null,
    'CIBIL score' => $application->credit_score,
];
$loanFields = [
    'Requested loan amount' => '₹' . number_format((float) $application->loan_amount, 2),
    'Requested tenure' => $application->tenure_years . ' years',
    'Preferred bank / NBFC' => $application->preferred_lender,
    'Loan purpose' => $application->purpose,
    'Property location' => data_get($application->specifics, 'property_location'),
    'Estimated property value' => data_get($application->specifics, 'property_value') ? '₹' . number_format((float) data_get($application->specifics, 'property_value'), 2) : null,
];
@endphp

@section('title', 'Loan Application ' . $application->application_number)

@section('content')
    <main class="mx-auto w-full max-w-[1500px] space-y-6">
        <header
            class="flex flex-col justify-between gap-5 rounded-3xl border border-emerald-400/20 bg-gradient-to-br from-[#073622] via-[#05271b] to-[#03170f] p-6 sm:flex-row sm:items-center sm:p-8">
            <div>
                <a href="{{ route('admin.loan.applications.index') }}" class="text-xs font-bold text-emerald-300">←
                    Application list</a>
                <p class="mt-4 text-[10px] font-black uppercase tracking-[.22em] text-emerald-300">Loan application ·
                    #{{ $application->id }}</p>
                <div class="mt-1 flex flex-wrap items-center gap-3">
                    <h1 class="break-all text-2xl font-black text-white sm:text-3xl">{{ $application->application_number }}
                    </h1><span
                        class="rounded-full border px-2.5 py-1 text-[11px] font-bold {{ $statusStyles[$application->status] ?? 'border-slate-300/15 bg-slate-300/10 text-slate-200' }}">{{ $statusLabels[$application->status] ?? str($application->status)->headline() }}</span>
                </div>
                <p class="mt-2 text-sm text-emerald-50/65">Submitted
                    {{ ($application->submitted_at ?? $application->created_at)?->format('d M Y, h:i A') ?? '—' }}</p>
            </div>
            <a href="{{ route('admin.loan.reports.index', ['category_id' => $application->loan_category_id]) }}"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-emerald-300/20 bg-emerald-300/[.06] px-4 text-sm font-bold text-emerald-100"><i
                    data-lucide="chart-no-axes-combined" class="h-4 w-4"></i> Related report</a>
        </header>

        @if(session('success'))
            <div role="status" class="rounded-xl border border-emerald-300/20 bg-emerald-300/10 p-4 text-sm text-emerald-100">
                {{ session('success') }}
        </div>@endif
        @if($errors->any())
            <div role="alert" class="rounded-xl border border-rose-300/20 bg-rose-300/10 p-4 text-sm text-rose-100">
                {{ $errors->first() }}
        </div>@endif

        <section class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[.2em] text-emerald-300">Selected loan</p>
                    <h2 class="mt-1 text-xl font-black text-white">
                        {{ $application->category?->name ?? 'Loan category unavailable' }}
                    </h2>
                    <p class="mt-1 text-sm text-neutral-400">
                        {{ $application->preferred_lender ?: 'No lender preference provided' }}
                    </p>
                </div>
                <span
                    class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-emerald-300/15 bg-emerald-300/[.06] text-emerald-200"><i
                        data-lucide="landmark" class="h-5 w-5"></i></span>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-white/[.07] p-3">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Applicant</p>
                    <p class="mt-1 font-semibold text-neutral-100">{{ $application->applicant_name }}</p>
                </div>
                <div class="rounded-xl border border-white/[.07] p-3">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Submitted by account</p>
                    <p class="mt-1 font-semibold text-neutral-100">{{ $application->user?->name ?? 'Unlinked user' }}</p>
                    <p class="mt-1 text-xs text-neutral-500">( {{ $application->user?->referral_code ?? '—' }} )</p>
                </div>
                <div class="rounded-xl border border-white/[.07] p-3">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Loan tenure</p>
                    <p class="mt-1 font-semibold text-neutral-100">{{ $application->tenure_years }} years</p>
                </div>
                <div class="rounded-xl border border-white/[.07] p-3">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Requested amount</p>
                    <p class="mt-1 font-semibold text-neutral-100">
                        ₹{{ number_format((float) $application->loan_amount, 2) }}</p>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.7fr)_minmax(300px,.75fr)]">
            <div class="space-y-6">
                <section class="overflow-hidden rounded-2xl border border-emerald-300/15 bg-[#041b12]/90">
                    <div class="border-b border-emerald-300/10 px-5 py-4">
                        <p class="text-[10px] font-black uppercase tracking-[.2em] text-emerald-300">Applicant</p>
                        <h2 class="mt-1 text-lg font-black text-white">Personal information</h2>
                        <p class="mt-1 text-xs text-neutral-400">Customer details submitted with this application.</p>
                    </div>
                    <dl class="grid gap-x-8 px-5 sm:grid-cols-2">@foreach($applicantFields as $label => $value)
                        <div class="border-b border-emerald-300/[.07] py-3.5">
                            <dt class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">{{ $label }}</dt>
                            <dd class="mt-1 break-words text-sm font-medium text-neutral-200">
                                {{ filled($value) ? $value : '—' }}
                            </dd>
                    </div>@endforeach
                    </dl>
                </section>

                <section class="overflow-hidden rounded-2xl border border-emerald-300/15 bg-[#041b12]/90">
                    <div class="border-b border-emerald-300/10 px-5 py-4">
                        <p class="text-[10px] font-black uppercase tracking-[.2em] text-emerald-300">Loan profile</p>
                        <h2 class="mt-1 text-lg font-black text-white">Employment & income</h2>
                    </div>
                    <dl class="grid gap-x-8 px-5 sm:grid-cols-2">@foreach($employmentFields as $label => $value)
                        <div class="border-b border-emerald-300/[.07] py-3.5">
                            <dt class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">{{ $label }}</dt>
                            <dd class="mt-1 break-words text-sm font-medium text-neutral-200">
                                {{ filled($value) ? $value : '—' }}
                            </dd>
                    </div>@endforeach
                    </dl>
                </section>

                <section class="overflow-hidden rounded-2xl border border-emerald-300/15 bg-[#041b12]/90">
                    <div class="border-b border-emerald-300/10 px-5 py-4">
                        <p class="text-[10px] font-black uppercase tracking-[.2em] text-emerald-300">
                            {{ $application->category?->name ?? 'Loan' }}
                        </p>
                        <h2 class="mt-1 text-lg font-black text-white">Loan requirements</h2>
                    </div>
                    <dl class="grid gap-x-8 px-5 sm:grid-cols-2">@foreach($loanFields as $label => $value)
                        <div class="border-b border-emerald-300/[.07] py-3.5">
                            <dt class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">{{ $label }}</dt>
                            <dd class="mt-1 break-words text-sm font-medium text-neutral-200">
                                {{ filled($value) ? $value : '—' }}
                            </dd>
                    </div>@endforeach
                    </dl>
                    @if($application->specifics)
                        <div class="border-t border-emerald-300/10 p-5">
                            <h3 class="mb-3 text-sm font-bold text-white">Additional category details</h3>
                            <dl class="grid gap-3 sm:grid-cols-2">
                                @foreach($application->specifics as $key => $value)@if(!in_array($key, ['property_location', 'property_value'], true))
                                    <div class="rounded-xl border border-white/[.07] bg-black/10 p-3">
                                        <dt class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">
                                            {{ str($key)->replace('_', ' ')->headline() }}
                                        </dt>
                                        <dd class="mt-1 break-words text-sm text-neutral-200">
                                            {{ is_scalar($value) ? $value : json_encode($value) }}
                                        </dd>
                                </div>@endif @endforeach
                            </dl>
                    </div>@endif
                </section>

                <section class="overflow-hidden rounded-2xl border border-emerald-300/15 bg-[#041b12]/90">
                    <div class="border-b border-emerald-300/10 px-5 py-4">
                        <p class="text-[10px] font-black uppercase tracking-[.2em] text-emerald-300">Verification</p>
                        <h2 class="mt-1 text-lg font-black text-white">Supporting documents</h2>
                        <p class="mt-1 text-xs text-neutral-400">Private files uploaded with the loan application.</p>
                    </div>
                    <div class="grid gap-4 p-5 sm:grid-cols-2">
                        @forelse($application->documents as $document)
                            <article class="overflow-hidden rounded-xl border border-emerald-300/15 bg-[#02120b]">
                                @if(str($document->mime_type)->startsWith('image/'))
                                    <a href="{{ route('admin.loan.applications.documents.preview', [$application, $document]) }}"
                                        target="_blank" rel="noopener"><img
                                            src="{{ route('admin.loan.applications.documents.preview', [$application, $document]) }}"
                                            alt="{{ str($document->document_type)->headline() }} preview"
                                            class="h-56 w-full bg-black/30 object-contain"></a>
                                @elseif($document->mime_type === 'application/pdf')
                                    <iframe src="{{ route('admin.loan.applications.documents.preview', [$application, $document]) }}"
                                        title="{{ str($document->document_type)->headline() }} PDF preview"
                                        class="h-56 w-full bg-white"></iframe>
                                @else
                                    <div class="grid h-56 place-items-center text-sm text-neutral-500">Preview unavailable</div>
                                @endif
                                <div class="flex items-center justify-between gap-3 border-t border-emerald-300/10 p-3">
                                    <div class="min-w-0"><strong
                                            class="text-sm text-white">{{ str($document->document_type)->replace('_', ' ')->headline() }}</strong><small
                                            class="block max-w-48 truncate text-neutral-500">{{ $document->original_name }}</small>
                                    </div><a
                                        href="{{ route('admin.loan.applications.documents.download', [$application, $document]) }}"
                                        class="inline-flex min-h-9 shrink-0 items-center gap-2 rounded-lg border border-emerald-300/15 px-3 text-xs font-bold text-emerald-200 hover:bg-emerald-300/10"><i
                                            data-lucide="download" class="h-4 w-4"></i> Download</a>
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full rounded-xl border border-dashed border-emerald-300/15 p-10 text-center"><i
                                    data-lucide="file-question" class="mx-auto h-7 w-7 text-neutral-500"></i>
                                <p class="mt-2 text-sm font-bold text-white">No documents uploaded</p>
                                <p class="mt-1 text-xs text-neutral-400">Documents will appear here after submission.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-emerald-300/15 bg-[#041b12]/90">
                    <div class="border-b border-emerald-300/10 px-5 py-4">
                        <p class="text-[10px] font-black uppercase tracking-[.2em] text-emerald-300">Audit trail</p>
                        <h2 class="mt-1 text-lg font-black text-white">Status history</h2>
                    </div>
                    <div class="space-y-3 p-5">@forelse($application->statusHistories as $history)
                        <article class="flex gap-3 rounded-xl border border-white/[.07] bg-black/10 p-4"><span
                                class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full {{ $history->new_status === 'approved' ? 'bg-emerald-300' : ($history->new_status === 'rejected' ? 'bg-rose-300' : 'bg-sky-300') }}"></span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-2"><strong
                                        class="text-sm text-white">{{ $statusLabels[$history->new_status] ?? str($history->new_status)->headline() }}</strong><time
                                        class="text-xs text-neutral-500">{{ $history->created_at?->format('d M Y, h:i A') }}</time>
                                </div>
                                <p class="mt-1 text-xs text-neutral-400">{{ $history->remarks ?: 'Status updated' }} ·
                                    {{ $history->changedBy?->name ?? 'System' }}
                                </p>
                            </div>
                    </article>@empty<p class="text-sm text-neutral-400">No status history available.</p>@endforelse
                    </div>
                </section>
            </div>

            <aside class="h-fit space-y-5 xl:sticky xl:top-5">
                <section class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-5">
                    <p class="text-[10px] font-black uppercase tracking-[.2em] text-emerald-300">Admin review</p>
                    <h2 class="mt-1 text-lg font-black text-white">Review decision</h2>
                    <p class="mt-1 text-xs text-neutral-400">Update the current application status and add an internal
                        remark.</p>
                    <form method="POST" action="{{ route('admin.loan.applications.status', $application) }}"
                        class="mt-5 space-y-4">@csrf
                        <label class="block space-y-2 text-xs font-bold text-neutral-300">Application status<select
                                name="status" required
                                class="min-h-11 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50">@foreach($statusLabels as $value => $label)
                                    <option value="{{ $value }}" @selected($application->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select></label>
                        <label class="block space-y-2 text-xs font-bold text-neutral-300">Admin remarks<textarea
                                name="admin_remarks" rows="6" placeholder="Add a review note or decision reason"
                                class="w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 py-3 text-sm text-white placeholder:text-neutral-500 outline-none focus:border-emerald-300/50">{{ old('admin_remarks', $application->admin_remarks) }}</textarea></label>
                        <button
                            class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-400 to-teal-300 px-5 text-sm font-black text-emerald-950"><i
                                data-lucide="save" class="h-4 w-4"></i> Save review</button>
                    </form>
                </section>
                <section class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-5">
                    <p class="text-[10px] font-black uppercase tracking-[.2em] text-emerald-300">Submission</p>
                    <dl class="mt-3 space-y-3">
                        <div class="flex justify-between gap-3 text-sm">
                            <dt class="text-neutral-400">Declaration</dt>
                            <dd class="font-semibold text-neutral-200">
                                {{ $application->declaration_accepted ? 'Accepted' : 'Not accepted' }}
                            </dd>
                        </div>
                        <div class="flex justify-between gap-3 text-sm">
                            <dt class="text-neutral-400">Created</dt>
                            <dd class="text-right text-neutral-200">{{ $application->created_at?->format('d M Y, h:i A') }}
                            </dd>
                        </div>
                        <div class="flex justify-between gap-3 text-sm">
                            <dt class="text-neutral-400">Last updated</dt>
                            <dd class="text-right text-neutral-200">{{ $application->updated_at?->format('d M Y, h:i A') }}
                            </dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>
        </main>
@endsection