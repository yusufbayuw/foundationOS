@extends('core::pdf.layout')

@section('title', 'Surat Resmi')

@section('document_title', strtoupper($letter->name ?? 'SURAT RESMI'))

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">Nomor Surat</td>
            <td><strong>{{ $letter->letter_number ?? $letter->code ?? '-' }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($letter->status ?? '-') }}</td>
        </tr>
        @if($letter->direction)
            <tr>
                <td class="meta-label">Arah</td>
                <td>{{ strtoupper($letter->direction) }}</td>
                <td class="meta-label">Kode</td>
                <td>{{ $letter->code ?? '-' }}</td>
            </tr>
        @endif
    </table>
@endsection

@section('content')
    @if($letter->description)
        <div style="margin-top: 16px; text-align: justify; white-space: pre-line;">{{ $letter->description }}</div>
    @else
        <p style="margin-top: 16px; text-align: justify;">
            Dokumen surat resmi dengan nomor <strong>{{ $letter->letter_number ?? $letter->code ?? '-' }}</strong>.
        </p>
    @endif

    @if($verificationUrl)
        <div style="margin-top: 24px; border-top: 1px solid #e5e7eb; padding-top: 12px;">
            <table style="width: 100%;">
                <tr>
                    <td style="vertical-align: top; width: 70%;">
                        <div class="section-title" style="margin-top: 0;">VERIFIKASI DOKUMEN</div>
                        <p style="margin-top: 8px; font-size: 10px; word-break: break-all;">
                            Scan QR code atau kunjungi tautan berikut untuk memverifikasi keaslian surat ini:
                        </p>
                        <p style="font-size: 9px; color: #555; word-break: break-all;">{{ $verificationUrl }}</p>
                    </td>
                    @if(! empty($qrSvg))
                        <td style="vertical-align: top; text-align: center; width: 30%;">
                            {!! $qrSvg !!}
                        </td>
                    @endif
                </tr>
            </table>
        </div>
    @endif
@endsection
