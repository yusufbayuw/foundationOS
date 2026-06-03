<?php

namespace Modules\Monitoring\Filament\Resources\MoodleSyncOutboxes;

use App\Models\MoodleSyncOutbox;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Monitoring\Filament\Resources\MoodleSyncOutboxes\Pages\ListMoodleSyncOutboxes;
use Modules\Monitoring\Filament\Resources\MoodleSyncOutboxes\Pages\ViewMoodleSyncOutbox;
use Modules\Monitoring\Filament\Resources\MoodleSyncOutboxes\Schemas\MoodleSyncOutboxInfolist;
use Modules\Monitoring\Filament\Resources\MoodleSyncOutboxes\Tables\MoodleSyncOutboxesTable;

class MoodleSyncOutboxResource extends ModuleResource
{
    protected static ?string $model = MoodleSyncOutbox::class;

    protected static ?string $recordTitleAttribute = 'dedupe_key';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return MoodleSyncOutboxInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MoodleSyncOutboxesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMoodleSyncOutboxes::route('/'),
            'view' => ViewMoodleSyncOutbox::route('/{record}'),
        ];
    }
}
