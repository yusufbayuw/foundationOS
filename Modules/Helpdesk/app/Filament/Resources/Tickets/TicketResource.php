<?php

namespace Modules\Helpdesk\Filament\Resources\Tickets;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Helpdesk\Filament\Resources\Tickets\Pages\CreateTicket;
use Modules\Helpdesk\Filament\Resources\Tickets\Pages\EditTicket;
use Modules\Helpdesk\Filament\Resources\Tickets\Pages\ListTickets;
use Modules\Helpdesk\Filament\Resources\Tickets\Pages\ViewTicket;
use Modules\Helpdesk\Filament\Resources\Tickets\Schemas\TicketForm;
use Modules\Helpdesk\Filament\Resources\Tickets\Tables\TicketsTable;
use Modules\Helpdesk\Models\Ticket;

class TicketResource extends ModuleResource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TicketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'view' => ViewTicket::route('/{record}'),
            'edit' => EditTicket::route('/{record}/edit'),
        ];
    }
}
