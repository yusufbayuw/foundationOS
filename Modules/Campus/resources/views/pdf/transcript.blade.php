@extends('core::pdf.layout')

@section('title', 'Transkrip / KHS')

@section('document_title', 'TRANSKRIP NILAI (KHS)')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">NIM</td>
            <td><strong>{{ $student->student_number }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($student->status ?? '-') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Nama</td>
            <td colspan="3"><strong>{{ $student->full_name ?? $student->user?->name ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Program Studi</td>
            <td>{{ $student->studyProgram?->name ?? '-' }}</td>
            <td class="meta-label">IPK</td>
            <td><strong>{{ $gpa !== null ? number_format((float) $gpa, 2) : '-' }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Total SKS</td>
            <td colspan="3">{{ $totalCredits ?? 0 }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">RIWAYAT NILAI</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Periode</th>
                <th>Kode MK</th>
                <th>Mata Kuliah</th>
                <th class="amount">SKS</th>
                <th class="amount">Nilai</th>
                <th class="amount">Bobot</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->studyPlan?->academicPeriod?->name ?? '-' }}</td>
                    <td>{{ $item->course?->code ?? '-' }}</td>
                    <td>{{ $item->course?->name ?? '-' }}</td>
                    <td class="amount">{{ $item->credits }}</td>
                    <td class="amount">{{ $item->studyResult?->grade_letter ?? '-' }}</td>
                    <td class="amount">{{ $item->studyResult?->grade_point !== null ? number_format((float) $item->studyResult->grade_point, 2) : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Belum ada nilai yang dipublikasikan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
