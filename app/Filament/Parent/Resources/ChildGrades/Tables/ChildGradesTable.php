<?php

namespace App\Filament\Parent\Resources\ChildGrades\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class ChildGradesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.user.name')
                    ->label(FilamentUi::text('Child')),
                TextColumn::make('assessment.name')
                    ->label(FilamentUi::text('Assessment')),
                TextColumn::make('score')
                    ->label(FilamentUi::field('score')),
            ]);
    }
}
