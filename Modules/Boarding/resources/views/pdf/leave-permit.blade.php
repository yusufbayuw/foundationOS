@extends('core::pdf.layout')

@section('title', 'Izin Keluar Asrama')

@section('document_title', 'SURAT IZIN KELUAR ASRAMA')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">Kode</td>
            <td><strong>{{ $permit->code ?? '-' }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($permit->status ?? '-') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Nama</td>
            <td colspan="3"><strong>{{ $permit->name ?? '-' }}</strong></td>
        </tr>
    </table>
@endsection

@section('content')
    @if($permit->description)
        <div style="margin: 16px 0;">
            <strong>Keterangan:</strong> {{ $permit->description }}
        </div>
    @endif

    @if(is_array($permit->meta) && $permit->meta !== [])
        <div class="section-title">DETAIL IZIN</div>
        <table class="data-table">
            @foreach($permit->meta as $key => $value)
                <tr>
                    <td style="width: 30%;">{{ ucfirst(str_replace('_', ' ', (string) $key)) }}</td>
                    <td>{{ is_scalar($value) ? $value : json_encode($value) }}</td>
                </tr>
            @endforeach
        </table>
    @endif
@endsection
