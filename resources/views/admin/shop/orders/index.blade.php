@extends('admin.layouts.app')

@section('title', 'Shop Orders - ZIVO PAY')

@section('content')
<div class="w-full space-y-6 font-sans">
    <header class="flex flex-col justify-between gap-5 rounded-3xl border-2 border-emerald-500/60 bg-[#042718] p-6 shadow-[0_0_35px_rgba(16,185,129,0.18)] sm:p-8 lg:flex-row lg:items-center">
        <div><div class="mb-1 flex items-center gap-2"><span class="rounded-full border border-emerald-500/40 bg-emerald-500/20 px-2.5 py-0.5 text-[10px] font-extrabold uppercase text-emerald-400">Shop</span><span class="text-xs font-black uppercase tracking-[3px] text-emerald-400">E-commerce management</span></div><h1 class="font-heading text-2xl font-black text-white sm:text-3xl">SHOP ORDER MANAGEMENT</h1><p class="mt-1 text-xs text-neutral-300">Review customer purchases, payment status, fulfillment and returns.</p></div>
        <a href="{{ route('admin.shop.products.index') }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-5 py-3 text-xs font-black uppercase tracking-wider text-emerald-300 transition hover:bg-emerald-500/20"><i data-lucide="package-search" class="h-4 w-4"></i>View catalog</a>
    </header>

    @if(session('success'))<div class="flex items-center gap-2 rounded-xl border border-emerald-500/50 bg-emerald-500/15 p-4 text-xs font-bold text-emerald-300"><i data-lucide="check-circle" class="h-4 w-4"></i>{{ session('success') }}</div>@endif

    <section class="space-y-5 rounded-3xl border border-emerald-500/30 bg-[#042718] p-4 shadow-2xl sm:p-6">
        <div class="flex flex-col justify-between gap-4 border-b border-emerald-500/20 pb-4 lg:flex-row lg:items-center">
            <div><h2 class="text-sm font-black uppercase tracking-wide text-white">Order activity</h2><p class="mt-1 text-[11px] text-neutral-400">Showing {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders</p></div>
            <form data-shop-live-filter action="{{ route('admin.shop.orders.index') }}" method="GET" class="flex flex-col gap-2 sm:flex-row">
                <label class="relative block min-w-0 flex-1 sm:w-64"><i data-lucide="search" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-emerald-400"></i><input name="search" value="{{ request('search') }}" placeholder="Order number, customer, phone..." class="w-full rounded-xl border border-emerald-500/40 bg-[#01140c] py-2.5 pl-9 pr-3 text-xs font-semibold text-white placeholder:text-neutral-500 focus:border-emerald-400 focus:outline-none"></label>
                <select name="status" class="rounded-xl border border-emerald-500/40 bg-[#01140c] px-3 py-2.5 text-xs font-semibold text-white focus:border-emerald-400 focus:outline-none"><option value="">All order statuses</option>@foreach(['placed','confirmed','processing','shipped','out_for_delivery','delivered','cancelled','return_requested','returned'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>@endforeach</select>
                <button class="rounded-xl border border-emerald-500/40 bg-emerald-500/20 px-4 py-2.5 text-xs font-bold text-emerald-300 transition hover:bg-emerald-500/30">Search</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1050px] text-left text-xs text-neutral-300">
                <thead class="border-b border-emerald-500/30 bg-[#02180f] text-[10px] font-bold uppercase tracking-wide text-emerald-400"><tr><th class="p-3.5">Order details</th><th class="p-3.5">Customer</th><th class="p-3.5">Order total</th><th class="p-3.5">Payment</th><th class="p-3.5">Order status</th><th class="p-3.5">Date placed</th><th class="p-3.5 text-right">Action</th></tr></thead>
                <tbody class="divide-y divide-emerald-500/15">
                    @forelse($orders as $order)
                        @php
                            $statusColor = match ($order->status) {
                                'delivered', 'returned' => 'border-emerald-500/40 bg-emerald-500/15 text-emerald-300',
                                'cancelled' => 'border-rose-500/40 bg-rose-500/15 text-rose-300',
                                'shipped', 'out_for_delivery' => 'border-sky-500/40 bg-sky-500/15 text-sky-300',
                                'placed', 'pending_payment', 'return_requested' => 'border-amber-500/40 bg-amber-500/15 text-amber-300',
                                default => 'border-teal-500/40 bg-teal-500/15 text-teal-300',
                            };
                        @endphp
                        <tr class="transition hover:bg-emerald-500/5">
                            <td class="p-3.5"><p class="font-mono text-sm font-bold text-white">{{ $order->order_number }}</p><p class="mt-1 text-[10px] text-neutral-500">{{ $order->items_count }} item{{ $order->items_count === 1 ? '' : 's' }}</p></td>
                            <td class="p-3.5"><p class="font-bold text-white">{{ $order->user?->name ?? $order->recipient_name }}</p><p class="mt-1 text-[10px] text-neutral-400">{{ $order->phone }}</p></td>
                            <td class="p-3.5 font-mono text-sm font-bold text-emerald-300">₹{{ number_format((float) $order->total, 2) }}<p class="mt-1 text-[10px] font-normal text-neutral-500">Subtotal ₹{{ number_format((float) $order->subtotal, 2) }}</p></td>
                            <td class="p-3.5"><span class="block font-bold capitalize text-white">{{ str($order->payment_method)->replace('_', ' ') }}</span><span class="mt-1 inline-flex rounded-full border px-2 py-0.5 text-[9px] font-black uppercase {{ $order->payment_status === 'paid' ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-300' : ($order->payment_status === 'failed' ? 'border-rose-500/40 bg-rose-500/10 text-rose-300' : 'border-amber-500/40 bg-amber-500/10 text-amber-300') }}">{{ $order->payment_status }}</span></td>
                            <td class="p-3.5"><span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[10px] font-black uppercase {{ $statusColor }}"><span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ str($order->status)->replace('_', ' ') }}</span></td>
                            <td class="p-3.5"><p class="font-semibold text-white">{{ $order->created_at->format('d M Y') }}</p><p class="mt-1 text-[10px] text-neutral-500">{{ $order->created_at->format('h:i A') }}</p></td>
                            <td class="p-3.5 text-right"><a href="{{ route('admin.shop.orders.show', $order) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 font-bold text-emerald-300 transition hover:bg-emerald-500/20"><i data-lucide="eye" class="h-3.5 w-3.5"></i>View order</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-12 text-center"><div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-2xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-300"><i data-lucide="receipt-text" class="h-5 w-5"></i></div><p class="font-bold text-white">No orders found</p><p class="mt-1 text-xs text-neutral-400">Orders placed through Shop checkout will appear here.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex flex-col gap-3 border-t border-emerald-500/15 pt-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-neutral-400">Showing {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders</p>
            {{ $orders->links() }}
        </div>
    </section>
</div>
@include('admin.shop.partials.realtime-validation')
@endsection
