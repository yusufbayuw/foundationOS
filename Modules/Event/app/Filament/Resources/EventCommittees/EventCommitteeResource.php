<?php

namespace Modules\Event\Filament\Resources\EventCommittees;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Event\Filament\Resources\EventCommittees\Pages\CreateEventCommittee;
use Modules\Event\Filament\Resources\EventCommittees\Pages\EditEventCommittee;
use Modules\Event\Filament\Resources\EventCommittees\Pages\ListEventCommittees;
use Modules\Event\Filament\Resources\EventCommittees\Pages\ViewEventCommittee;
use Modules\Event\Filament\Resources\EventCommittees\Schemas\EventCommitteeForm;
use Modules\Event\Filament\Resources\EventCommittees\Schemas\EventCommitteeInfolist;
use Modules\Event\Filament\Resources\EventCommittees\Tables\EventCommitteesTable;
use Modules\Event\Models\EventCommittee;

class EventCommitteeResource extends ModuleResource
{
    protected static ?string $model = EventCommittee::class;

    public static function form(Schema $schema): Schema
    {
        return EventCommitteeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EventCommitteeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventCommitteesTable::configure($table);
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
            'index' => ListEventCommittees::route('/'),
            'create' => CreateEventCommittee::route('/create'),
            'view' => ViewEventCommittee::route('/{record}'),
            'edit' => EditEventCommittee::route('/{record}/edit'),
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
