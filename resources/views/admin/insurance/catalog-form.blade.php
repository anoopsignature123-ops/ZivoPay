@extends('admin.layouts.app')

@php
    $titles = ['categories' => ['Category', 'Insurance category', 'tags'], 'companies' => ['Company', 'Insurance company', 'building-2'], 'products' => ['Product', 'Insurance product', 'shield-check'], 'product-types' => ['Product Type', 'Insurance product type', 'shapes']];
    [$singular, $entity, $icon] = $titles[$resource];
    $editing = $record->exists;
    $indexRoute = 'admin.insurance.'.$resource.'.index';
    $actionRoute = 'admin.insurance.'.$resource.'.'.($editing ? 'update' : 'store');
@endphp

@section('title', ($editing ? 'Edit ' : 'Add ').$singular.' - ZIVO PAY')

@section('content')
<main class="mx-auto w-full max-w-5xl space-y-6">
    <header class="flex flex-col justify-between gap-4 rounded-3xl border border-emerald-400/25 bg-gradient-to-br from-[#073622] via-[#05271b] to-[#03170f] p-6 sm:flex-row sm:items-center sm:p-8"><div><a href="{{ route($indexRoute) }}" class="text-xs font-bold text-emerald-300">← {{ $singular }} list</a><div class="mt-4 flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl border border-emerald-300/20 bg-emerald-300/10 text-emerald-200"><i data-lucide="{{ $icon }}" class="h-5 w-5"></i></span><div><p class="text-[10px] font-black uppercase tracking-[.2em] text-emerald-300">Insurance catalog</p><h1 class="mt-1 text-2xl font-black text-white">{{ $editing ? 'Edit '.$singular : 'Add '.$singular }}</h1></div></div></div><span class="w-fit rounded-full border border-emerald-300/15 bg-emerald-300/[.06] px-3 py-1.5 text-xs font-bold text-emerald-100">{{ $entity }}</span></header>
    @if(session('error'))<div role="alert" class="rounded-xl border border-rose-300/20 bg-rose-300/10 p-4 text-sm text-rose-100">{{ session('error') }}</div>@endif
    @if($errors->any())<div role="alert" class="rounded-xl border border-rose-300/20 bg-rose-300/10 p-4 text-sm text-rose-100"><p class="font-bold">Please review the form:</p><ul class="mt-2 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route($actionRoute, $editing ? $record : []) }}" enctype="multipart/form-data" class="space-y-6 rounded-3xl border border-emerald-300/15 bg-[#041b12]/95 p-5 shadow-2xl shadow-black/15 sm:p-8">
        @csrf @if($editing) @method('PUT') @endif
        @if(in_array($resource, ['companies', 'products'], true))<label class="block space-y-2"><span class="text-xs font-bold uppercase tracking-wider text-emerald-100/70">Category <span class="text-rose-300">*</span></span><select name="insurance_category_id" required id="insurance-category" class="min-h-12 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-4 text-sm text-white outline-none focus:border-emerald-300/60"><option value="">Select category</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string)old('insurance_category_id', $record->insurance_category_id) === (string)$category->id)>{{ $category->name }}</option>@endforeach</select>@error('insurance_category_id')<span class="text-xs text-rose-300">{{ $message }}</span>@enderror</label>@elseif($resource === 'product-types')<label class="block space-y-2"><span class="text-xs font-bold uppercase tracking-wider text-emerald-100/70">Category <span class="text-rose-300">*</span></span><select name="insurance_category_id" required id="insurance-category" class="min-h-12 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-4 text-sm text-white outline-none focus:border-emerald-300/60"><option value="">Select category</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string)old('insurance_category_id', $record->insurance_category_id) === (string)$category->id)>{{ $category->name }}</option>@endforeach</select>@error('insurance_category_id')<span class="text-xs text-rose-300">{{ $message }}</span>@enderror</label>@endif
        @if($resource === 'products')<label class="block space-y-2"><span class="text-xs font-bold uppercase tracking-wider text-emerald-100/70">Company <span class="text-rose-300">*</span></span><select name="insurance_company_id" required id="insurance-company" class="min-h-12 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-4 text-sm text-white outline-none focus:border-emerald-300/60"><option value="">Select company</option>@foreach($companies as $company)<option value="{{ $company->id }}" data-category="{{ $company->insurance_category_id }}" @selected((string)old('insurance_company_id', $record->insurance_company_id) === (string)$company->id)>{{ $company->name }}{{ $company->category ? ' · '.$company->category->name : '' }}</option>@endforeach</select>@error('insurance_company_id')<span class="text-xs text-rose-300">{{ $message }}</span>@enderror</label>@endif
        @if($resource === 'product-types')<div class="grid gap-5 sm:grid-cols-2"><label class="block space-y-2"><span class="text-xs font-bold uppercase tracking-wider text-emerald-100/70">Company <span class="text-rose-300">*</span></span><select name="insurance_company_id" required id="insurance-company" class="min-h-12 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-4 text-sm text-white outline-none focus:border-emerald-300/60"><option value="">Select company</option>@foreach($companies as $company)<option value="{{ $company->id }}" data-category="{{ $company->insurance_category_id }}" @selected((string)old('insurance_company_id', $record->insurance_company_id) === (string)$company->id)>{{ $company->name }} · {{ $company->category?->name }}</option>@endforeach</select>@error('insurance_company_id')<span class="text-xs text-rose-300">{{ $message }}</span>@enderror</label><label class="block space-y-2"><span class="text-xs font-bold uppercase tracking-wider text-emerald-100/70">Product <span class="text-rose-300">*</span></span><select name="insurance_product_id" required id="insurance-product" class="min-h-12 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-4 text-sm text-white outline-none focus:border-emerald-300/60"><option value="">Select product</option>@foreach($products as $product)<option value="{{ $product->id }}" data-category="{{ $product->insurance_category_id }}" data-company="{{ $product->insurance_company_id }}" @selected((string)old('insurance_product_id', $record->insurance_product_id) === (string)$product->id)>{{ $product->name }} · {{ $product->company?->name }}</option>@endforeach</select>@error('insurance_product_id')<span class="text-xs text-rose-300">{{ $message }}</span>@enderror</label></div>@endif
        <div class="grid gap-5 sm:grid-cols-2"><label class="block space-y-2"><span class="text-xs font-bold uppercase tracking-wider text-emerald-100/70">{{ $singular }} name <span class="text-rose-300">*</span></span><input name="name" required maxlength="191" value="{{ old('name', $record->name) }}" placeholder="Enter {{ strtolower($singular) }} name" class="min-h-12 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-4 text-sm text-white placeholder:text-neutral-500 outline-none focus:border-emerald-300/60">@error('name')<span class="text-xs text-rose-300">{{ $message }}</span>@enderror</label><label class="block space-y-2"><span class="text-xs font-bold uppercase tracking-wider text-emerald-100/70">Slug <span class="font-normal normal-case tracking-normal text-neutral-500">(optional, generated if blank)</span></span><input name="slug" maxlength="191" value="{{ old('slug', $record->slug) }}" placeholder="example-slug" class="min-h-12 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-4 text-sm text-white placeholder:text-neutral-500 outline-none focus:border-emerald-300/60">@error('slug')<span class="text-xs text-rose-300">{{ $message }}</span>@enderror</label></div>
        <label class="block space-y-2"><span class="text-xs font-bold uppercase tracking-wider text-emerald-100/70">Description</span><textarea name="description" rows="4" maxlength="5000" placeholder="Add a short description" class="w-full rounded-xl border border-emerald-300/15 bg-[#02120b] p-4 text-sm text-white placeholder:text-neutral-500 outline-none focus:border-emerald-300/60">{{ old('description', $record->description) }}</textarea>@error('description')<span class="text-xs text-rose-300">{{ $message }}</span>@enderror</label>
        <div class="grid gap-5 sm:grid-cols-2"><label class="block space-y-2"><span class="text-xs font-bold uppercase tracking-wider text-emerald-100/70">Display order</span><input type="number" name="display_order" min="0" max="4294967295" value="{{ old('display_order', $record->display_order ?? 0) }}" class="min-h-12 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-4 text-sm text-white outline-none focus:border-emerald-300/60">@error('display_order')<span class="text-xs text-rose-300">{{ $message }}</span>@enderror</label><div class="space-y-2"><span class="block text-xs font-bold uppercase tracking-wider text-emerald-100/70">Image <span class="font-normal normal-case tracking-normal text-neutral-500">(JPG, PNG or WebP · up to 4 MB)</span></span><div class="flex items-center gap-4"><label class="inline-flex min-h-11 cursor-pointer items-center gap-2 rounded-xl border border-emerald-300/20 bg-emerald-300/[.06] px-4 text-sm font-bold text-emerald-100"><i data-lucide="upload" class="h-4 w-4"></i>Choose image<input type="file" name="image" accept="image/jpeg,image/png,image/webp" data-image-input class="sr-only"></label><div class="grid h-14 w-14 place-items-center overflow-hidden rounded-xl border border-emerald-300/20 bg-black/20">@if($record->image)<img data-image-preview src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($record->image) }}" alt="Current image" class="h-full w-full object-cover">@else<img data-image-preview alt="Image preview" class="hidden h-full w-full object-cover"><i data-lucide="image" data-image-placeholder class="h-5 w-5 text-neutral-500"></i>@endif</div></div>@error('image')<span class="text-xs text-rose-300">{{ $message }}</span>@enderror</div></div>
        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-emerald-300/15 bg-emerald-300/[.04] p-4"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $record->exists ? $record->is_active : true)) class="h-4 w-4 rounded border-emerald-300/30 bg-[#02120b] text-emerald-400 focus:ring-emerald-400"><span><span class="block text-sm font-bold text-white">Active</span><span class="mt-0.5 block text-xs text-neutral-400">Active {{ strtolower($singular) }} is available in the insurance catalog.</span></span></label>
        <div class="flex flex-col-reverse gap-3 border-t border-emerald-300/10 pt-5 sm:flex-row sm:justify-end"><a href="{{ route($indexRoute) }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-white/10 px-5 text-sm font-bold text-neutral-300">Cancel</a><button class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-400 to-teal-300 px-6 text-sm font-black text-emerald-950"><i data-lucide="save" class="h-4 w-4"></i>{{ $editing ? 'Save changes' : 'Create '.$singular }}</button></div>
    </form>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const category = document.getElementById('insurance-category');
    const company = document.getElementById('insurance-company');
    const product = document.getElementById('insurance-product');
    if (category && company) {
        const syncCompanies = () => {
            [...company.options].forEach((option) => {
                if (!option.value) return;
                option.hidden = category.value !== '' && option.dataset.category !== category.value;
                if (option.hidden && option.selected) company.value = '';
            });
        };
        category.addEventListener('change', syncCompanies);
        syncCompanies();
    }
    const syncProducts = () => {
        if (!product) return;
        [...product.options].forEach((option) => {
            if (!option.value) return;
            const categoryMatches = !category?.value || option.dataset.category === category.value;
            const companyMatches = !company?.value || option.dataset.company === company.value;
            option.hidden = !categoryMatches || !companyMatches;
            if (option.hidden && option.selected) product.value = '';
        });
    };
    company?.addEventListener('change', syncProducts);
    category?.addEventListener('change', syncProducts);
    syncProducts();
    const input = document.querySelector('[data-image-input]');
    const preview = document.querySelector('[data-image-preview]');
    const placeholder = document.querySelector('[data-image-placeholder]');
    input?.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!file || !preview) return;
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        placeholder?.classList.add('hidden');
    });
});
</script>
@endpush
