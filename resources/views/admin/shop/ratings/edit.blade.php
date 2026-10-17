@extends('admin.layouts.app')

@section('title', 'Edit Shop Rating - ZIVO PAY')

@section('content')
<div class="mx-auto max-w-2xl space-y-4">
    <header class="rounded-2xl border border-emerald-500/30 bg-[#042718] p-5">
        <p class="text-[10px] font-black uppercase tracking-[2px] text-emerald-400">Shop management</p>
        <h1 class="text-xl font-black text-white">Edit product rating</h1>
        <p class="mt-1 text-xs text-neutral-400">{{ $rating->product?->name ?? 'Product removed' }} · {{ $rating->user?->name ?? 'User removed' }}</p>
    </header>
    @if($errors->any())<div class="rounded-lg border border-rose-500/30 bg-rose-500/10 p-3 text-sm text-rose-200">Please correct the highlighted fields.</div>@endif
    <form method="POST" action="{{ route('admin.shop.ratings.update', $rating) }}" class="space-y-4 rounded-2xl border border-emerald-500/20 bg-[#042718] p-5">
        @csrf
        @method('PUT')
        <label class="block text-xs font-bold text-emerald-300">Rating
            <select required name="rating" class="mt-1 w-full rounded-lg border border-emerald-500/25 bg-[#01140c] px-3 py-2.5 text-white focus:border-emerald-400 focus:outline-none">
                @foreach(range(5, 1) as $value)
                    <option value="{{ $value }}" @selected((int) old('rating', $rating->rating) === $value)>{{ $value }} {{ $value === 1 ? 'star' : 'stars' }}</option>
                @endforeach
            </select>
            @error('rating')<span class="mt-1 block text-rose-300">{{ $message }}</span>@enderror
        </label>
        <label class="block text-xs font-bold text-emerald-300">Review <span class="font-normal text-neutral-500">(optional)</span>
            <textarea name="review" rows="5" maxlength="2000" placeholder="Customer feedback..." class="mt-1 w-full rounded-lg border border-emerald-500/25 bg-[#01140c] px-3 py-2.5 text-white placeholder:text-neutral-500 focus:border-emerald-400 focus:outline-none">{{ old('review', $rating->review) }}</textarea>
            @error('review')<span class="mt-1 block text-rose-300">{{ $message }}</span>@enderror
        </label>
        <div class="flex justify-end gap-2"><a href="{{ route('admin.shop.ratings.index') }}" class="rounded-lg border border-white/15 px-4 py-2 text-xs text-neutral-300 hover:bg-white/5">Cancel</a><button class="rounded-lg bg-emerald-400 px-5 py-2 text-xs font-black uppercase text-[#00140c] hover:bg-emerald-300">Save rating</button></div>
    </form>
</div>
@endsection
