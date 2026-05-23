<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.5; color: #1a1a1a; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header { border-bottom: 2px solid #1d4ed8; padding-bottom: 12px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #1d4ed8; font-size: 16px; }
        .header p { margin: 2px 0; color: #555; font-size: 11px; }
        .info-grid { width: 100%; margin-bottom: 20px; }
        .info-grid td { padding: 3px 6px; vertical-align: top; }
        .info-grid .label { color: #555; width: 130px; }
        .info-grid .colon { width: 10px; }
        .section-title {
            background-color: #1d4ed8;
            color: #fff;
            padding: 4px 8px;
            font-weight: bold;
            margin-top: 16px;
            margin-bottom: 0;
        }
        .component-table { width: 100%; border-collapse: collapse; }
        .component-table td { padding: 4px 8px; border-bottom: 1px solid #e5e7eb; }
        .component-table .amount { text-align: right; }
        .total-row { font-weight: bold; background-color: #f3f4f6; }
        .net-salary { background-color: #1d4ed8; color: #fff; font-weight: bold; font-size: 13px; }
        .footer { margin-top: 30px; font-size: 11px; color: #777; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        .signature-grid { width: 100%; margin-top: 40px; }
        .signature-grid td { text-align: center; vertical-align: bottom; padding-top: 40px; border-top: 1px solid #000; }
    </style>
</head>
<body>

    <div class="header text-center">
        <h2>{{ strtoupper($slip->employee->organization?->name ?? $tenant->name ?? 'Perusahaan') }}</h2>
        <p>SLIP GAJI KARYAWAN &mdash; {{ strtoupper($slip->period_label) }}</p>
    </div>

    <table class="info-grid">
        <tr>
            <td class="label">Nama Karyawan</td>
            <td class="colon">:</td>
            <td><strong>{{ $slip->employee->full_name }}</strong></td>
            <td class="label">No. Karyawan</td>
            <td class="colon">:</td>
            <td>{{ $slip->employee->employee_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td>
            <td class="colon">:</td>
            <td>{{ $slip->employee->position?->name ?? '-' }}</td>
            <td class="label">Departemen</td>
            <td class="colon">:</td>
            <td>{{ $slip->employee->department?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Periode</td>
            <td class="colon">:</td>
            <td>{{ $slip->period_label }}</td>
            <td class="label">Status</td>
            <td class="colon">:</td>
            <td>{{ strtoupper($slip->status) }}</td>
        </tr>
        <tr>
            <td class="label">Hari Kerja</td>
            <td class="colon">:</td>
            <td>{{ $slip->working_days ?? '-' }} hari ({{ $slip->working_hours ?? '-' }} jam)</td>
            <td class="label">Lembur</td>
            <td class="colon">:</td>
            <td>{{ $slip->overtime_hours ?? '0' }} jam</td>
        </tr>
    </table>

    {{-- Pendapatan --}}
    <div class="section-title">PENDAPATAN</div>
    <table class="component-table">
        <tr>
            <td>Gaji Pokok</td>
            <td class="amount">Rp {{ number_format((float)$slip->basic_salary, 0, ',', '.') }}</td>
        </tr>
        @foreach ($slip->earnings_details ?? [] as $item)
        <tr>
            <td>{{ $item['name'] }}</td>
            <td class="amount">Rp {{ number_format((float)$item['amount'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td>Total Pendapatan</td>
            <td class="amount">Rp {{ number_format((float)$slip->total_earnings, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- Potongan --}}
    <div class="section-title">POTONGAN</div>
    <table class="component-table">
        @forelse ($slip->deductions_details ?? [] as $item)
        <tr>
            <td>{{ $item['name'] }}</td>
            <td class="amount">Rp {{ number_format((float)$item['amount'], 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="2" style="color:#aaa;font-style:italic;">Tidak ada potongan</td></tr>
        @endforelse
        <tr class="total-row">
            <td>Total Potongan</td>
            <td class="amount">Rp {{ number_format((float)$slip->total_deductions, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- Gaji Bersih --}}
    <table class="component-table" style="margin-top:8px;">
        <tr class="net-salary">
            <td style="padding:8px;">GAJI BERSIH (TAKE HOME PAY)</td>
            <td class="amount" style="padding:8px;">Rp {{ number_format((float)$slip->net_salary, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if($slip->paid_at)
    <p style="margin-top:12px;font-size:11px;color:#555;">
        Dibayarkan pada: {{ $slip->paid_at->format('d/m/Y H:i') }}
        @if($slip->paid_via) &mdash; via {{ $slip->paid_via }} @endif
    </p>
    @endif

    <div class="footer">
        <em>Slip gaji ini diterbitkan secara elektronik dan sah tanpa tanda tangan basah.</em>
    </div>

    <table class="signature-grid">
        <tr>
            <td width="33%">Karyawan<br><br>{{ $slip->employee->full_name }}</td>
            <td width="33%">HRD / Payroll</td>
            <td width="33%">Direktur / Pimpinan</td>
        </tr>
    </table>

</body>
</html>
