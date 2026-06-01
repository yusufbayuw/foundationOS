@extends('core::pdf.layout')

@section('title', 'Surat Penerimaan')

@section('document_title', 'SURAT KEPUTUSAN PENERIMAAN')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">No. Pendaftaran</td>
            <td><strong>{{ $applicant->registration_number ?? '-' }}</strong></td>
            <td class="meta-label">Periode</td>
            <td>{{ $admissionPeriod?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Nama</td>
            <td colspan="3"><strong>{{ $applicant->full_name }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Program Diterima</td>
            <td colspan="3">{{ $acceptedProgram?->name ?? '-' }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <p style="margin-top: 16px; text-align: justify;">
        Berdasarkan hasil seleksi Penerimaan Peserta Didik Baru periode
        <strong>{{ $admissionPeriod?->name ?? '-' }}</strong>, dengan ini kami sampaikan bahwa:
    </p>

    <table class="meta-bar" style="margin-top: 12px;">
        <tr>
            <td class="meta-label">Nama Lengkap</td>
            <td>{{ $applicant->full_name }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tempat, Tgl Lahir</td>
            <td>{{ $applicant->birth_place ?? '-' }}, {{ optional($applicant->birth_date)->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Asal Sekolah</td>
            <td>{{ $applicant->previous_school ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Nilai Rata-rata</td>
            <td>{{ $applicant->average_score ?? '-' }}</td>
        </tr>
    </table>

    <p style="margin-top: 16px; text-align: justify;">
        <strong>DITERIMA</strong> sebagai peserta didik baru pada program
        <strong>{{ $acceptedProgram?->name ?? '-' }}</strong>.
        @if($admissionPeriod?->announcement_date)
            Keputusan ini berlaku sejak tanggal {{ $admissionPeriod->announcement_date->format('d/m/Y') }}.
        @endif
    </p>

    <p style="margin-top: 12px; text-align: justify;">
        Mohon melakukan daftar ulang sesuai jadwal yang ditetapkan panitia. Surat ini dicetak sebagai bukti resmi penerimaan.
    </p>
@endsection
