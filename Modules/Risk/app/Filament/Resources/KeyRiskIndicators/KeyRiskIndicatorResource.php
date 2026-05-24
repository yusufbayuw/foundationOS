<?php

namespace Modules\Risk\Filament\Resources\KeyRiskIndicators;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Risk\Filament\Resources\KeyRiskIndicators\Pages\CreateKeyRiskIndicator;
use Modules\Risk\Filament\Resources\KeyRiskIndicators\Pages\EditKeyRiskIndicator;
use Modules\Risk\Filament\Resources\KeyRiskIndicators\Pages\ListKeyRiskIndicators;
use Modules\Risk\Filament\Resources\KeyRiskIndicators\Pages\ViewKeyRiskIndicator;
use Modules\Risk\Filament\Resources\KeyRiskIndicators\Schemas\KeyRiskIndicatorForm;
use Modules\Risk\Filament\Resources\KeyRiskIndicators\Schemas\KeyRiskIndicatorInfolist;
use Modules\Risk\Filament\Resources\KeyRiskIndicators\Tables\KeyRiskIndicatorsTable;
use Modules\Risk\Models\KeyRiskIndicator;

class KeyRiskIndicatorResource extends ModuleResource
{
    protected static ?string $model = KeyRiskIndicator::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return KeyRiskIndicatorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KeyRiskIndicatorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KeyRiskIndicatorsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKeyRiskIndicators::route('/'),
            'create' => CreateKeyRiskIndicator::route('/create'),
            'view' => ViewKeyRiskIndicator::route('/{record}'),
            'edit' => EditKeyRiskIndicator::route('/{record}/edit'),
        ];
    }
}
