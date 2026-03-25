<?php

namespace Modules\School\Filament\Resources\StudentGrades\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StudentGradeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('student.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Student')),
                TextEntry::make('assessment.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Assessment')),
                TextEntry::make('graded_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('graded_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('score')
                    ->label(\Modules\Core\Support\FilamentUi::field('score'))
                    ->numeric(),
                TextEntry::make('score_letter')
                    ->label(\Modules\Core\Support\FilamentUi::field('score_letter'))
                    ->placeholder('-'),
                TextEntry::make('weight')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight'))
                    ->numeric(),
                TextEntry::make('final_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('final_score'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_passed')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('graded_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('graded_at'))
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('is_locked')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
