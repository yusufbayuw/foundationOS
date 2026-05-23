<?php

namespace App\Filament\Parent\Resources\ChildAnnouncements\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class ChildAnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(FilamentUi::field('title')),
                TextColumn::make('published_at')
                    ->label(FilamentUi::field('published_at'))
                    ->dateTime(),
            ])
            ->defaultSort('published_at', 'desc');
    }
}
