<?php

namespace App\Filament\Parent\Resources\MyChildren\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class MyChildrenTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nis')->label(FilamentUi::field('nis')),
                TextColumn::make('user.name')->label(FilamentUi::field('name'))->searchable(),
                TextColumn::make('schoolClass.name')->label(FilamentUi::field('school_class_id')),
                TextColumn::make('status')->label(FilamentUi::field('status'))->badge(),
            ]);
    }
}
