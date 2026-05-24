<?php

namespace Modules\Event\Filament\Resources\EventSponsors;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Event\Filament\Resources\EventSponsors\Pages\CreateEventSponsor;
use Modules\Event\Filament\Resources\EventSponsors\Pages\EditEventSponsor;
use Modules\Event\Filament\Resources\EventSponsors\Pages\ListEventSponsors;
use Modules\Event\Filament\Resources\EventSponsors\Pages\ViewEventSponsor;
use Modules\Event\Filament\Resources\EventSponsors\Schemas\EventSponsorForm;
use Modules\Event\Filament\Resources\EventSponsors\Schemas\EventSponsorInfolist;
use Modules\Event\Filament\Resources\EventSponsors\Tables\EventSponsorsTable;
use Modules\Event\Models\EventSponsor;

class EventSponsorResource extends ModuleResource
{
    protected static ?string $model = EventSponsor::class;

    public static function form(Schema $schema): Schema
    {
        return EventSponsorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EventSponsorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventSponsorsTable::configure($table);
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
            'index' => ListEventSponsors::route('/'),
            'create' => CreateEventSponsor::route('/create'),
            'view' => ViewEventSponsor::route('/{record}'),
            'edit' => EditEventSponsor::route('/{record}/edit'),
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
