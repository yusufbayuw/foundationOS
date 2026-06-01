@extends('core::pdf.layout')

@section('title', 'Yudisium')

@section('document_title', 'SURAT / BERITA ACARA YUDISIUM')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">Nama</td>
            <td colspan="3"><strong>{{ $yudisium->name }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal</td>
            <td>{{ optional($yudisium->held_at)->format('d/m/Y') ?? '-' }}</td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($yudisium->status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tahun Akademik</td>
            <td>{{ $yudisium->academicYear?->name ?? '-' }}</td>
            <td class="meta-label">Organisasi</td>
            <td>{{ $yudisium->organization?->name ?? '-' }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <p style="margin-top: 16px; line-height: 1.6;">
        Dokumen ini merupakan surat/berita acara yudisium
        <strong>{{ $yudisium->name }}</strong>
        yang diselenggarakan pada tanggal
        <strong>{{ optional($yudisium->held_at)->format('d F Y') ?? 'belum ditentukan' }}</strong>.
    </p>
@endsection
