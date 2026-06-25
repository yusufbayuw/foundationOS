@extends('core::pdf.layout')

@section('title', 'Bukti Peminjaman')

@section('document_title', 'BUKTI PEMINJAMAN BUKU')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. Peminjaman</td>
            <td><strong>#{{ $loan->id }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($loan->status instanceof \BackedEnum ? $loan->status->value : (string) $loan->status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Anggota</td>
            <td><strong>{{ $loan->member?->member_number ?? '-' }}</strong></td>
            <td class="meta-label">Petugas</td>
            <td>{{ $loan->processedBy?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal Pinjam</td>
            <td>{{ optional($loan->loan_date)->format('d/m/Y') }}</td>
            <td class="meta-label">Jatuh Tempo</td>
            <td>{{ optional($loan->due_date)->format('d/m/Y') }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">DETAIL BUKU</div>
    <table class="data-table">
        <tr>
            <td style="width: 30%;">Judul</td>
            <td>{{ $loan->bookCopy?->book?->title ?? '-' }}</td>
        </tr>
        <tr>
            <td>Barcode / Eksemplar</td>
            <td>{{ $loan->bookCopy?->barcode ?? $loan->bookCopy?->copy_number ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Kembali</td>
            <td>{{ optional($loan->return_date)->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td>Denda</td>
            <td class="amount">{{ number_format((float) ($loan->fine_amount ?? 0), 0, ',', '.') }}</td>
        </tr>
    </table>

    @if($loan->notes)
        <div style="margin-top: 16px;">
            <strong>Catatan:</strong> {{ $loan->notes }}
        </div>
    @endif
@endsection
