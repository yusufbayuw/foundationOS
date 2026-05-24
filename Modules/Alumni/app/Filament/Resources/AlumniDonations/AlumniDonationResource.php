<?php

namespace Modules\Alumni\Filament\Resources\AlumniDonations;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Alumni\Filament\Resources\AlumniDonations\Pages\CreateAlumniDonation;
use Modules\Alumni\Filament\Resources\AlumniDonations\Pages\EditAlumniDonation;
use Modules\Alumni\Filament\Resources\AlumniDonations\Pages\ListAlumniDonations;
use Modules\Alumni\Filament\Resources\AlumniDonations\Pages\ViewAlumniDonation;
use Modules\Alumni\Filament\Resources\AlumniDonations\Schemas\AlumniDonationForm;
use Modules\Alumni\Filament\Resources\AlumniDonations\Schemas\AlumniDonationInfolist;
use Modules\Alumni\Filament\Resources\AlumniDonations\Tables\AlumniDonationsTable;
use Modules\Alumni\Models\AlumniDonation;
use Modules\Core\Filament\Support\ModuleResource;

class AlumniDonationResource extends ModuleResource
{
    protected static ?string $model = AlumniDonation::class;

    public static function form(Schema $schema): Schema
    {
        return AlumniDonationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AlumniDonationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AlumniDonationsTable::configure($table);
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
            'index' => ListAlumniDonations::route('/'),
            'create' => CreateAlumniDonation::route('/create'),
            'view' => ViewAlumniDonation::route('/{record}'),
            'edit' => EditAlumniDonation::route('/{record}/edit'),
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
