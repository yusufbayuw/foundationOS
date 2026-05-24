<?php

namespace Modules\Cafeteria\Filament\Resources\CafeteriaInspections;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Cafeteria\Filament\Resources\CafeteriaInspections\Pages\CreateCafeteriaInspection;
use Modules\Cafeteria\Filament\Resources\CafeteriaInspections\Pages\EditCafeteriaInspection;
use Modules\Cafeteria\Filament\Resources\CafeteriaInspections\Pages\ListCafeteriaInspections;
use Modules\Cafeteria\Filament\Resources\CafeteriaInspections\Pages\ViewCafeteriaInspection;
use Modules\Cafeteria\Filament\Resources\CafeteriaInspections\Schemas\CafeteriaInspectionForm;
use Modules\Cafeteria\Filament\Resources\CafeteriaInspections\Schemas\CafeteriaInspectionInfolist;
use Modules\Cafeteria\Filament\Resources\CafeteriaInspections\Tables\CafeteriaInspectionsTable;
use Modules\Cafeteria\Models\CafeteriaInspection;
use Modules\Core\Filament\Support\ModuleResource;

class CafeteriaInspectionResource extends ModuleResource
{
    protected static ?string $model = CafeteriaInspection::class;

    public static function form(Schema $schema): Schema
    {
        return CafeteriaInspectionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CafeteriaInspectionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CafeteriaInspectionsTable::configure($table);
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
            'index' => ListCafeteriaInspections::route('/'),
            'create' => CreateCafeteriaInspection::route('/create'),
            'view' => ViewCafeteriaInspection::route('/{record}'),
            'edit' => EditCafeteriaInspection::route('/{record}/edit'),
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
