@extends('admin.layouts.app')

@section('title', 'Shop Banners - ZIVO PAY')

@section('content')
<div class="w-full space-y-6 font-sans">
    <header class="flex flex-col justify-between gap-5 rounded-3xl border-2 border-emerald-500/60 bg-[#042718] p-6 shadow-[0_0_35px_rgba(16,185,129,0.18)] sm:p-8 lg:flex-row lg:items-center">
        <div><div class="mb-1 flex items-center gap-2"><span class="rounded-full border border-emerald-500/40 bg-emerald-500/20 px-2.5 py-0.5 text-[10px] font-extrabold uppercase text-emerald-400">Shop</span><span class="text-xs font-black uppercase tracking-[3px] text-emerald-400">E-commerce management</span></div><h1 class="font-heading text-2xl font-black text-white sm:text-3xl">HOME BANNER MANAGEMENT</h1><p class="mt-1 text-xs text-neutral-300">Manage the promotional banners shown in the Shop app.</p></div>
        <a href="{{ route('admin.shop.banners.create') }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 px-5 py-3 text-xs font-black uppercase tracking-wider text-black shadow-lg transition hover:scale-[1.02]"><i data-lucide="image-plus" class="h-4 w-4"></i>Add banner</a>
    </header>

    @if(session('success'))<div class="flex items-center gap-2 rounded-xl border border-emerald-500/50 bg-emerald-500/15 p-4 text-xs font-bold text-emerald-300"><i data-lucide="check-circle" class="h-4 w-4"></i>{{ session('success') }}</div>@endif

    <section class="space-y-5 rounded-3xl border border-emerald-500/30 bg-[#042718] p-4 shadow-2xl sm:p-6">
        <div class="flex flex-col justify-between gap-4 border-b border-emerald-500/20 pb-4 xl:flex-row xl:items-center">
            <nav class="flex flex-wrap items-center gap-2" aria-label="Banner visibility filters">
                @php($filters = request()->except(['status', 'page']))
                <a href="{{ route('admin.shop.banners.index', $filters) }}" class="rounded-xl px-4 py-2 text-xs font-bold transition {{ !request('status') ? 'bg-emerald-500 text-black shadow-md' : 'border border-emerald-500/30 bg-[#01140c] text-neutral-300 hover:text-emerald-300' }}">All banners</a>
                <a href="{{ route('admin.shop.banners.index', array_merge($filters, ['status' => 'active'])) }}" class="rounded-xl px-4 py-2 text-xs font-bold transition {{ request('status') === 'active' ? 'bg-emerald-500/20 text-emerald-300 ring-1 ring-emerald-400/50' : 'border border-emerald-500/30 bg-[#01140c] text-neutral-300 hover:text-emerald-300' }}">Active</a>
                <a href="{{ route('admin.shop.banners.index', array_merge($filters, ['status' => 'inactive'])) }}" class="rounded-xl px-4 py-2 text-xs font-bold transition {{ request('status') === 'inactive' ? 'bg-rose-500/20 text-rose-300 ring-1 ring-rose-400/40' : 'border border-emerald-500/30 bg-[#01140c] text-neutral-300 hover:text-rose-300' }}">Hidden</a>
            </nav>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <span class="font-mono text-xs text-neutral-400">Total banners: <strong class="text-emerald-400">{{ $banners->total() }}</strong></span>
                <form data-shop-live-filter action="{{ route('admin.shop.banners.index') }}" method="GET" class="flex gap-2">
                    @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                    <label class="relative block min-w-0 flex-1 sm:w-64"><i data-lucide="search" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-emerald-400"></i><input name="search" value="{{ request('search') }}" placeholder="Search banner title..." class="w-full rounded-xl border border-emerald-500/40 bg-[#01140c] py-2.5 pl-9 pr-3 text-xs font-semibold text-white placeholder:text-neutral-500 focus:border-emerald-400 focus:outline-none"></label>
                    <button class="rounded-xl border border-emerald-500/40 bg-emerald-500/20 px-4 py-2.5 text-xs font-bold text-emerald-300 transition hover:bg-emerald-500/30">Search</button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1050px] text-left text-xs text-neutral-300">
                <thead class="border-b border-emerald-500/30 bg-[#02180f] text-[10px] font-bold uppercase tracking-wide text-emerald-400"><tr><th class="p-3.5">Banner</th><th class="p-3.5">Target</th><th class="p-3.5">Schedule</th><th class="p-3.5">Display order</th><th class="p-3.5">Visibility</th><th class="p-3.5 text-right">Actions</th></tr></thead>
                <tbody class="divide-y divide-emerald-500/15">
                    @forelse($banners as $banner)
                        <tr class="transition hover:bg-emerald-500/5">
                            <td class="max-w-md p-3.5"><div class="flex items-center gap-3"><img src="{{ asset('storage/'.$banner->image_path) }}" class="h-14 w-24 shrink-0 rounded-xl border border-emerald-500/20 object-cover" alt="{{ $banner->title }}"><div class="min-w-0"><p class="truncate text-sm font-bold text-white">{{ $banner->title }}</p><p class="mt-1 line-clamp-1 text-[10px] text-neutral-400">{{ $banner->subtitle ?: 'No subtitle' }}</p></div></div></td>
                            <td class="max-w-xs p-3.5"><span class="block truncate text-neutral-300">{{ $banner->target_url ?: 'No target link' }}</span></td>
                            <td class="p-3.5"><p class="text-white">{{ $banner->starts_at?->format('d M Y, h:i A') ?? 'Always on' }}</p><p class="mt-1 text-[10px] text-neutral-500">Ends {{ $banner->ends_at?->format('d M Y, h:i A') ?? 'No end date' }}</p></td>
                            <td class="p-3.5"><span class="rounded-lg bg-[#01140c] px-2.5 py-1.5 font-mono font-bold text-white">{{ $banner->sort_order }}</span></td>
                            <td class="p-3.5"><span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[10px] font-black uppercase {{ $banner->is_active ? 'border-emerald-500/40 bg-emerald-500/15 text-emerald-300' : 'border-neutral-600 bg-neutral-700/40 text-neutral-300' }}"><span class="h-1.5 w-1.5 rounded-full {{ $banner->is_active ? 'bg-emerald-400' : 'bg-neutral-400' }}"></span>{{ $banner->is_active ? 'Active' : 'Hidden' }}</span></td>
                            <td class="p-3.5 text-right"><div class="inline-flex items-center gap-2"><a href="{{ route('admin.shop.banners.edit', $banner) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 font-bold text-emerald-300 transition hover:bg-emerald-500/20"><i data-lucide="pencil" class="h-3.5 w-3.5"></i>Edit</a><form method="POST" action="{{ route('admin.shop.banners.destroy', $banner) }}" onsubmit="return confirm('Delete this banner?')">@csrf @method('DELETE')<button class="inline-flex items-center gap-1.5 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 font-bold text-rose-300 transition hover:bg-rose-500/20"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i>Delete</button></form></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-12 text-center"><div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-2xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-300"><i data-lucide="images" class="h-5 w-5"></i></div><p class="font-bold text-white">No banners found</p><p class="mt-1 text-xs text-neutral-400">Add a banner or change your search filters.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex flex-col gap-3 border-t border-emerald-500/15 pt-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-neutral-400">Showing {{ $banners->firstItem() ?? 0 }}–{{ $banners->lastItem() ?? 0 }} of {{ $banners->total() }} banners</p>
            {{ $banners->links() }}
        </div>
    </section>
</div>
@include('admin.shop.partials.realtime-validation')
@endsection
