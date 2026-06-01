@extends('core::pdf.layout')

@section('title', 'Permintaan Pembelian')

@section('document_title', 'PERMINTAAN PEMBELIAN')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. Permintaan</td>
            <td><strong>{{ $requisition->request_number }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($requisition->status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal</td>
            <td>{{ optional($requisition->request_date)->format('d/m/Y') }}</td>
            <td class="meta-label">Dibutuhkan</td>
            <td>{{ optional($requisition->required_date)->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Pemohon</td>
            <td>{{ $requisition->requester?->name ?? '-' }}</td>
            <td class="meta-label">Disetujui Oleh</td>
            <td>{{ $requisition->approver?->name ?? '-' }}</td>
        </tr>
    </table>
@endsection

@section('content')
    @if($requisition->justification)
        <p><strong>Alasan:</strong> {{ $requisition->justification }}</p>
    @endif

    <div class="section-title">RINCIAN BARANG/JASA</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th class="amount">Qty</th>
                <th class="amount">Est. Harga</th>
                <th class="amount">Est. Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requisition->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description ?? $item->procurementItem?->name ?? '-' }}</td>
                    <td class="amount">{{ $item->quantity_requested }}</td>
                    <td class="amount">{{ number_format((float) $item->estimated_unit_price, 0, ',', '.') }}</td>
                    <td class="amount">{{ number_format((float) $item->estimated_total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="data-table" style="margin-top: 12px; width: 50%; float: right;">
        <tr class="total-row">
            <td>Total Estimasi</td>
            <td class="amount">{{ number_format((float) $requisition->total_estimated_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if($requisition->notes)
        <div style="clear: both; margin-top: 80px;">
            <strong>Catatan:</strong> {{ $requisition->notes }}
        </div>
    @endif
@endsection
