<?php

namespace Modules\Core\Filament\Resources\Tenants;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\Tenants\Pages\CreateTenant;
use Modules\Core\Filament\Resources\Tenants\Pages\EditTenant;
use Modules\Core\Filament\Resources\Tenants\Pages\ListTenants;
use Modules\Core\Filament\Resources\Tenants\Pages\ViewTenant;
use Modules\Core\Filament\Resources\Tenants\Schemas\TenantForm;
use Modules\Core\Filament\Resources\Tenants\Schemas\TenantInfolist;
use Modules\Core\Filament\Resources\Tenants\Tables\TenantsTable;
use Modules\Core\Models\Tenant;

class TenantResource extends LocalizedResource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TenantForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TenantInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantsTable::configure($table);
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
            'index' => ListTenants::route('/'),
            'create' => CreateTenant::route('/create'),
            'view' => ViewTenant::route('/{record}'),
            'edit' => EditTenant::route('/{record}/edit'),
        ];
    }
}
