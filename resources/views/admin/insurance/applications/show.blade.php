@extends('admin.layouts.app')

@php
    $statusLabels = [
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        'under_review' => 'Under review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];
    $statusStyles = [
        'draft' => 'border-slate-300/15 bg-slate-300/10 text-slate-200',
        'submitted' => 'border-sky-300/15 bg-sky-300/10 text-sky-200',
        'under_review' => 'border-amber-300/15 bg-amber-300/10 text-amber-200',
        'approved' => 'border-emerald-300/15 bg-emerald-300/10 text-emerald-200',
        'rejected' => 'border-rose-300/15 bg-rose-300/10 text-rose-200',
    ];
    $applicationFields = [
        'Customer name' => $application->name,
        'Guardian / parent name' => $application->guardian_name,
        'Email address' => $application->email ?: $application->user?->email,
        'Mobile number' => $application->phone ?: $application->user?->mobile,
        'Date of birth' => $application->date_of_birth?->format('d M Y'),
        'Gender' => str($application->gender)->headline(),
        'Address' => $application->address,
        'District' => $application->district,
        'State' => $application->state,
        'PIN code' => $application->pin_code,
        'Declaration accepted' => $application->is_declaration_accepted ? 'Yes' : 'No',
    ];
    $specificationSections = [
        'Life insurance details' => $application->specifications,
        'Health insurance details' => $application->healthSpecifications,
        'Selected type details' => $application->generalSpecifications,
        'Coverage requirements' => $application->requirements,
        'Preferences' => $application->additions,
    ];
    $fieldLabels = [
        'personal_name' => 'Insured person / proposer name',
        'annual_income' => 'Annual income amount',
        'annual_income_range' => 'Annual income range',
        'nominee_relation' => 'Nominee relationship',
        'policy_term' => 'Policy term (years)',
        'sum_assured' => 'Sum assured',
        'is_smoker' => 'Smokes or uses tobacco',
        'plan_type' => 'Policy type',
        'number_of_dependents' => 'Members covered',
        'pre_existing_conditions' => 'Pre-existing medical conditions',
        'current_medications' => 'Current medications',
        'product_type_id' => 'Product type',
        'registration_number' => 'Vehicle registration / property ID',
        'registration_year' => 'Registration year',
        'manufacturing_year' => 'Manufacturing year',
        'engine_number' => 'Engine number',
        'chassis_number' => 'Chassis number',
        'fuel_type' => 'Fuel type',
        'vehicle_usage' => 'Vehicle usage',
        'seating_capacity' => 'Seating capacity',
        'load_capacity' => 'Load capacity',
        'rto_location' => 'RTO location',
        'permit_number' => 'Permit number',
        'fitness_certificate_expiry' => 'Fitness certificate expiry',
        'previous_policy_number' => 'Previous policy number',
        'previous_insurer' => 'Previous insurer',
        'previous_policy_start_date' => 'Previous policy start date',
        'renewal_date' => 'Renewal date',
        'claim_history' => 'Claim history',
        'ncb_percentage' => 'No claim bonus',
        'existing_policy_type' => 'Existing policy type',
        'insurance_type' => 'Insurance type',
        'property_number' => 'Property ID',
        'property_type' => 'Property type',
        'ownership_type' => 'Ownership type',
        'property_address' => 'Property address',
        'construction_year' => 'Construction year',
        'built_up_area' => 'Built-up area',
        'construction_type' => 'Construction type',
        'number_of_floors' => 'Number of floors',
        'building_value' => 'Building value',
        'contents_value' => 'Contents value',
        'occupancy_status' => 'Occupancy status',
        'fire_safety_details' => 'Fire safety details',
        'travel_type' => 'Travel type',
        'destination_country' => 'Destination country',
        'trip_type' => 'Trip type',
        'travel_start_date' => 'Travel start date',
        'travel_end_date' => 'Travel end date',
        'travellers_count' => 'Number of travellers',
        'passport_number' => 'Passport number',
        'visa_type' => 'Visa type',
        'travel_purpose' => 'Travel purpose',
        'employment_type' => 'Employment type',
        'risk_category' => 'Occupation risk category',
        'disability_coverage' => 'Disability coverage',
        'accidental_death_coverage' => 'Accidental death coverage',
        'coverage_amount' => 'Required coverage amount',
        'monthly_budget' => 'Preferred monthly budget',
        'coverage_duration' => 'Coverage duration (years)',
        'special_requirements' => 'Special requirements',
        'is_existing_policy' => 'Has an existing policy',
        'contact_time' => 'Preferred contact time',
        'language_preference' => 'Preferred language',
        'additional_remarks' => 'Additional remarks',
    ];
    $skipFields = ['id', 'application_detail_id', 'created_at', 'updated_at'];
    $currencyFields = ['annual_income', 'sum_assured', 'coverage_amount', 'monthly_budget'];
@endphp

@section('title', $application->application_number.' - Insurance Application')

@section('content')
<div class="mx-auto w-full max-w-[1500px] space-y-6">
    <header class="flex flex-col justify-between gap-5 rounded-3xl border border-emerald-400/20 bg-gradient-to-br from-[#073622] via-[#05271b] to-[#03170f] p-6 sm:flex-row sm:items-center sm:p-8">
        <div class="space-y-3">
            <a href="{{ route('admin.insurance.applications.index') }}" class="text-xs font-bold text-emerald-300 hover:text-emerald-200">← Application list</a>
            <p class="text-[10px] font-black uppercase tracking-[.22em] text-emerald-300">Insurance application · #{{ $application->id }}</p>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-black text-white sm:text-3xl">{{ $application->application_number }}</h1>
                <span class="rounded-full border px-3 py-1 text-xs font-bold {{ $statusStyles[$application->status] ?? $statusStyles['draft'] }}">{{ $statusLabels[$application->status] ?? str($application->status)->headline() }}</span>
            </div>
            <p class="text-sm text-emerald-50/65">Submitted {{ ($application->submitted_at ?? $application->created_at)?->format('d M Y, h:i A') ?? 'date unavailable' }}</p>
        </div>
        <a href="{{ route('admin.insurance.reports.index', ['search' => $application->application_number]) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-emerald-300/20 px-4 text-xs font-bold text-emerald-100 hover:bg-emerald-300/[.06]">
            <i data-lucide="chart-no-axes-combined" class="h-4 w-4"></i> Related report
        </a>
    </header>

    @if(session('success'))
        <div role="status" class="rounded-xl border border-emerald-300/20 bg-emerald-300/10 p-4 text-sm text-emerald-100">{{ session('success') }}</div>
    @endif

    <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1.7fr)_minmax(310px,.8fr)]">
        <div class="space-y-6">
            <section class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[.2em] text-emerald-300">Selected plan</p>
                        <h2 class="mt-1 text-xl font-black text-white">{{ $application->product?->name ?? 'Product unavailable' }}</h2>
                        <p class="mt-1 text-sm text-neutral-400">{{ $application->company?->name ?? 'Company unavailable' }} · {{ $application->category?->name ?? 'Category unavailable' }}</p>
                    </div>
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-emerald-300/15 bg-emerald-300/[.06] text-emerald-200"><i data-lucide="shield-check" class="h-5 w-5"></i></span>
                </div>
                <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-xl border border-white/[.07] p-3"><p class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Applicant</p><p class="mt-1 font-semibold text-neutral-100">{{ $application->name }}</p></div>
                    <div class="rounded-xl border border-white/[.07] p-3"><p class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Submitted by account</p><p class="mt-1 font-semibold text-neutral-100">{{ $application->user?->name ?? ($application->user_id ? 'User #'.$application->user_id : 'No linked user') }}</p><p class="mt-1 text-xs text-neutral-500">User ID: {{ $application->user_id ?? '—' }}</p></div>
                    <div class="rounded-xl border border-white/[.07] p-3"><p class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Policy term</p><p class="mt-1 font-semibold text-neutral-100">{{ $application->specifications?->policy_term ? $application->specifications->policy_term.' years' : '—' }}</p></div>
                    <div class="rounded-xl border border-white/[.07] p-3"><p class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Coverage amount</p><p class="mt-1 font-semibold text-neutral-100">{{ $application->requirements?->coverage_amount ? '₹'.number_format((float) $application->requirements->coverage_amount, 2) : ($application->specifications?->sum_assured ? '₹'.number_format((float) $application->specifications->sum_assured, 2) : '—') }}</p></div>
                </div>
            </section>

            <section class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-5 sm:p-6">
                <div class="mb-5"><p class="text-[10px] font-black uppercase tracking-[.2em] text-emerald-300">Applicant</p><h2 class="mt-1 text-lg font-black text-white">Personal information</h2></div>
                <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
                    @foreach($applicationFields as $label => $value)
                        @if(filled($value))
                            <div class="border-b border-white/[.06] pb-3"><dt class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">{{ $label }}</dt><dd class="mt-1 whitespace-pre-line break-words text-sm text-neutral-100">{{ $value }}</dd></div>
                        @endif
                    @endforeach
                </dl>
            </section>

            @foreach($specificationSections as $sectionTitle => $sectionModel)
                @if($sectionModel)
                    @php
                        $fields = collect($sectionModel->getAttributes())
                            ->except($skipFields)
                            ->reject(fn ($value) => blank($value));
                    @endphp
                    @if($fields->isNotEmpty())
                        <section class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-5 sm:p-6">
                            <div class="mb-5 flex items-center justify-between gap-3">
                                <div><p class="text-[10px] font-black uppercase tracking-[.2em] text-emerald-300">Submitted details</p><h2 class="mt-1 text-lg font-black text-white">{{ $sectionTitle }}</h2></div>
                                @if($sectionModel === $application->generalSpecifications && $sectionModel->productType)
                                    <span class="rounded-full border border-emerald-300/20 bg-emerald-300/[.07] px-3 py-1.5 text-xs font-bold text-emerald-100">{{ $sectionModel->productType->name }}</span>
                                @endif
                            </div>
                            <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
                                @foreach($fields as $field => $value)
                                    <div class="border-b border-white/[.06] pb-3">
                                        <dt class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">{{ $fieldLabels[$field] ?? str($field)->headline() }}</dt>
                                        <dd class="mt-1 whitespace-pre-line break-words text-sm text-neutral-100">
                                            @if($value instanceof \Illuminate\Support\Carbon)
                                                {{ $value->format('d M Y') }}
                                            @elseif(in_array($field, ['is_smoker', 'is_existing_policy'], true))
                                                {{ $value === 'yes' ? 'Yes' : 'No' }}
                                            @elseif(in_array($field, $currencyFields, true))
                                                ₹{{ number_format((float) $value, 2) }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>
                        </section>
                    @endif
                @endif
            @endforeach

            <section class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-5 sm:p-6">
                <div class="mb-4 flex items-center justify-between gap-3"><div><p class="text-[10px] font-black uppercase tracking-[.2em] text-emerald-300">Verification</p><h2 class="mt-1 text-lg font-black text-white">Submitted documents</h2><p class="mt-1 text-xs text-neutral-400">Preview uploaded documents here or download the originals.</p></div><span class="rounded-full bg-white/[.06] px-2.5 py-1 text-xs font-bold text-neutral-300">{{ $application->documents->count() }}</span></div>
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($application->documents as $document)
                    <article class="overflow-hidden rounded-xl border border-white/[.08] bg-[#02120b]">
                        <a href="{{ route('admin.insurance.applications.documents.preview', [$application, $document->id]) }}" target="_blank" rel="noopener" class="group block bg-black/20" aria-label="Open preview for {{ $document->original_name ?: str($document->document_type)->headline() }}">
                            <img src="{{ route('admin.insurance.applications.documents.preview', [$application, $document->id]) }}" alt="{{ str($document->document_type)->headline() }} document preview" loading="lazy" class="h-64 w-full object-contain p-2 transition group-hover:scale-[1.01]">
                        </a>
                        <div class="flex flex-col justify-between gap-3 border-t border-white/[.06] p-4 sm:flex-row sm:items-center">
                            <div class="min-w-0"><p class="truncate text-sm font-semibold text-white">{{ $document->original_name ?: str($document->document_type)->headline() }}</p><p class="mt-0.5 text-xs text-neutral-500">{{ str($document->document_type)->headline() }}{{ $document->mime_type ? ' · '.$document->mime_type : '' }}</p></div>
                            <a href="{{ route('admin.insurance.applications.documents.show', [$application, $document->id]) }}" class="inline-flex min-h-9 shrink-0 items-center justify-center gap-2 rounded-lg border border-emerald-300/15 px-3 text-xs font-bold text-emerald-200 hover:bg-emerald-300/10"><i data-lucide="download" class="h-4 w-4"></i> Download</a>
                        </div>
                    </article>
                @empty
                    <p class="rounded-xl border border-dashed border-white/10 px-4 py-8 text-center text-sm text-neutral-400">No documents were attached to this application.</p>
                @endforelse
                </div>
            </section>
        </div>

        <aside class="space-y-6 xl:sticky xl:top-5">
            <section class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/95 p-5 shadow-xl shadow-black/10">
                <div class="mb-4"><p class="text-[10px] font-black uppercase tracking-[.2em] text-emerald-300">Admin action</p><h2 class="mt-1 text-lg font-black text-white">Review application</h2><p class="mt-1 text-xs leading-5 text-neutral-400">Choose the review status and save an internal note.</p></div>
                <form method="POST" action="{{ route('admin.insurance.applications.status', $application) }}" class="space-y-4">
                    @csrf
                    <label class="block space-y-2"><span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Status</span><select name="status" required class="min-h-11 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50">@foreach($statusLabels as $status => $label)<option value="{{ $status }}" @selected(old('status', $application->status) === $status)>{{ $label }}</option>@endforeach</select>@error('status')<span class="text-xs text-rose-300">{{ $message }}</span>@enderror</label>
                    <label class="block space-y-2"><span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Admin remarks</span><textarea name="admin_remarks" rows="5" maxlength="5000" placeholder="Add a review note or reason for the decision" class="w-full rounded-xl border border-emerald-300/15 bg-[#02120b] p-3 text-sm text-white placeholder:text-neutral-500 outline-none focus:border-emerald-300/50">{{ old('admin_remarks', $application->admin_remarks) }}</textarea>@error('admin_remarks')<span class="text-xs text-rose-300">{{ $message }}</span>@enderror</label>
                    <button class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-400 to-teal-300 px-4 text-sm font-black text-emerald-950"><i data-lucide="save" class="h-4 w-4"></i> Save review</button>
                </form>
            </section>

            <section class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-5"><p class="text-[10px] font-black uppercase tracking-[.2em] text-emerald-300">Latest decision</p><p class="mt-2 whitespace-pre-line text-sm leading-6 text-neutral-200">{{ $application->admin_remarks ?: 'No review remarks have been added.' }}</p><p class="mt-4 border-t border-white/[.06] pt-3 text-xs text-neutral-500">{{ $application->approved_at ? 'Approved '.$application->approved_at->format('d M Y, h:i A') : 'Approval pending' }}</p></section>
        </aside>
    </div>
</div>
@endsection
