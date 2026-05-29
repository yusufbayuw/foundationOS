<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $exam->name }} — Token cards</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .meta { font-size: 10px; color: #555; margin-bottom: 16px; }
        .grid { width: 100%; border-collapse: collapse; }
        .card { width: 48%; display: inline-block; vertical-align: top; border: 1px solid #ccc; border-radius: 6px; padding: 10px; margin: 0 1% 12px 0; box-sizing: border-box; page-break-inside: avoid; }
        .card h2 { font-size: 12px; margin: 0 0 6px; }
        .token { font-size: 18px; font-weight: bold; letter-spacing: 2px; margin: 8px 0; }
        .label { color: #666; font-size: 9px; text-transform: uppercase; }
        .value { margin-bottom: 6px; }
    </style>
</head>
<body>
    <h1>{{ $exam->name }}</h1>
    <p class="meta">
        {{ $tenant?->name ?? '' }}
        @if($exam->scheduled_start_at)
            · {{ $exam->scheduled_start_at->format('d M Y H:i') }}
        @endif
    </p>

    @forelse($participants as $participant)
        <div class="card">
            <h2>{{ $participant->student_name }}</h2>
            <div>
                <div class="label">Identifier</div>
                <div class="value">{{ $participant->student_identifier ?? '—' }}</div>
            </div>
            <div>
                <div class="label">Token</div>
                <div class="token">{{ $participant->activeToken?->token ?? '—' }}</div>
            </div>
        </div>
    @empty
        <p>No participants with tokens.</p>
    @endforelse
</body>
</html>
