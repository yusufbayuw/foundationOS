<?php

namespace Modules\Capacity\Filament\Resources\CapacityUtilizations;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Capacity\Filament\Resources\CapacityUtilizations\Pages\CreateCapacityUtilization;
use Modules\Capacity\Filament\Resources\CapacityUtilizations\Pages\EditCapacityUtilization;
use Modules\Capacity\Filament\Resources\CapacityUtilizations\Pages\ListCapacityUtilizations;
use Modules\Capacity\Filament\Resources\CapacityUtilizations\Pages\ViewCapacityUtilization;
use Modules\Capacity\Filament\Resources\CapacityUtilizations\Schemas\CapacityUtilizationForm;
use Modules\Capacity\Filament\Resources\CapacityUtilizations\Schemas\CapacityUtilizationInfolist;
use Modules\Capacity\Filament\Resources\CapacityUtilizations\Tables\CapacityUtilizationsTable;
use Modules\Capacity\Models\CapacityUtilization;
use Modules\Core\Filament\Support\ModuleResource;

class CapacityUtilizationResource extends ModuleResource
{
    protected static ?string $model = CapacityUtilization::class;

    public static function form(Schema $schema): Schema
    {
        return CapacityUtilizationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CapacityUtilizationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CapacityUtilizationsTable::configure($table);
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
            'index' => ListCapacityUtilizations::route('/'),
            'create' => CreateCapacityUtilization::route('/create'),
            'view' => ViewCapacityUtilization::route('/{record}'),
            'edit' => EditCapacityUtilization::route('/{record}/edit'),
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
