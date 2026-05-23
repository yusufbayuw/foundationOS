<?php

namespace Modules\Core\Filament\Resources\Broadcasts;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\Broadcasts\Pages\CreateBroadcast;
use Modules\Core\Filament\Resources\Broadcasts\Pages\EditBroadcast;
use Modules\Core\Filament\Resources\Broadcasts\Pages\ListBroadcasts;
use Modules\Core\Filament\Resources\Broadcasts\Schemas\BroadcastForm;
use Modules\Core\Filament\Resources\Broadcasts\Tables\BroadcastsTable;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Core\Models\Broadcast;

class BroadcastResource extends ModuleResource
{
    protected static ?string $model = Broadcast::class;

    protected static ?string $recordTitleAttribute = 'subject';

    public static function form(Schema $schema): Schema
    {
        return BroadcastForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BroadcastsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBroadcasts::route('/'),
            'create' => CreateBroadcast::route('/create'),
            'edit' => EditBroadcast::route('/{record}/edit'),
        ];
    }
}
