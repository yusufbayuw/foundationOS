@extends('core::pdf.layout')

@section('title', 'Kwitansi Donasi')

@section('document_title', 'KWITANSI DONASI')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. Donasi</td>
            <td><strong>{{ $donation->donation_number }}</strong></td>
            <td class="meta-label">Status Pembayaran</td>
            <td>{{ strtoupper($donation->payment_status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Donatur</td>
            <td><strong>{{ $donation->donor?->is_anonymous ? 'Anonim' : ($donation->donor?->name ?? '-') }}</strong></td>
            <td class="meta-label">Kampanye</td>
            <td>{{ $donation->campaign?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal Bayar</td>
            <td>{{ optional($donation->paid_at)->format('d/m/Y H:i') ?? '-' }}</td>
            <td class="meta-label">Referensi</td>
            <td>{{ $donation->payment_reference ?? '-' }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div style="margin: 24px 0; text-align: center;">
        <p style="font-size: 14px;">Telah diterima donasi sebesar:</p>
        <p style="font-size: 20px; font-weight: bold; margin: 12px 0;">
            Rp {{ number_format((float) $donation->amount, 0, ',', '.') }}
        </p>
        <p style="font-size: 14px;">untuk kampanye <strong>{{ $donation->campaign?->name ?? '-' }}</strong></p>
    </div>
@endsection
