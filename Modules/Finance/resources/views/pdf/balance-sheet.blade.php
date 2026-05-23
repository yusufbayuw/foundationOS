<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Neraca</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ strtoupper($tenant->name ?? 'Perusahaan') }}</h2>
        <p>NERACA (BALANCE SHEET)</p>
        <p>Per Tanggal: {{ $data['as_of'] }}</p>
        @if(!$data['is_balanced'])
            <p style="color:red;font-weight:bold;">⚠ Neraca tidak seimbang!</p>
        @endif
    </div>

    @foreach([
        ['key' => 'assets', 'label' => 'ASET', 'total_key' => 'total_assets'],
        ['key' => 'liabilities', 'label' => 'LIABILITAS', 'total_key' => 'total_liabilities'],
        ['key' => 'equity', 'label' => 'EKUITAS', 'total_key' => 'total_equity'],
    ] as $section)
    <div class="section-title">{{ $section['label'] }}</div>
    <table>
        <tbody>
        @forelse($data[$section['key']] as $row)
            <tr><td>{{ $row->code }} — {{ $row->name }}</td><td class="amount">{{ number_format($row->balance, 0, ',', '.') }}</td></tr>
        @empty
            <tr><td colspan="2" style="text-align:center;color:#aaa;font-style:italic;">—</td></tr>
        @endforelse
        <tr class="total-row">
            <td>Total {{ $section['label'] }}</td>
            <td class="amount">{{ number_format($data[$section['total_key']], 0, ',', '.') }}</td>
        </tr>
        </tbody>
    </table>
    @endforeach
</body>
</html>
