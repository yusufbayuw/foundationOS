@extends('core::pdf.layout')

@section('title', 'Permintaan Penawaran')

@section('document_title', 'PERMINTAAN PENAWARAN (RFQ)')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. RFQ</td>
            <td><strong>{{ $rfq->rfq_number }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($rfq->status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal RFQ</td>
            <td>{{ optional($rfq->rfq_date)->format('d/m/Y') }}</td>
            <td class="meta-label">Batas Penawaran</td>
            <td>{{ optional($rfq->closing_date)->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">No. Permintaan</td>
            <td>{{ $rfq->purchaseRequisition?->request_number ?? '-' }}</td>
            <td class="meta-label">Estimasi Anggaran</td>
            <td>{{ number_format((float) $rfq->total_estimated_budget, 0, ',', '.') }} {{ $rfq->currency }}</td>
        </tr>
    </table>
@endsection

@section('content')
    @if($rfq->description)
        <p><strong>Deskripsi:</strong> {{ $rfq->description }}</p>
    @endif

    <div class="section-title">RINCIAN PENAWARAN</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th class="amount">Qty</th>
                <th class="amount">Est. Anggaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rfq->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description ?? $item->procurementItem?->name ?? '-' }}</td>
                    <td class="amount">{{ $item->quantity }}</td>
                    <td class="amount">{{ number_format((float) $item->estimated_budget, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($rfq->vendors->isNotEmpty())
        <div class="section-title" style="margin-top: 24px;">VENDOR DIUNDANG</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Vendor</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rfq->vendors as $index => $rfqVendor)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $rfqVendor->vendor?->name ?? '-' }}</td>
                        <td>{{ strtoupper($rfqVendor->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($rfq->notes)
        <p style="margin-top: 16px;"><strong>Catatan:</strong> {{ $rfq->notes }}</p>
    @endif
@endsection
