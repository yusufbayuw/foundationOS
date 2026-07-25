<?php

namespace Modules\MerchOrder\Filament\Resources\MerchOrders\Tables;

use Filament\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;
use Modules\MerchOrder\Filament\Exports\MerchOrderExporter;
use Modules\MerchOrder\Models\MerchOrder;

class MerchOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label(FilamentUi::field('code'))->searchable(),
                TextColumn::make('name')->label(FilamentUi::field('name'))->searchable()->sortable(),
                TextColumn::make('status')->label(FilamentUi::field('status'))->badge(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(MerchOrderExporter::class)
                    ->visible(fn (): bool => auth()->user()?->can('export', MerchOrder::class) ?? false)
                    ->authorize(fn (): bool => auth()->user()?->can('export', MerchOrder::class) ?? false),
            ])
            ->defaultSort('name');
    }
}
