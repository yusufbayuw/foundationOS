<?php

namespace Modules\School\Filament\Resources\Extracurriculars\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class ExtracurricularsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('advisor.name')
                    ->label(FilamentUi::text('Advisor')),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status')),
            ]);
    }
}
