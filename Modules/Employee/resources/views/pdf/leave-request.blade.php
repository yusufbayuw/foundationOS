@extends('core::pdf.layout')

@section('title', 'Surat Cuti')

@section('document_title', 'SURAT PERMOHONAN CUTI')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">Karyawan</td>
            <td><strong>{{ $leaveRequest->employee?->full_name ?? '-' }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($leaveRequest->status) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Jenis Cuti</td>
            <td>{{ $leaveRequest->leave_type ?? '-' }}</td>
            <td class="meta-label">Total Hari</td>
            <td>{{ $leaveRequest->total_days ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal Mulai</td>
            <td>{{ optional($leaveRequest->start_date)->format('d/m/Y') }}</td>
            <td class="meta-label">Tanggal Selesai</td>
            <td>{{ optional($leaveRequest->end_date)->format('d/m/Y') }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">DETAIL PERMOHONAN</div>
    <table class="data-table">
        <tr>
            <td style="width: 30%;">Pengganti</td>
            <td>{{ $leaveRequest->substituteEmployee?->full_name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Atasan</td>
            <td>{{ $leaveRequest->supervisor?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Disetujui Oleh</td>
            <td>{{ $leaveRequest->approver?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Persetujuan</td>
            <td>{{ optional($leaveRequest->approved_at)->format('d/m/Y H:i') ?? '-' }}</td>
        </tr>
    </table>

    @if($leaveRequest->reason)
        <div style="margin-top: 16px;">
            <strong>Alasan:</strong> {{ $leaveRequest->reason }}
        </div>
    @endif
@endsection
