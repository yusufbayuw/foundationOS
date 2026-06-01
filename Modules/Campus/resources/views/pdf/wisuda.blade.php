@extends('core::pdf.layout')

@section('title', 'Wisuda')

@section('document_title', 'SURAT / BERITA ACARA WISUDA')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">Nama Acara</td>
            <td colspan="3"><strong>{{ $wisuda->name }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal</td>
            <td>{{ optional($wisuda->held_at)->format('d/m/Y') ?? '-' }}</td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($wisuda->status) }}</td>
        </tr>
        @if($yudisium)
            <tr>
                <td class="meta-label">Yudisium</td>
                <td colspan="3">{{ $yudisium->name }}</td>
            </tr>
        @endif
    </table>
@endsection

@section('content')
    <p style="margin-top: 16px; line-height: 1.6;">
        Dokumen ini merupakan surat/berita acara wisuda
        <strong>{{ $wisuda->name }}</strong>
        yang diselenggarakan pada tanggal
        <strong>{{ optional($wisuda->held_at)->format('d F Y') ?? 'belum ditentukan' }}</strong>.
    </p>

    @if($yudisium)
        <p style="line-height: 1.6;">
            Acara wisuda ini terkait dengan yudisium
            <strong>{{ $yudisium->name }}</strong>
            ({{ optional($yudisium->held_at)->format('d/m/Y') ?? '-' }}).
        </p>
    @endif
@endsection
