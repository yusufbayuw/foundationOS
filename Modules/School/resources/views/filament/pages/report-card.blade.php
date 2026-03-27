<x-filament-panels::page>
    <x-filament::section>
        {{ $this->form }}
    </x-filament::section>
    
    @if($academic_period_id && $student_id)
        @php
            $service = new \Modules\School\Services\ReportCardService();
            $data = $service->generate($student_id, $academic_period_id);
        @endphp
        
        <x-filament::section>
            <x-slot name="heading">
                Preview Rapor - {{ $data['student']->user->name ?? 'N/A' }}
            </x-slot>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <span class="font-bold">Kelas:</span> {{ $data['class_name'] }}<br>
                    <span class="font-bold">Periode:</span> {{ $data['period']->name }}
                </div>
                <div class="text-right">
                    <span class="font-bold">Kehadiran:</span> Sakit ({{ $data['attendance']['sick'] }}), Izin ({{ $data['attendance']['permission'] }}), Alpa ({{ $data['attendance']['absent'] }})
                </div>
            </div>
            
            <table class="w-full text-left border-collapse border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="border border-gray-200 dark:border-gray-700 p-2">Mata Pelajaran</th>
                        <th class="border border-gray-200 dark:border-gray-700 p-2 text-center">Rata-Rata Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['subjects'] as $subject)
                        <tr>
                            <td class="border border-gray-200 dark:border-gray-700 p-2">{{ $subject['name'] }}</td>
                            <td class="border border-gray-200 dark:border-gray-700 p-2 text-center">{{ $subject['average'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="border border-gray-200 dark:border-gray-700 p-4 text-center text-gray-500">Belum ada nilai untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($data['subjects']) > 0)
                <tfoot class="bg-gray-50 dark:bg-gray-800 font-bold">
                    <tr>
                        <td class="border border-gray-200 dark:border-gray-700 p-2 text-right">Rata-Rata Keseluruhan</td>
                        <td class="border border-gray-200 dark:border-gray-700 p-2 text-center">{{ $data['overall_average'] }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </x-filament::section>
    @endif
</x-filament-panels::page>
