<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Cms\Exceptions\DuplicateCmsSiteCodeException;
use Modules\Cms\Exceptions\DuplicatePageSlugException;
use Modules\Cms\Models\Site;
use Modules\Cms\Services\CmsSiteRegistrationService;
use Modules\Cms\Services\PagePublishingService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class CmsPublishingServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_site_registration_and_page_publish_flow(): void
    {
        ['tenant' => $tenant] = $this->makeTenantContext(['core', 'cms']);

        $site = app(CmsSiteRegistrationService::class)->register(
            $tenant->id,
            'main-site',
            'Main Website',
            'school.example.test',
        );

        $this->assertSame('main-site', $site->code);

        $page = app(PagePublishingService::class)->createDraft(
            $tenant->id,
            $site->id,
            'About Us',
            'Tentang Kami',
            'About Us',
        );

        $this->assertSame('about-us', $page->slug);
        $this->assertSame('draft', $page->status);

        $published = app(PagePublishingService::class)->publish($page);
        $this->assertSame('published', $published->status);
        $this->assertNotNull($published->published_at);
    }

    public function test_duplicate_site_code_is_rejected(): void
    {
        ['tenant' => $tenant] = $this->makeTenantContext(['core', 'cms']);

        $service = app(CmsSiteRegistrationService::class);
        $service->register($tenant->id, 'portal', 'Portal');

        $this->expectException(DuplicateCmsSiteCodeException::class);
        $service->register($tenant->id, 'portal', 'Portal Duplicate');
    }

    public function test_duplicate_page_slug_is_rejected(): void
    {
        ['tenant' => $tenant] = $this->makeTenantContext(['core', 'cms']);

        $site = Site::factory()->create(['tenant_id' => $tenant->id]);

        app(PagePublishingService::class)->createDraft($tenant->id, $site->id, 'news', 'Berita', 'News');

        $this->expectException(DuplicatePageSlugException::class);
        app(PagePublishingService::class)->createDraft($tenant->id, $site->id, 'News', 'Berita 2');
    }
}
