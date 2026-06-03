<?php

namespace Tests\Feature {

    use Illuminate\Support\Facades\App;
    use Modules\Core\Support\FilamentUi;
    use Modules\School\Tests\MockSchoolClass;
    use PHPUnit\Framework\Attributes\DataProvider;
    use Tests\TestCase;

    class FilamentUiTranslationsTest extends TestCase
    {
        protected function setUp(): void
        {
            parent::setUp();
            App::setLocale('id');
        }

        protected function tearDown(): void
        {
            App::setLocale(config('app.fallback_locale', 'en'));
            parent::tearDown();
        }

        /** @return array<string, array{string, string}> */
        public static function phraseProvider(): array
        {
            return [
                'Phone number' => ['Phone number', 'Nomor telepon'],
                'Started at' => ['Started at', 'Dimulai pada'],
                'Finished at' => ['Finished at', 'Selesai pada'],
                'Due at' => ['Due at', 'Jatuh tempo'],
                'Published at' => ['Published at', 'Dipublikasikan pada'],
                'Birth date' => ['Birth date', 'Tanggal lahir'],
                'Birth place' => ['Birth place', 'Tempat lahir'],
                'Father name' => ['Father name', 'Nama ayah'],
                'Mother name' => ['Mother name', 'Nama ibu'],
                'Quantity' => ['Quantity', 'Jumlah'],
                'Unit price' => ['Unit price', 'Harga satuan'],
                'Total amount' => ['Total amount', 'Total'],
                'Sub total' => ['Sub total', 'Subtotal'],
                'Tax' => ['Tax', 'Pajak'],
                'Discount' => ['Discount', 'Diskon'],
                'Reference number' => ['Reference number', 'Nomor referensi'],
                'Reference' => ['Reference', 'Referensi'],
                'Remarks' => ['Remarks', 'Catatan'],
                'Notes' => ['Notes', 'Catatan'],
                'Description' => ['Description', 'Deskripsi'],
                'Status' => ['Status', 'Status'],
                'Exam definition' => ['Exam definition', 'Definisi ujian'],
                'Ai prompt template' => ['Ai prompt template', 'Template prompt AI'],
                'Active' => ['Active', 'Aktif'],
                'Inactive' => ['Inactive', 'Nonaktif'],
                'Exam question bank' => ['Exam question bank', 'Bank soal ujian'],
                'Exam question' => ['Exam question', 'Soal ujian'],
                'Download demo import CSV' => ['Download demo import CSV', 'Unduh CSV impor demo'],
                'Question import completed.' => ['Question import completed.', 'Impor soal selesai.'],
                'Mark as ready' => ['Mark as ready', 'Tandai siap'],
                'Total questions' => ['Total questions', 'Total soal'],
                'Total score' => ['Total score', 'Total skor'],
                'Duplicate exam' => ['Duplicate exam', 'Duplikasi ujian'],
                'Exam participants' => ['Exam participants', 'Peserta ujian'],
                'Generate from class' => ['Generate from class', 'Generate dari rombel'],
                'Print token cards' => ['Print token cards', 'Cetak kartu token'],
                'Exam published to runtime.' => ['Exam published to runtime.', 'Ujian dipublikasikan ke runtime.'],
                'Republish to runtime' => ['Republish to runtime', 'Publikasikan ulang ke runtime'],
                'Runtime sync failed.' => ['Runtime sync failed.', 'Sinkronisasi runtime gagal.'],
                'Results synced from runtime.' => ['Results synced from runtime.', 'Hasil disinkronkan dari runtime.'],
                'Exam attempts' => ['Exam attempts', 'Percobaan ujian'],
                'Grade essay' => ['Grade essay', 'Nilai esai'],
                'Export CSV' => ['Export CSV', 'Ekspor CSV'],
                'Analytics' => ['Analytics', 'Analitik'],
                'School student' => ['School student', 'Siswa sekolah'],
                'Assigned' => ['Assigned', 'Ditugaskan'],
                'Ready' => ['Ready', 'Siap'],
                'Olympiad level' => ['Olympiad level', 'Tingkat olimpiade'],
                'MI mapping' => ['MI mapping', 'Pemetaan MI'],
                'General information' => ['General information', 'Informasi umum'],
                'Settings' => ['Settings', 'Pengaturan'],
                'Scope' => ['Scope', 'Cakupan'],
                'Configuration' => ['Configuration', 'Konfigurasi'],
                'Approval' => ['Approval', 'Persetujuan'],
                'Trigger event' => ['Trigger event', 'Event pemicu'],
                'Trigger mode' => ['Trigger mode', 'Mode pemicu'],
                'Subject type' => ['Subject type', 'Tipe subjek'],
                'Subject label' => ['Subject label', 'Label subjek'],
                'Step' => ['Step', 'Langkah'],
                'Steps' => ['Steps', 'Langkah'],
                'Action type' => ['Action type', 'Jenis aksi'],
                'Sort order' => ['Sort order', 'Urutan'],
                'Version' => ['Version', 'Versi'],
                'Current step' => ['Current step', 'Langkah saat ini'],
                'Requester' => ['Requester', 'Pemohon'],
                'Status before' => ['Status before', 'Status sebelum'],
                'Status after' => ['Status after', 'Status sesudah'],
                'Workflow version' => ['Workflow version', 'Versi workflow'],
                'Step type' => ['Step type', 'Tipe langkah'],
                'To step' => ['To step', 'Ke langkah'],
                'Calculation' => ['Calculation', 'Kalkulasi'],
                'Payment details' => ['Payment details', 'Detail pembayaran'],
                'Payment method' => ['Payment method', 'Metode pembayaran'],
                'Loan limits' => ['Loan limits', 'Batas peminjaman'],
                'Quantity and pricing' => ['Quantity and pricing', 'Jumlah & harga'],
                'Is taxable' => ['Is taxable', 'Kena pajak'],
                'Is mandatory' => ['Is mandatory', 'Wajib'],
                'Created by' => ['Created by', 'Dibuat oleh'],
                'Updated by' => ['Updated by', 'Diperbarui oleh'],
                'Preferred locale' => ['Preferred locale', 'Bahasa'],
                // Existing phrases still work
                'Student' => ['Student', 'Siswa'],
                'Academic year' => ['Academic year', 'Tahun akademik'],
                'Is active' => ['Is active', 'Aktif'],
                'Created at' => ['Created at', 'Dibuat pada'],
                'Updated at' => ['Updated at', 'Diperbarui pada'],
            ];
        }

        #[DataProvider('phraseProvider')]
        public function test_phrase_translates_correctly(string $input, string $expected): void
        {
            $this->assertSame($expected, FilamentUi::text($input));
        }

        public function test_proper_noun_abbreviations_stay_unchanged(): void
        {
            // All-caps acronyms pass through unchanged
            $this->assertSame('SLA', FilamentUi::text('SLA'));
            $this->assertSame('RFQ', FilamentUi::text('RFQ'));
        }

        public function test_plural_phrases_translate_correctly(): void
        {
            $this->assertSame('Siswa', FilamentUi::text('Students'));
            $this->assertSame('Tahun Akademik', FilamentUi::text('Academic years'));
            $this->assertSame('Pengguna', FilamentUi::text('Users'));
        }

        public function test_field_method_translates_snake_case(): void
        {
            $this->assertSame('Dimulai pada', FilamentUi::field('started_at'));
            $this->assertSame('Selesai pada', FilamentUi::field('finished_at'));
            $this->assertSame('Jatuh tempo', FilamentUi::field('due_at'));
            $this->assertSame('Dipublikasikan pada', FilamentUi::field('published_at'));
            $this->assertSame('Bahasa', FilamentUi::field('preferred_locale'));
        }

        public function test_resolves_translations_from_core_lang_file(): void
        {
            $this->assertSame('Tagihan & Langganan', FilamentUi::text('Billing & Subscription'));
            $this->assertSame('Marketplace Modul', FilamentUi::text('Module Marketplace'));
        }

        public function test_resolves_translations_from_caller_module_lang_file(): void
        {
            $this->assertSame('Siswa', MockSchoolClass::translate('Student'));
            $this->assertSame('Guru', MockSchoolClass::translate('Teacher'));
        }
    }
}

namespace Modules\School\Tests {
    use Modules\Core\Support\FilamentUi;

    class MockSchoolClass
    {
        public static function translate(string $value): string
        {
            return FilamentUi::text($value);
        }
    }
}
