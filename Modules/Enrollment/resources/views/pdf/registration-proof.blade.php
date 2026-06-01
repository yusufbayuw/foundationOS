@extends('core::pdf.layout')

@section('title', 'Bukti Daftar Ulang')

@section('document_title', 'BUKTI DAFTAR ULANG')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. Pendaftaran</td>
            <td><strong>{{ $applicant?->registration_number ?? '-' }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($registration->status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal Daftar Ulang</td>
            <td>{{ optional($registration->registration_date)->format('d/m/Y') ?? '-' }}</td>
            <td class="meta-label">Pembayaran</td>
            <td>{{ strtoupper($registration->payment_status ?? '-') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Nama Peserta</td>
            <td colspan="3"><strong>{{ $applicant?->full_name ?? '-' }}</strong></td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">RINCIAN BIAYA</div>
    <table class="data-table">
        <tr>
            <td>Total Biaya</td>
            <td class="amount">{{ number_format((float) $registration->total_fee, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Terbayar</td>
            <td class="amount">{{ number_format((float) $registration->paid_amount, 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td>Sisa</td>
            <td class="amount">{{ number_format(max(0, (float) $registration->total_fee - (float) $registration->paid_amount), 0, ',', '.') }}</td>
        </tr>
    </table>

    @if($registration->uniform_size)
        <p style="margin-top: 12px;"><strong>Ukuran Seragam:</strong> {{ $registration->uniform_size }}</p>
    @endif

    @if(! empty($registration->documents_received))
        <div class="section-title">DOKUMEN DITERIMA</div>
        <ul style="margin: 8px 0; padding-left: 18px;">
            @foreach($registration->documents_received as $document)
                <li>{{ is_array($document) ? ($document['name'] ?? json_encode($document)) : $document }}</li>
            @endforeach
        </ul>
    @endif

    @if($registration->notes)
        <p style="margin-top: 12px;"><strong>Catatan:</strong> {{ $registration->notes }}</p>
    @endif

    @if($registration->completed_at)
        <p style="margin-top: 12px;" class="text-muted">
            Diselesaikan pada {{ $registration->completed_at->format('d/m/Y H:i') }}
            @if($registration->completedBy)
                oleh {{ $registration->completedBy->name }}
            @endif
        </p>
    @endif
@endsection
