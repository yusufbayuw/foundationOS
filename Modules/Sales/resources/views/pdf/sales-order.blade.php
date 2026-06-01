@extends('core::pdf.layout')

@section('title', 'Sales Order')

@section('document_title', 'SALES ORDER')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. Order</td>
            <td><strong>{{ $salesOrder->order_number }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($salesOrder->status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Pelanggan</td>
            <td><strong>{{ $salesOrder->customer?->name ?? '-' }}</strong></td>
            <td class="meta-label">Tanggal</td>
            <td>{{ optional($salesOrder->order_date)->format('d/m/Y') }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">RINCIAN ORDER</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Uraian</th>
                <th class="amount">Qty</th>
                <th class="amount">Harga</th>
                <th class="amount">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salesOrder->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="amount">{{ $item->quantity }}</td>
                    <td class="amount">{{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                    <td class="amount">{{ number_format((float) $item->line_total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada item.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="data-table" style="margin-top: 12px; width: 50%; float: right;">
        <tr><td>Subtotal</td><td class="amount">{{ number_format((float) $salesOrder->subtotal, 0, ',', '.') }}</td></tr>
        <tr><td>Pajak</td><td class="amount">{{ number_format((float) $salesOrder->tax_amount, 0, ',', '.') }}</td></tr>
        <tr class="total-row"><td>Total</td><td class="amount">{{ number_format((float) $salesOrder->total_amount, 0, ',', '.') }}</td></tr>
    </table>
@endsection
