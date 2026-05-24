<?php

namespace Modules\MerchOrder\Filament\Resources\UniformPackages;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\MerchOrder\Filament\Resources\UniformPackages\Pages\CreateUniformPackage;
use Modules\MerchOrder\Filament\Resources\UniformPackages\Pages\EditUniformPackage;
use Modules\MerchOrder\Filament\Resources\UniformPackages\Pages\ListUniformPackages;
use Modules\MerchOrder\Filament\Resources\UniformPackages\Pages\ViewUniformPackage;
use Modules\MerchOrder\Filament\Resources\UniformPackages\Schemas\UniformPackageForm;
use Modules\MerchOrder\Filament\Resources\UniformPackages\Schemas\UniformPackageInfolist;
use Modules\MerchOrder\Filament\Resources\UniformPackages\Tables\UniformPackagesTable;
use Modules\MerchOrder\Models\UniformPackage;

class UniformPackageResource extends ModuleResource
{
    protected static ?string $model = UniformPackage::class;

    public static function form(Schema $schema): Schema
    {
        return UniformPackageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UniformPackageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UniformPackagesTable::configure($table);
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
            'index' => ListUniformPackages::route('/'),
            'create' => CreateUniformPackage::route('/create'),
            'view' => ViewUniformPackage::route('/{record}'),
            'edit' => EditUniformPackage::route('/{record}/edit'),
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
