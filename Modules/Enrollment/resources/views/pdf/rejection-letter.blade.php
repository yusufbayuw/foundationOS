@extends('core::pdf.layout')

@section('title', 'Surat Penolakan')

@section('document_title', 'SURAT PENGUMUMAN HASIL SELEKSI')

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
            <td class="meta-label">Pilihan 1</td>
            <td>{{ $applicant->firstProgramChoice?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Pilihan 2</td>
            <td>{{ $applicant->secondProgramChoice?->name ?? '-' }}</td>
        </tr>
    </table>

    <p style="margin-top: 16px; text-align: justify;">
        <strong>BELUM DAPAT DITERIMA</strong> pada periode seleksi ini. Keputusan ini diambil berdasarkan kuota dan hasil penilaian panitia seleksi.
    </p>

    <p style="margin-top: 12px; text-align: justify;">
        Terima kasih atas partisipasi Anda. Kami mendoakan kesuksesan di kesempatan berikutnya.
    </p>
@endsection
