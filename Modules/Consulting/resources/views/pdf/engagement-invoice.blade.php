@extends('core::pdf.layout')

@section('title', 'Invoice Konsultasi')

@section('document_title', 'INVOICE ENGAGEMENT KONSULTASI')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">Kode</td>
            <td><strong>{{ $invoice->code ?? '-' }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($invoice->status ?? '-') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Nama</td>
            <td colspan="3"><strong>{{ $invoice->name ?? '-' }}</strong></td>
        </tr>
    </table>
@endsection

@section('content')
    @if($invoice->description)
        <div style="margin: 16px 0;">
            <strong>Keterangan:</strong> {{ $invoice->description }}
        </div>
    @endif

    @if(is_array($invoice->meta) && $invoice->meta !== [])
        <div class="section-title">RINCIAN INVOICE</div>
        <table class="data-table">
            @foreach($invoice->meta as $key => $value)
                <tr>
                    <td style="width: 30%;">{{ ucfirst(str_replace('_', ' ', (string) $key)) }}</td>
                    <td>{{ is_scalar($value) ? $value : json_encode($value) }}</td>
                </tr>
            @endforeach
        </table>
    @endif
@endsection
