@extends('core::pdf.layout')

@section('title', 'Rekapitulasi Absensi')

@section('document_title', 'REKAPITULASI ABSENSI SISWA')

@section('meta')
    <table class="meta-bar">
        <tr>
            <td class="meta-label">Kelas</td>
            <td><strong>{{ $schoolClass->name }}</strong></td>
            <td class="meta-label">Periode</td>
            <td>{{ $period->name }}</td>
        </tr>
        <tr>
            <td class="meta-label">Bulan</td>
            <td>{{ sprintf('%02d', $month) }}/{{ $year }}</td>
            <td class="meta-label">Jumlah Siswa</td>
            <td>{{ $rows->count() }}</td>
        </tr>
    </table>
@endsection

@section('content')
    <div class="section-title">RINGKASAN KEHADIRAN</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Siswa</th>
                <th class="amount">Hadir</th>
                <th class="amount">Sakit</th>
                <th class="amount">Izin</th>
                <th class="amount">Alpa</th>
                <th class="amount">Terlambat</th>
                <th class="amount">%</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->student_name }}</td>
                    <td class="amount">{{ $row->total_present }}</td>
                    <td class="amount">{{ $row->total_sick }}</td>
                    <td class="amount">{{ $row->total_permission }}</td>
                    <td class="amount">{{ $row->total_absent }}</td>
                    <td class="amount">{{ $row->total_late }}</td>
                    <td class="amount">{{ $row->percentage }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data kehadiran untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
