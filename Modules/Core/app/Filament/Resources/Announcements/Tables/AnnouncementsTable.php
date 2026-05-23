<?php

namespace Modules\Core\Filament\Resources\Announcements\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class AnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(FilamentUi::field('title'))
                    ->searchable(),
                TextColumn::make('audience')
                    ->label(FilamentUi::field('audience')),
                TextColumn::make('published_at')
                    ->label(FilamentUi::field('published_at'))
                    ->dateTime(),
            ]);
    }
}
