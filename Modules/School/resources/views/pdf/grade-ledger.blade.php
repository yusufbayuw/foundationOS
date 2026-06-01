@extends('core::pdf.layout')

@section('title', 'Buku Nilai Kelas')

@section('document_title', 'BUKU NILAI KELAS')

@push('styles')
    <style>
        table.data-table th,
        table.data-table td { font-size: 9px; padding: 3px 4px; }
    </style>
@endpush

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">Kelas</td>
            <td><strong>{{ $schoolClass->name }}</strong></td>
            <td class="meta-label">Periode</td>
            <td>{{ $period->name }}</td>
        </tr>
        <tr>
            <td class="meta-label">Jumlah Siswa</td>
            <td>{{ $rows->count() }}</td>
            <td class="meta-label">Jumlah Penilaian</td>
            <td>{{ $assessments->count() }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">LEDGER NILAI SISWA</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>NIS</th>
                @foreach($assessments as $assessment)
                    <th class="amount" title="{{ $assessment->name }}">
                        {{ $assessment->subject?->code ?? $assessment->subject?->name ?? 'Mapel' }}<br>
                        <span style="font-weight: normal;">{{ \Illuminate\Support\Str::limit($assessment->name, 12) }}</span>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['student_name'] }}</td>
                    <td>{{ $row['nis'] ?? '-' }}</td>
                    @foreach($assessments as $assessment)
                        <td class="amount">
                            @php $score = $row['scores'][$assessment->id] ?? null; @endphp
                            {{ $score !== null ? number_format((float) $score, 0) : '-' }}
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 3 + $assessments->count() }}" class="text-center">Belum ada data nilai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
