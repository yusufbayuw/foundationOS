@extends('core::pdf.layout')

@section('title', 'Rapor Siswa')

@section('document_title', 'HASIL PENCAPAIAN KOMPETENSI PESERTA DIDIK')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">Nama Siswa</td>
            <td><strong>{{ $data['student']->user->name ?? '-' }}</strong></td>
            <td class="meta-label">Kelas</td>
            <td>{{ $data['class_name'] }}</td>
        </tr>
        <tr>
            <td class="meta-label">NIS / NISN</td>
            <td>{{ $data['student']->nis }} / {{ $data['student']->nisn ?? '-' }}</td>
            <td class="meta-label">Periode</td>
            <td>{{ $data['period']->name }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">NILAI MATA PELAJARAN</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>Mata Pelajaran</th>
                <th class="amount" style="width: 20%;">Rata-Rata</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($data['subjects'] as $subject)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $subject['name'] }}</td>
                    <td class="amount">{{ $subject['average'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Belum ada nilai untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($data['subjects']) > 0)
            <tfoot>
                <tr class="highlight-row">
                    <td colspan="2">RATA-RATA KESELURUHAN</td>
                    <td class="amount">{{ $data['overall_average'] }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="section-title">KETIDAKHADIRAN</div>
    <table class="data-table" style="width: 60%;">
        <tr><td>Sakit</td><td class="amount">{{ $data['attendance']['sick'] }} hari</td></tr>
        <tr><td>Izin</td><td class="amount">{{ $data['attendance']['permission'] }} hari</td></tr>
        <tr><td>Tanpa Keterangan</td><td class="amount">{{ $data['attendance']['absent'] }} hari</td></tr>
    </table>
@endsection
