@extends('admin.layouts.app')

@section('title', 'Razorpay Payment Gateway Settings - ZIVO PAY Admin')

@section('content')
@php($activeCredentialMode = old('mode', $settings->mode))
@php($gatewayEnabled = (bool) $settings->enabled)

<div class="mx-auto w-full max-w-5xl space-y-4">

    {{-- HEADER --}}
    <div class="relative overflow-hidden rounded-2xl border border-emerald-500/30 bg-gradient-to-r from-[#021a0f] to-[#042718] px-5 py-4 shadow-xl">
        <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-emerald-500/10 blur-2xl"></div>
        <div class="relative flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-emerald-400/30 bg-emerald-600/25">
                    <svg class="h-5 w-5" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M372.7 0H139.3L0 512h189.5l30.8-116.1H100.9L213.6 0h84.8L182.8 395.9H372.7L512 0H372.7z" fill="#528FF0"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="rounded-full border border-emerald-400/40 bg-emerald-500/15 px-2 py-0.5 text-[9px] font-black uppercase tracking-widest text-emerald-300">Payment Gateway</span>
                        @if($gatewayEnabled)
                            <span data-gateway-header-status class="flex items-center gap-1 rounded-full border border-emerald-400/40 bg-emerald-500/15 px-2 py-0.5 text-[9px] font-black uppercase tracking-widest text-emerald-300">
                                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-400"></span> Live
                            </span>
                        @else
                            <span data-gateway-header-status class="flex items-center gap-1 rounded-full border border-neutral-500/40 bg-neutral-700/20 px-2 py-0.5 text-[9px] font-black uppercase tracking-widest text-neutral-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-neutral-500"></span> Offline
                            </span>
                        @endif
                    </div>
                    <h1 class="mt-0.5 text-base font-black text-white">Razorpay Gateway Settings</h1>
                </div>
            </div>
            <div class="hidden items-center gap-2 sm:flex">
                <div class="rounded-xl border border-emerald-400/20 bg-emerald-500/10 px-3 py-1.5 text-center">
                    <span class="block text-[9px] font-black uppercase tracking-wider text-emerald-300">Mode</span>
                    <span data-gateway-header-mode class="text-xs font-black {{ $activeCredentialMode === 'live' ? 'text-emerald-300' : 'text-amber-300' }}">{{ strtoupper($activeCredentialMode) }}</span>
                </div>
                <div class="rounded-xl border border-emerald-400/20 bg-emerald-500/10 px-3 py-1.5 text-center">
                    <span class="block text-[9px] font-black uppercase tracking-wider text-emerald-300">Status</span>
                    <span class="text-xs font-black {{ $gatewayEnabled ? 'text-emerald-300' : 'text-rose-300' }}">{{ $gatewayEnabled ? 'Enabled' : 'Disabled' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="flex items-center gap-2 rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-2.5 text-xs font-bold text-emerald-300">
            <i data-lucide="check-circle-2" class="h-3.5 w-3.5 shrink-0 text-emerald-400"></i>{{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="flex items-center gap-2 rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-2.5 text-xs font-bold text-rose-300">
            <i data-lucide="alert-triangle" class="h-3.5 w-3.5 shrink-0 text-rose-400"></i>{{ $errors->first() }}
        </div>
    @endif

    <form id="gatewaySettingsForm" method="POST" action="{{ route('admin.payment-gateway.update') }}" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- TOP CONTROL ROW — 3 compact cards in one row --}}
        <div class="grid grid-cols-3 gap-3">

            {{-- Card 1: Gateway Toggle --}}
            <label class="cursor-pointer">
                <input type="checkbox" name="enabled" value="1" @checked($gatewayEnabled) class="peer sr-only">
                <div class="flex h-full items-center justify-between gap-2 rounded-xl border border-emerald-500/40 bg-[#042718] px-3 py-2.5 transition-all hover:border-emerald-400/70 peer-checked:border-emerald-400/60 peer-checked:bg-emerald-900/20">
                    <div class="flex items-center gap-2">
                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-emerald-500/30 bg-emerald-500/15">
                            <i data-lucide="toggle-right" class="h-3 w-3 text-emerald-400"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wide text-white">Gateway Enable</p>
                            <p class="text-[9px] {{ $gatewayEnabled ? 'text-emerald-400' : 'text-neutral-500' }}">
                                {{ $gatewayEnabled ? '● Accepting payments' : '○ Payments blocked' }}
                            </p>
                        </div>
                    </div>
                    <div class="relative h-4 w-8 shrink-0 rounded-full border transition-all {{ $gatewayEnabled ? 'bg-emerald-500 border-emerald-400' : 'bg-neutral-700 border-neutral-600' }}">
                        <div class="absolute top-0.5 h-3 w-3 rounded-full bg-white shadow transition-all {{ $gatewayEnabled ? 'left-4' : 'left-0.5' }}"></div>
                    </div>
                </div>
            </label>

            {{-- Card 2: Active Mode --}}
            <div id="activeGatewayModeCard"
                class="flex items-center justify-between gap-2 rounded-xl border px-3 py-2.5 transition-all
                {{ $activeCredentialMode === 'test' ? 'border-amber-400/50 bg-amber-900/15' : 'border-emerald-500/40 bg-[#042718]' }}">
                <div class="flex items-center gap-2">
                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border {{ $activeCredentialMode === 'test' ? 'border-amber-500/30 bg-amber-500/15' : 'border-emerald-500/30 bg-emerald-500/15' }}">
                        <i data-lucide="zap" class="h-3 w-3 {{ $activeCredentialMode === 'test' ? 'text-amber-400' : 'text-emerald-400' }}"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wide text-white">Active Mode</p>
                        <p data-mode-description class="text-[9px] text-neutral-400">{{ $activeCredentialMode === 'test' ? 'Sandbox — no real money' : 'Production — live' }}</p>
                    </div>
                </div>
                <span id="selectedGatewayModeLabel" data-mode="{{ $activeCredentialMode }}"
                    class="shrink-0 rounded-md px-2 py-0.5 text-[9px] font-black uppercase {{ $activeCredentialMode === 'test' ? 'bg-amber-400/20 text-amber-200 border border-amber-400/30' : 'bg-emerald-400/20 text-emerald-200 border border-emerald-400/30' }}">
                    {{ $activeCredentialMode }}
                </span>
                <input type="hidden" name="mode" id="activeGatewayMode" value="{{ $activeCredentialMode }}">
            </div>

            {{-- Card 3: Emergency Control --}}
            <div id="gatewayEmergencyCard"
                class="flex items-center justify-between gap-2 rounded-xl border px-3 py-2.5 transition-all
                {{ $gatewayEnabled ? 'border-rose-500/40 bg-rose-900/10' : 'border-emerald-500/40 bg-[#042718]' }}">
                <div class="flex items-center gap-2">
                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border {{ $gatewayEnabled ? 'border-rose-500/30 bg-rose-500/15' : 'border-emerald-500/30 bg-emerald-500/15' }}">
                        <i data-lucide="{{ $gatewayEnabled ? 'shield-x' : 'shield-check' }}" class="h-3.5 w-3.5 {{ $gatewayEnabled ? 'text-rose-400' : 'text-emerald-400' }}"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wide text-white">Emergency</p>
                        <p class="text-[9px] {{ $gatewayEnabled ? 'text-rose-400' : 'text-neutral-500' }}">
                            {{ $gatewayEnabled ? '● Payments OPEN' : '○ Payments BLOCKED' }}
                        </p>
                    </div>
                </div>
                <button type="button" id="gatewayToggleButton"
                    class="shrink-0 rounded-lg px-2 py-1 text-[9px] font-black uppercase tracking-wider transition
                    {{ $gatewayEnabled ? 'bg-rose-500/20 text-rose-200 border border-rose-500/30 hover:bg-rose-500/30' : 'bg-emerald-400/15 text-emerald-200 border border-emerald-400/30 hover:bg-emerald-400/25' }}">
                    {{ $gatewayEnabled ? 'Turn OFF' : 'Turn ON' }}
                </button>
            </div>
        </div>

        {{-- CREDENTIALS SECTION --}}
        <div class="rounded-2xl border border-emerald-500/30 bg-[#042718] p-4">
            <div class="mb-3 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <div class="flex h-6 w-6 items-center justify-center rounded-lg border border-emerald-400/30 bg-emerald-500/15">
                        <i data-lucide="key-round" class="h-3 w-3 text-emerald-400"></i>
                    </div>
                    <div>
                        <h2 class="text-xs font-black uppercase tracking-wide text-white">API Credentials</h2>
                        <p class="text-[9px] text-neutral-500">Click a card to set it as active mode</p>
                    </div>
                </div>
                <a href="https://dashboard.razorpay.com/app/keys" target="_blank"
                    class="hidden items-center gap-1 rounded-lg border border-emerald-400/20 bg-emerald-500/10 px-2.5 py-1 text-[9px] font-black uppercase tracking-wider text-emerald-300 hover:bg-emerald-500/20 transition sm:flex">
                    <i data-lucide="external-link" class="h-3 w-3"></i> Razorpay Dashboard
                </a>
            </div>

            <div class="grid gap-3 xl:grid-cols-2">
                @foreach(['test' => ['label' => 'Test / Sandbox', 'color' => 'amber', 'icon' => 'flask-conical', 'desc' => 'No real money. Safe for QA.', 'badge' => 'SANDBOX'], 'live' => ['label' => 'Live / Production', 'color' => 'emerald', 'icon' => 'globe', 'desc' => 'Real transactions. Use with caution.', 'badge' => 'PRODUCTION']] as $mode => $cfg)
                @php($isActive = $activeCredentialMode === $mode)
                @php($c = $cfg['color'])
                <section
                    role="radio"
                    aria-checked="{{ $isActive ? 'true' : 'false' }}"
                    tabindex="0"
                    data-gateway-mode="{{ $mode }}"
                    class="gateway-mode-card cursor-pointer rounded-xl border p-3.5 transition-all duration-200 focus:outline-none
                        {{ $isActive
                            ? ($mode === 'test'
                                ? 'border-amber-400/60 bg-amber-900/20 shadow-lg shadow-amber-900/10'
                                : 'border-emerald-400/60 bg-emerald-900/20 shadow-lg shadow-emerald-900/10')
                            : 'border-neutral-700/40 bg-[#0a1628] hover:border-' . $c . '-400/40' }}">

                    {{-- Card Header --}}
                    <div class="mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex h-6 w-6 items-center justify-center rounded-lg border border-{{ $c }}-500/30 bg-{{ $c }}-500/15">
                                <i data-lucide="{{ $cfg['icon'] }}" class="h-3 w-3 text-{{ $c }}-400"></i>
                            </div>
                            <div>
                                <h3 class="text-[10px] font-black uppercase tracking-wide text-{{ $c }}-300">{{ $cfg['label'] }}</h3>
                                <p class="text-[9px] text-neutral-500">{{ $cfg['desc'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="rounded-full border border-{{ $c }}-400/20 bg-{{ $c }}-500/10 px-2 py-0.5 text-[8px] font-black uppercase tracking-wider text-{{ $c }}-400">
                                {{ $cfg['badge'] }}
                            </span>
                            <span data-selected-indicator="{{ $mode }}"
                                class="{{ $isActive ? 'flex' : 'hidden' }} items-center gap-1 rounded-full border border-{{ $c }}-400/40 bg-{{ $c }}-400/15 px-2 py-0.5 text-[8px] font-black uppercase text-{{ $c }}-200">
                                <span class="h-1 w-1 animate-pulse rounded-full bg-{{ $c }}-400"></span> ACTIVE
                            </span>
                        </div>
                    </div>

                    {{-- Fields --}}
                    <div class="grid gap-2 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-[9px] font-black uppercase tracking-wider text-{{ $c }}-400">
                                <i data-lucide="fingerprint" class="inline h-2.5 w-2.5"></i> Key ID
                            </label>
                            <input
                                name="{{ $mode }}_key_id"
                                value="{{ old($mode . '_key_id', $settings->keyIdFor($mode)) }}"
                                autocomplete="off"
                                maxlength="255"
                                pattern="rzp_{{ $mode }}_[A-Za-z0-9]+"
                                title="Valid Razorpay {{ $mode }} Key ID (rzp_{{ $mode }}_...)"
                                placeholder="rzp_{{ $mode }}_..."
                                class="w-full rounded-lg border border-{{ $c }}-500/20 bg-[#020912] px-2.5 py-1.5 text-[11px] text-white font-mono placeholder-neutral-600 focus:border-{{ $c }}-300 focus:outline-none transition">
                            @error($mode . '_key_id')
                                <span class="mt-0.5 block text-[9px] text-rose-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 flex items-center gap-1 text-[9px] font-black uppercase tracking-wider text-{{ $c }}-400">
                                <i data-lucide="lock-keyhole" class="h-2.5 w-2.5"></i> Key Secret
                                @if(filled($settings->keySecretFor($mode)))
                                    <span class="rounded bg-{{ $c }}-500/10 border border-{{ $c }}-400/20 px-1 text-[8px] text-{{ $c }}-300 font-bold normal-case tracking-normal">Saved</span>
                                @endif
                            </label>
                            <input
                                type="password"
                                name="{{ $mode }}_key_secret"
                                data-configured="{{ filled($settings->keySecretFor($mode)) ? 'true' : 'false' }}"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="2000"
                                placeholder="{{ filled($settings->keySecretFor($mode)) ? '••••••• (leave blank to keep)' : 'Enter Key Secret...' }}"
                                class="w-full rounded-lg border border-{{ $c }}-500/20 bg-[#020912] px-2.5 py-1.5 text-[11px] text-white font-mono placeholder-neutral-600 focus:border-{{ $c }}-300 focus:outline-none transition">
                            @error($mode . '_key_secret')
                                <span class="mt-0.5 block text-[9px] text-rose-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-1 flex items-center gap-1 text-[9px] font-black uppercase tracking-wider text-{{ $c }}-400">
                                <i data-lucide="webhook" class="h-2.5 w-2.5"></i> Webhook Secret
                                <span class="font-normal normal-case tracking-normal text-neutral-600">(optional)</span>
                                @if(filled($settings->webhookSecretFor($mode)))
                                    <span class="rounded bg-{{ $c }}-500/10 border border-{{ $c }}-400/20 px-1 text-[8px] text-{{ $c }}-300 font-bold normal-case tracking-normal">Saved</span>
                                @endif
                            </label>
                            <input
                                type="password"
                                name="{{ $mode }}_webhook_secret"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="2000"
                                placeholder="{{ filled($settings->webhookSecretFor($mode)) ? '••••••• (leave blank to keep)' : 'Webhook secret (optional)...' }}"
                                class="w-full rounded-lg border border-{{ $c }}-500/20 bg-[#020912] px-2.5 py-1.5 text-[11px] text-white font-mono placeholder-neutral-600 focus:border-{{ $c }}-300 focus:outline-none transition">
                            @error($mode . '_webhook_secret')
                                <span class="mt-0.5 block text-[9px] text-rose-400">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if(!$isActive)
                        <p class="mt-2 text-[9px] text-neutral-600">
                            <i data-lucide="mouse-pointer-click" class="inline h-2.5 w-2.5"></i>
                            Click to set <span class="font-bold text-{{ $c }}-500 uppercase">{{ $mode }}</span> as active mode
                        </p>
                    @endif
                </section>
                @endforeach
            </div>
        </div>

        {{-- WEBHOOK URL BAR --}}
        <div class="flex flex-col gap-2 rounded-xl border border-emerald-500/20 bg-emerald-900/10 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-emerald-400/30 bg-emerald-500/15">
                    <i data-lucide="webhook" class="h-3 w-3 text-emerald-400"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black uppercase tracking-widest text-emerald-300">Webhook Callback URL</p>
                    <code class="mt-0.5 block truncate text-[10px] text-emerald-200 font-mono">{{ url('/api/payment/razorpay/webhook') }}</code>
                    <p class="mt-0.5 text-[9px] text-neutral-500">Subscribe to <code class="text-emerald-400">payment.captured</code> in Razorpay Dashboard → Webhooks</p>
                </div>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <span class="rounded-lg border border-amber-400/30 bg-amber-500/10 px-2 py-1 text-[8px] font-bold uppercase tracking-wider text-amber-300">
                    ⚠ HTTPS Required
                </span>
                <button type="button" onclick="navigator.clipboard.writeText('{{ url('/api/payment/razorpay/webhook') }}').then(()=>{ this.textContent='✓ Copied'; setTimeout(()=>this.textContent='Copy URL',2000) })"
                    class="rounded-lg border border-emerald-400/30 bg-emerald-500/10 px-2.5 py-1 text-[9px] font-bold uppercase tracking-wider text-emerald-300 hover:bg-emerald-500/20 transition">
                    Copy URL
                </button>
            </div>
        </div>

        {{-- SAVE BUTTON ROW --}}
        <div class="flex items-center justify-between gap-4 rounded-xl border border-emerald-500/15 bg-[#042718] px-4 py-3">
            <p class="text-[9px] text-neutral-500">
                <i data-lucide="info" class="inline h-3 w-3 text-emerald-400 mr-0.5"></i>
                Credential secrets are encrypted. Key IDs are visible for reference only.
            </p>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" id="saveGatewaySettings"
                    class="flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-5 py-2 text-[10px] font-black uppercase tracking-wider text-white shadow-lg shadow-emerald-900/30 hover:from-emerald-500 hover:to-emerald-400 transition">
                    <i data-lucide="save" class="h-3.5 w-3.5"></i> Save Settings
                </button>
            </div>
        </div>
    </form>
</div>

{{-- CONFIRM DIALOG --}}
<div id="gatewayConfirmDialog" role="dialog" aria-modal="true" aria-labelledby="gatewayConfirmTitle"
    class="fixed inset-0 z-[10000] hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="w-full max-w-sm space-y-3 rounded-2xl border border-emerald-400/30 bg-gradient-to-br from-[#021a0f] to-[#042718] p-5 shadow-2xl">
        <div class="flex items-center gap-2.5">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl border border-emerald-400/30 bg-emerald-500/15">
                <i data-lucide="shield-check" class="h-4 w-4 text-emerald-400"></i>
            </div>
            <div>
                <p class="text-[9px] font-black uppercase tracking-widest text-emerald-300">Confirm Action</p>
                <h2 id="gatewayConfirmTitle" class="text-sm font-black text-white">Save gateway settings?</h2>
            </div>
        </div>
        <p id="confirmGatewayDescription" class="rounded-xl border border-white/5 bg-white/5 p-3 text-xs text-neutral-300"></p>
        <div class="flex justify-end gap-2">
            <button type="button" id="cancelGatewaySave"
                class="rounded-xl border border-neutral-600 px-3 py-1.5 text-xs font-bold text-neutral-300 transition hover:border-neutral-500 hover:text-white">
                Cancel
            </button>
            <button type="button" id="confirmGatewaySave"
                class="flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-4 py-1.5 text-xs font-black text-white shadow-lg hover:from-emerald-500 hover:to-emerald-400 transition">
                <i data-lucide="check" class="h-3.5 w-3.5"></i> Confirm & Save
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (() => {
        const form               = document.getElementById('gatewaySettingsForm');
        const saveButton         = document.getElementById('saveGatewaySettings');
        const modeInput          = document.getElementById('activeGatewayMode');
        const modeLabel          = document.getElementById('selectedGatewayModeLabel');
        const modeCard           = document.getElementById('activeGatewayModeCard');
        const emergencyCard      = document.getElementById('gatewayEmergencyCard');
        const headerStatus       = document.querySelector('[data-gateway-header-status]');
        const headerMode         = document.querySelector('[data-gateway-header-mode]');
        const gatewayEnabledInput = form.querySelector('[name="enabled"]');
        const gatewayToggleButton = document.getElementById('gatewayToggleButton');
        const dialog             = document.getElementById('gatewayConfirmDialog');
        const confirmTitle       = document.getElementById('gatewayConfirmTitle');
        const confirmDescription = document.getElementById('confirmGatewayDescription');
        let confirmed            = false;
        let togglePreviousValue  = null;

        const requestSettingsConfirmation = () => {
            form.setAttribute('data-no-loader', 'true');
            form.requestSubmit();
            form.removeAttribute('data-no-loader');
        };

        // Sync the Emergency card + toggle button UI to match the checkbox state
        const syncToggleButton = () => {
            const on = gatewayEnabledInput.checked;

            if (headerStatus) {
                headerStatus.textContent = on ? 'Live' : 'Offline';
                headerStatus.className = on
                    ? 'flex items-center gap-1 rounded-full border border-emerald-400/40 bg-emerald-500/15 px-2 py-0.5 text-[9px] font-black uppercase tracking-widest text-emerald-300'
                    : 'flex items-center gap-1 rounded-full border border-neutral-500/40 bg-neutral-700/20 px-2 py-0.5 text-[9px] font-black uppercase tracking-widest text-neutral-400';
                const statusDot = headerStatus.querySelector('span');
                statusDot.className = `h-1.5 w-1.5 rounded-full ${on ? 'animate-pulse bg-emerald-400' : 'bg-neutral-500'}`;
            }

            // Emergency card border/bg
            emergencyCard.className = [
                'flex items-center justify-between gap-2 rounded-xl border px-3 py-2.5 transition-all',
                on ? 'border-rose-500/40 bg-rose-900/10' : 'border-emerald-500/40 bg-[#042718]',
            ].join(' ');

            // Icon inside the icon-box
            const iconBox = emergencyCard.querySelector('.flex.h-6.w-6');
            if (iconBox) {
                iconBox.className = [
                    'flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border',
                    on ? 'border-rose-500/30 bg-rose-500/15' : 'border-emerald-500/30 bg-emerald-500/15',
                ].join(' ');
            }
            const icon = emergencyCard.querySelector('[data-lucide]');
            if (icon) {
                icon.setAttribute('data-lucide', on ? 'shield-x' : 'shield-check');
                icon.className = `h-3.5 w-3.5 ${on ? 'text-rose-400' : 'text-emerald-400'}`;
            }

            // Status text (second <p> inside the text div)
            const textDiv = emergencyCard.querySelector('div > div');
            if (textDiv) {
                const statusP = textDiv.querySelectorAll('p')[1];
                if (statusP) {
                    statusP.textContent = on ? '● Payments OPEN' : '○ Payments BLOCKED';
                    statusP.className   = `text-[9px] ${on ? 'text-rose-400' : 'text-neutral-500'}`;
                }
            }

            // Toggle button
            gatewayToggleButton.textContent = on ? 'Turn OFF' : 'Turn ON';
            gatewayToggleButton.className = [
                'shrink-0 rounded-lg px-2 py-1 text-[9px] font-black uppercase tracking-wider transition',
                on
                    ? 'bg-rose-500/20 text-rose-200 border border-rose-500/30 hover:bg-rose-500/30'
                    : 'bg-emerald-400/15 text-emerald-200 border border-emerald-400/30 hover:bg-emerald-400/25',
            ].join(' ');

            if (window.lucide) window.lucide.createIcons();
        };

        // Toggle button click — flip enabled, then immediately submit
        gatewayToggleButton.addEventListener('click', () => {
            togglePreviousValue = gatewayEnabledInput.checked;
            gatewayEnabledInput.checked = !gatewayEnabledInput.checked;
            syncToggleButton();
            requestSettingsConfirmation();
        });

        gatewayEnabledInput.addEventListener('change', syncToggleButton);

        // Switch active credential mode card
        const selectMode = (mode) => {
            modeInput.value       = mode;
            modeLabel.textContent = mode;
            modeLabel.dataset.mode = mode;
            modeLabel.className = [
                'shrink-0 rounded-md px-2 py-0.5 text-[9px] font-black uppercase border',
                mode === 'test'
                    ? 'bg-amber-400/20 text-amber-200 border-amber-400/30'
                    : 'bg-emerald-400/20 text-emerald-200 border-emerald-400/30',
            ].join(' ');

            // Active Mode card border
            modeCard.classList.toggle('border-amber-400/50',  mode === 'test');
            modeCard.classList.toggle('bg-amber-900/15',      mode === 'test');
            modeCard.classList.toggle('border-emerald-500/40', mode !== 'test');
            modeCard.classList.toggle('bg-[#042718]',         mode !== 'test');
            const modeIconBox = modeCard.querySelector('.flex.h-6.w-6');
            if (modeIconBox) {
                modeIconBox.className = [
                    'flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border',
                    mode === 'test' ? 'border-amber-500/30 bg-amber-500/15' : 'border-emerald-500/30 bg-emerald-500/15',
                ].join(' ');
            }
            const modeIcon = modeCard.querySelector('[data-lucide="zap"]');
            if (modeIcon) {
                modeIcon.className = `h-3 w-3 ${mode === 'test' ? 'text-amber-400' : 'text-emerald-400'}`;
            }
            const modeDescription = modeCard.querySelector('[data-mode-description]');
            if (modeDescription) {
                modeDescription.textContent = mode === 'test' ? 'Sandbox — no real money' : 'Production — live';
            }
            if (headerMode) {
                headerMode.textContent = mode.toUpperCase();
                headerMode.classList.toggle('text-amber-300', mode === 'test');
                headerMode.classList.toggle('text-emerald-300', mode === 'live');
            }

            document.querySelectorAll('[data-gateway-mode]').forEach(card => {
                const selected = card.dataset.gatewayMode === mode;
                card.setAttribute('aria-checked', selected ? 'true' : 'false');
                const testCard = card.dataset.gatewayMode === 'test';
                card.classList.toggle('border-amber-400/60', selected && testCard);
                card.classList.toggle('bg-amber-900/20', selected && testCard);
                card.classList.toggle('shadow-lg', selected);
                card.classList.toggle('shadow-amber-900/10', selected && testCard);
                card.classList.toggle('border-emerald-400/60', selected && !testCard);
                card.classList.toggle('bg-emerald-900/20', selected && !testCard);
                card.classList.toggle('shadow-emerald-900/10', selected && !testCard);
                card.classList.toggle('border-neutral-700/40', !selected);
                card.classList.toggle('bg-[#0a1628]', !selected);
                const ind = card.querySelector('[data-selected-indicator]');
                if (ind) {
                    ind.classList.toggle('hidden', !selected);
                    ind.classList.toggle('flex',   selected);
                }
            });
            if (window.lucide) window.lucide.createIcons();
        };

        document.querySelectorAll('[data-gateway-mode]').forEach(card => {
            card.addEventListener('click',   e => { if (!e.target.closest('input')) selectMode(card.dataset.gatewayMode); });
            card.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); selectMode(card.dataset.gatewayMode); } });
        });

        // Dialog helpers
        const closeFallbackDialog  = () => { dialog.classList.add('hidden'); dialog.classList.remove('flex'); };
        const cancelFallbackDialog = () => {
            closeFallbackDialog();
            if (togglePreviousValue !== null) {
                gatewayEnabledInput.checked = togglePreviousValue;
                togglePreviousValue = null;
                syncToggleButton();
            }
        };
        const submitAfterConfirmation = () => {
            confirmed           = true;
            togglePreviousValue = null;
            form.requestSubmit();
        };

        saveButton.addEventListener('click', requestSettingsConfirmation);

        form.addEventListener('submit', async (event) => {
            // If already confirmed, let the native submit proceed and reset flag
            if (confirmed) {
                confirmed = false;
                return;
            }
            event.preventDefault();
            event.stopImmediatePropagation();

            const mode       = modeInput.value;
            const activeCard = document.querySelector(`[data-gateway-mode="${mode}"]`);
            const keyIdInput = activeCard.querySelector(`[name="${mode}_key_id"]`);
            const keyId      = keyIdInput ? keyIdInput.value.trim() : '';
            const keySecret  = activeCard.querySelector(`[name="${mode}_key_secret"]`);

            // Validate input patterns/lengths before showing confirmation
            const invalidInput = [...form.querySelectorAll('input[pattern], input[minlength]')]
                .find(i => i.value.trim() !== '' && !i.checkValidity());
            if (invalidInput) { invalidInput.reportValidity(); return; }

            // Require credentials when enabling
            if (gatewayEnabledInput.checked && (!keyId || (!keySecret.value.trim() && keySecret.dataset.configured !== 'true'))) {
                const msg = `${mode === 'test' ? 'Test' : 'Live'} mode needs both Key ID and Key Secret before enabling.`;
                if (window.Swal) {
                    await window.Swal.fire({ title: 'Credentials required', text: msg, icon: 'error', confirmButtonText: 'Review', confirmButtonColor: '#10b981', background: '#042718', color: '#f8fafc' });
                } else {
                    alert(msg);
                }
                if (togglePreviousValue !== null) { gatewayEnabledInput.checked = togglePreviousValue; togglePreviousValue = null; syncToggleButton(); }
                activeCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            const on       = gatewayEnabledInput.checked;
            const isToggle = togglePreviousValue !== null;
            const desc  = isToggle
                ? (on ? 'Users can now start Razorpay payments.' : 'New payments will be blocked until re-enabled.')
                : (on && mode === 'live' ? 'Live keys will be used. Verify they are correct.' : (on ? 'Test mode — no real money.' : 'Gateway stays OFF.'));
            const title = isToggle
                ? (on ? 'Enable Razorpay?' : 'Turn off Razorpay?')
                : `Save ${mode.toUpperCase()} settings?`;

            if (window.Swal) {
                const res = await window.Swal.fire({
                    title,
                    text: desc,
                    icon: (isToggle || (on && mode === 'live')) ? 'warning' : 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Confirm & Save',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#334155',
                    background: '#042718',
                    color: '#f8fafc',
                    backdrop: 'rgba(0,0,0,.8)',
                    reverseButtons: true,
                    allowOutsideClick: false,
                });
                if (res.isConfirmed) {
                    submitAfterConfirmation();
                } else if (togglePreviousValue !== null) {
                    gatewayEnabledInput.checked = togglePreviousValue;
                    togglePreviousValue = null;
                    syncToggleButton();
                }
                return;
            }

            // Fallback dialog (no SweetAlert)
            confirmTitle.textContent       = title;
            confirmDescription.textContent = desc;
            dialog.classList.remove('hidden');
            dialog.classList.add('flex');
            document.getElementById('confirmGatewaySave').focus();
        });

        document.getElementById('cancelGatewaySave').addEventListener('click', cancelFallbackDialog);
        document.getElementById('confirmGatewaySave').addEventListener('click', () => { closeFallbackDialog(); submitAfterConfirmation(); });
        dialog.addEventListener('click', e => { if (e.target === dialog) cancelFallbackDialog(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && !dialog.classList.contains('hidden')) cancelFallbackDialog(); });
    })();
</script>
@endpush

@endsection
