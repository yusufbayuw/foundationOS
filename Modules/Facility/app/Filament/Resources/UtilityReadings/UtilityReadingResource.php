<?php

namespace Modules\Facility\Filament\Resources\UtilityReadings;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Facility\Filament\Resources\UtilityReadings\Pages\CreateUtilityReading;
use Modules\Facility\Filament\Resources\UtilityReadings\Pages\EditUtilityReading;
use Modules\Facility\Filament\Resources\UtilityReadings\Pages\ListUtilityReadings;
use Modules\Facility\Filament\Resources\UtilityReadings\Pages\ViewUtilityReading;
use Modules\Facility\Filament\Resources\UtilityReadings\Schemas\UtilityReadingForm;
use Modules\Facility\Filament\Resources\UtilityReadings\Schemas\UtilityReadingInfolist;
use Modules\Facility\Filament\Resources\UtilityReadings\Tables\UtilityReadingsTable;
use Modules\Facility\Models\UtilityReading;

class UtilityReadingResource extends ModuleResource
{
    protected static ?string $model = UtilityReading::class;

    public static function form(Schema $schema): Schema
    {
        return UtilityReadingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UtilityReadingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UtilityReadingsTable::configure($table);
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
            'index' => ListUtilityReadings::route('/'),
            'create' => CreateUtilityReading::route('/create'),
            'view' => ViewUtilityReading::route('/{record}'),
            'edit' => EditUtilityReading::route('/{record}/edit'),
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
