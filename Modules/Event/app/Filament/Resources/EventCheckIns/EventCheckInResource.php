<?php

namespace Modules\Event\Filament\Resources\EventCheckIns;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Event\Filament\Resources\EventCheckIns\Pages\CreateEventCheckIn;
use Modules\Event\Filament\Resources\EventCheckIns\Pages\EditEventCheckIn;
use Modules\Event\Filament\Resources\EventCheckIns\Pages\ListEventCheckIns;
use Modules\Event\Filament\Resources\EventCheckIns\Pages\ViewEventCheckIn;
use Modules\Event\Filament\Resources\EventCheckIns\Schemas\EventCheckInForm;
use Modules\Event\Filament\Resources\EventCheckIns\Schemas\EventCheckInInfolist;
use Modules\Event\Filament\Resources\EventCheckIns\Tables\EventCheckInsTable;
use Modules\Event\Models\EventCheckIn;

class EventCheckInResource extends ModuleResource
{
    protected static ?string $model = EventCheckIn::class;

    public static function form(Schema $schema): Schema
    {
        return EventCheckInForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EventCheckInInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventCheckInsTable::configure($table);
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
            'index' => ListEventCheckIns::route('/'),
            'create' => CreateEventCheckIn::route('/create'),
            'view' => ViewEventCheckIn::route('/{record}'),
            'edit' => EditEventCheckIn::route('/{record}/edit'),
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
