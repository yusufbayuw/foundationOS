@extends('core::pdf.layout')

@section('title', 'Berita Acara Serah Terima')

@section('document_title', 'BERITA ACARA SERAH TERIMA (BAST)')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. Penerimaan</td>
            <td><strong>{{ $receipt->receipt_number }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($receipt->status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal</td>
            <td>{{ optional($receipt->receipt_date)->format('d/m/Y') }}</td>
            <td class="meta-label">No. PO</td>
            <td>{{ $receipt->purchaseOrder?->po_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Vendor</td>
            <td>{{ $receipt->purchaseOrder?->vendor?->name ?? '-' }}</td>
            <td class="meta-label">Diterima Oleh</td>
            <td>{{ $receipt->receiver?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Surat Jalan</td>
            <td>{{ $receipt->delivery_note_number ?? '-' }}</td>
            <td class="meta-label">No. Pengiriman Vendor</td>
            <td>{{ $receipt->supplier_delivery_number ?? '-' }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">RINCIAN PENERIMAAN</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th class="amount">Diterima</th>
                <th class="amount">Diterima Baik</th>
                <th class="amount">Harga</th>
                <th class="amount">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receipt->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->purchaseOrderItem?->description ?? $item->purchaseOrderItem?->procurementItem?->name ?? '-' }}</td>
                    <td class="amount">{{ $item->quantity_received }}</td>
                    <td class="amount">{{ $item->quantity_accepted }}</td>
                    <td class="amount">{{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                    <td class="amount">{{ number_format((float) $item->total_amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($receipt->inspection_notes)
        <p style="margin-top: 16px;"><strong>Catatan Inspeksi:</strong> {{ $receipt->inspection_notes }}</p>
    @endif

    @if($receipt->notes)
        <p><strong>Catatan:</strong> {{ $receipt->notes }}</p>
    @endif
@endsection
