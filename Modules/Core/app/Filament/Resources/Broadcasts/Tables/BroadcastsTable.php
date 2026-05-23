<?php

namespace Modules\Core\Filament\Resources\Broadcasts\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class BroadcastsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->label(FilamentUi::field('subject'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status')),
                TextColumn::make('sent_at')
                    ->label(FilamentUi::field('sent_at'))
                    ->dateTime(),
            ]);
    }
}
