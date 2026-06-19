<?php

namespace Tests\Feature;

use App\Filament\Imports\AiPromptTemplateImporter;
use App\Filament\Imports\AllergyImporter;
use App\Filament\Imports\AssetCategoryImporter;
use App\Filament\Imports\AuditProgramImporter;
use App\Filament\Imports\CapacityResourceImporter;
use App\Filament\Imports\CmsSiteImporter;
use App\Filament\Imports\CompanyPartnerImporter;
use App\Filament\Imports\ConsultingClientImporter;
use App\Filament\Imports\CounselorImporter;
use App\Filament\Imports\CustomerImporter;
use App\Filament\Imports\DocumentFolderImporter;
use App\Filament\Imports\DonorImporter;
use App\Filament\Imports\DormitoryImporter;
use App\Filament\Imports\EventImporter;
use App\Filament\Imports\ExamQuestionBankImporter;
use App\Filament\Imports\GuardImporter;
use App\Filament\Imports\InstructorImporter;
use App\Filament\Imports\IsoControlImporter;
use App\Filament\Imports\KpiAreaImporter;
use App\Filament\Imports\LegalDocumentImporter;
use App\Filament\Imports\LetterCategoryImporter;
use App\Filament\Imports\MenuImporter;
use App\Filament\Imports\NotificationTemplateImporter;
use App\Filament\Imports\PrintTemplateImporter;
use App\Filament\Imports\PropertyImporter;
use App\Filament\Imports\QualityStandardImporter;
use App\Filament\Imports\RiskCategoryImporter;
use App\Filament\Imports\RoomImporter;
use App\Filament\Imports\RouteImporter;
use App\Filament\Imports\SellerImporter;
use App\Filament\Imports\SoftwareLicenseImporter;
use App\Filament\Imports\TicketCategoryImporter;
use App\Filament\Imports\UniformPackageImporter;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Ai\Models\AiPromptTemplate;
use Modules\Alumni\Models\CompanyPartner;
use Modules\Asset\Models\AssetCategory;
use Modules\Boarding\Models\Dormitory;
use Modules\Cafeteria\Models\Menu;
use Modules\Capacity\Models\CapacityResource;
use Modules\Clinic\Models\Allergy;
use Modules\Cms\Models\Page;
use Modules\Cms\Models\Site;
use Modules\Consulting\Models\ConsultingClient;
use Modules\Counseling\Models\Counselor;
use Modules\Dms\Models\DocumentFolder;
use Modules\Donation\Models\Donor;
use Modules\EducationQa\Models\QualityStandard;
use Modules\EOffice\Models\LetterCategory;
use Modules\Event\Models\Event as EventModel;
use Modules\Exam\Models\ExamQuestionBank;
use Modules\Facility\Models\Room;
use Modules\Helpdesk\Models\TicketCategory;
use Modules\InternalAudit\Models\AuditProgram;
use Modules\IsoCompliance\Models\IsoControl;
use Modules\ItOps\Models\SoftwareLicense;
use Modules\KpiEnterprise\Models\KpiArea;
use Modules\Legal\Models\LegalDocument;
use Modules\Marketplace\Models\Seller;
use Modules\MerchOrder\Models\UniformPackage;
use Modules\Messaging\Models\NotificationTemplate;
use Modules\PhysicalSecurity\Models\Guard;
use Modules\Printing\Models\PrintTemplate;
use Modules\Property\Models\Property as PropertyModel;
use Modules\Risk\Models\RiskCategory;
use Modules\Sales\Models\Customer;
use Modules\Training\Models\Instructor;
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
        $this->assertGreaterThanOrEqual(3, count(RouteImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(PropertyImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(InstructorImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(SellerImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(LegalDocumentImporter::getColumns()));
        $this->assertGreaterThanOrEqual(2, count(CmsSiteImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(AssetCategoryImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(TicketCategoryImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(RoomImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(LetterCategoryImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(DocumentFolderImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(SoftwareLicenseImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(DormitoryImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(MenuImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(GuardImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(CounselorImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(AllergyImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(EventImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(UniformPackageImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(CompanyPartnerImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(NotificationTemplateImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(PrintTemplateImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(AuditProgramImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(IsoControlImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(QualityStandardImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(KpiAreaImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(CapacityResourceImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(ConsultingClientImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(ExamQuestionBankImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(AiPromptTemplateImporter::getColumns()));
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

    public function test_dms_itops_boarding_cafeteria_factories_persist(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext([
            'core', 'dms', 'itops', 'boarding', 'cafeteria',
        ]);

        $folder = DocumentFolder::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $license = SoftwareLicense::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $dormitory = Dormitory::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $menu = Menu::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $this->assertNotEmpty($folder->code);
        $this->assertNotEmpty($license->code);
        $this->assertNotEmpty($dormitory->code);
        $this->assertNotEmpty($menu->code);
    }

    public function test_physicalsecurity_counseling_clinic_event_factories_persist(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext([
            'core', 'physicalsecurity', 'counseling', 'clinic', 'event',
        ]);

        $guard = Guard::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $counselor = Counselor::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $allergy = Allergy::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $event = EventModel::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $this->assertNotEmpty($guard->code);
        $this->assertNotEmpty($counselor->code);
        $this->assertNotEmpty($allergy->code);
        $this->assertNotEmpty($event->code);
    }

    public function test_merchorder_alumni_messaging_printing_factories_persist(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext([
            'core', 'merchorder', 'alumni', 'messaging', 'printing',
        ]);

        $package = UniformPackage::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $partner = CompanyPartner::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $template = NotificationTemplate::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $printTemplate = PrintTemplate::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $this->assertNotEmpty($package->code);
        $this->assertNotEmpty($partner->code);
        $this->assertNotEmpty($template->code);
        $this->assertNotEmpty($printTemplate->code);
    }

    public function test_final_batch_factories_persist(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext([
            'core', 'internalaudit', 'isocompliance', 'educationqa', 'exam',
            'kpienterprise', 'capacity', 'ai', 'consulting',
        ]);

        $this->assertNotEmpty(AuditProgram::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ])->code);

        $this->assertNotEmpty(IsoControl::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ])->code);

        $this->assertNotEmpty(QualityStandard::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ])->code);

        $this->assertNotEmpty(ExamQuestionBank::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ])->code);

        $this->assertNotEmpty(KpiArea::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ])->code);

        $this->assertNotEmpty(CapacityResource::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ])->code);

        $this->assertNotEmpty(AiPromptTemplate::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ])->code);

        $this->assertNotEmpty(ConsultingClient::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ])->code);
    }
}
