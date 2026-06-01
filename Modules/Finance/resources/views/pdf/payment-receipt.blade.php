@extends('core::pdf.layout')

@section('title', 'Kwitansi Pembayaran')

@section('document_title', 'KWITANSI PEMBAYARAN')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. Kwitansi</td>
            <td><strong>{{ $payment->payment_number }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($payment->status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal Bayar</td>
            <td>{{ optional($payment->payment_date)->format('d/m/Y') }}</td>
            <td class="meta-label">Metode</td>
            <td>{{ $payment->payment_method ?? '-' }}</td>
        </tr>
        @if($invoice)
            <tr>
                <td class="meta-label">No. Invoice</td>
                <td>{{ $invoice->invoice_number }}</td>
                <td class="meta-label">Referensi</td>
                <td>{{ $payment->reference_number ?? '-' }}</td>
            </tr>
        @endif
    </table>
@endsection

@section('content')
    <table class="data-table">
        <tr class="highlight-row">
            <td>Jumlah Dibayar</td>
            <td class="amount">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
        </tr>
        @if($payment->bank_name)
            <tr><td>Bank</td><td>{{ $payment->bank_name }}</td></tr>
        @endif
        @if($payment->account_holder)
            <tr><td>Pemilik Rekening</td><td>{{ $payment->account_holder }}</td></tr>
        @endif
        @if($payment->verified_at)
            <tr>
                <td>Diverifikasi</td>
                <td>{{ $payment->verified_at->format('d/m/Y H:i') }} oleh {{ $payment->verifiedBy?->name ?? '-' }}</td>
            </tr>
        @endif
        @if($payment->verification_notes)
            <tr><td>Catatan</td><td>{{ $payment->verification_notes }}</td></tr>
        @endif
    </table>
@endsection
