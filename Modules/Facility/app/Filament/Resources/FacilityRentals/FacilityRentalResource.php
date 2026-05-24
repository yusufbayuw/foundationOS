<?php

namespace Modules\Facility\Filament\Resources\FacilityRentals;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Facility\Filament\Resources\FacilityRentals\Pages\CreateFacilityRental;
use Modules\Facility\Filament\Resources\FacilityRentals\Pages\EditFacilityRental;
use Modules\Facility\Filament\Resources\FacilityRentals\Pages\ListFacilityRentals;
use Modules\Facility\Filament\Resources\FacilityRentals\Pages\ViewFacilityRental;
use Modules\Facility\Filament\Resources\FacilityRentals\Schemas\FacilityRentalForm;
use Modules\Facility\Filament\Resources\FacilityRentals\Schemas\FacilityRentalInfolist;
use Modules\Facility\Filament\Resources\FacilityRentals\Tables\FacilityRentalsTable;
use Modules\Facility\Models\FacilityRental;

class FacilityRentalResource extends ModuleResource
{
    protected static ?string $model = FacilityRental::class;

    public static function form(Schema $schema): Schema
    {
        return FacilityRentalForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FacilityRentalInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FacilityRentalsTable::configure($table);
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
            'index' => ListFacilityRentals::route('/'),
            'create' => CreateFacilityRental::route('/create'),
            'view' => ViewFacilityRental::route('/{record}'),
            'edit' => EditFacilityRental::route('/{record}/edit'),
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
