@extends('admin.layouts.app')

@php
    $channelLabels = ['mobile_api' => 'Mobile app API', 'member_web' => 'Member portal', 'admin_web' => 'Admin portal'];
@endphp

@section('title', 'User Login Audit - ZIVO PAY')

@section('content')
<main class="mx-auto w-full max-w-[1600px] space-y-6">
    <header class="flex flex-col justify-between gap-5 rounded-3xl border border-emerald-400/20 bg-gradient-to-br from-[#073622] via-[#05271b] to-[#03170f] p-6 sm:flex-row sm:items-center sm:p-8">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[.22em] text-emerald-300">Security & activity</p>
            <h1 class="mt-1 text-2xl font-black text-white sm:text-3xl">User Login Audit</h1>
            <p class="mt-2 max-w-2xl text-sm text-emerald-50/65">Review successful mobile API, member portal and admin portal sign-ins with their network and device details.</p>
        </div>
        <span class="inline-flex min-h-10 items-center gap-2 self-start rounded-full border border-emerald-300/20 bg-emerald-300/[.06] px-4 text-xs font-bold text-emerald-100 sm:self-center">
            <i data-lucide="shield-check" class="h-4 w-4"></i> Successful logins only
        </span>
    </header>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-4"><p class="text-xs font-semibold text-neutral-400">Matching login events</p><p class="mt-2 text-2xl font-black text-white">{{ number_format($totalLogins) }}</p></article>
        <article class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-4"><p class="text-xs font-semibold text-neutral-400">Today</p><p class="mt-2 text-2xl font-black text-white">{{ number_format($todayLogins) }}</p></article>
        <article class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-4"><p class="text-xs font-semibold text-neutral-400">Mobile app logins</p><p class="mt-2 text-2xl font-black text-white">{{ number_format($mobileLogins) }}</p></article>
        <article class="rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-4"><p class="text-xs font-semibold text-neutral-400">Unique users</p><p class="mt-2 text-2xl font-black text-white">{{ number_format($uniqueUsers) }}</p></article>
    </section>

    <form method="GET" action="{{ route('admin.reports.login-audit') }}" class="grid gap-3 rounded-2xl border border-emerald-300/15 bg-[#041b12]/90 p-4 sm:grid-cols-2 xl:grid-cols-5">
        <label class="relative block xl:col-span-2">
            <span class="sr-only">Search users, IPs, and devices</span>
            <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-500"></i>
            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name, mobile, email, user ID, IP or device" class="min-h-11 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] pl-10 pr-3 text-sm text-white placeholder:text-neutral-500 outline-none focus:border-emerald-300/50">
        </label>
        <select name="channel" aria-label="Login channel" class="min-h-11 rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50">
            <option value="">All login sources</option>
            <option value="mobile_api" @selected(($filters['channel'] ?? '') === 'mobile_api')>Mobile app API</option>
            <option value="member_web" @selected(($filters['channel'] ?? '') === 'member_web')>Member portal</option>
            <option value="admin_web" @selected(($filters['channel'] ?? '') === 'admin_web')>Admin portal</option>
        </select>
        <label><span class="sr-only">From date</span><input type="date" name="from" value="{{ $filters['from'] ?? '' }}" aria-label="From date" class="min-h-11 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50"></label>
        <label><span class="sr-only">To date</span><input type="date" name="to" value="{{ $filters['to'] ?? '' }}" aria-label="To date" class="min-h-11 w-full rounded-xl border border-emerald-300/15 bg-[#02120b] px-3 text-sm text-white outline-none focus:border-emerald-300/50"></label>
        <div class="flex gap-2 sm:col-span-2 xl:col-span-5">
            <button class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl border border-emerald-300/25 bg-emerald-300/10 px-4 text-sm font-bold text-emerald-100 hover:bg-emerald-300/15"><i data-lucide="list-filter" class="h-4 w-4"></i> Apply filters</button>
            <a href="{{ route('admin.reports.login-audit') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-white/10 px-4 text-sm font-bold text-neutral-300 hover:bg-white/5">Reset</a>
        </div>
    </form>

    <section class="overflow-hidden rounded-2xl border border-emerald-300/15 bg-[#041b12]/90">
        <div class="flex flex-col justify-between gap-2 border-b border-emerald-300/10 px-5 py-4 sm:flex-row sm:items-center">
            <div><h2 class="font-bold text-white">Login activity</h2><p class="mt-1 text-xs text-neutral-400">{{ number_format($logins->total()) }} events match these filters</p></div>
            <p class="text-xs text-neutral-500">Network location is shown by IP address. Precise geographic lookup is not enabled.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1250px] text-left text-sm">
                <thead class="bg-emerald-300/[.04] text-[10px] font-black uppercase tracking-wider text-emerald-300">
                    <tr><th class="px-5 py-3">User</th><th class="px-4 py-3">Source / method</th><th class="px-4 py-3">Network</th><th class="px-4 py-3">Device</th><th class="px-4 py-3">Browser / user agent</th><th class="px-5 py-3">Login time</th></tr>
                </thead>
                <tbody class="divide-y divide-emerald-300/[.07]">
                    @forelse($logins as $login)
                        <tr class="align-top hover:bg-emerald-300/[.025]">
                            <td class="px-5 py-4">
                                <p class="font-bold text-white">{{ $login->user?->name ?? 'Deleted user' }}</p>
                                <p class="mt-1 text-xs text-emerald-300">{{ $login->user?->referral_code ?? 'User #'.$login->user_id }}</p>
                                <p class="mt-1 text-xs text-neutral-400">{{ $login->user?->mobile ?? '—' }} · {{ $login->user?->email ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-4"><span class="rounded-full border border-emerald-300/15 bg-emerald-300/[.06] px-2.5 py-1 text-[11px] font-bold text-emerald-100">{{ $channelLabels[$login->channel] ?? str($login->channel)->headline() }}</span><p class="mt-2 text-xs capitalize text-neutral-400">{{ $login->login_method }}</p></td>
                            <td class="px-4 py-4 font-mono text-xs text-neutral-200">{{ $login->ip_address ?? '—' }}</td>
                            <td class="px-4 py-4 text-neutral-200">
                                <p>{{ $login->device_name ?? $login->device_model ?? 'Device not provided' }}</p>
                                @if($login->device_name && $login->device_model)<p class="mt-1 text-xs text-neutral-400">{{ $login->device_model }}</p>@endif
                                <p class="mt-1 text-xs text-neutral-400">{{ collect([$login->platform, $login->os_version])->filter()->implode(' · ') ?: 'Platform not provided' }}</p>
                                @if($login->app_version)<p class="mt-1 text-xs text-neutral-500">App {{ $login->app_version }}</p>@endif
                            </td>
                            <td class="max-w-sm whitespace-normal break-words px-4 py-4 text-xs leading-relaxed text-neutral-400">{{ $login->user_agent ?: 'User agent not provided' }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-xs text-neutral-200">{{ $login->logged_in_at?->format('d M Y, h:i:s A') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-16 text-center"><p class="font-semibold text-neutral-200">No login activity found</p><p class="mt-1 text-sm text-neutral-500">Successful logins will appear here once the login audit migration is applied.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logins->hasPages())<div class="border-t border-emerald-300/10 px-5 py-4">{{ $logins->links() }}</div>@endif
    </section>
</main>
@endsection
