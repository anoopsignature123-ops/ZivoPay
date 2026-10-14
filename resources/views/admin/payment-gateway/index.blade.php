@extends('admin.layouts.app')

@section('title', 'Payment Gateway Settings - ZIVO PAY')

@section('content')
@php($activeCredentialMode = old('mode', $settings->mode))
@php($gatewayEnabled = (bool) old('enabled', $settings->enabled))
<div class="mx-auto w-full max-w-7xl space-y-4">
    <header
        class="flex flex-col justify-between gap-3 rounded-2xl border border-emerald-500/30 bg-[#042718] p-4 shadow-xl sm:flex-row sm:items-center sm:p-5">
        <div>
            <span class="text-[10px] font-black uppercase tracking-[2px] text-emerald-400">Payments &
                integrations</span>
            <h1 class="mt-1 text-xl font-black text-white">Razorpay Payment Gateway</h1>
            <p class="mt-1 text-xs text-neutral-300">Test and Live credentials are encrypted. Secrets stay hidden after
                saving.</p>
        </div>
        <span
            class="inline-flex w-fit items-center gap-2 rounded-full border border-emerald-500/30 bg-[#01140c] px-3 py-2 text-[10px] font-black uppercase tracking-wider text-emerald-300">
            <span class="h-2 w-2 rounded-full {{ $settings->enabled ? 'bg-emerald-400' : 'bg-neutral-500' }}"></span>
            {{ $settings->enabled ? 'Gateway enabled' : 'Gateway disabled' }}
        </span>
        </header>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 p-3 text-sm font-bold text-emerald-300">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-500/40 bg-rose-500/10 p-3 text-sm font-bold text-rose-300">
            {{ $errors->first() }}
        </div>
    @endif
<form id="gatewaySettingsForm" method="POST" action="{{ route('admin.payment-gateway.update') }}"
    class="space-y-4 rounded-2xl border border-emerald-500/30 bg-[#042718] p-3 sm:p-4">
        @csrf
        @method('PUT')
<div class="grid gap-3 md:grid-cols-3">
    <label
        class="flex items-center justify-between rounded-xl border border-emerald-500/30 bg-[#01140c] px-3 py-2.5 text-sm font-bold text-white">
        <span>Gateway enabled <span class="block pt-0.5 text-[11px] font-normal text-neutral-400">Allow users to
                start checkout</span></span>
        <input type="checkbox" name="enabled" value="1" @checked($gatewayEnabled)
            class="h-5 w-5 accent-emerald-400">
            </label>
            <div class="flex items-center justify-between rounded-xl border border-emerald-500/30 bg-[#01140c] px-3 py-2.5">
                <span class="text-xs font-bold text-neutral-300">Active payment mode</span>
                <span id="selectedGatewayModeLabel"
                    class="rounded-full bg-emerald-400/10 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-emerald-300">{{ $activeCredentialMode }}</span>
                <input type="hidden" name="mode" id="activeGatewayMode" value="{{ $activeCredentialMode }}">
            </div>
            <div class="flex items-center justify-between gap-3 rounded-xl border border-rose-500/25 bg-[#01140c] px-3 py-2.5">
                <span class="text-[11px] font-bold text-neutral-300">Emergency control</span>
                <button type="button" id="gatewayToggleButton"
                    class="rounded-lg px-3 py-2 text-[10px] font-black uppercase tracking-wide transition {{ $gatewayEnabled ? 'bg-rose-500/15 text-rose-200 hover:bg-rose-500/25' : 'bg-emerald-400/15 text-emerald-200 hover:bg-emerald-400/25' }}">
                    {{ $gatewayEnabled ? 'Turn gateway off' : 'Turn gateway on' }}
                </button>
            </div>
        </div>

        <div>
            <div class="mb-2 flex items-center justify-between gap-2">
                <h2 class="text-sm font-black text-white">Gateway credentials</h2>
                <p class="text-[10px] text-neutral-400">Click a card to select the active mode.</p>
            </div>
            <div class="grid gap-3 xl:grid-cols-2">
                @foreach(['test' => 'Test', 'live' => 'Live'] as $mode => $heading)
                    <section role="radio" aria-checked="{{ $activeCredentialMode === $mode ? 'true' : 'false' }}" tabindex="0"
                        data-gateway-mode="{{ $mode }}"
                        class="gateway-mode-card cursor-pointer rounded-xl border p-3 transition focus:outline-none focus:ring-2 focus:ring-emerald-300 {{ $activeCredentialMode === $mode ? ($mode === 'test' ? 'border-amber-300 bg-amber-400/10 shadow-lg shadow-amber-950/20' : 'border-emerald-300 bg-emerald-400/10 shadow-lg shadow-emerald-950/20') : 'border-emerald-500/25 bg-[#01140c] hover:border-emerald-400/60' }}">
                        <div class="mb-2 flex items-center justify-between border-b border-white/10 pb-2">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full {{ $mode === 'test' ? 'bg-amber-300' : 'bg-emerald-300' }}"></span>
                                <h3
                                    class="text-xs font-black uppercase tracking-wider {{ $mode === 'test' ? 'text-amber-200' : 'text-emerald-200' }}">
                                    {{ $heading }} credentials
                                </h3>
                            </div>
                            <span
                                class="text-[9px] font-bold uppercase tracking-wide text-neutral-400">{{ $mode === 'test' ? 'Sandbox' : 'Production' }}</span>
                            <span data-selected-indicator="{{ $mode }}"
                                class="{{ $activeCredentialMode === $mode ? '' : 'hidden' }} rounded-full bg-emerald-400/15 px-2 py-0.5 text-[9px] font-black uppercase text-emerald-200">Selected</span>
                        </div>
                        <div class="grid gap-2 sm:grid-cols-2">
                            <label class="block text-[10px] font-bold uppercase tracking-wide text-emerald-400">Key ID
                                <input name="{{ $mode }}_key_id" value="{{ old($mode . '_key_id', $settings->keyIdFor($mode)) }}"
                                    autocomplete="off" maxlength="255" pattern="rzp_{{ $mode }}_[A-Za-z0-9]+"
                                    title="Enter a valid Razorpay {{ $mode }} Key ID (rzp_{{ $mode }}_...)"
                                    class="mt-1 w-full rounded-lg border border-emerald-500/35 bg-[#001009] px-2.5 py-2 text-xs font-normal normal-case text-white focus:border-emerald-300 focus:outline-none">
                                @error($mode . '_key_id')<span
                                class="mt-1 block text-[10px] normal-case text-rose-300">{{ $message }}</span>@enderror
                            </label>
                            <label class="block text-[10px] font-bold uppercase tracking-wide text-emerald-400">Key Secret
                                <input type="password" name="{{ $mode }}_key_secret"
                                    data-configured="{{ filled($settings->keySecretFor($mode)) ? 'true' : 'false' }}"
                                    autocomplete="new-password" minlength="8" maxlength="2000"
                                    placeholder="{{ filled($settings->keySecretFor($mode)) ? 'Configured — leave blank to keep' : 'Enter API key secret' }}"
                                    class="mt-1 w-full rounded-lg border border-emerald-500/35 bg-[#001009] px-2.5 py-2 text-xs font-normal normal-case text-white focus:border-emerald-300 focus:outline-none">
                                @error($mode . '_key_secret')<span
                                class="mt-1 block text-[10px] normal-case text-rose-300">{{ $message }}</span>@enderror
                            </label>
                            <label class="block text-[10px] font-bold uppercase tracking-wide text-emerald-400 sm:col-span-2">Webhook
                                Secret <span class="font-normal normal-case text-neutral-500">(optional)</span>
                                <input type="password" name="{{ $mode }}_webhook_secret" autocomplete="new-password" minlength="8"
                                    maxlength="2000"
                                    placeholder="{{ filled($settings->webhookSecretFor($mode)) ? 'Configured — leave blank to keep' : 'Webhook secret for this mode' }}"
                                    class="mt-1 w-full rounded-lg border border-emerald-500/35 bg-[#001009] px-2.5 py-2 text-xs font-normal normal-case text-white focus:border-emerald-300 focus:outline-none">
                                @error($mode . '_webhook_secret')<span
                                class="mt-1 block text-[10px] normal-case text-rose-300">{{ $message }}</span>@enderror
                            </label>
                                </div>
                            </section>
                @endforeach
            </div>
            </div>

            <div
                class="flex flex-col justify-between gap-2 rounded-xl border border-emerald-500/20 bg-[#01140c] px-3 py-2.5 sm:flex-row sm:items-center">
                <div class="min-w-0">
                    <p class="text-[10px] font-black uppercase tracking-wider text-emerald-400">Webhook URL</p>
                    <code class="mt-0.5 block break-all text-[11px] text-white">{{ url('/api/payment/razorpay/webhook') }}</code>
                    <p class="mt-0.5 text-[10px] text-neutral-400">Subscribe to <code>payment.captured</code>; configure a
                        separate webhook secret for each mode.</p>
                </div>
                <span class="w-fit shrink-0 rounded-lg bg-amber-400/10 px-2.5 py-1.5 text-[9px] font-bold text-amber-200">Public
                    HTTPS required</span>
            </div>

            <div class="flex justify-end">
            <button type="submit" id="gatewayFormSubmitter" class="hidden" aria-hidden="true" tabindex="-1"></button>
                <button type="button" id="saveGatewaySettings"
                    class="rounded-lg bg-emerald-400 px-5 py-2.5 text-xs font-black uppercase tracking-wide text-[#00140c] transition hover:bg-emerald-300">Save
                    settings</button>
        </div>
    </form>
</div>
<div id="gatewayConfirmDialog" role="dialog" aria-modal="true" aria-labelledby="gatewayConfirmTitle"
    aria-describedby="confirmGatewayDescription"
    class="fixed inset-0 z-[10000] hidden items-center justify-center bg-black/70 p-4"
    style="background: rgba(0, 0, 0, .72);">
    <div class="w-full max-w-md space-y-4 rounded-2xl border border-emerald-400/30 bg-[#042718] p-5 text-white shadow-2xl"
        style="background-color: #042718; color: #fff;">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[2px] text-emerald-300">Confirm gateway change</p>
            <h2 id="gatewayConfirmTitle" class="mt-1 text-lg font-black">Save gateway settings?</h2>
            <p id="confirmGatewayDescription" class="mt-2 text-sm text-neutral-300"></p>
        </div>
        <div class="flex justify-end gap-2">
            <button type="button" id="cancelGatewaySave"
                class="rounded-lg border border-white/20 px-4 py-2 text-sm font-bold text-neutral-200"
                style="background: transparent; color: #e5e5e5;">Cancel</button>
            <button type="button" id="confirmGatewaySave"
                class="rounded-lg bg-emerald-400 px-4 py-2 text-sm font-black text-[#00140c]"
                style="background: #34d399; color: #00140c;">Confirm and save</button>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        (() => {
            const form = document.getElementById('gatewaySettingsForm');
            const saveButton = document.getElementById('saveGatewaySettings');
            const formSubmitter = document.getElementById('gatewayFormSubmitter');
            const modeInput = document.getElementById('activeGatewayMode');
            const modeLabel = document.getElementById('selectedGatewayModeLabel');
            const gatewayEnabledInput = form.querySelector('[name="enabled"]');
            const gatewayToggleButton = document.getElementById('gatewayToggleButton');
            const dialog = document.getElementById('gatewayConfirmDialog');
            const confirmTitle = document.getElementById('gatewayConfirmTitle');
            const confirmDescription = document.getElementById('confirmGatewayDescription');
            let confirmed = false;
            let togglePreviousValue = null;

            const syncToggleButton = () => {
                gatewayToggleButton.textContent = gatewayEnabledInput.checked ? 'Turn gateway off' : 'Turn gateway on';
                gatewayToggleButton.classList.toggle('bg-rose-500/15', gatewayEnabledInput.checked);
                gatewayToggleButton.classList.toggle('text-rose-200', gatewayEnabledInput.checked);
                gatewayToggleButton.classList.toggle('hover:bg-rose-500/25', gatewayEnabledInput.checked);
                gatewayToggleButton.classList.toggle('bg-emerald-400/15', !gatewayEnabledInput.checked);
                gatewayToggleButton.classList.toggle('text-emerald-200', !gatewayEnabledInput.checked);
                gatewayToggleButton.classList.toggle('hover:bg-emerald-400/25', !gatewayEnabledInput.checked);
            };

            gatewayToggleButton.addEventListener('click', () => {
                togglePreviousValue = gatewayEnabledInput.checked;
                gatewayEnabledInput.checked = !gatewayEnabledInput.checked;
                syncToggleButton();
                form.requestSubmit(formSubmitter);
            });

            gatewayEnabledInput.addEventListener('change', syncToggleButton);

            const selectMode = (mode) => {
                modeInput.value = mode;
                modeLabel.textContent = mode;
                document.querySelectorAll('[data-gateway-mode]').forEach((card) => {
                    const selected = card.dataset.gatewayMode === mode;
                    const testCard = card.dataset.gatewayMode === 'test';
                    card.setAttribute('aria-checked', selected ? 'true' : 'false');
                    card.classList.toggle('border-amber-300', selected && testCard);
                    card.classList.toggle('bg-amber-400/10', selected && testCard);
                    card.classList.toggle('shadow-lg', selected);
                    card.classList.toggle('shadow-amber-950/20', selected && testCard);
                    card.classList.toggle('border-emerald-300', selected && !testCard);
                    card.classList.toggle('bg-emerald-400/10', selected && !testCard);
                    card.classList.toggle('shadow-emerald-950/20', selected && !testCard);
                    card.classList.toggle('border-emerald-500/25', !selected);
                    card.classList.toggle('bg-[#01140c]', !selected);
                    card.querySelector('[data-selected-indicator]').classList.toggle('hidden', !selected);
                });
            };

            document.querySelectorAll('[data-gateway-mode]').forEach((card) => {
                card.addEventListener('click', (event) => {
                    if (event.target.closest('input')) {
                        return;
                    }

                    selectMode(card.dataset.gatewayMode);
                });
                card.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        selectMode(card.dataset.gatewayMode);
                    }
                });
            });

            const closeFallbackDialog = () => {
                dialog.classList.add('hidden');
                dialog.classList.remove('flex');
            };

            const cancelFallbackDialog = () => {
                closeFallbackDialog();
                if (togglePreviousValue !== null) {
                    gatewayEnabledInput.checked = togglePreviousValue;
                    togglePreviousValue = null;
                    syncToggleButton();
                }
            };

            const submitAfterConfirmation = () => {
                confirmed = true;
                togglePreviousValue = null;
                form.requestSubmit(formSubmitter);
            };

            saveButton.addEventListener('click', () => form.requestSubmit(formSubmitter));

            form.addEventListener('submit', async (event) => {
                if (confirmed) {
                    return;
                }

                event.preventDefault();
                event.stopImmediatePropagation();
                event.stopPropagation();

                const mode = modeInput.value;
                const activeCard = document.querySelector(`[data-gateway-mode="${mode}"]`);
                const keyIdInput = activeCard.querySelector(`[name="${mode}_key_id"]`);
                const keyId = keyIdInput.value.trim();
                const keySecret = activeCard.querySelector(`[name="${mode}_key_secret"]`);

                const invalidCredentialInput = [...form.querySelectorAll('input[pattern], input[minlength]')]
                    .find((input) => input.value.trim() !== '' && !input.checkValidity());

                if (invalidCredentialInput) {
                    invalidCredentialInput.reportValidity();

                    return;
                }

                if (gatewayEnabledInput.checked && (!keyId || (!keySecret.value.trim() && keySecret.dataset.configured !== 'true'))) {
                    const message = `${mode === 'test' ? 'Test' : 'Live'} mode needs both a valid Key ID and Key Secret before the gateway can be enabled.`;

                    if (window.Swal) {
                        await window.Swal.fire({
                            title: 'Credentials required',
                            text: message,
                            icon: 'error',
                            confirmButtonText: 'Review credentials',
                            confirmButtonColor: '#00c995',
                            background: '#042718',
                            color: '#f8fafc',
                        });
                    } else {
                        window.showToast?.('Credentials required', message, 'error');
                    }

                    if (togglePreviousValue !== null) {
                        gatewayEnabledInput.checked = togglePreviousValue;
                        togglePreviousValue = null;
                        syncToggleButton();
                    }

                    activeCard.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    return;
                }

                const isEnabled = gatewayEnabledInput.checked;
                const isToggleAction = togglePreviousValue !== null;
                const description = isToggleAction
                    ? (isEnabled
                        ? 'Users will be able to start Razorpay payments using the selected mode.'
                        : 'New Razorpay payments will be blocked until you turn the gateway back on. Existing payment verification will continue.')
                    : (isEnabled && mode === 'live'
                        ? 'Live credentials will be used for user payments. Confirm that your Live keys are correct before switching.'
                        : (isEnabled
                            ? 'Razorpay Test mode will be used. Test transactions do not charge real money.'
                            : 'The gateway remains off. Users will not be able to start new Razorpay payments.'));
                const confirmationTitle = isToggleAction
                    ? (isEnabled ? 'Enable Razorpay gateway?' : 'Turn off Razorpay gateway?')
                    : `Save ${mode.toUpperCase()} gateway settings?`;

                if (window.Swal) {
                    const result = await window.Swal.fire({
                        title: confirmationTitle,
                        text: description,
                        icon: isToggleAction || (isEnabled && mode === 'live') ? 'warning' : 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Confirm and save',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#00c995',
                        cancelButtonColor: '#334155',
                        background: '#042718',
                        color: '#f8fafc',
                        backdrop: 'rgba(0, 0, 0, .72)',
                        reverseButtons: true,
                        allowOutsideClick: false,
                    });

                    if (result.isConfirmed) {
                        submitAfterConfirmation();
                    } else if (togglePreviousValue !== null) {
                        gatewayEnabledInput.checked = togglePreviousValue;
                        togglePreviousValue = null;
                        syncToggleButton();
                    }

                    return;
                }

                confirmTitle.textContent = confirmationTitle;
                confirmDescription.textContent = description;
                dialog.classList.remove('hidden');
                dialog.classList.add('flex');
                document.getElementById('confirmGatewaySave').focus();
            });

            document.getElementById('cancelGatewaySave').addEventListener('click', cancelFallbackDialog);
            document.getElementById('confirmGatewaySave').addEventListener('click', () => {
                closeFallbackDialog();
                submitAfterConfirmation();
            });
            dialog.addEventListener('click', (event) => {
                if (event.target === dialog) {
                    cancelFallbackDialog();
                }
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !dialog.classList.contains('hidden')) {
                    cancelFallbackDialog();
                }
            });
        })();
    </script>
@endpush

@endsection
