<?php

namespace Modules\Event\Filament\Resources\Events;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Event\Filament\Resources\Events\Pages\CreateEvent;
use Modules\Event\Filament\Resources\Events\Pages\EditEvent;
use Modules\Event\Filament\Resources\Events\Pages\ListEvents;
use Modules\Event\Filament\Resources\Events\Pages\ViewEvent;
use Modules\Event\Filament\Resources\Events\Schemas\EventForm;
use Modules\Event\Filament\Resources\Events\Tables\EventsTable;
use Modules\Event\Models\Event;

class EventResource extends ModuleResource
{
    protected static ?string $model = Event::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return EventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'view' => ViewEvent::route('/{record}'),
            'edit' => EditEvent::route('/{record}/edit'),
        ];
    }
}
