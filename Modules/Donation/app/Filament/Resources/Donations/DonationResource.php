<?php

namespace Modules\Donation\Filament\Resources\Donations;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Donation\Filament\Resources\Donations\Pages\CreateDonation;
use Modules\Donation\Filament\Resources\Donations\Pages\EditDonation;
use Modules\Donation\Filament\Resources\Donations\Pages\ListDonations;
use Modules\Donation\Filament\Resources\Donations\Pages\ViewDonation;
use Modules\Donation\Filament\Resources\Donations\Schemas\DonationForm;
use Modules\Donation\Filament\Resources\Donations\Schemas\DonationInfolist;
use Modules\Donation\Filament\Resources\Donations\Tables\DonationsTable;
use Modules\Donation\Models\Donation;

class DonationResource extends ModuleResource
{
    protected static ?string $model = Donation::class;

    public static function form(Schema $schema): Schema
    {
        return DonationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DonationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonationsTable::configure($table);
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
            'index' => ListDonations::route('/'),
            'create' => CreateDonation::route('/create'),
            'view' => ViewDonation::route('/{record}'),
            'edit' => EditDonation::route('/{record}/edit'),
        ];
    }
}
