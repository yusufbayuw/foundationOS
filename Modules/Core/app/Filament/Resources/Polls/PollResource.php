<?php

namespace Modules\Core\Filament\Resources\Polls;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\Polls\Pages\CreatePoll;
use Modules\Core\Filament\Resources\Polls\Pages\EditPoll;
use Modules\Core\Filament\Resources\Polls\Pages\ListPolls;
use Modules\Core\Filament\Resources\Polls\Pages\ViewPoll;
use Modules\Core\Filament\Resources\Polls\Schemas\PollForm;
use Modules\Core\Filament\Resources\Polls\Schemas\PollInfolist;
use Modules\Core\Filament\Resources\Polls\Tables\PollsTable;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Core\Models\Poll;

class PollResource extends ModuleResource
{
    protected static ?string $model = Poll::class;

    public static function form(Schema $schema): Schema
    {
        return PollForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PollInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PollsTable::configure($table);
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
            'index' => ListPolls::route('/'),
            'create' => CreatePoll::route('/create'),
            'view' => ViewPoll::route('/{record}'),
            'edit' => EditPoll::route('/{record}/edit'),
        ];
    }
}
