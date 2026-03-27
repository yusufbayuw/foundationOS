<x-filament-panels::page>
    <x-filament::card>
        {{ $this->form }}
    </x-filament::card>

    @php
        $recap = $this->getRecapData();
    @endphp

    @if($recap->isNotEmpty())
        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-900 border-b border-gray-200 dark:bg-white/5 dark:text-white dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 font-semibold text-left">Nama Siswa</th>
                        <th class="px-6 py-3 font-semibold text-center">Hadir</th>
                        <th class="px-6 py-3 font-semibold text-center">Sakit</th>
                        <th class="px-6 py-3 font-semibold text-center">Izin</th>
                        <th class="px-6 py-3 font-semibold text-center">Alpa</th>
                        <th class="px-6 py-3 font-semibold text-center">Terlambat</th>
                        <th class="px-6 py-3 font-semibold text-center">% Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($recap as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">{{ $row->student_name }}</td>
                            <td class="px-6 py-4 text-center dark:text-gray-300">{{ $row->total_present }}</td>
                            <td class="px-6 py-4 text-center"><span class="text-yellow-600 font-medium">{{ $row->total_sick }}</span></td>
                            <td class="px-6 py-4 text-center"><span class="text-blue-600 font-medium">{{ $row->total_permission }}</span></td>
                            <td class="px-6 py-4 text-center"><span class="text-red-600 font-medium">{{ $row->total_absent }}</span></td>
                            <td class="px-6 py-4 text-center"><span class="text-orange-500 font-medium">{{ $row->total_late }}</span></td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $colorClass = $row->percentage >= 90 ? 'text-green-600 dark:text-green-400' : ($row->percentage >= 75 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400');
                                @endphp
                                <span class="font-bold {{ $colorClass }}">{{ $row->percentage }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($class_id && $academic_period_id)
        <div class="px-4 py-8 text-gray-500 rounded-lg bg-gray-50 border border-gray-200 dark:bg-gray-800 dark:border-gray-700 text-center dark:text-gray-400">
            Sebuah kriteria telah dipilih, namun tidak ada data kehadiran siswa yang ditemukan.
        </div>
    @endif
</x-filament-panels::page>
