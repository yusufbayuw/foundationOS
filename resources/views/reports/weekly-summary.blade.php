<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Weekly Summary — {{ $tenant->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        td, th { border: 1px solid #ddd; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Weekly Summary — {{ $tenant->name }}</h1>
    <p>Period: {{ $period['from'] }} – {{ $period['to'] }}</p>
    <table>
        <tr><th>Metric</th><th>Value</th></tr>
        <tr><td>Revenue MTD</td><td>Rp {{ number_format($metrics['revenue_mtd'] ?? 0, 0, ',', '.') }}</td></tr>
        <tr><td>Payroll MTD</td><td>Rp {{ number_format($metrics['payroll_mtd'] ?? 0, 0, ',', '.') }}</td></tr>
        <tr><td>Outstanding AR</td><td>Rp {{ number_format($metrics['outstanding_ar'] ?? 0, 0, ',', '.') }}</td></tr>
        <tr><td>Outstanding AP</td><td>Rp {{ number_format($metrics['outstanding_ap'] ?? 0, 0, ',', '.') }}</td></tr>
        <tr><td>Pending Approvals</td><td>{{ $metrics['pending_approvals'] ?? 0 }}</td></tr>
        <tr><td>Payroll / Revenue (7d)</td><td>{{ $cost['efficiency_ratio'] ?? '-' }}</td></tr>
    </table>
</body>
</html>
