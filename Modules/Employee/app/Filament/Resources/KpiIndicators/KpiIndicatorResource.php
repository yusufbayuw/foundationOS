<?php

namespace Modules\Employee\Filament\Resources\KpiIndicators;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Employee\Filament\Resources\KpiIndicators\Pages\CreateKpiIndicator;
use Modules\Employee\Filament\Resources\KpiIndicators\Pages\EditKpiIndicator;
use Modules\Employee\Filament\Resources\KpiIndicators\Pages\ListKpiIndicators;
use Modules\Employee\Filament\Resources\KpiIndicators\Pages\ViewKpiIndicator;
use Modules\Employee\Filament\Resources\KpiIndicators\Schemas\KpiIndicatorForm;
use Modules\Employee\Filament\Resources\KpiIndicators\Schemas\KpiIndicatorInfolist;
use Modules\Employee\Filament\Resources\KpiIndicators\Tables\KpiIndicatorsTable;
use Modules\Employee\Models\KpiIndicator;

class KpiIndicatorResource extends LocalizedResource
{
    protected static ?string $model = KpiIndicator::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return KpiIndicatorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KpiIndicatorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KpiIndicatorsTable::configure($table);
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
            'index' => ListKpiIndicators::route('/'),
            'create' => CreateKpiIndicator::route('/create'),
            'view' => ViewKpiIndicator::route('/{record}'),
            'edit' => EditKpiIndicator::route('/{record}/edit'),
        ];
    }
}
