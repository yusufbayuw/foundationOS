<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Arus Kas</title>
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
        <p>LAPORAN ARUS KAS</p>
        <p>Periode: {{ $data['period_from'] }} s/d {{ $data['period_to'] }}</p>
    </div>

    @foreach([
        ['key' => 'operating', 'label' => 'AKTIVITAS OPERASIONAL'],
        ['key' => 'investing', 'label' => 'AKTIVITAS INVESTASI'],
        ['key' => 'financing', 'label' => 'AKTIVITAS PENDANAAN'],
    ] as $section)
        <div class="section-title">{{ $section['label'] }}</div>
        <table>
            <tbody>
            @forelse($data[$section['key']] as $row)
                <tr>
                    <td>{{ $row->code }} - {{ $row->name }}</td>
                    <td class="amount">
                        {{ $row->balance >= 0 ? '' : '(' }}{{ number_format(abs($row->balance), 0, ',', '.') }}{{ $row->balance >= 0 ? '' : ')' }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="2" style="text-align:center;color:#aaa;font-style:italic;">-</td></tr>
            @endforelse
            </tbody>
        </table>
    @endforeach

    <table style="margin-top:10px;">
        <tr class="net-row">
            <td style="padding:8px;">KENAIKAN / PENURUNAN KAS BERSIH</td>
            <td class="amount" style="padding:8px;">Rp {{ number_format($data['net_cash'], 0, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>
