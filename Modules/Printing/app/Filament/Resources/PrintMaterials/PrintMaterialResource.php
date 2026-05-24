<?php

namespace Modules\Printing\Filament\Resources\PrintMaterials;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Printing\Filament\Resources\PrintMaterials\Pages\CreatePrintMaterial;
use Modules\Printing\Filament\Resources\PrintMaterials\Pages\EditPrintMaterial;
use Modules\Printing\Filament\Resources\PrintMaterials\Pages\ListPrintMaterials;
use Modules\Printing\Filament\Resources\PrintMaterials\Pages\ViewPrintMaterial;
use Modules\Printing\Filament\Resources\PrintMaterials\Schemas\PrintMaterialForm;
use Modules\Printing\Filament\Resources\PrintMaterials\Schemas\PrintMaterialInfolist;
use Modules\Printing\Filament\Resources\PrintMaterials\Tables\PrintMaterialsTable;
use Modules\Printing\Models\PrintMaterial;

class PrintMaterialResource extends ModuleResource
{
    protected static ?string $model = PrintMaterial::class;

    public static function form(Schema $schema): Schema
    {
        return PrintMaterialForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PrintMaterialInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrintMaterialsTable::configure($table);
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
            'index' => ListPrintMaterials::route('/'),
            'create' => CreatePrintMaterial::route('/create'),
            'view' => ViewPrintMaterial::route('/{record}'),
            'edit' => EditPrintMaterial::route('/{record}/edit'),
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
