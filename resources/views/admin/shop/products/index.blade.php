@extends('admin.layouts.app')

@section('title', 'Shop Products - ZIVO PAY')

@section('content')
<div class="w-full space-y-6 font-sans">
    <header class="flex flex-col justify-between gap-5 rounded-3xl border-2 border-emerald-500/60 bg-[#042718] p-6 shadow-[0_0_35px_rgba(16,185,129,0.18)] sm:p-8 lg:flex-row lg:items-center">
        <div>
            <div class="mb-1 flex items-center gap-2"><span class="rounded-full border border-emerald-500/40 bg-emerald-500/20 px-2.5 py-0.5 text-[10px] font-extrabold uppercase text-emerald-400">Shop</span><span class="text-xs font-black uppercase tracking-[3px] text-emerald-400">E-commerce management</span></div>
            <h1 class="font-heading text-2xl font-black text-white sm:text-3xl">PRODUCT CATALOG</h1>
            <p class="mt-1 text-xs text-neutral-300">Manage product details, pricing, stock and visibility in the app.</p>
        </div>
        <a href="{{ route('admin.shop.products.create') }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 px-5 py-3 text-xs font-black uppercase tracking-wider text-black shadow-lg transition hover:scale-[1.02]"><i data-lucide="package-plus" class="h-4 w-4"></i>Add new product</a>
    </header>

    @if(session('success'))<div class="flex items-center gap-2 rounded-xl border border-emerald-500/50 bg-emerald-500/15 p-4 text-xs font-bold text-emerald-300"><i data-lucide="check-circle" class="h-4 w-4"></i>{{ session('success') }}</div>@endif

    <section class="space-y-5 rounded-3xl border border-emerald-500/30 bg-[#042718] p-4 shadow-2xl sm:p-6">
        <div class="flex flex-col justify-between gap-4 border-b border-emerald-500/20 pb-4 xl:flex-row xl:items-center">
            <nav class="flex flex-wrap items-center gap-2" aria-label="Product visibility filters">
                @php($filters = request()->except(['status', 'page']))
                <a href="{{ route('admin.shop.products.index', $filters) }}" class="rounded-xl px-4 py-2 text-xs font-bold transition {{ !request('status') ? 'bg-emerald-500 text-black shadow-md' : 'border border-emerald-500/30 bg-[#01140c] text-neutral-300 hover:text-emerald-300' }}">All products</a>
                <a href="{{ route('admin.shop.products.index', array_merge($filters, ['status' => 'active'])) }}" class="rounded-xl px-4 py-2 text-xs font-bold transition {{ request('status') === 'active' ? 'bg-emerald-500/20 text-emerald-300 ring-1 ring-emerald-400/50' : 'border border-emerald-500/30 bg-[#01140c] text-neutral-300 hover:text-emerald-300' }}">Active</a>
                <a href="{{ route('admin.shop.products.index', array_merge($filters, ['status' => 'inactive'])) }}" class="rounded-xl px-4 py-2 text-xs font-bold transition {{ request('status') === 'inactive' ? 'bg-rose-500/20 text-rose-300 ring-1 ring-rose-400/40' : 'border border-emerald-500/30 bg-[#01140c] text-neutral-300 hover:text-rose-300' }}">Hidden</a>
            </nav>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <span class="font-mono text-xs text-neutral-400">Total products: <strong class="text-emerald-400">{{ $products->total() }}</strong></span>
                <form data-shop-live-filter action="{{ route('admin.shop.products.index') }}" method="GET" class="flex gap-2">
                    @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                    <label class="relative block min-w-0 flex-1 sm:w-64"><i data-lucide="search" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-emerald-400"></i><input name="search" value="{{ request('search') }}" placeholder="Search name or SKU..." class="w-full rounded-xl border border-emerald-500/40 bg-[#01140c] py-2.5 pl-9 pr-3 text-xs font-semibold text-white placeholder:text-neutral-500 focus:border-emerald-400 focus:outline-none"></label>
                    <button class="rounded-xl border border-emerald-500/40 bg-emerald-500/20 px-4 py-2.5 text-xs font-bold text-emerald-300 transition hover:bg-emerald-500/30">Search</button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1050px] text-left text-xs text-neutral-300">
                <thead class="border-b border-emerald-500/30 bg-[#02180f] text-[10px] font-bold uppercase tracking-wide text-emerald-400"><tr><th class="p-3.5">Product details</th><th class="p-3.5">Category</th><th class="p-3.5">Price / MRP</th><th class="p-3.5">Stock</th><th class="p-3.5">Highlights</th><th class="p-3.5">Visibility</th><th class="p-3.5 text-right">Actions</th></tr></thead>
                <tbody class="divide-y divide-emerald-500/15">
                    @forelse($products as $product)
                        <tr class="transition hover:bg-emerald-500/5">
                            <td class="max-w-sm p-3.5"><div class="flex items-center gap-3">@if($product->image_paths && count($product->image_paths))<img src="{{ asset('storage/'.$product->image_paths[0]) }}" class="h-12 w-12 shrink-0 rounded-xl border border-emerald-500/20 object-cover" alt="{{ $product->name }}">@else<div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-emerald-500/10 text-emerald-300"><i data-lucide="image" class="h-5 w-5"></i></div>@endif<div class="min-w-0"><p class="truncate text-sm font-bold text-white">{{ $product->name }}</p><p class="text-[10px] text-neutral-500">SKU: {{ $product->sku }}</p></div></div></td>
                            <td class="p-3.5"><span class="rounded-lg border border-emerald-500/20 bg-[#01140c] px-2.5 py-1.5 text-[11px] text-neutral-200">{{ $product->category->name }}</span></td>
                            <td class="p-3.5"><span class="font-mono text-sm font-bold text-emerald-300">₹{{ number_format((float) $product->price, 2) }}</span><span class="mt-0.5 block font-mono text-[10px] text-neutral-500 line-through">₹{{ number_format((float) $product->mrp, 2) }}</span></td>
                            <td class="p-3.5"><span class="font-mono font-bold {{ $product->stock > 0 ? 'text-white' : 'text-rose-300' }}">{{ number_format($product->stock) }}</span><span class="ml-1 text-[10px] text-neutral-500">{{ $product->stock > 0 ? 'in stock' : 'sold out' }}</span></td>
                            <td class="p-3.5"><div class="flex flex-wrap gap-1.5">@if($product->is_featured)<span class="rounded-full border border-sky-400/30 bg-sky-500/10 px-2 py-1 text-[9px] font-bold uppercase text-sky-300">Featured</span>@endif @if($product->is_flash_deal)<span class="rounded-full border border-amber-400/30 bg-amber-500/10 px-2 py-1 text-[9px] font-bold uppercase text-amber-300">Flash deal</span>@endif @if(! $product->is_featured && ! $product->is_flash_deal)<span class="text-neutral-500">—</span>@endif</div></td>
                            <td class="p-3.5"><span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[10px] font-black uppercase {{ $product->is_active ? 'border-emerald-500/40 bg-emerald-500/15 text-emerald-300' : 'border-neutral-600 bg-neutral-700/40 text-neutral-300' }}"><span class="h-1.5 w-1.5 rounded-full {{ $product->is_active ? 'bg-emerald-400' : 'bg-neutral-400' }}"></span>{{ $product->is_active ? 'Active' : 'Hidden' }}</span></td>
                            <td class="p-3.5 text-right"><div class="inline-flex items-center gap-2"><a href="{{ route('admin.shop.products.edit', $product) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 font-bold text-emerald-300 transition hover:bg-emerald-500/20"><i data-lucide="pencil" class="h-3.5 w-3.5"></i>Edit</a><form method="POST" action="{{ route('admin.shop.products.destroy', $product) }}" onsubmit="return confirm('Remove this product from the shop?')">@csrf @method('DELETE')<button class="inline-flex items-center gap-1.5 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 font-bold text-rose-300 transition hover:bg-rose-500/20"><i data-lucide="archive" class="h-3.5 w-3.5"></i>Remove</button></form></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-12 text-center"><div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-2xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-300"><i data-lucide="package-search" class="h-5 w-5"></i></div><p class="font-bold text-white">No products found</p><p class="mt-1 text-xs text-neutral-400">Add a product or change your search filters.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex flex-col gap-3 border-t border-emerald-500/15 pt-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-neutral-400">Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</p>
            {{ $products->links() }}
        </div>
    </section>
</div>
@include('admin.shop.partials.realtime-validation')
@endsection
