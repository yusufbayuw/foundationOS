@extends('core::pdf.layout')

@section('title', 'Tagihan Vendor')

@section('document_title', 'TAGIHAN VENDOR')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. Tagihan</td>
            <td><strong>{{ $bill->bill_number }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($bill->status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal</td>
            <td>{{ optional($bill->bill_date)->format('d/m/Y') }}</td>
            <td class="meta-label">Jatuh Tempo</td>
            <td>{{ optional($bill->due_date)->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Vendor</td>
            <td><strong>{{ $bill->vendor?->name ?? '-' }}</strong></td>
            <td class="meta-label">No. PO</td>
            <td>{{ $bill->purchaseOrder?->po_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">No. Penerimaan</td>
            <td>{{ $bill->goodsReceipt?->receipt_number ?? '-' }}</td>
            <td class="meta-label">Status Pembayaran</td>
            <td>{{ strtoupper($bill->payment_status ?? '-') }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">RINCIAN TAGIHAN</div>
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
            @foreach($bill->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->purchaseOrderItem?->description ?? $item->purchaseOrderItem?->procurementItem?->name ?? '-' }}</td>
                    <td class="amount">{{ $item->quantity }}</td>
                    <td class="amount">{{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                    <td class="amount">{{ number_format((float) $item->line_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="data-table" style="margin-top: 12px; width: 50%; float: right;">
        <tr><td>Subtotal</td><td class="amount">{{ number_format((float) $bill->subtotal, 0, ',', '.') }}</td></tr>
        <tr><td>Diskon</td><td class="amount">{{ number_format((float) $bill->discount_amount, 0, ',', '.') }}</td></tr>
        <tr><td>Pajak</td><td class="amount">{{ number_format((float) $bill->tax_amount, 0, ',', '.') }}</td></tr>
        <tr class="total-row"><td>Total</td><td class="amount">{{ number_format((float) $bill->total_amount, 0, ',', '.') }}</td></tr>
        <tr><td>Terbayar</td><td class="amount">{{ number_format((float) $bill->amount_paid, 0, ',', '.') }}</td></tr>
    </table>

    @if($bill->notes)
        <div style="clear: both; margin-top: 80px;">
            <strong>Catatan:</strong> {{ $bill->notes }}
        </div>
    @endif
@endsection
