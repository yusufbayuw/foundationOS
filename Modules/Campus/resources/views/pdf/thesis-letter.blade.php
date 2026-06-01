@extends('core::pdf.layout')

@section('title', 'Surat Tugas Skripsi')

@section('document_title', 'SURAT PENGAJUAN / TUGAS SKRIPSI')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">NIM</td>
            <td>{{ $student?->student_number ?? '-' }}</td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($thesis->status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Mahasiswa</td>
            <td colspan="3"><strong>{{ $student?->full_name ?? $student?->user?->name ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Program Studi</td>
            <td colspan="3">{{ $student?->studyProgram?->name ?? '-' }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">JUDUL PENELITIAN</div>
    <p style="margin-top: 8px;"><strong>{{ $thesis->title }}</strong></p>

    @if($thesis->research_area)
        <p><span class="text-muted">Bidang:</span> {{ $thesis->research_area }}</p>
    @endif

    <div class="section-title">PEMBIMBING & PENGUJI</div>
    <table class="data-table">
        <tr>
            <td>Pembimbing</td>
            <td>{{ $thesis->advisorLecturer?->user?->name ?? $thesis->advisorLecturer?->nidn ?? '-' }}</td>
        </tr>
        <tr>
            <td>Penguji</td>
            <td>{{ $thesis->examinerLecturer?->user?->name ?? $thesis->examinerLecturer?->nidn ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Sidang</td>
            <td>{{ optional($thesis->defense_date)->format('d/m/Y H:i') ?? '-' }}</td>
        </tr>
        @if($thesis->grade_letter)
            <tr>
                <td>Nilai</td>
                <td>{{ $thesis->grade_letter }} ({{ $thesis->grade_point !== null ? number_format((float) $thesis->grade_point, 2) : '-' }})</td>
            </tr>
        @endif
    </table>

    @if($thesis->notes)
        <div style="margin-top: 12px;">
            <strong>Catatan:</strong> {{ $thesis->notes }}
        </div>
    @endif
@endsection
