<?php

namespace Modules\Employee\Filament\Resources\Positions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Employee\Filament\Resources\Positions\Pages\CreatePosition;
use Modules\Employee\Filament\Resources\Positions\Pages\EditPosition;
use Modules\Employee\Filament\Resources\Positions\Pages\ListPositions;
use Modules\Employee\Filament\Resources\Positions\Pages\ViewPosition;
use Modules\Employee\Filament\Resources\Positions\Schemas\PositionForm;
use Modules\Employee\Filament\Resources\Positions\Schemas\PositionInfolist;
use Modules\Employee\Filament\Resources\Positions\Tables\PositionsTable;
use Modules\Employee\Models\Position;

class PositionResource extends LocalizedResource
{
    protected static ?string $model = Position::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PositionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PositionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PositionsTable::configure($table);
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
            'index' => ListPositions::route('/'),
            'create' => CreatePosition::route('/create'),
            'view' => ViewPosition::route('/{record}'),
            'edit' => EditPosition::route('/{record}/edit'),
        ];
    }
}
