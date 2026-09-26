<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\App;
use Modules\Core\Services\ProductProfileCatalog;
use Tests\TestCase;

class TenantSetupCenterTest extends TestCase
{
    public function test_home_page_is_available(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_product_profile_catalog_follows_active_locale(): void
    {
        $catalog = app(ProductProfileCatalog::class);
        $originalLocale = App::getLocale();

        try {
            App::setLocale('en');

            $this->assertSame('School (K-12)', $catalog->label('school'));
            $this->assertSame(
                ['school academics', 'student admissions', 'finance', 'HR', 'library'],
                $catalog->capabilities('school'),
            );
            $this->assertStringContainsString('Focus: school academics', $catalog->descriptions()['school']);

            App::setLocale('id');

            $this->assertSame('Sekolah (K-12)', $catalog->label('school'));
            $this->assertSame(
                ['akademik sekolah', 'penerimaan siswa', 'keuangan', 'SDM', 'perpustakaan'],
                $catalog->capabilities('school'),
            );
            $this->assertStringContainsString('Fokus: akademik sekolah', $catalog->descriptions()['school']);
        } finally {
            App::setLocale($originalLocale);
        }
    }

    public function test_setup_center_copy_has_english_and_indonesian_variants(): void
    {
        $originalLocale = App::getLocale();

        try {
            App::setLocale('en');

            $this->assertSame('Setup Center', __('core::core.setup_center.title'));
            $this->assertSame(
                '3 of 7 steps completed',
                __('core::core.setup_center.steps_completed', ['completed' => 3, 'total' => 7]),
            );
            $this->assertSame('Action required', __('core::core.setup_center.badges.action_required'));

            App::setLocale('id');

            $this->assertSame('Pusat Setup', __('core::core.setup_center.title'));
            $this->assertSame(
                '3 dari 7 langkah selesai',
                __('core::core.setup_center.steps_completed', ['completed' => 3, 'total' => 7]),
            );
            $this->assertSame('Perlu tindakan', __('core::core.setup_center.badges.action_required'));
        } finally {
            App::setLocale($originalLocale);
        }
    }
}
