<?php

namespace Tests\Support;

use Modules\Alumni\Filament\Resources\CompanyPartners\Pages\ListCompanyPartners;
use Modules\Alumni\Models\CompanyPartner;
use Modules\Boarding\Filament\Resources\Dormitories\Pages\ListDormitories;
use Modules\Boarding\Models\Dormitory;
use Modules\Cafeteria\Filament\Resources\Menus\Pages\ListMenus;
use Modules\Cafeteria\Models\Menu;
use Modules\Capacity\Filament\Resources\CapacityResources\Pages\ListCapacityResources;
use Modules\Capacity\Models\CapacityResource;
use Modules\Clinic\Filament\Resources\Allergies\Pages\ListAllergies;
use Modules\Clinic\Models\Allergy;
use Modules\Consulting\Filament\Resources\ConsultingClients\Pages\ListConsultingClients;
use Modules\Consulting\Models\ConsultingClient;
use Modules\Dms\Filament\Resources\DocumentFolders\Pages\ListDocumentFolders;
use Modules\Dms\Models\DocumentFolder;
use Modules\Event\Filament\Resources\Events\Pages\ListEvents;
use Modules\Event\Models\Event;
use Modules\MerchOrder\Filament\Resources\UniformPackages\Pages\ListUniformPackages;
use Modules\MerchOrder\Models\UniformPackage;
use Modules\PhysicalSecurity\Filament\Resources\Guards\Pages\ListGuards;
use Modules\PhysicalSecurity\Models\Guard;
use Modules\Printing\Filament\Resources\PrintTemplates\Pages\ListPrintTemplates;
use Modules\Printing\Models\PrintTemplate;
use Modules\Property\Filament\Resources\Properties\Pages\ListProperties;
use Modules\Property\Models\Property as PropertyModel;
use Modules\Transport\Filament\Resources\Routes\Pages\ListRoutes;
use Modules\Transport\Models\Route;

/**
 * Historically thin GA modules (ANALISIS §6) — shared registry for H2/H3 smoke tests.
 */
final class ThinGaModuleCatalog
{
    /**
     * @return list<array{
     *     key: string,
     *     modules: list<string>,
     *     model: class-string,
     *     list_page: class-string,
     *     feature_test: string,
     * }>
     */
    public static function entries(): array
    {
        return [
            [
                'key' => 'transport',
                'modules' => ['core', 'transport'],
                'model' => Route::class,
                'list_page' => ListRoutes::class,
                'feature_test' => 'RouteRegistrationServiceTest.php',
            ],
            [
                'key' => 'property',
                'modules' => ['core', 'property'],
                'model' => PropertyModel::class,
                'list_page' => ListProperties::class,
                'feature_test' => 'PropertyRegistrationServiceTest.php',
            ],
            [
                'key' => 'cafeteria',
                'modules' => ['core', 'cafeteria'],
                'model' => Menu::class,
                'list_page' => ListMenus::class,
                'feature_test' => 'MenuRegistrationServiceTest.php',
            ],
            [
                'key' => 'boarding',
                'modules' => ['core', 'boarding'],
                'model' => Dormitory::class,
                'list_page' => ListDormitories::class,
                'feature_test' => 'DormitoryRegistrationServiceTest.php',
            ],
            [
                'key' => 'alumni',
                'modules' => ['core', 'alumni'],
                'model' => CompanyPartner::class,
                'list_page' => ListCompanyPartners::class,
                'feature_test' => 'CompanyPartnerRegistrationServiceTest.php',
            ],
            [
                'key' => 'printing',
                'modules' => ['core', 'printing'],
                'model' => PrintTemplate::class,
                'list_page' => ListPrintTemplates::class,
                'feature_test' => 'PrintTemplateRegistrationServiceTest.php',
            ],
            [
                'key' => 'physicalsecurity',
                'modules' => ['core', 'physicalsecurity'],
                'model' => Guard::class,
                'list_page' => ListGuards::class,
                'feature_test' => 'GuardRegistrationServiceTest.php',
            ],
            [
                'key' => 'consulting',
                'modules' => ['core', 'consulting'],
                'model' => ConsultingClient::class,
                'list_page' => ListConsultingClients::class,
                'feature_test' => 'ConsultingClientRegistrationServiceTest.php',
            ],
            [
                'key' => 'merchorder',
                'modules' => ['core', 'merchorder'],
                'model' => UniformPackage::class,
                'list_page' => ListUniformPackages::class,
                'feature_test' => 'UniformPackageRegistrationServiceTest.php',
            ],
            [
                'key' => 'event',
                'modules' => ['core', 'event'],
                'model' => Event::class,
                'list_page' => ListEvents::class,
                'feature_test' => 'EventRegistrationServiceTest.php',
            ],
            [
                'key' => 'clinic',
                'modules' => ['core', 'clinic'],
                'model' => Allergy::class,
                'list_page' => ListAllergies::class,
                'feature_test' => 'AllergyRegistrationServiceTest.php',
            ],
            [
                'key' => 'capacity',
                'modules' => ['core', 'capacity'],
                'model' => CapacityResource::class,
                'list_page' => ListCapacityResources::class,
                'feature_test' => 'CapacityResourceRegistrationServiceTest.php',
            ],
            [
                'key' => 'dms',
                'modules' => ['core', 'dms'],
                'model' => DocumentFolder::class,
                'list_page' => ListDocumentFolders::class,
                'feature_test' => 'DocumentFolderRegistrationServiceTest.php',
            ],
        ];
    }
}
