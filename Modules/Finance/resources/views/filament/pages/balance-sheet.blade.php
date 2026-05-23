<x-filament-panels::page>
    @if(empty($reportData))
        <x-filament::section>
            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ \Modules\Core\Support\FilamentUi::text('No data available.') }}</p>
        </x-filament::section>
    @else
    <div class="text-xs text-gray-500 dark:text-gray-400 mb-4">
        {{ \Modules\Core\Support\FilamentUi::text('As Of') }}: <strong>{{ $reportData['as_of'] }}</strong>
        @if(!$reportData['is_balanced'])
            <span class="ml-4 text-red-500 font-bold">⚠ {{ \Modules\Core\Support\FilamentUi::text('Balance sheet is not balanced!') }}</span>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Assets --}}
        <x-filament::section :heading="\Modules\Core\Support\FilamentUi::text('Assets')">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="text-left py-1 font-medium text-gray-600 dark:text-gray-400">{{ \Modules\Core\Support\FilamentUi::text('Account') }}</th>
                    <th class="text-right py-1 font-medium text-gray-600 dark:text-gray-400">{{ \Modules\Core\Support\FilamentUi::text('Amount') }}</th>
                </tr></thead>
                <tbody>
                    @forelse($reportData['assets'] as $row)
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
                        <td class="py-2 text-gray-800 dark:text-gray-200">{{ \Modules\Core\Support\FilamentUi::text('Total Assets') }}</td>
                        <td class="py-2 text-right text-blue-700 dark:text-blue-400">{{ number_format($reportData['total_assets'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </x-filament::section>

        {{-- Liabilities + Equity --}}
        <div class="space-y-4">
            <x-filament::section :heading="\Modules\Core\Support\FilamentUi::text('Liabilities')">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-1 font-medium text-gray-600 dark:text-gray-400">{{ \Modules\Core\Support\FilamentUi::text('Account') }}</th>
                        <th class="text-right py-1 font-medium text-gray-600 dark:text-gray-400">{{ \Modules\Core\Support\FilamentUi::text('Amount') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse($reportData['liabilities'] as $row)
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
                            <td class="py-2 text-gray-800 dark:text-gray-200">{{ \Modules\Core\Support\FilamentUi::text('Total Liabilities') }}</td>
                            <td class="py-2 text-right text-orange-700 dark:text-orange-400">{{ number_format($reportData['total_liabilities'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </x-filament::section>

            <x-filament::section :heading="\Modules\Core\Support\FilamentUi::text('Equity')">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-1 font-medium text-gray-600 dark:text-gray-400">{{ \Modules\Core\Support\FilamentUi::text('Account') }}</th>
                        <th class="text-right py-1 font-medium text-gray-600 dark:text-gray-400">{{ \Modules\Core\Support\FilamentUi::text('Amount') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse($reportData['equity'] as $row)
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
                            <td class="py-2 text-gray-800 dark:text-gray-200">{{ \Modules\Core\Support\FilamentUi::text('Total Equity') }}</td>
                            <td class="py-2 text-right text-purple-700 dark:text-purple-400">{{ number_format($reportData['total_equity'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </x-filament::section>
        </div>
    </div>
    @endif
</x-filament-panels::page>
