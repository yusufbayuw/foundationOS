@extends('core::pdf.layout')

@section('title', 'Sertifikat Pelatihan')

@section('document_title', 'SERTIFIKAT PELATIHAN')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. Sertifikat</td>
            <td><strong>{{ $certificate->certificate_number }}</strong></td>
            <td class="meta-label">Tanggal Terbit</td>
            <td>{{ optional($certificate->issued_at)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Peserta</td>
            <td><strong>{{ $enrollment?->participant_name ?? '-' }}</strong></td>
            <td class="meta-label">Program</td>
            <td>{{ $enrollment?->batch?->program?->name ?? '-' }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div style="margin: 24px 0; text-align: center;">
        <p style="font-size: 14px;">Dengan ini menyatakan bahwa:</p>
        <p style="font-size: 18px; font-weight: bold; margin: 12px 0;">
            {{ $enrollment?->participant_name ?? 'Peserta' }}
        </p>
        <p style="font-size: 14px;">telah menyelesaikan pelatihan:</p>
        <p style="font-size: 16px; font-weight: bold; margin: 12px 0;">
            {{ $enrollment?->batch?->program?->name ?? 'Program Pelatihan' }}
        </p>
    </div>

    @if(! empty($qrSvg))
        <div style="text-align: center; margin-top: 24px;">
            {!! $qrSvg !!}
            <p style="font-size: 10px; margin-top: 8px;">Scan untuk verifikasi sertifikat</p>
        </div>
    @endif
@endsection
