<?php

namespace Modules\Event\Filament\Resources\EventParticipants;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Event\Filament\Resources\EventParticipants\Pages\CreateEventParticipant;
use Modules\Event\Filament\Resources\EventParticipants\Pages\EditEventParticipant;
use Modules\Event\Filament\Resources\EventParticipants\Pages\ListEventParticipants;
use Modules\Event\Filament\Resources\EventParticipants\Pages\ViewEventParticipant;
use Modules\Event\Filament\Resources\EventParticipants\Schemas\EventParticipantForm;
use Modules\Event\Filament\Resources\EventParticipants\Schemas\EventParticipantInfolist;
use Modules\Event\Filament\Resources\EventParticipants\Tables\EventParticipantsTable;
use Modules\Event\Models\EventParticipant;

class EventParticipantResource extends ModuleResource
{
    protected static ?string $model = EventParticipant::class;

    public static function form(Schema $schema): Schema
    {
        return EventParticipantForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EventParticipantInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventParticipantsTable::configure($table);
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
            'index' => ListEventParticipants::route('/'),
            'create' => CreateEventParticipant::route('/create'),
            'view' => ViewEventParticipant::route('/{record}'),
            'edit' => EditEventParticipant::route('/{record}/edit'),
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
