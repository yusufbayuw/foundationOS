@extends('core::pdf.layout')

@section('title', 'Invoice Tagihan Siswa')

@section('document_title', 'INVOICE TAGIHAN SISWA')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. Invoice</td>
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
            <td class="meta-label">Kepada</td>
            <td colspan="3"><strong>{{ $recipientName }}</strong></td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">RINCIAN TAGIHAN</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Uraian</th>
                <th class="amount">Qty</th>
                <th class="amount">Harga</th>
                <th class="amount">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description ?? $item->tuitionType?->name ?? '-' }}</td>
                    <td class="amount">{{ $item->quantity }}</td>
                    <td class="amount">{{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                    <td class="amount">{{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="data-table" style="margin-top: 12px; width: 50%; float: right;">
        <tr><td>Subtotal</td><td class="amount">{{ number_format((float) $invoice->amount, 0, ',', '.') }}</td></tr>
        <tr><td>Diskon</td><td class="amount">{{ number_format((float) $invoice->discount_amount, 0, ',', '.') }}</td></tr>
        <tr><td>Denda</td><td class="amount">{{ number_format((float) $invoice->penalty_amount, 0, ',', '.') }}</td></tr>
        <tr class="highlight-row"><td>Total</td><td class="amount">{{ number_format((float) $invoice->total_amount, 0, ',', '.') }}</td></tr>
        <tr><td>Terbayar</td><td class="amount">{{ number_format((float) $invoice->paid_amount, 0, ',', '.') }}</td></tr>
        <tr class="total-row"><td>Sisa</td><td class="amount">{{ number_format((float) $invoice->remaining_amount, 0, ',', '.') }}</td></tr>
    </table>

    @if($invoice->notes)
        <div style="clear: both; margin-top: 80px;">
            <strong>Catatan:</strong> {{ $invoice->notes }}
        </div>
    @endif
@endsection
