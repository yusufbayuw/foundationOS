<?php

namespace Modules\MerchOrder\Filament\Resources\BookPackages;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\MerchOrder\Filament\Resources\BookPackages\Pages\CreateBookPackage;
use Modules\MerchOrder\Filament\Resources\BookPackages\Pages\EditBookPackage;
use Modules\MerchOrder\Filament\Resources\BookPackages\Pages\ListBookPackages;
use Modules\MerchOrder\Filament\Resources\BookPackages\Pages\ViewBookPackage;
use Modules\MerchOrder\Filament\Resources\BookPackages\Schemas\BookPackageForm;
use Modules\MerchOrder\Filament\Resources\BookPackages\Schemas\BookPackageInfolist;
use Modules\MerchOrder\Filament\Resources\BookPackages\Tables\BookPackagesTable;
use Modules\MerchOrder\Models\BookPackage;

class BookPackageResource extends ModuleResource
{
    protected static ?string $model = BookPackage::class;

    public static function form(Schema $schema): Schema
    {
        return BookPackageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BookPackageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookPackagesTable::configure($table);
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
            'index' => ListBookPackages::route('/'),
            'create' => CreateBookPackage::route('/create'),
            'view' => ViewBookPackage::route('/{record}'),
            'edit' => EditBookPackage::route('/{record}/edit'),
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
