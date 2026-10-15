@extends('admin.layouts.app')

@section('title', 'Shop Promo Codes - ZIVO PAY')

@section('content')
<div class="w-full space-y-6 font-sans">
    <header class="flex flex-col justify-between gap-5 rounded-3xl border-2 border-emerald-500/60 bg-[#042718] p-6 shadow-[0_0_35px_rgba(16,185,129,0.18)] sm:p-8 lg:flex-row lg:items-center">
        <div><div class="mb-1 flex items-center gap-2"><span class="rounded-full border border-emerald-500/40 bg-emerald-500/20 px-2.5 py-0.5 text-[10px] font-extrabold uppercase text-emerald-400">Shop</span><span class="text-xs font-black uppercase tracking-[3px] text-emerald-400">E-commerce management</span></div><h1 class="font-heading text-2xl font-black text-white sm:text-3xl">PROMO CODE MANAGEMENT</h1><p class="mt-1 text-xs text-neutral-300">Create and manage discount codes for Shop customers.</p></div>
        <a href="{{ route('admin.shop.promos.create') }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 px-5 py-3 text-xs font-black uppercase tracking-wider text-black shadow-lg transition hover:scale-[1.02]"><i data-lucide="ticket-plus" class="h-4 w-4"></i>Add promo code</a>
    </header>

    @if(session('success'))<div class="flex items-center gap-2 rounded-xl border border-emerald-500/50 bg-emerald-500/15 p-4 text-xs font-bold text-emerald-300"><i data-lucide="check-circle" class="h-4 w-4"></i>{{ session('success') }}</div>@endif

    <section class="space-y-5 rounded-3xl border border-emerald-500/30 bg-[#042718] p-4 shadow-2xl sm:p-6">
        <div class="flex flex-col justify-between gap-4 border-b border-emerald-500/20 pb-4 xl:flex-row xl:items-center">
            <nav class="flex flex-wrap items-center gap-2" aria-label="Promo visibility filters">
                @php($filters = request()->except(['status', 'page']))
                <a href="{{ route('admin.shop.promos.index', $filters) }}" class="rounded-xl px-4 py-2 text-xs font-bold transition {{ !request('status') ? 'bg-emerald-500 text-black shadow-md' : 'border border-emerald-500/30 bg-[#01140c] text-neutral-300 hover:text-emerald-300' }}">All promos</a>
                <a href="{{ route('admin.shop.promos.index', array_merge($filters, ['status' => 'active'])) }}" class="rounded-xl px-4 py-2 text-xs font-bold transition {{ request('status') === 'active' ? 'bg-emerald-500/20 text-emerald-300 ring-1 ring-emerald-400/50' : 'border border-emerald-500/30 bg-[#01140c] text-neutral-300 hover:text-emerald-300' }}">Active</a>
                <a href="{{ route('admin.shop.promos.index', array_merge($filters, ['status' => 'inactive'])) }}" class="rounded-xl px-4 py-2 text-xs font-bold transition {{ request('status') === 'inactive' ? 'bg-rose-500/20 text-rose-300 ring-1 ring-rose-400/40' : 'border border-emerald-500/30 bg-[#01140c] text-neutral-300 hover:text-rose-300' }}">Hidden</a>
            </nav>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <span class="font-mono text-xs text-neutral-400">Total promo codes: <strong class="text-emerald-400">{{ $promos->total() }}</strong></span>
                <form data-shop-live-filter action="{{ route('admin.shop.promos.index') }}" method="GET" class="flex gap-2">
                    @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                    <label class="relative block min-w-0 flex-1 sm:w-64"><i data-lucide="search" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-emerald-400"></i><input name="search" value="{{ request('search') }}" placeholder="Search code or description..." class="w-full rounded-xl border border-emerald-500/40 bg-[#01140c] py-2.5 pl-9 pr-3 text-xs font-semibold text-white placeholder:text-neutral-500 focus:border-emerald-400 focus:outline-none"></label>
                    <button class="rounded-xl border border-emerald-500/40 bg-emerald-500/20 px-4 py-2.5 text-xs font-bold text-emerald-300 transition hover:bg-emerald-500/30">Search</button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] text-left text-xs text-neutral-300">
                <thead class="border-b border-emerald-500/30 bg-[#02180f] text-[10px] font-bold uppercase tracking-wide text-emerald-400"><tr><th class="p-3.5">Promo code</th><th class="p-3.5">Discount</th><th class="p-3.5">Minimum cart</th><th class="p-3.5">Usage</th><th class="p-3.5">Valid period</th><th class="p-3.5">Status</th><th class="p-3.5 text-right">Actions</th></tr></thead>
                <tbody class="divide-y divide-emerald-500/15">
                    @forelse($promos as $promo)
                        <tr class="transition hover:bg-emerald-500/5">
                            <td class="max-w-sm p-3.5"><div class="flex items-center gap-3"><div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-emerald-500/25 bg-emerald-500/10 text-emerald-300"><i data-lucide="ticket-percent" class="h-4 w-4"></i></div><div class="min-w-0"><p class="font-mono text-sm font-black tracking-wide text-white">{{ $promo->code }}</p><p class="truncate text-[10px] text-neutral-400">{{ $promo->description ?: 'No description added' }}</p></div></div></td>
                            <td class="p-3.5"><span class="inline-flex rounded-lg border border-amber-500/30 bg-amber-500/10 px-2.5 py-1.5 font-mono font-bold text-amber-300">{{ $promo->discount_type === 'percent' ? rtrim(rtrim(number_format((float) $promo->discount_value, 2), '0'), '.').'%' : '₹'.number_format((float) $promo->discount_value, 2) }}</span>@if($promo->maximum_discount)<span class="mt-1 block text-[10px] text-neutral-500">Max ₹{{ number_format((float) $promo->maximum_discount, 2) }}</span>@endif</td>
                            <td class="p-3.5 font-mono text-white">₹{{ number_format((float) $promo->minimum_subtotal, 2) }}</td>
                            <td class="p-3.5"><p class="font-mono font-bold text-white">{{ number_format($promo->usage_count) }} <span class="font-normal text-neutral-500">/ {{ $promo->usage_limit ? number_format($promo->usage_limit) : '∞' }}</span></p><div class="mt-1.5 h-1.5 w-24 overflow-hidden rounded-full bg-[#01140c]"><div class="h-full rounded-full bg-emerald-400" style="width: {{ $promo->usage_limit ? min(100, ($promo->usage_count / $promo->usage_limit) * 100) : 0 }}%"></div></div></td>
                            <td class="p-3.5"><p class="text-white">{{ $promo->starts_at?->format('d M Y') ?? 'No start date' }}</p><p class="mt-1 text-[10px] text-neutral-500">to {{ $promo->ends_at?->format('d M Y') ?? 'No expiry' }}</p></td>
                            <td class="p-3.5"><span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[10px] font-black uppercase {{ $promo->is_active ? 'border-emerald-500/40 bg-emerald-500/15 text-emerald-300' : 'border-neutral-600 bg-neutral-700/40 text-neutral-300' }}"><span class="h-1.5 w-1.5 rounded-full {{ $promo->is_active ? 'bg-emerald-400' : 'bg-neutral-400' }}"></span>{{ $promo->is_active ? 'Active' : 'Hidden' }}</span></td>
                            <td class="p-3.5 text-right"><div class="inline-flex items-center gap-2"><a href="{{ route('admin.shop.promos.edit', $promo) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 font-bold text-emerald-300 transition hover:bg-emerald-500/20"><i data-lucide="pencil" class="h-3.5 w-3.5"></i>Edit</a><form method="POST" action="{{ route('admin.shop.promos.destroy', $promo) }}" onsubmit="return confirm('Delete this promo code?')">@csrf @method('DELETE')<button class="inline-flex items-center gap-1.5 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 font-bold text-rose-300 transition hover:bg-rose-500/20"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i>Delete</button></form></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-12 text-center"><div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-2xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-300"><i data-lucide="ticket" class="h-5 w-5"></i></div><p class="font-bold text-white">No promo codes found</p><p class="mt-1 text-xs text-neutral-400">Create a promo code or change your search filters.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex flex-col gap-3 border-t border-emerald-500/15 pt-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-neutral-400">Showing {{ $promos->firstItem() ?? 0 }}–{{ $promos->lastItem() ?? 0 }} of {{ $promos->total() }} promo codes</p>
            {{ $promos->links() }}
        </div>
    </section>
</div>
@include('admin.shop.partials.realtime-validation')
@endsection
