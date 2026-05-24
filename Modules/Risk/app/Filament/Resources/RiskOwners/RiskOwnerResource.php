<?php

namespace Modules\Risk\Filament\Resources\RiskOwners;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Risk\Filament\Resources\RiskOwners\Pages\CreateRiskOwner;
use Modules\Risk\Filament\Resources\RiskOwners\Pages\EditRiskOwner;
use Modules\Risk\Filament\Resources\RiskOwners\Pages\ListRiskOwners;
use Modules\Risk\Filament\Resources\RiskOwners\Pages\ViewRiskOwner;
use Modules\Risk\Filament\Resources\RiskOwners\Schemas\RiskOwnerForm;
use Modules\Risk\Filament\Resources\RiskOwners\Schemas\RiskOwnerInfolist;
use Modules\Risk\Filament\Resources\RiskOwners\Tables\RiskOwnersTable;
use Modules\Risk\Models\RiskOwner;

class RiskOwnerResource extends ModuleResource
{
    protected static ?string $model = RiskOwner::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RiskOwnerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RiskOwnerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RiskOwnersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRiskOwners::route('/'),
            'create' => CreateRiskOwner::route('/create'),
            'view' => ViewRiskOwner::route('/{record}'),
            'edit' => EditRiskOwner::route('/{record}/edit'),
        ];
    }
}
