<?php

namespace Modules\Enrollment\Filament\Resources\ExamResults\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ExamResultInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('applicant.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Applicant')),
                TextEntry::make('examiner.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Examiner'))
                    ->placeholder('-'),
                TextEntry::make('seat_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('seat_number'))
                    ->placeholder('-'),
                TextEntry::make('score')
                    ->label(\Modules\Core\Support\FilamentUi::field('score'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('score_components')
                    ->label(\Modules\Core\Support\FilamentUi::field('score_components'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('grade')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade'))
                    ->placeholder('-'),
                IconEntry::make('is_passed')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('examSchedule.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Exam schedule'))
                    ->placeholder('-'),
            ]);
    }
}
