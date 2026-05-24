<?php

namespace Modules\Helpdesk\Filament\Resources\TicketSatisfactions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Helpdesk\Filament\Resources\TicketSatisfactions\Pages\CreateTicketSatisfaction;
use Modules\Helpdesk\Filament\Resources\TicketSatisfactions\Pages\EditTicketSatisfaction;
use Modules\Helpdesk\Filament\Resources\TicketSatisfactions\Pages\ListTicketSatisfactions;
use Modules\Helpdesk\Filament\Resources\TicketSatisfactions\Pages\ViewTicketSatisfaction;
use Modules\Helpdesk\Filament\Resources\TicketSatisfactions\Schemas\TicketSatisfactionForm;
use Modules\Helpdesk\Filament\Resources\TicketSatisfactions\Schemas\TicketSatisfactionInfolist;
use Modules\Helpdesk\Filament\Resources\TicketSatisfactions\Tables\TicketSatisfactionsTable;
use Modules\Helpdesk\Models\TicketSatisfaction;

class TicketSatisfactionResource extends ModuleResource
{
    protected static ?string $model = TicketSatisfaction::class;

    public static function form(Schema $schema): Schema
    {
        return TicketSatisfactionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TicketSatisfactionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketSatisfactionsTable::configure($table);
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
            'index' => ListTicketSatisfactions::route('/'),
            'create' => CreateTicketSatisfaction::route('/create'),
            'view' => ViewTicketSatisfaction::route('/{record}'),
            'edit' => EditTicketSatisfaction::route('/{record}/edit'),
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
