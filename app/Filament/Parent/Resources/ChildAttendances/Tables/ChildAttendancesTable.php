<?php

namespace App\Filament\Parent\Resources\ChildAttendances\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class ChildAttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.user.name')
                    ->label(FilamentUi::text('Child')),
                TextColumn::make('attendance_date')
                    ->label(FilamentUi::field('attendance_date'))
                    ->date(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status')),
            ])
            ->defaultSort('date', 'desc');
    }
}
