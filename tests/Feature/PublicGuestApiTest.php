<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Alumni\Models\JobPosting;
use Modules\Cms\Models\Page;
use Modules\Cms\Models\PageBlock;
use Modules\Cms\Models\Site;
use Modules\Core\Models\Tenant;
use Modules\Donation\Models\Campaign;
use Modules\Event\Models\Event;
use Modules\Marketplace\Models\MarketplaceProduct;
use Tests\TestCase;

class PublicGuestApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_can_only_see_active_public_catalog_resources(): void
    {
        $tenant = Tenant::factory()->create();

        $publicCampaign = Campaign::withoutTenantScope()->create($this->catalogPayload($tenant, [
            'code' => 'DON-PUBLIC',
            'name' => 'Public Campaign',
            'category' => 'education',
            'goal_amount' => 1000000,
            'raised_amount' => 250000,
            'is_public' => true,
        ]));
        Campaign::withoutTenantScope()->create($this->catalogPayload($tenant, [
            'code' => 'DON-PRIVATE',
            'name' => 'Private Campaign',
            'is_public' => false,
        ]));
        Campaign::withoutTenantScope()->create($this->catalogPayload($tenant, [
            'code' => 'DON-INACTIVE',
            'name' => 'Inactive Campaign',
            'status' => 'draft',
            'is_public' => true,
        ]));

        $event = Event::withoutTenantScope()->create($this->catalogPayload($tenant, ['code' => 'EVT-PUBLIC', 'name' => 'Public Event']));
        Event::withoutTenantScope()->create($this->catalogPayload($tenant, ['code' => 'EVT-INACTIVE', 'name' => 'Inactive Event', 'status' => 'draft']));

        $jobPosting = JobPosting::withoutTenantScope()->create($this->catalogPayload($tenant, ['code' => 'JOB-PUBLIC', 'name' => 'Public Job']));
        JobPosting::withoutTenantScope()->create($this->catalogPayload($tenant, ['code' => 'JOB-INACTIVE', 'name' => 'Inactive Job', 'status' => 'closed']));

        $product = MarketplaceProduct::withoutTenantScope()->create($this->catalogPayload($tenant, ['code' => 'PROD-PUBLIC', 'name' => 'Public Product']));
        MarketplaceProduct::withoutTenantScope()->create($this->catalogPayload($tenant, ['code' => 'PROD-INACTIVE', 'name' => 'Inactive Product', 'status' => 'draft']));

        $this->getJson('/api/v1/app/public/campaigns')
            ->assertOk()
            ->assertJsonPath('data.0.id', $publicCampaign->id)
            ->assertJsonMissing(['name' => 'Private Campaign'])
            ->assertJsonMissing(['name' => 'Inactive Campaign'])
            ->assertJsonMissingPath('data.0.tenant_id')
            ->assertJsonMissingPath('data.0.organization_id')
            ->assertJsonMissingPath('data.0.status')
            ->assertJsonMissingPath('data.0.is_public');

        $this->assertPublicCatalogEndpoint('/api/v1/app/public/events', $event->id, 'Inactive Event');
        $this->assertPublicCatalogEndpoint('/api/v1/app/public/job-postings', $jobPosting->id, 'Inactive Job');
        $this->assertPublicCatalogEndpoint('/api/v1/app/public/products', $product->id, 'Inactive Product');
    }

    public function test_guest_can_see_published_cms_homepage_only(): void
    {
        $tenant = Tenant::factory()->create();
        $site = Site::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Main Site',
            'is_active' => true,
        ]);

        $homepage = Page::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
            'slug' => 'home',
            'title_id' => 'Beranda',
            'title_en' => 'Homepage',
            'template' => 'home',
            'status' => 'published',
            'published_at' => now(),
        ]);
        PageBlock::query()->create([
            'page_id' => $homepage->id,
            'block_type' => 'hero',
            'sort_order' => 1,
            'content' => ['heading' => 'Welcome'],
        ]);
        Page::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
            'slug' => 'about',
            'title_id' => 'Tentang Kami',
            'template' => 'default',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->getJson('/api/v1/app/public/cms/homepage')
            ->assertOk()
            ->assertJsonPath('data.id', $homepage->id)
            ->assertJsonPath('data.slug', 'home')
            ->assertJsonPath('data.blocks.0.block_type', 'hero')
            ->assertJsonMissing(['title_id' => 'Tentang Kami'])
            ->assertJsonMissingPath('data.tenant_id')
            ->assertJsonMissingPath('data.site_id')
            ->assertJsonMissingPath('data.status');
    }

    public function test_guest_cannot_access_private_authenticated_or_unpublished_resources(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized();
        $this->getJson('/api/v1/students')->assertUnauthorized();
        $this->getJson('/api/v1/employees')->assertUnauthorized();
        $this->getJson('/api/v1/app/public/donations')->assertNotFound();
        $this->getJson('/api/v1/app/public/orders')->assertNotFound();
        $this->getJson('/api/v1/app/public/voucher-claims')->assertNotFound();
        $this->getJson('/api/v1/app/public/notifications')->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function catalogPayload(Tenant $tenant, array $overrides = []): array
    {
        return array_merge([
            'tenant_id' => $tenant->id,
            'code' => Str::upper(Str::random(10)),
            'name' => 'Public Item',
            'status' => 'active',
            'description' => 'Public description',
            'meta' => ['summary' => 'Public summary'],
        ], $overrides);
    }

    private function assertPublicCatalogEndpoint(string $uri, int $expectedId, string $missingName): void
    {
        $this->getJson($uri)
            ->assertOk()
            ->assertJsonPath('data.0.id', $expectedId)
            ->assertJsonMissing(['name' => $missingName])
            ->assertJsonMissingPath('data.0.tenant_id')
            ->assertJsonMissingPath('data.0.organization_id')
            ->assertJsonMissingPath('data.0.status');
    }
}
