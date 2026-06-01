@extends('core::pdf.layout')

@section('title', 'Purchase Order')

@section('document_title', 'PURCHASE ORDER')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. PO</td>
            <td><strong>{{ $purchaseOrder->po_number }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($purchaseOrder->status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal PO</td>
            <td>{{ optional($purchaseOrder->po_date)->format('d/m/Y') }}</td>
            <td class="meta-label">Vendor</td>
            <td><strong>{{ $purchaseOrder->vendor?->name ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Pengiriman</td>
            <td>{{ optional($purchaseOrder->delivery_date)->format('d/m/Y') ?? '-' }}</td>
            <td class="meta-label">Lokasi</td>
            <td>{{ $purchaseOrder->delivery_location ?? '-' }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">RINCIAN PO</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th class="amount">Qty</th>
                <th class="amount">Harga</th>
                <th class="amount">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description ?? $item->procurementItem?->name ?? '-' }}</td>
                    <td class="amount">{{ $item->quantity }}</td>
                    <td class="amount">{{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                    <td class="amount">{{ number_format((float) $item->line_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="data-table" style="margin-top: 12px; width: 50%; float: right;">
        <tr><td>Subtotal</td><td class="amount">{{ number_format((float) $purchaseOrder->subtotal, 0, ',', '.') }}</td></tr>
        <tr><td>Diskon</td><td class="amount">{{ number_format((float) $purchaseOrder->discount_amount, 0, ',', '.') }}</td></tr>
        <tr><td>Pajak</td><td class="amount">{{ number_format((float) $purchaseOrder->tax_amount, 0, ',', '.') }}</td></tr>
        <tr class="total-row"><td>Total</td><td class="amount">{{ number_format((float) $purchaseOrder->total_amount, 0, ',', '.') }}</td></tr>
    </table>

    @if($purchaseOrder->terms_conditions)
        <div style="clear: both; margin-top: 80px;">
            <strong>Syarat & Ketentuan:</strong> {{ $purchaseOrder->terms_conditions }}
        </div>
    @endif
@endsection
