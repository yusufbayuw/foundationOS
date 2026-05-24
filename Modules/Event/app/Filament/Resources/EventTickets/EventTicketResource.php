<?php

namespace Modules\Event\Filament\Resources\EventTickets;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Event\Filament\Resources\EventTickets\Pages\CreateEventTicket;
use Modules\Event\Filament\Resources\EventTickets\Pages\EditEventTicket;
use Modules\Event\Filament\Resources\EventTickets\Pages\ListEventTickets;
use Modules\Event\Filament\Resources\EventTickets\Pages\ViewEventTicket;
use Modules\Event\Filament\Resources\EventTickets\Schemas\EventTicketForm;
use Modules\Event\Filament\Resources\EventTickets\Schemas\EventTicketInfolist;
use Modules\Event\Filament\Resources\EventTickets\Tables\EventTicketsTable;
use Modules\Event\Models\EventTicket;

class EventTicketResource extends ModuleResource
{
    protected static ?string $model = EventTicket::class;

    public static function form(Schema $schema): Schema
    {
        return EventTicketForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EventTicketInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventTicketsTable::configure($table);
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
            'index' => ListEventTickets::route('/'),
            'create' => CreateEventTicket::route('/create'),
            'view' => ViewEventTicket::route('/{record}'),
            'edit' => EditEventTicket::route('/{record}/edit'),
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
