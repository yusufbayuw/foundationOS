@extends('core::pdf.layout')

@section('title', 'Faktur Pelanggan')

@section('document_title', 'FAKTUR PELANGGAN')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. Faktur</td>
            <td><strong>{{ $invoice->invoice_number }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($invoice->status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal</td>
            <td>{{ optional($invoice->issue_date)->format('d/m/Y') }}</td>
            <td class="meta-label">Jatuh Tempo</td>
            <td>{{ optional($invoice->due_date)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Pelanggan</td>
            <td colspan="3"><strong>{{ $invoice->customer_name }}</strong></td>
        </tr>
        @if($invoice->customer_address)
            <tr>
                <td class="meta-label">Alamat</td>
                <td colspan="3">{{ $invoice->customer_address }}</td>
            </tr>
        @endif
    </table>
@endsection

@section('content')
    <div class="section-title">RINCIAN</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Deskripsi</th>
                <th class="amount">Qty</th>
                <th class="amount">Harga</th>
                <th class="amount">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description ?? '-' }}</td>
                    <td class="amount">{{ $item->quantity }}</td>
                    <td class="amount">{{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                    <td class="amount">{{ number_format((float) $item->line_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="data-table" style="margin-top: 12px; width: 50%; float: right;">
        <tr><td>Subtotal</td><td class="amount">{{ number_format((float) $invoice->subtotal, 0, ',', '.') }}</td></tr>
        <tr><td>Diskon</td><td class="amount">{{ number_format((float) $invoice->discount_amount, 0, ',', '.') }}</td></tr>
        <tr><td>Pajak</td><td class="amount">{{ number_format((float) $invoice->tax_amount, 0, ',', '.') }}</td></tr>
        <tr class="highlight-row"><td>Total</td><td class="amount">{{ number_format((float) $invoice->total_amount, 0, ',', '.') }}</td></tr>
        <tr><td>Terbayar</td><td class="amount">{{ number_format((float) $invoice->paid_amount, 0, ',', '.') }}</td></tr>
        <tr class="total-row"><td>Sisa</td><td class="amount">{{ number_format((float) $invoice->remaining_amount, 0, ',', '.') }}</td></tr>
    </table>
@endsection
