<?php

namespace Modules\Donation\Filament\Resources\Donors;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Donation\Filament\Resources\Donors\Pages\CreateDonor;
use Modules\Donation\Filament\Resources\Donors\Pages\EditDonor;
use Modules\Donation\Filament\Resources\Donors\Pages\ListDonors;
use Modules\Donation\Filament\Resources\Donors\Pages\ViewDonor;
use Modules\Donation\Filament\Resources\Donors\Schemas\DonorForm;
use Modules\Donation\Filament\Resources\Donors\Schemas\DonorInfolist;
use Modules\Donation\Filament\Resources\Donors\Tables\DonorsTable;
use Modules\Donation\Models\Donor;

class DonorResource extends ModuleResource
{
    protected static ?string $model = Donor::class;

    public static function form(Schema $schema): Schema
    {
        return DonorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DonorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonorsTable::configure($table);
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
            'index' => ListDonors::route('/'),
            'create' => CreateDonor::route('/create'),
            'view' => ViewDonor::route('/{record}'),
            'edit' => EditDonor::route('/{record}/edit'),
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
