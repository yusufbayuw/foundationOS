<x-filament-panels::page>
    <x-filament::section>
        {{ $this->form }}
    </x-filament::section>

    @php
        $ledger = $this->getLedgerPreview();
    @endphp

    @if($ledger)
        <x-filament::section>
            <x-slot name="heading">
                {{ $ledger['schoolClass']->name }} — {{ $ledger['period']->name }}
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse border border-gray-200 dark:border-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="border border-gray-200 dark:border-gray-700 p-2">Siswa</th>
                            @foreach($ledger['assessments'] as $assessment)
                                <th class="border border-gray-200 dark:border-gray-700 p-2 text-center">
                                    {{ $assessment->subject?->name ?? 'Mapel' }}<br>
                                    <span class="text-xs text-gray-500">{{ $assessment->name }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ledger['rows'] as $row)
                            <tr>
                                <td class="border border-gray-200 dark:border-gray-700 p-2">{{ $row['student_name'] }}</td>
                                @foreach($ledger['assessments'] as $assessment)
                                    <td class="border border-gray-200 dark:border-gray-700 p-2 text-center">
                                        @php $score = $row['scores'][$assessment->id] ?? null; @endphp
                                        {{ $score !== null ? number_format((float) $score, 0) : '-' }}
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 1 + $ledger['assessments']->count() }}" class="border border-gray-200 dark:border-gray-700 p-4 text-center text-gray-500">
                                    Belum ada data nilai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
