@extends('core::pdf.layout')

@section('title', 'Sertifikat Prestasi')

@section('document_title', 'SERTIFIKAT PRESTASI')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">Nama Siswa</td>
            <td><strong>{{ $achievement->student?->user?->name ?? '-' }}</strong></td>
            <td class="meta-label">NIS</td>
            <td>{{ $achievement->student?->nis ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Jenis Prestasi</td>
            <td>{{ $achievement->achievementType?->name ?? '-' }}</td>
            <td class="meta-label">Tanggal</td>
            <td>{{ optional($achievement->event_date)->format('d/m/Y') ?? '-' }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div style="margin: 24px 0; text-align: center;">
        <p style="font-size: 14px;">Dengan ini menyatakan bahwa:</p>
        <p style="font-size: 18px; font-weight: bold; margin: 12px 0;">
            {{ $achievement->student?->user?->name ?? 'Peserta Didik' }}
        </p>
        <p style="font-size: 14px;">telah meraih prestasi:</p>
        <p style="font-size: 16px; font-weight: bold; margin: 12px 0;">{{ $achievement->title }}</p>
    </div>

    <table class="data-table">
        <tr>
            <td style="width: 30%;">Acara / Kompetisi</td>
            <td>{{ $achievement->event_name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Lokasi</td>
            <td>{{ $achievement->event_location ?? '-' }}</td>
        </tr>
        <tr>
            <td>Penyelenggara</td>
            <td>{{ $achievement->organizer ?? '-' }}</td>
        </tr>
        <tr>
            <td>Peringkat</td>
            <td>{{ $achievement->rank_position ?? '-' }}</td>
        </tr>
        <tr>
            <td>No. Sertifikat</td>
            <td>{{ $achievement->certificate_number ?? '-' }}</td>
        </tr>
    </table>

    @if($achievement->description)
        <div style="margin-top: 16px;">
            <strong>Keterangan:</strong> {{ $achievement->description }}
        </div>
    @endif
@endsection
