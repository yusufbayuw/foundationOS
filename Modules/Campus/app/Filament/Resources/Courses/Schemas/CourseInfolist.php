<?php

namespace Modules\Campus\Filament\Resources\Courses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('studyProgram.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Study program'))
                    ->placeholder('-'),
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('credits')
                    ->label(\Modules\Core\Support\FilamentUi::field('credits'))
                    ->numeric(),
                TextEntry::make('theory_credits')
                    ->label(\Modules\Core\Support\FilamentUi::field('theory_credits'))
                    ->numeric(),
                TextEntry::make('practicum_credits')
                    ->label(\Modules\Core\Support\FilamentUi::field('practicum_credits'))
                    ->numeric(),
                TextEntry::make('semester_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('semester_level'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('course_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('course_type')),
                IconEntry::make('is_mandatory')
                    ->boolean(),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_active')
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
