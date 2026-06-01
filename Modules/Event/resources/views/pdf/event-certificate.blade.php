@extends('core::pdf.layout')

@section('title', 'Sertifikat Acara')

@section('document_title', 'SERTIFIKAT ACARA')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">Kode</td>
            <td><strong>{{ $certificate->code ?? '-' }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($certificate->status ?? '-') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Nama</td>
            <td colspan="3"><strong>{{ $certificate->name ?? '-' }}</strong></td>
        </tr>
    </table>
@endsection

@section('content')
    <div style="margin: 24px 0; text-align: center;">
        <p style="font-size: 16px; font-weight: bold;">{{ $certificate->name ?? 'Peserta Acara' }}</p>
    </div>

    @if($certificate->description)
        <div style="margin-top: 16px; text-align: center;">
            {{ $certificate->description }}
        </div>
    @endif
@endsection
