@extends('admin.layouts.app')

@php
    $isCategory = $resource === 'categories';
    $isCompany = $resource === 'companies';
    $isProduct = $resource === 'products';
    $isProductType = $resource === 'product-types';
    $createRoute = 'admin.insurance.'.$resource.'.create';
    $editRoute = 'admin.insurance.'.$resource.'.edit';
    $deleteRoute = 'admin.insurance.'.$resource.'.destroy';
@endphp

@section('title', $title.' - ZIVO PAY')

@section('content')
<main class="mx-auto w-full max-w-[1500px] space-y-6">
    <header class="flex flex-col justify-between gap-5 rounded-3xl border border-emerald-400/20 bg-gradient-to-br from-[#073622] via-[#05271b] to-[#03170f] p-6 sm:flex-row sm:items-center sm:p-8">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[.22em] text-emerald-300">Insurance management</p>
            <h1 class="mt-2 text-2xl font-black text-white sm:text-3xl">{{ $title }}</h1>
            <p class="mt-2 max-w-2xl text-sm text-emerald-50/65">{{ $description }}</p>
        </div>
        <a href="{{ route($createRoute) }}" class="inline-flex min-h-12 shrink-0 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-400 to-teal-300 px-5 text-sm font-black text-emerald-950 shadow-lg shadow-emerald-950/30 transition hover:brightness-110">
            <i data-lucide="plus" class="h-4 w-4"></i> Add {{ $singular }}
        </a>
    </header>

    @if(session('success'))<div role="status" class="rounded-xl border border-emerald-300/20 bg-emerald-300/10 p-4 text-sm text-emerald-100">{{ session('success') }}</div>@endif
    @if(session('error'))<div role="alert" class="rounded-xl border border-rose-300/20 bg-rose-300/10 p-4 text-sm text-rose-100">{{ session('error') }}</div>@endif

    <section class="grid gap-3 sm:grid-cols-3" aria-label="{{ $singular }} summary">
        @foreach([['Total records', $stats['total'], 'layers-3'], ['Active', $stats['active'], 'circle-check'], ['Inactive', $stats['inactive'], 'circle-pause']] as [$label, $value, $symbol])
            <article class="flex items-center gap-4 rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-4 sm:p-5">
                <span class="grid h-11 w-11 place-items-center rounded-xl border border-emerald-300/15 bg-emerald-300/[.07] text-emerald-200"><i data-lucide="{{ $symbol }}" class="h-5 w-5"></i></span>
                <div><p class="text-xs font-semibold text-neutral-400">{{ $label }}</p><p class="mt-1 text-2xl font-black text-white">{{ number_format($value) }}</p></div>
            </article>
        @endforeach
    </section>

    <form method="GET" action="{{ route('admin.insurance.'.$resource.'.index') }}" class="grid gap-3 rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-4 sm:grid-cols-2 lg:grid-cols-5">
        <label class="relative block"><span class="sr-only">Search {{ strtolower($title) }}</span><i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-500"></i><input type="search" name="search" value="{{ request('search') }}" placeholder="Search name or slug" class="min-h-11 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] pl-10 pr-3 text-sm text-white placeholder:text-neutral-500 outline-none focus:border-emerald-300/50"></label>
        @if(!$isCategory)<select name="category_id" aria-label="Filter by category" class="min-h-11 rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50"><option value="">All categories</option>@foreach($categories ?? [] as $category)<option value="{{ $category->id }}" @selected((string)request('category_id') === (string)$category->id)>{{ $category->name }}</option>@endforeach</select>@endif
        @if($isProduct || $isProductType)<select name="company_id" aria-label="Filter by company" class="min-h-11 rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50"><option value="">All companies</option>@foreach($companies ?? [] as $company)<option value="{{ $company->id }}" @selected((string)request('company_id') === (string)$company->id)>{{ $company->name }}</option>@endforeach</select>@endif
        @if($isProductType)<select name="product_id" aria-label="Filter by product" class="min-h-11 rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50"><option value="">All products</option>@foreach($products ?? [] as $product)<option value="{{ $product->id }}" @selected((string)request('product_id') === (string)$product->id)>{{ $product->name }}</option>@endforeach</select>@endif
        <select name="status" aria-label="Filter by status" class="min-h-11 rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50"><option value="">All statuses</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="inactive" @selected(request('status') === 'inactive')>Inactive</option></select>
        <div class="flex gap-2"><button class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl border border-emerald-300/25 bg-emerald-300/10 px-4 text-sm font-bold text-emerald-100 hover:bg-emerald-300/15"><i data-lucide="sliders-horizontal" class="h-4 w-4"></i> Apply filters</button><a href="{{ route('admin.insurance.'.$resource.'.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-white/10 px-4 text-sm font-bold text-neutral-300 hover:bg-white/5">Reset</a></div>
    </form>

    <section class="overflow-hidden rounded-2xl border border-emerald-300/15 bg-[#041b12]/90">
        <div class="flex items-center justify-between gap-3 border-b border-emerald-300/10 px-5 py-4"><div><h2 class="font-bold text-white">{{ $singular }} records</h2><p class="mt-1 text-xs text-neutral-400">{{ $records->total() }} total · 12 per page</p></div><span class="rounded-full border border-emerald-300/15 bg-emerald-300/[.06] px-3 py-1 text-xs font-bold text-emerald-200">{{ $records->total() }} {{ $records->total() === 1 ? 'record' : 'records' }}</span></div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left text-sm">
                <thead class="bg-emerald-300/[.04] text-[10px] font-black uppercase tracking-wider text-emerald-300"><tr>
                    <th class="px-5 py-3">{{ $singular }}</th>
                    @if(!$isCategory)<th class="px-4 py-3">Category</th>@endif
                    @if($isProduct || $isProductType)<th class="px-4 py-3">Company</th>@endif
                    @if($isProductType)<th class="px-4 py-3">Product</th>@endif
                    @if(!$isProductType)<th class="px-4 py-3">Related records</th>@endif
                    <th class="px-4 py-3">Order</th><th class="px-4 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-emerald-300/[.07]">
                @forelse($records as $record)
                    @php
                        $recordActive = (bool) $record->is_active;
                        $hasDependencies = $isCategory
                            ? ($record->companies_count > 0 || $record->products_count > 0 || $record->product_types_count > 0)
                            : ($isCompany ? ($record->products_count > 0 || $record->product_types_count > 0) : ($isProduct && $record->product_types_count > 0));
                    @endphp
                    <tr class="transition hover:bg-emerald-300/[.025]">
                        <td class="px-5 py-4"><div class="flex min-w-48 items-center gap-3">@if($record->image)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($record->image) }}" alt="" class="h-11 w-11 rounded-xl border border-emerald-300/15 object-cover">@else<span class="grid h-11 w-11 place-items-center rounded-xl border border-emerald-300/15 bg-emerald-300/[.06] text-emerald-200"><i data-lucide="{{ $icon }}" class="h-5 w-5"></i></span>@endif<div><p class="font-bold text-white">{{ $record->name }}</p><p class="mt-0.5 text-xs text-neutral-500">{{ $record->slug }}</p></div></div></td>
                        @if(!$isCategory)<td class="px-4 py-4 text-neutral-300">{{ $record->category?->name ?? 'All categories' }}</td>@endif
                        @if($isProduct || $isProductType)<td class="px-4 py-4 text-neutral-300">{{ $record->company?->name ?? 'All companies' }}</td>@endif
                        @if($isProductType)<td class="px-4 py-4 text-neutral-300">{{ $record->product?->name ?? 'All products' }}</td>@endif
                        @if(!$isProductType)<td class="px-4 py-4"><div class="flex flex-wrap gap-1.5 text-[11px]">@if($isCategory)<span class="rounded-lg bg-white/[.05] px-2 py-1 text-neutral-300">{{ $record->companies_count }} companies</span><span class="rounded-lg bg-white/[.05] px-2 py-1 text-neutral-300">{{ $record->products_count }} products</span><span class="rounded-lg bg-white/[.05] px-2 py-1 text-neutral-300">{{ $record->product_types_count }} types</span>@elseif($isCompany)<span class="rounded-lg bg-white/[.05] px-2 py-1 text-neutral-300">{{ $record->products_count }} products</span><span class="rounded-lg bg-white/[.05] px-2 py-1 text-neutral-300">{{ $record->product_types_count }} types</span>@else<span class="rounded-lg bg-white/[.05] px-2 py-1 text-neutral-300">{{ $record->product_types_count }} types</span>@endif</div></td>@endif
                        <td class="px-4 py-4 text-neutral-300">{{ $record->display_order }}</td><td class="px-4 py-4"><span class="rounded-full border px-2.5 py-1 text-[11px] font-bold {{ $recordActive ? 'border-emerald-300/20 bg-emerald-300/10 text-emerald-200' : 'border-neutral-400/15 bg-white/5 text-neutral-400' }}">{{ $recordActive ? 'Active' : 'Inactive' }}</span></td>
                        <td class="px-5 py-4"><div class="flex justify-end gap-2"><a href="{{ route($editRoute, $record) }}" aria-label="Edit {{ $record->name }}" class="grid h-9 w-9 place-items-center rounded-lg border border-emerald-300/15 text-emerald-200 hover:bg-emerald-300/10"><i data-lucide="pencil" class="h-4 w-4"></i></a><form method="POST" action="{{ route($deleteRoute, $record) }}" onsubmit="return confirm('Delete {{ addslashes($singular) }} ‘{{ addslashes($record->name) }}’? This cannot be undone.')">@csrf @method('DELETE')<button type="submit" aria-label="Delete {{ $record->name }}" @disabled($hasDependencies) title="{{ $hasDependencies ? 'Remove linked records before deleting' : 'Delete record' }}" class="grid h-9 w-9 place-items-center rounded-lg border border-rose-300/15 text-rose-200 hover:bg-rose-300/10 disabled:cursor-not-allowed disabled:opacity-35"><i data-lucide="trash-2" class="h-4 w-4"></i></button></form></div></td>
                    </tr>
                @empty
                    <tr><td colspan="{{ 4 + (!$isCategory ? 1 : 0) + (($isProduct || $isProductType) ? 1 : 0) + ($isProductType ? 1 : 0) + (!$isProductType ? 1 : 0) }}" class="px-5 py-16 text-center"><span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl border border-emerald-300/15 bg-emerald-300/[.05] text-emerald-200"><i data-lucide="inbox" class="h-5 w-5"></i></span><p class="mt-3 font-bold text-white">No {{ strtolower($title) }} found</p><p class="mt-1 text-sm text-neutral-400">Create a {{ strtolower($singular) }} or adjust your filters.</p><a href="{{ route($createRoute) }}" class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-emerald-300"><i data-lucide="plus" class="h-4 w-4"></i> Add {{ $singular }}</a></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())<div class="border-t border-emerald-300/10 px-5 py-4">{{ $records->links() }}</div>@endif
    </section>
</main>
@endsection
