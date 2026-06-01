@extends('core::pdf.layout')

@section('title', 'Kontrak Hukum')

@section('document_title', 'KONTRAK HUKUM')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">Kode</td>
            <td><strong>{{ $contract->code ?? '-' }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($contract->status ?? '-') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Nama</td>
            <td colspan="3"><strong>{{ $contract->name ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Berlaku Sejak</td>
            <td>{{ optional($contract->effective_date)->format('d/m/Y') ?? '-' }}</td>
            <td class="meta-label">Berakhir</td>
            <td>{{ optional($contract->expires_at)->format('d/m/Y') ?? '-' }}</td>
        </tr>
    </table>
@endsection

@section('content')
    @if($contract->description)
        <div style="margin: 16px 0;">
            <strong>Ringkasan:</strong> {{ $contract->description }}
        </div>
    @endif

    <table class="data-table">
        <tr>
            <td style="width: 30%;">Perpanjangan Otomatis</td>
            <td>{{ $contract->auto_renew ? 'Ya' : 'Tidak' }}</td>
        </tr>
        <tr>
            <td>Periode Pemberitahuan (hari)</td>
            <td>{{ $contract->notice_period_days ?? '-' }}</td>
        </tr>
    </table>
@endsection
