<!DOCTYPE html>
<html lang="{{ $locale ?? 'id' }}">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Document')</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.45;
            color: #1a1a1a;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-muted { color: #555; }
        .mb-0 { margin-bottom: 0; }
        .mt-0 { margin-top: 0; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.data-table th,
        table.data-table td { padding: 4px 6px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        table.data-table th { background: {{ $primaryColor ?? '#6366f1' }}; color: #fff; text-align: left; }
        .amount { text-align: right; white-space: nowrap; }
        .section-title {
            background: {{ $primaryColor ?? '#6366f1' }};
            color: #fff;
            padding: 4px 8px;
            font-weight: bold;
            margin-top: 14px;
            margin-bottom: 0;
        }
        .meta-bar {
            width: 100%;
            margin: 12px 0;
            border: 1px solid #e5e7eb;
            border-collapse: collapse;
        }
        .meta-bar td { padding: 4px 8px; border-bottom: 1px solid #e5e7eb; }
        .meta-label { color: #555; width: 120px; }
        .total-row { font-weight: bold; background: #f3f4f6; }
        .highlight-row { background: {{ $primaryColor ?? '#6366f1' }}; color: #fff; font-weight: bold; }
    </style>
    @stack('styles')
</head>
<body>
    @include('core::pdf.partials.header')

    @hasSection('meta')
        @yield('meta')
    @endif

    @yield('content')

    @include('core::pdf.partials.signature')

    @include('core::pdf.partials.footer')
</body>
</html>
