<?php

namespace Modules\Cafeteria\Filament\Resources\CafeteriaTenants;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Cafeteria\Filament\Resources\CafeteriaTenants\Pages\CreateCafeteriaTenant;
use Modules\Cafeteria\Filament\Resources\CafeteriaTenants\Pages\EditCafeteriaTenant;
use Modules\Cafeteria\Filament\Resources\CafeteriaTenants\Pages\ListCafeteriaTenants;
use Modules\Cafeteria\Filament\Resources\CafeteriaTenants\Pages\ViewCafeteriaTenant;
use Modules\Cafeteria\Filament\Resources\CafeteriaTenants\Schemas\CafeteriaTenantForm;
use Modules\Cafeteria\Filament\Resources\CafeteriaTenants\Schemas\CafeteriaTenantInfolist;
use Modules\Cafeteria\Filament\Resources\CafeteriaTenants\Tables\CafeteriaTenantsTable;
use Modules\Cafeteria\Models\CafeteriaTenant;
use Modules\Core\Filament\Support\ModuleResource;

class CafeteriaTenantResource extends ModuleResource
{
    protected static ?string $model = CafeteriaTenant::class;

    public static function form(Schema $schema): Schema
    {
        return CafeteriaTenantForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CafeteriaTenantInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CafeteriaTenantsTable::configure($table);
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
            'index' => ListCafeteriaTenants::route('/'),
            'create' => CreateCafeteriaTenant::route('/create'),
            'view' => ViewCafeteriaTenant::route('/{record}'),
            'edit' => EditCafeteriaTenant::route('/{record}/edit'),
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
