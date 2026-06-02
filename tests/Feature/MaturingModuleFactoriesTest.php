<?php

namespace Tests\Feature;

use App\Filament\Imports\CustomerImporter;
use App\Filament\Imports\DonorImporter;
use App\Filament\Imports\RiskCategoryImporter;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Donation\Models\Donor;
use Modules\Cms\Models\Page;
use Modules\Cms\Models\Site;
use Modules\Legal\Models\LegalDocument;
use Modules\Marketplace\Models\Seller;
use Modules\Property\Models\Property as PropertyModel;
use Modules\Risk\Models\RiskCategory;
use Modules\Sales\Models\Customer;
use Modules\Training\Models\Instructor;
use Modules\Asset\Models\AssetCategory;
use Modules\EOffice\Models\LetterCategory;
use Modules\Facility\Models\Room;
use Modules\Helpdesk\Models\TicketCategory;
use Modules\Transport\Models\Route;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class MaturingModuleFactoriesTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_donor_factory_persists_for_tenant(): void
    {
        ['tenant' => $tenant] = $this->makeTenantContext(['core', 'donation']);

        $donor = Donor::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertNotNull($donor->email);
        $this->assertFalse($donor->is_anonymous);
    }

    public function test_customer_factory_cooperative_state(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'sales']);

        $customer = Customer::factory()
            ->cooperative('M-100', 7.5)
            ->create([
                'tenant_id' => $tenant->id,
                'organization_id' => $organization->id,
            ]);

        $this->assertTrue($customer->is_cooperative_member);
        $this->assertEqualsWithDelta(7.5, (float) $customer->member_discount_percent, 0.01);
    }

    public function test_risk_category_factory_persists(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'risk']);

        $category = RiskCategory::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $this->assertSame('active', $category->status);
        $this->assertNotEmpty($category->code);
    }

    public function test_route_factory_persists_for_tenant(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'transport']);

        $route = Route::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $this->assertSame('active', $route->status);
    }

    public function test_property_and_instructor_factories_persist(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'property', 'training']);

        $property = PropertyModel::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $instructor = Instructor::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $this->assertNotEmpty($property->code);
        $this->assertNotEmpty($instructor->code);
    }

    public function test_ga_modules_expose_csv_import_columns(): void
    {
        $this->assertGreaterThanOrEqual(3, count(DonorImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(CustomerImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(RiskCategoryImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(\App\Filament\Imports\RouteImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(\App\Filament\Imports\PropertyImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(\App\Filament\Imports\InstructorImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(\App\Filament\Imports\SellerImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(\App\Filament\Imports\LegalDocumentImporter::getColumns()));
        $this->assertGreaterThanOrEqual(2, count(\App\Filament\Imports\CmsSiteImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(\App\Filament\Imports\AssetCategoryImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(\App\Filament\Imports\TicketCategoryImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(\App\Filament\Imports\RoomImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(\App\Filament\Imports\LetterCategoryImporter::getColumns()));
    }

    public function test_marketplace_cms_legal_factories_persist(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext([
            'core', 'marketplace', 'cms', 'legal',
        ]);

        $seller = Seller::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $site = Site::factory()->create(['tenant_id' => $tenant->id]);
        $page = Page::factory()->create([
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
        ]);

        $document = LegalDocument::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $this->assertNotEmpty($seller->code);
        $this->assertNotEmpty($page->slug);
        $this->assertNotEmpty($document->code);
    }

    public function test_asset_helpdesk_facility_eoffice_factories_persist(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext([
            'core', 'asset', 'helpdesk', 'facility', 'eoffice',
        ]);

        $assetCategory = AssetCategory::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $ticketCategory = TicketCategory::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $room = Room::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $letterCategory = LetterCategory::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $this->assertNotEmpty($assetCategory->code);
        $this->assertGreaterThan(0, $ticketCategory->response_hours);
        $this->assertTrue($room->is_bookable);
        $this->assertNotEmpty($letterCategory->code);
    }
}
