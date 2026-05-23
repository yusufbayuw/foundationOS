<x-filament-panels::page>
    @if(empty($reportData))
        <x-filament::section>
            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ \Modules\Core\Support\FilamentUi::text('No data available.') }}</p>
        </x-filament::section>
    @else
    <div class="text-xs text-gray-500 dark:text-gray-400 mb-4">
        {{ \Modules\Core\Support\FilamentUi::text('Period') }}: <strong>{{ $reportData['period_from'] }}</strong> — <strong>{{ $reportData['period_to'] }}</strong>
    </div>

    {{-- Revenue --}}
    <x-filament::section :heading="\Modules\Core\Support\FilamentUi::text('Revenue')">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="text-left py-1 font-medium text-gray-600 dark:text-gray-400">{{ \Modules\Core\Support\FilamentUi::text('Account') }}</th>
                    <th class="text-right py-1 font-medium text-gray-600 dark:text-gray-400">{{ \Modules\Core\Support\FilamentUi::text('Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData['revenue'] as $row)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="py-1.5 text-gray-700 dark:text-gray-300">{{ $row->code }} — {{ $row->name }}</td>
                    <td class="py-1.5 text-right text-gray-700 dark:text-gray-300">{{ number_format($row->balance, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="py-2 text-gray-400 italic text-center">—</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-gray-400 dark:border-gray-600 font-bold">
                    <td class="py-2 text-gray-800 dark:text-gray-200">{{ \Modules\Core\Support\FilamentUi::text('Total Revenue') }}</td>
                    <td class="py-2 text-right text-green-700 dark:text-green-400">{{ number_format($reportData['total_revenue'], 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </x-filament::section>

    {{-- Expenses --}}
    <x-filament::section :heading="\Modules\Core\Support\FilamentUi::text('Expenses')" class="mt-4">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="text-left py-1 font-medium text-gray-600 dark:text-gray-400">{{ \Modules\Core\Support\FilamentUi::text('Account') }}</th>
                    <th class="text-right py-1 font-medium text-gray-600 dark:text-gray-400">{{ \Modules\Core\Support\FilamentUi::text('Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData['expenses'] as $row)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="py-1.5 text-gray-700 dark:text-gray-300">{{ $row->code }} — {{ $row->name }}</td>
                    <td class="py-1.5 text-right text-gray-700 dark:text-gray-300">{{ number_format($row->balance, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="py-2 text-gray-400 italic text-center">—</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-gray-400 dark:border-gray-600 font-bold">
                    <td class="py-2 text-gray-800 dark:text-gray-200">{{ \Modules\Core\Support\FilamentUi::text('Total Expenses') }}</td>
                    <td class="py-2 text-right text-red-700 dark:text-red-400">{{ number_format($reportData['total_expenses'], 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </x-filament::section>

    {{-- Net Income --}}
    <div class="mt-4 rounded-xl border-2 {{ $reportData['net_income'] >= 0 ? 'border-green-500 bg-green-50 dark:bg-green-950' : 'border-red-500 bg-red-50 dark:bg-red-950' }} px-6 py-4 flex justify-between items-center">
        <span class="font-bold text-lg text-gray-800 dark:text-gray-100">{{ \Modules\Core\Support\FilamentUi::text('Net Income') }}</span>
        <span class="font-bold text-xl {{ $reportData['net_income'] >= 0 ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
            Rp {{ number_format($reportData['net_income'], 0, ',', '.') }}
        </span>
    </div>
    @endif
</x-filament-panels::page>
