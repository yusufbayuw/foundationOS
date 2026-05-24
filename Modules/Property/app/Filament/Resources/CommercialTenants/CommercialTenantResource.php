<?php

namespace Modules\Property\Filament\Resources\CommercialTenants;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Property\Filament\Resources\CommercialTenants\Pages\CreateCommercialTenant;
use Modules\Property\Filament\Resources\CommercialTenants\Pages\EditCommercialTenant;
use Modules\Property\Filament\Resources\CommercialTenants\Pages\ListCommercialTenants;
use Modules\Property\Filament\Resources\CommercialTenants\Pages\ViewCommercialTenant;
use Modules\Property\Filament\Resources\CommercialTenants\Schemas\CommercialTenantForm;
use Modules\Property\Filament\Resources\CommercialTenants\Schemas\CommercialTenantInfolist;
use Modules\Property\Filament\Resources\CommercialTenants\Tables\CommercialTenantsTable;
use Modules\Property\Models\CommercialTenant;

class CommercialTenantResource extends ModuleResource
{
    protected static ?string $model = CommercialTenant::class;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return CommercialTenantForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CommercialTenantInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommercialTenantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommercialTenants::route('/'),
            'create' => CreateCommercialTenant::route('/create'),
            'view' => ViewCommercialTenant::route('/{record}'),
            'edit' => EditCommercialTenant::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
