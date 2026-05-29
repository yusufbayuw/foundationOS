@php use Illuminate\Support\Str; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $exam->name }} — Results report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        h2 { font-size: 13px; margin: 18px 0 8px; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
        .meta { font-size: 10px; color: #555; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f5f5f5; font-size: 10px; text-transform: uppercase; }
        ul { margin: 0; padding-left: 18px; }
        li { margin-bottom: 4px; }
    </style>
</head>
<body>
    <h1>{{ $exam->name }}</h1>
    <p class="meta">
        {{ $tenant?->name ?? '' }}
        · {{ now()->format('d M Y H:i') }}
    </p>

    <h2>{{ __('Results') }}</h2>
    <table>
        <thead>
            <tr>
                <th>Result ID</th>
                <th>Participant</th>
                <th>Identifier</th>
                <th>Score</th>
                <th>%</th>
                <th>Passed</th>
                <th>Submitted</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ Str::limit($row['result_id'] ?? '', 8) }}</td>
                    <td>{{ $row['participant_name'] ?? '—' }}</td>
                    <td>{{ $row['identifier'] ?? '—' }}</td>
                    <td>{{ $row['score'] ?? '—' }}</td>
                    <td>{{ $row['percentage'] ?? '—' }}</td>
                    <td>{{ $row['passed'] ?? '—' }}</td>
                    <td>{{ $row['submitted_at'] ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No results synced yet.</td>
                </tr>
            @endforelse
        </table>

    @foreach($analytics['sections'] ?? [] as $section)
        <h2>{{ $section['title'] }}</h2>
        {!! $section['html'] !!}
    @endforeach
</body>
</html>
