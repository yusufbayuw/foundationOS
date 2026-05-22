<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\App;
use Modules\Core\Support\FilamentUi;
use Tests\TestCase;

class FilamentUiHonorsLocaleTest extends TestCase
{
    public function test_text_returns_indonesian_when_locale_is_id(): void
    {
        App::setLocale('id');

        $this->assertSame('Siswa', FilamentUi::text('Student'));
        $this->assertSame('Pengguna', FilamentUi::text('User'));
        $this->assertSame('Keuangan', FilamentUi::text('Finance'));
    }

    public function test_text_returns_original_when_locale_is_en(): void
    {
        App::setLocale('en');

        $this->assertSame('Student', FilamentUi::text('Student'));
        $this->assertSame('User', FilamentUi::text('User'));
        $this->assertSame('Finance', FilamentUi::text('Finance'));
    }

    public function test_field_returns_indonesian_for_known_field_in_id_locale(): void
    {
        App::setLocale('id');

        $this->assertSame('Siswa', FilamentUi::field('student'));
        $this->assertSame('Nama', FilamentUi::field('name'));
        $this->assertSame('Dibuat pada', FilamentUi::field('created_at'));
    }

    public function test_field_returns_english_label_in_en_locale(): void
    {
        App::setLocale('en');

        $this->assertSame('Student', FilamentUi::field('student'));
        $this->assertSame('Name', FilamentUi::field('name'));
        $this->assertSame('Created At', FilamentUi::field('created_at'));
    }

    public function test_is_indonesian_returns_true_when_locale_is_id(): void
    {
        App::setLocale('id');
        $this->assertTrue(FilamentUi::isIndonesian());
    }

    public function test_is_indonesian_returns_false_when_locale_is_en(): void
    {
        App::setLocale('en');
        $this->assertFalse(FilamentUi::isIndonesian());
    }
}
