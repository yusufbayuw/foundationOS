<x-filament-panels::page>
    @if(empty($reportData))
        <x-filament::section>
            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ \Modules\Core\Support\FilamentUi::text('No data available.') }}</p>
        </x-filament::section>
    @else
    <div class="text-xs text-gray-500 dark:text-gray-400 mb-4">
        {{ \Modules\Core\Support\FilamentUi::text('Period') }}: <strong>{{ $reportData['period_from'] }}</strong> — <strong>{{ $reportData['period_to'] }}</strong>
    </div>

    @foreach([
        ['key' => 'operating', 'label' => \Modules\Core\Support\FilamentUi::text('Operating Activities')],
        ['key' => 'investing', 'label' => \Modules\Core\Support\FilamentUi::text('Investing Activities')],
        ['key' => 'financing', 'label' => \Modules\Core\Support\FilamentUi::text('Financing Activities')],
    ] as $section)
    <x-filament::section :heading="$section['label']" class="mb-4">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-gray-200 dark:border-gray-700">
                <th class="text-left py-1 font-medium text-gray-600 dark:text-gray-400">{{ \Modules\Core\Support\FilamentUi::text('Account') }}</th>
                <th class="text-right py-1 font-medium text-gray-600 dark:text-gray-400">{{ \Modules\Core\Support\FilamentUi::text('Amount') }}</th>
            </tr></thead>
            <tbody>
                @forelse($reportData[$section['key']] as $row)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="py-1.5 text-gray-700 dark:text-gray-300">{{ $row->code }} — {{ $row->name }}</td>
                    <td class="py-1.5 text-right {{ $row->balance >= 0 ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                        {{ $row->balance >= 0 ? '' : '(' }}{{ number_format(abs($row->balance), 0, ',', '.') }}{{ $row->balance >= 0 ? '' : ')' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="2" class="py-2 text-gray-400 italic text-center">—</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-filament::section>
    @endforeach

    {{-- Net Cash --}}
    <div class="rounded-xl border-2 {{ $reportData['net_cash'] >= 0 ? 'border-green-500 bg-green-50 dark:bg-green-950' : 'border-red-500 bg-red-50 dark:bg-red-950' }} px-6 py-4 flex justify-between items-center">
        <span class="font-bold text-lg text-gray-800 dark:text-gray-100">{{ \Modules\Core\Support\FilamentUi::text('Net Cash') }}</span>
        <span class="font-bold text-xl {{ $reportData['net_cash'] >= 0 ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
            Rp {{ number_format($reportData['net_cash'], 0, ',', '.') }}
        </span>
    </div>
    @endif
</x-filament-panels::page>
