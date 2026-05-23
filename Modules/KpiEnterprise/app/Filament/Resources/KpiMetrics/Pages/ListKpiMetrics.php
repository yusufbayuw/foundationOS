<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiMetrics\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\KpiEnterprise\Filament\Resources\KpiMetrics\KpiMetricResource;

class ListKpiMetrics extends ListRecords
{
    protected static string $resource = KpiMetricResource::class;
}
