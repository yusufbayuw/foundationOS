<?php

namespace Modules\Alumni\Filament\Resources\Alumni\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class AlumniTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label(FilamentUi::field('code'))->searchable(),
                TextColumn::make('name')->label(FilamentUi::field('name'))->searchable()->sortable(),
                TextColumn::make('status')->label(FilamentUi::field('status'))->badge(),
            ])
            ->defaultSort('name');
    }
}
