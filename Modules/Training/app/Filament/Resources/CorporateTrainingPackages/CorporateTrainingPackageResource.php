<?php

namespace Modules\Training\Filament\Resources\CorporateTrainingPackages;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Training\Filament\Resources\CorporateTrainingPackages\Pages\CreateCorporateTrainingPackage;
use Modules\Training\Filament\Resources\CorporateTrainingPackages\Pages\EditCorporateTrainingPackage;
use Modules\Training\Filament\Resources\CorporateTrainingPackages\Pages\ListCorporateTrainingPackages;
use Modules\Training\Filament\Resources\CorporateTrainingPackages\Pages\ViewCorporateTrainingPackage;
use Modules\Training\Filament\Resources\CorporateTrainingPackages\Schemas\CorporateTrainingPackageForm;
use Modules\Training\Filament\Resources\CorporateTrainingPackages\Schemas\CorporateTrainingPackageInfolist;
use Modules\Training\Filament\Resources\CorporateTrainingPackages\Tables\CorporateTrainingPackagesTable;
use Modules\Training\Models\CorporateTrainingPackage;

class CorporateTrainingPackageResource extends ModuleResource
{
    protected static ?string $model = CorporateTrainingPackage::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CorporateTrainingPackageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CorporateTrainingPackageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CorporateTrainingPackagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCorporateTrainingPackages::route('/'),
            'create' => CreateCorporateTrainingPackage::route('/create'),
            'view' => ViewCorporateTrainingPackage::route('/{record}'),
            'edit' => EditCorporateTrainingPackage::route('/{record}/edit'),
        ];
    }
}
