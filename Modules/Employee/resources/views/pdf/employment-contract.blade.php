@extends('core::pdf.layout')

@section('title', 'Kontrak Kerja')

@section('document_title', 'KONTRAK KERJA')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. Kontrak</td>
            <td><strong>{{ $contract->contract_number ?? '-' }}</strong></td>
            <td class="meta-label">Status</td>
            <td>{{ strtoupper($contract->status ?? '-') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Karyawan</td>
            <td><strong>{{ $contract->employee?->full_name ?? '-' }}</strong></td>
            <td class="meta-label">Jenis Kontrak</td>
            <td>{{ $contract->contract_type ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Mulai</td>
            <td>{{ optional($contract->start_date)->format('d/m/Y') }}</td>
            <td class="meta-label">Berakhir</td>
            <td>{{ optional($contract->end_date)->format('d/m/Y') ?? '-' }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">KETENTUAN KONTRAK</div>
    <table class="data-table">
        <tr>
            <td style="width: 30%;">Gaji Pokok</td>
            <td class="amount">{{ number_format((float) ($contract->basic_salary ?? 0), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Lokasi Kerja</td>
            <td>{{ $contract->work_location ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jam Kerja / Minggu</td>
            <td>{{ $contract->work_hours_per_week ?? '-' }}</td>
        </tr>
        <tr>
            <td>Masa Percobaan (bulan)</td>
            <td>{{ $contract->probation_period_months ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanda Tangan Karyawan</td>
            <td>{{ $contract->signed_by_employee ? 'Ya' : 'Belum' }}</td>
        </tr>
        <tr>
            <td>Tanda Tangan Perusahaan</td>
            <td>{{ $contract->signed_by_employer ? 'Ya' : 'Belum' }}</td>
        </tr>
    </table>

    @if($contract->termination_clause)
        <div style="margin-top: 16px;">
            <strong>Klausul Pemutusan:</strong> {{ $contract->termination_clause }}
        </div>
    @endif
@endsection
