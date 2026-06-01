@extends('core::pdf.layout')

@section('title', 'Tagihan Denda')

@section('document_title', 'TAGIHAN DENDA PERPUSTAKAAN')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. Denda</td>
            <td><strong>#{{ $fine->id }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($fine->status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Anggota</td>
            <td><strong>{{ $fine->loan?->member?->member_number ?? '-' }}</strong></td>
            <td class="meta-label">Jenis</td>
            <td>{{ $fine->fine_type ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal Terbit</td>
            <td>{{ optional($fine->issued_at)->format('d/m/Y') }}</td>
            <td class="meta-label">Tanggal Bayar</td>
            <td>{{ optional($fine->paid_at)->format('d/m/Y') ?? '-' }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">RINCIAN DENDA</div>
    <table class="data-table">
        <tr>
            <td style="width: 30%;">Buku</td>
            <td>{{ $fine->loan?->bookCopy?->book?->title ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jumlah Denda</td>
            <td class="amount">{{ number_format((float) $fine->amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Terbayar</td>
            <td class="amount">{{ number_format((float) ($fine->paid_amount ?? 0), 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td>Sisa</td>
            <td class="amount">{{ number_format((float) $fine->amount - (float) ($fine->paid_amount ?? 0), 0, ',', '.') }}</td>
        </tr>
    </table>

    @if($fine->notes)
        <div style="margin-top: 16px;">
            <strong>Catatan:</strong> {{ $fine->notes }}
        </div>
    @endif
@endsection
