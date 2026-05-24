<?php

namespace Modules\Event\Filament\Resources\EventProposals;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Event\Filament\Resources\EventProposals\Pages\CreateEventProposal;
use Modules\Event\Filament\Resources\EventProposals\Pages\EditEventProposal;
use Modules\Event\Filament\Resources\EventProposals\Pages\ListEventProposals;
use Modules\Event\Filament\Resources\EventProposals\Pages\ViewEventProposal;
use Modules\Event\Filament\Resources\EventProposals\Schemas\EventProposalForm;
use Modules\Event\Filament\Resources\EventProposals\Schemas\EventProposalInfolist;
use Modules\Event\Filament\Resources\EventProposals\Tables\EventProposalsTable;
use Modules\Event\Models\EventProposal;

class EventProposalResource extends ModuleResource
{
    protected static ?string $model = EventProposal::class;

    public static function form(Schema $schema): Schema
    {
        return EventProposalForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EventProposalInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventProposalsTable::configure($table);
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
            'index' => ListEventProposals::route('/'),
            'create' => CreateEventProposal::route('/create'),
            'view' => ViewEventProposal::route('/{record}'),
            'edit' => EditEventProposal::route('/{record}/edit'),
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
