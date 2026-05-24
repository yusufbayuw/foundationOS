<?php

namespace Modules\Core\Filament\Resources\PollResponses;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\PollResponses\Pages\CreatePollResponse;
use Modules\Core\Filament\Resources\PollResponses\Pages\EditPollResponse;
use Modules\Core\Filament\Resources\PollResponses\Pages\ListPollResponses;
use Modules\Core\Filament\Resources\PollResponses\Pages\ViewPollResponse;
use Modules\Core\Filament\Resources\PollResponses\Schemas\PollResponseForm;
use Modules\Core\Filament\Resources\PollResponses\Schemas\PollResponseInfolist;
use Modules\Core\Filament\Resources\PollResponses\Tables\PollResponsesTable;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Core\Models\PollResponse;

class PollResponseResource extends ModuleResource
{
    protected static ?string $model = PollResponse::class;

    public static function form(Schema $schema): Schema
    {
        return PollResponseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PollResponseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PollResponsesTable::configure($table);
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
            'index' => ListPollResponses::route('/'),
            'create' => CreatePollResponse::route('/create'),
            'view' => ViewPollResponse::route('/{record}'),
            'edit' => EditPollResponse::route('/{record}/edit'),
        ];
    }
}
