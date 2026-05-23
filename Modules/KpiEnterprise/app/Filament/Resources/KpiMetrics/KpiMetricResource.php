<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiMetrics;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\KpiEnterprise\Filament\Resources\KpiMetrics\Pages\CreateKpiMetric;
use Modules\KpiEnterprise\Filament\Resources\KpiMetrics\Pages\EditKpiMetric;
use Modules\KpiEnterprise\Filament\Resources\KpiMetrics\Pages\ListKpiMetrics;
use Modules\KpiEnterprise\Filament\Resources\KpiMetrics\Pages\ViewKpiMetric;
use Modules\KpiEnterprise\Filament\Resources\KpiMetrics\Schemas\KpiMetricForm;
use Modules\KpiEnterprise\Filament\Resources\KpiMetrics\Tables\KpiMetricsTable;
use Modules\KpiEnterprise\Models\KpiMetric;

class KpiMetricResource extends ModuleResource
{
    protected static ?string $model = KpiMetric::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return KpiMetricForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KpiMetricsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKpiMetrics::route('/'),
            'create' => CreateKpiMetric::route('/create'),
            'view' => ViewKpiMetric::route('/{record}'),
            'edit' => EditKpiMetric::route('/{record}/edit'),
        ];
    }
}
