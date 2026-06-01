@extends('core::pdf.layout')

@section('title', 'Kartu Jadwal Ujian')

@section('document_title', 'KARTU JADWAL UJIAN SELEKSI')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">Nama Jadwal</td>
            <td><strong>{{ $examSchedule->name }}</strong></td>
            <td class="meta-label">Jenis</td>
            <td>{{ strtoupper($examSchedule->type ?? '-') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Periode</td>
            <td>{{ $admissionPeriod?->name ?? '-' }}</td>
            <td class="meta-label">Tanggal</td>
            <td>{{ optional($examSchedule->date)->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Waktu</td>
            <td>{{ $examSchedule->start_time ?? '-' }} - {{ $examSchedule->end_time ?? '-' }}</td>
            <td class="meta-label">Lokasi</td>
            <td>{{ $examSchedule->location ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Kapasitas</td>
            <td>{{ $examSchedule->room_capacity ?? '-' }}</td>
            <td class="meta-label">Terdaftar</td>
            <td>{{ $examSchedule->registered_count ?? 0 }}</td>
        </tr>
    </table>
@endsection

@section('content')
    @if($examSchedule->instructions)
        <div class="section-title">PETUNJUK PELAKSANAAN</div>
        <p style="margin-top: 8px; text-align: justify;">{{ $examSchedule->instructions }}</p>
    @endif

    @if($examSchedule->examResults->isNotEmpty())
        <div class="section-title">PESERTA TERDAFTAR</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. Pendaftaran</th>
                    <th>Nama Peserta</th>
                </tr>
            </thead>
            <tbody>
                @foreach($examSchedule->examResults as $index => $result)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $result->applicant?->registration_number ?? '-' }}</td>
                        <td>{{ $result->applicant?->full_name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
