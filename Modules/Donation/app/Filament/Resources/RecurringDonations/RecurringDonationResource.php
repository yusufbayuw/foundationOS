<?php

namespace Modules\Donation\Filament\Resources\RecurringDonations;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Donation\Filament\Resources\RecurringDonations\Pages\CreateRecurringDonation;
use Modules\Donation\Filament\Resources\RecurringDonations\Pages\EditRecurringDonation;
use Modules\Donation\Filament\Resources\RecurringDonations\Pages\ListRecurringDonations;
use Modules\Donation\Filament\Resources\RecurringDonations\Pages\ViewRecurringDonation;
use Modules\Donation\Filament\Resources\RecurringDonations\Schemas\RecurringDonationForm;
use Modules\Donation\Filament\Resources\RecurringDonations\Schemas\RecurringDonationInfolist;
use Modules\Donation\Filament\Resources\RecurringDonations\Tables\RecurringDonationsTable;
use Modules\Donation\Models\RecurringDonation;

class RecurringDonationResource extends ModuleResource
{
    protected static ?string $model = RecurringDonation::class;

    public static function form(Schema $schema): Schema
    {
        return RecurringDonationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RecurringDonationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RecurringDonationsTable::configure($table);
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
            'index' => ListRecurringDonations::route('/'),
            'create' => CreateRecurringDonation::route('/create'),
            'view' => ViewRecurringDonation::route('/{record}'),
            'edit' => EditRecurringDonation::route('/{record}/edit'),
        ];
    }
}
