<?php

namespace Tests\ImportExport;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Modules\Core\Models\Tenant;
use Tests\TestCase;

class FinancialReportExportTest extends TestCase
{
    public function test_cash_flow_pdf_export_route_is_registered(): void
    {
        $this->assertTrue(Route::has('finance.reports.cash-flow.pdf'));
    }

    public function test_cash_flow_pdf_view_renders_report_sections(): void
    {
        $tenant = new Tenant(['name' => 'Finance Tenant']);
        $data = [
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'operating' => collect([(object) ['code' => '1101', 'name' => 'Kas Operasional', 'balance' => 1_000_000]]),
            'investing' => collect([(object) ['code' => '1201', 'name' => 'Aset Tetap', 'balance' => -250_000]]),
            'financing' => collect([(object) ['code' => '3101', 'name' => 'Modal Disetor', 'balance' => 500_000]]),
            'net_cash' => 1_250_000,
        ];

        $html = View::make('finance::pdf.cash-flow', [
            'tenant' => $tenant,
            'data' => $data,
        ])->render();

        $this->assertStringContainsString('LAPORAN ARUS KAS', $html);
        $this->assertStringContainsString('AKTIVITAS OPERASIONAL', $html);
        $this->assertStringContainsString('AKTIVITAS INVESTASI', $html);
        $this->assertStringContainsString('AKTIVITAS PENDANAAN', $html);
        $this->assertStringContainsString('Kas Operasional', $html);
    }
}
