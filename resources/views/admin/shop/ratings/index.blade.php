@extends('admin.layouts.app')

@section('title', 'Shop Ratings - ZIVO PAY')

@section('content')
<div class="w-full space-y-6 font-sans">
    <header class="flex flex-col justify-between gap-4 rounded-3xl border-2 border-emerald-500/50 bg-[#042718] p-6 shadow-[0_0_30px_rgba(16,185,129,0.14)] sm:flex-row sm:items-center">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[3px] text-emerald-400">E-commerce management</p>
            <h1 class="mt-1 text-2xl font-black text-white">PRODUCT RATINGS</h1>
            <p class="mt-1 text-xs text-neutral-300">Review and manage customer ratings and feedback.</p>
        </div>
        <form method="GET" action="{{ route('admin.shop.ratings.index') }}" class="flex gap-2">
            <input name="search" value="{{ request('search') }}" placeholder="Search product or customer..." class="w-full rounded-xl border border-emerald-500/40 bg-[#01140c] px-3 py-2.5 text-xs text-white placeholder:text-neutral-500 focus:border-emerald-400 focus:outline-none sm:w-64">
            <button class="rounded-xl border border-emerald-500/40 bg-emerald-500/20 px-4 py-2.5 text-xs font-bold text-emerald-300 hover:bg-emerald-500/30">Search</button>
        </form>
    </header>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 p-3 text-sm font-semibold text-emerald-300">{{ session('success') }}</div>
    @endif

    <section class="space-y-4 rounded-3xl border border-emerald-500/30 bg-[#042718] p-4 shadow-xl sm:p-6">
        <div class="flex items-center justify-between text-xs text-neutral-400">
            <span>Customer reviews</span>
            <span>Total ratings: <strong class="text-emerald-300">{{ $ratings->total() }}</strong></span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left text-xs text-neutral-300">
                <thead class="border-b border-emerald-500/30 bg-[#02180f] text-[10px] font-bold uppercase tracking-wide text-emerald-400">
                    <tr><th class="p-3">Product</th><th class="p-3">Customer</th><th class="p-3">Rating</th><th class="p-3">Review</th><th class="p-3">Date</th><th class="p-3 text-right">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-emerald-500/15">
                    @forelse($ratings as $rating)
                        <tr class="hover:bg-emerald-500/5">
                            <td class="p-3 font-semibold text-white">{{ $rating->product?->name ?? 'Product removed' }}</td>
                            <td class="p-3"><p class="font-semibold text-white">{{ $rating->user?->name ?? 'User removed' }}</p><p class="mt-1 text-[10px] text-neutral-500">{{ $rating->user?->email ?? $rating->user?->mobile }}</p></td>
                            <td class="p-3"><span class="font-black text-amber-300">{{ str_repeat('★', $rating->rating) }}{{ str_repeat('☆', 5 - $rating->rating) }}</span><span class="ml-1 text-neutral-400">{{ $rating->rating }}/5</span></td>
                            <td class="max-w-md p-3 text-neutral-300">{{ $rating->review ?: 'No written review' }}</td>
                            <td class="p-3 whitespace-nowrap text-neutral-400">{{ $rating->created_at?->format('d M Y') }}</td>
                            <td class="p-3 text-right"><div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.shop.ratings.edit', $rating) }}" class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 font-bold text-emerald-300 hover:bg-emerald-500/20">Edit</a>
                                <form method="POST" action="{{ route('admin.shop.ratings.destroy', $rating) }}" onsubmit="return confirm('Delete this rating?')">@csrf @method('DELETE')<button class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 font-bold text-rose-300 hover:bg-rose-500/20">Delete</button></form>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-12 text-center text-neutral-400">No product ratings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex flex-col gap-3 border-t border-emerald-500/15 pt-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-neutral-400">Showing {{ $ratings->firstItem() ?? 0 }}–{{ $ratings->lastItem() ?? 0 }} of {{ $ratings->total() }} ratings</p>
            {{ $ratings->links() }}
        </div>
    </section>
</div>
@endsection
