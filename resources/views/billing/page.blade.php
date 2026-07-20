<x-filament-panels::page>
    @php
        $tenant = filament()->getTenant();
        $plan = $tenant?->subscriptionPlan;
        $status = $tenant?->status ?? 'unknown';
        $statusColor = match($status) {
            'active' => 'success',
            'trial' => 'warning',
            'past_due' => 'danger',
            'suspended' => 'danger',
            default => 'gray',
        };
    @endphp

    {{-- Status Banner --}}
    @if ($tenant?->isLocked())
        <x-filament::section>
            <div class="flex items-center gap-3 text-danger-600 dark:text-danger-400">
                <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-6 h-6" />
                <div>
                    <p class="font-semibold">{{ __('Akses ditangguhkan') }}</p>
                    <p class="text-sm">{{ __('Langganan Anda telah berakhir. Bayar invoice di bawah untuk memulihkan akses.') }}</p>
                </div>
            </div>
        </x-filament::section>
    @elseif ($tenant?->isInGracePeriod())
        <x-filament::section>
            <div class="flex items-center gap-3 text-warning-600 dark:text-warning-400">
                <x-filament::icon icon="heroicon-o-clock" class="w-6 h-6" />
                <div>
                    <p class="font-semibold">{{ __('Masa tenggang aktif') }}</p>
                    <p class="text-sm">{{ __('Pembayaran diperlukan sebelum') }} {{ $tenant->grace_period_ends_at?->translatedFormat('d F Y') }}</p>
                </div>
            </div>
        </x-filament::section>
    @endif

    {{-- Current Plan Summary --}}
    <x-filament::section>
        <x-slot name="heading">{{ \Modules\Core\Support\FilamentUi::text('Current Plan') }}</x-slot>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Plan') }}</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $plan?->name ?? __('No Plan') }}
                </p>
            </div>
            <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Status') }}</p>
                <p class="mt-1">
                    <x-filament::badge :color="$statusColor">{{ ucfirst($status) }}</x-filament::badge>
                </p>
            </div>
            <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Expires') }}</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                    {{ $tenant?->subscription_expires_at?->translatedFormat('d F Y') ?? '-' }}
                </p>
            </div>
            <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ \Modules\Core\Support\FilamentUi::text('Monthly Estimate') }}</p>
                <p class="mt-1 text-lg font-semibold text-primary-600 dark:text-primary-400">
                    {{ $this->formatAmount($currentAmounts['total'] ?? 0) }}
                </p>
            </div>
        </div>

        @if ($currentAmounts && $currentAmounts['total'] > 0)
            <div class="mt-4 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Rincian tagihan') }}</p>
                <div class="space-y-1 text-sm">
                    @if ($currentAmounts['base'] > 0)
                        <div class="flex justify-between">
                            <span>{{ __('Base plan') }}</span>
                            <span>{{ $this->formatAmount($currentAmounts['base']) }}</span>
                        </div>
                    @endif
                    @if ($currentAmounts['seats'] > 0)
                        <div class="flex justify-between">
                            <span>{{ __('User seats') }} ({{ $currentAmounts['active_seats'] }} users)</span>
                            <span>{{ $this->formatAmount($currentAmounts['seats']) }}</span>
                        </div>
                    @endif
                    @if ($currentAmounts['modules'] > 0)
                        <div class="flex justify-between">
                            <span>{{ __('Active modules') }} ({{ $currentAmounts['active_modules'] }} modules)</span>
                            <span>{{ $this->formatAmount($currentAmounts['modules']) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-1 font-semibold">
                        <span>Total</span>
                        <span>{{ $this->formatAmount($currentAmounts['total']) }}</span>
                    </div>
                </div>
            </div>
        @endif
    </x-filament::section>

    {{-- Invoice History --}}
    <x-filament::section>
        <x-slot name="heading">{{ \Modules\Core\Support\FilamentUi::text('Invoice History') }}</x-slot>
        <x-slot name="headerEnd">
            <x-filament::button
                wire:click="generateInvoice"
                size="sm"
                color="gray"
                icon="heroicon-o-document-plus"
            >
                {{ \Modules\Core\Support\FilamentUi::text('Generate Invoice') }}
            </x-filament::button>
        </x-slot>

        @php $invoices = $this->getRecentInvoices(); @endphp

        <div wire:poll.10s>
        @if ($invoices->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">{{ __('Belum ada invoice.') }}</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 font-medium text-gray-600 dark:text-gray-400">{{ __('Invoice') }}</th>
                            <th class="text-left py-2 font-medium text-gray-600 dark:text-gray-400">{{ __('Period') }}</th>
                            <th class="text-right py-2 font-medium text-gray-600 dark:text-gray-400">{{ __('Amount') }}</th>
                            <th class="text-left py-2 font-medium text-gray-600 dark:text-gray-400">{{ __('Status') }}</th>
                            <th class="text-right py-2 font-medium text-gray-600 dark:text-gray-400">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td class="py-2 font-mono text-xs">{{ $invoice->invoice_number }}</td>
                                <td class="py-2">
                                    {{ $invoice->period_start?->format('M Y') ?? '-' }}
                                </td>
                                <td class="py-2 text-right">
                                    {{ \Modules\Core\Support\CurrencyFormatter::format((float) $invoice->amount, $invoice->currency ?: $this->currency) }}
                                </td>
                                <td class="py-2">
                                    <div class="flex flex-col gap-1">
                                        <x-filament::badge
                                            :color="match($invoice->payment_status) {
                                                'paid' => 'success',
                                                'pending' => 'warning',
                                                'failed' => 'danger',
                                                default => 'gray',
                                            }"
                                        >
                                            {{ ucfirst($invoice->payment_status ?? 'unknown') }}
                                        </x-filament::badge>

                                        @if (data_get($invoice->metadata, 'payment_session_status') === 'queued' || data_get($invoice->metadata, 'payment_session_status') === 'preparing')
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Preparing payment session…') }}</span>
                                        @elseif (data_get($invoice->metadata, 'payment_session_status') === 'ready')
                                            <span class="text-xs text-success-600 dark:text-success-400">{{ __('Payment ready') }}</span>
                                        @elseif (data_get($invoice->metadata, 'payment_session_status') === 'failed')
                                            <span class="text-xs text-danger-600 dark:text-danger-400">{{ __('Payment session failed') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-2 text-right">
                                    @if (in_array($invoice->payment_status, ['pending', 'failed']))
                                        <x-filament::button
                                            wire:click="payInvoice({{ $invoice->id }})"
                                            size="xs"
                                            :color="data_get($invoice->metadata, 'payment_session_status') === 'ready' ? 'success' : 'primary'"
                                            :icon="data_get($invoice->metadata, 'payment_session_status') === 'ready' ? 'heroicon-o-arrow-top-right-on-square' : 'heroicon-o-credit-card'"
                                        >
                                            {{ data_get($invoice->metadata, 'payment_session_status') === 'ready' ? __('Open Payment') : \Modules\Core\Support\FilamentUi::text('Pay') }}
                                        </x-filament::button>
                                    @elseif ($invoice->payment_status === 'paid')
                                        <span class="text-success-600 dark:text-success-400 text-xs">✓ Paid</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        </div>
    </x-filament::section>

    {{-- Midtrans Snap Script --}}
    @push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endpush

    @script
    <script>
        $wire.on('open-midtrans-snap', ({ token }) => {
            if (typeof snap !== 'undefined') {
                snap.pay(token, {
                    onSuccess: function (result) {
                        window.location.reload();
                    },
                    onPending: function (result) {
                        window.location.reload();
                    },
                    onError: function (result) {
                        console.error('Midtrans error', result);
                    },
                    onClose: function () {
                        // user closed without paying
                    },
                });
            }
        });
    </script>
    @endscript
</x-filament-panels::page>
