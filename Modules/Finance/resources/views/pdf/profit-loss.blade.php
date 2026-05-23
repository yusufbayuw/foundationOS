<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Laba Rugi</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; }
        .header { text-align: center; border-bottom: 2px solid #1d4ed8; padding-bottom: 10px; margin-bottom: 16px; }
        .header h2 { margin: 0; color: #1d4ed8; font-size: 15px; }
        .header p { margin: 2px 0; color: #555; font-size: 10px; }
        .section-title { background-color: #1d4ed8; color: #fff; padding: 4px 8px; font-weight: bold; margin: 12px 0 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 3px 6px; border-bottom: 1px solid #e5e7eb; }
        .amount { text-align: right; }
        .total-row { font-weight: bold; background: #f3f4f6; }
        .net-row { background: #1d4ed8; color: #fff; font-weight: bold; font-size: 13px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ strtoupper($tenant->name ?? 'Perusahaan') }}</h2>
        <p>LAPORAN LABA RUGI</p>
        <p>Periode: {{ $data['period_from'] }} s/d {{ $data['period_to'] }}</p>
    </div>

    <div class="section-title">PENDAPATAN</div>
    <table>
        <tbody>
        @forelse($data['revenue'] as $row)
            <tr><td>{{ $row->code }} — {{ $row->name }}</td><td class="amount">{{ number_format($row->balance, 0, ',', '.') }}</td></tr>
        @empty
            <tr><td colspan="2" style="text-align:center;color:#aaa;font-style:italic;">—</td></tr>
        @endforelse
        <tr class="total-row">
            <td>Total Pendapatan</td>
            <td class="amount">{{ number_format($data['total_revenue'], 0, ',', '.') }}</td>
        </tr>
        </tbody>
    </table>

    <div class="section-title" style="margin-top:8px;">BEBAN</div>
    <table>
        <tbody>
        @forelse($data['expenses'] as $row)
            <tr><td>{{ $row->code }} — {{ $row->name }}</td><td class="amount">{{ number_format($row->balance, 0, ',', '.') }}</td></tr>
        @empty
            <tr><td colspan="2" style="text-align:center;color:#aaa;font-style:italic;">—</td></tr>
        @endforelse
        <tr class="total-row">
            <td>Total Beban</td>
            <td class="amount">{{ number_format($data['total_expenses'], 0, ',', '.') }}</td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top:10px;">
        <tr class="net-row">
            <td style="padding:8px;">LABA / RUGI BERSIH</td>
            <td class="amount" style="padding:8px;">Rp {{ number_format($data['net_income'], 0, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>
