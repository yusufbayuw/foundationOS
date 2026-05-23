<?php

namespace Modules\IsoCompliance\Filament\Resources\IsoControls;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\IsoCompliance\Filament\Resources\IsoControls\Pages\CreateIsoControl;
use Modules\IsoCompliance\Filament\Resources\IsoControls\Pages\EditIsoControl;
use Modules\IsoCompliance\Filament\Resources\IsoControls\Pages\ListIsoControls;
use Modules\IsoCompliance\Filament\Resources\IsoControls\Pages\ViewIsoControl;
use Modules\IsoCompliance\Filament\Resources\IsoControls\Schemas\IsoControlForm;
use Modules\IsoCompliance\Filament\Resources\IsoControls\Tables\IsoControlsTable;
use Modules\IsoCompliance\Models\IsoControl;

class IsoControlResource extends ModuleResource
{
    protected static ?string $model = IsoControl::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return IsoControlForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IsoControlsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIsoControls::route('/'),
            'create' => CreateIsoControl::route('/create'),
            'view' => ViewIsoControl::route('/{record}'),
            'edit' => EditIsoControl::route('/{record}/edit'),
        ];
    }
}
