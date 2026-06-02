<?php

namespace Modules\Cms\Filament\Resources\Sites\Tables;

use App\Filament\Imports\CmsSiteImporter;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class SitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('domain')
                    ->label(FilamentUi::field('domain'))
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label(FilamentUi::field('is_active'))
                    ->boolean(),
            ])
            ->defaultSort('name')
            ->toolbarActions([
                ...ImportTableActions::make(CmsSiteImporter::class),
            ]);
    }
}
