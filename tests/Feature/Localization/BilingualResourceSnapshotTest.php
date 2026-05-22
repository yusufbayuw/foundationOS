<?php

namespace Tests\Feature\Localization;

use Illuminate\Support\Facades\App;
use Modules\Core\Support\FilamentUi;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Epic 8.2 — Regression guard: verify key UI labels for each major module
 * resolve correctly in both locales (id / en).
 *
 * Rather than full HTML snapshots (fragile against Filament minor updates),
 * we assert the specific phrase strings used by each module's resources.
 */
class BilingualResourceSnapshotTest extends TestCase
{
    /** @return array<string, array{string, string, string}> */
    public static function modulePhrasesProvider(): array
    {
        return [
            // [english phrase, expected in 'id', expected in 'en']

            // Workflow
            'workflow: instance' => ['Workflow instance', 'Instans workflow', 'Workflow instance'],
            'workflow: steps' => ['Workflow steps', 'Langkah Workflow', 'Workflow steps'],
            'workflow: trigger event' => ['Trigger event', 'Event pemicu', 'Trigger event'],
            'workflow: action type' => ['Action type', 'Jenis aksi', 'Action type'],
            'workflow: status before' => ['Status before', 'Status sebelum', 'Status before'],
            'workflow: current step' => ['Current step', 'Langkah saat ini', 'Current step'],
            'workflow: requester' => ['Requester', 'Pemohon', 'Requester'],

            // Procurement
            'procurement: unit price' => ['Unit price', 'Harga satuan', 'Unit price'],
            'procurement: total amount' => ['Total amount', 'Total', 'Total amount'],
            'procurement: quantity' => ['Quantity', 'Jumlah', 'Quantity'],
            'procurement: tax' => ['Tax', 'Pajak', 'Tax'],
            'procurement: discount' => ['Discount', 'Diskon', 'Discount'],
            'procurement: reference' => ['Reference', 'Referensi', 'Reference'],

            // Finance
            'finance: payment method' => ['Payment method', 'Metode pembayaran', 'Payment method'],
            'finance: payment details' => ['Payment details', 'Detail pembayaran', 'Payment details'],
            'finance: calculation' => ['Calculation', 'Kalkulasi', 'Calculation'],
            'finance: sub total' => ['Sub total', 'Subtotal', 'Sub total'],

            // School
            'school: academic year' => ['Academic year', 'Tahun akademik', 'Academic year'],
            'school: curriculum' => ['Curriculum', 'Kurikulum', 'Curriculum'],
            'school: general information' => ['General information', 'Informasi umum', 'General information'],
            'school: basic information' => ['Basic information', 'Informasi dasar', 'Basic information'],
            'school: personal information' => ['Personal information', 'Informasi pribadi', 'Personal information'],

            // Campus
            'campus: study program' => ['Study program', 'Program Studi', 'Study program'],
            'campus: academic information' => ['Academic information', 'Informasi akademik', 'Academic information'],
            'campus: thesis details' => ['Thesis details', 'Detail tesis', 'Thesis details'],
            'campus: student number' => ['Student number', 'NIM', 'Student number'],

            // Library
            'library: loan limits' => ['Loan limits', 'Batas peminjaman', 'Loan limits'],
            'library: loan details' => ['Loan details', 'Detail peminjaman', 'Loan details'],
            'library: fine details' => ['Fine details', 'Detail denda', 'Fine details'],
            'library: separate authors' => ['Separate authors with commas.', 'Pisahkan penulis dengan koma.', 'Separate authors with commas.'],
            'library: tenant-wide data' => ['Optional. Leave blank for tenant-wide data.', 'Opsional. Kosongkan untuk data tenant-wide.', 'Optional. Leave blank for tenant-wide data.'],

            // Employee
            'employee: employment contract' => ['Employment contract', 'Kontrak kerja', 'Employment contract'],
            'employee: leave request' => ['Leave request', 'Permohonan cuti', 'Leave request'],
            'employee: created by' => ['Created by', 'Dibuat oleh', 'Created by'],
            'employee: updated by' => ['Updated by', 'Diperbarui oleh', 'Updated by'],

            // Core
            'core: preferred locale' => ['Preferred locale', 'Bahasa', 'Preferred locale'],
            'core: is active' => ['Is active', 'Aktif', 'Is active'],
            'core: timestamps' => ['Timestamps', 'Waktu', 'Timestamps'],
            'core: settings' => ['Settings', 'Pengaturan', 'Settings'],

            // Workflow note placeholder
            'workflow: action note' => ['Note for this action (optional).', 'Catatan aksi ini (opsional).', 'Note for this action (optional).'],
        ];
    }

    #[DataProvider('modulePhrasesProvider')]
    public function test_phrase_resolves_in_indonesian_locale(string $phrase, string $expectedId, string $expectedEn): void
    {
        App::setLocale('id');
        $this->assertSame($expectedId, FilamentUi::text($phrase));
    }

    #[DataProvider('modulePhrasesProvider')]
    public function test_phrase_resolves_in_english_locale(string $phrase, string $expectedId, string $expectedEn): void
    {
        App::setLocale('en');
        $this->assertSame($expectedEn, FilamentUi::text($phrase));
    }

    public function test_field_helper_translates_in_indonesian_locale(): void
    {
        App::setLocale('id');

        $cases = [
            'preferred_locale' => 'Bahasa',
            'phone_number' => 'Nomor telepon',
            'academic_year_id' => 'Tahun akademik',
            'workflow_instance' => 'Instans workflow',
            'payment_method' => 'Metode pembayaran',
        ];

        foreach ($cases as $field => $expected) {
            $this->assertSame($expected, FilamentUi::field($field), "field('$field') failed");
        }
    }

    public function test_field_helper_returns_humanised_english_in_en_locale(): void
    {
        App::setLocale('en');

        // field() in English returns the title-cased humanised version of the snake_case name
        $cases = [
            'preferred_locale' => 'Preferred Locale',
            'phone_number' => 'Phone Number',
            'payment_method' => 'Payment Method',
        ];

        foreach ($cases as $field => $expected) {
            $this->assertSame($expected, FilamentUi::field($field), "field('$field') failed");
        }
    }

    protected function tearDown(): void
    {
        App::setLocale(config('app.fallback_locale', 'en'));
        parent::tearDown();
    }
}
