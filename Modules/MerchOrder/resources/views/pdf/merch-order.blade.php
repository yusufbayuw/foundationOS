@extends('core::pdf.layout')

@section('title', 'Order Merchandise')

@section('document_title', 'ORDER MERCHANDISE')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">Kode</td>
            <td><strong>{{ $merchOrder->code ?? '-' }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($merchOrder->status->label()) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Nama</td>
            <td colspan="3"><strong>{{ $merchOrder->name ?? '-' }}</strong></td>
        </tr>
    </table>
@endsection

@section('content')
    @if($merchOrder->description)
        <div style="margin: 16px 0;">
            <strong>Keterangan:</strong> {{ $merchOrder->description }}
        </div>
    @endif

    @if(is_array($merchOrder->meta) && $merchOrder->meta !== [])
        <div class="section-title">DETAIL ORDER</div>
        <table class="data-table">
            @foreach($merchOrder->meta as $key => $value)
                <tr>
                    <td style="width: 30%;">{{ ucfirst(str_replace('_', ' ', (string) $key)) }}</td>
                    <td>{{ is_scalar($value) ? $value : json_encode($value) }}</td>
                </tr>
            @endforeach
        </table>
    @endif
@endsection
