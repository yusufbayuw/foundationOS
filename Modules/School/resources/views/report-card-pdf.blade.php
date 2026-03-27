<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; line-height: 1.4; color: #333; }
        .text-center { text-align: center; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 6px; }
        .table th { background-color: #f3f4f6; }
        .header-info { width: 100%; margin-bottom: 20px; }
        .header-info td { padding: 4px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="text-center" style="margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 15px;">
        <h2 style="margin: 0; padding: 0;">HASIL PENCAPAIAN KOMPETENSI PESERTA DIDIK</h2>
    </div>
    
    <table class="header-info">
        <tr>
            <td width="20%">Nama Siswa</td>
            <td width="30%">: {{ $data['student']->user->name ?? '-' }}</td>
            <td width="20%">Kelas</td>
            <td width="30%">: {{ $data['class_name'] }}</td>
        </tr>
        <tr>
            <td>NIS / NISN</td>
            <td>: {{ $data['student']->nis }} / {{ $data['student']->nisn ?? '-' }}</td>
            <td>Periode</td>
            <td>: {{ $data['period']->name }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="50%">Mata Pelajaran</th>
                <th width="45%">Rata-Rata Nilai</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($data['subjects'] as $subject)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $subject['name'] }}</td>
                    <td class="text-center">{{ $subject['average'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Belum ada nilai untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($data['subjects']) > 0)
        <tfoot>
            <tr>
                <th colspan="2" style="text-align: right; font-weight: bold;">RATA-RATA KESELURUHAN</th>
                <th class="text-center" style="font-weight: bold;">{{ $data['overall_average'] }}</th>
            </tr>
        </tfoot>
        @endif
    </table>

    <div style="margin-top: 30px;">
        <h3>Ketidakhadiran</h3>
        <table class="table" style="width: 50%;">
            <tr><td width="50%">Sakit</td><td width="50%" class="text-center">{{ $data['attendance']['sick'] }} hari</td></tr>
            <tr><td>Izin</td><td class="text-center">{{ $data['attendance']['permission'] }} hari</td></tr>
            <tr><td>Tanpa Keterangan</td><td class="text-center">{{ $data['attendance']['absent'] }} hari</td></tr>
        </table>
    </div>
    
    <table style="width: 100%; margin-top: 60px;">
        <tr>
            <td width="50%">
                <br>
                Orang Tua / Wali
                <br><br><br><br><br>
                (........................................)
            </td>
            <td width="50%" class="text-center">
                Diberikan di: .......................<br>
                Tanggal: {{ date('d F Y') }}<br>
                Wali Kelas
                <br><br><br><br><br>
                (........................................)
            </td>
        </tr>
    </table>
</body>
</html>
