<?php

namespace Modules\School\Filament\Resources\Students\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;
use Modules\School\Models\Student;

class StudentProfile360Infolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make(FilamentUi::text('Student profile 360'))
                    ->tabs([
                        Tab::make(FilamentUi::text('Academic'))
                            ->schema([
                                TextEntry::make('studentGrades_count')
                                    ->label(FilamentUi::text('Grade records'))
                                    ->state(fn (Student $record): int => $record->studentGrades()->count()),
                                TextEntry::make('attendances_count')
                                    ->label(FilamentUi::text('Attendance records'))
                                    ->state(fn (Student $record): int => $record->attendances()->count()),
                            ]),
                        Tab::make(FilamentUi::text('Finance'))
                            ->schema([
                                TextEntry::make('invoices_count')
                                    ->label(FilamentUi::text('Invoices'))
                                    ->state(fn (Student $record): int => $record->studentInvoices()->count()),
                            ]),
                        Tab::make(FilamentUi::text('Achievements & violations'))
                            ->schema([
                                TextEntry::make('achievements_count')
                                    ->label(FilamentUi::text('Achievements'))
                                    ->state(fn (Student $record): int => $record->studentAchievements()->count()),
                                TextEntry::make('violations_count')
                                    ->label(FilamentUi::text('Violations'))
                                    ->state(fn (Student $record): int => $record->violations()->count()),
                            ]),
                        Tab::make(FilamentUi::text('Risk score'))
                            ->schema([
                                TextEntry::make('riskScore.composite_score')
                                    ->label(FilamentUi::text('Composite score'))
                                    ->placeholder('-'),
                                TextEntry::make('riskScore.is_at_risk')
                                    ->label(FilamentUi::field('is_at_risk'))
                                    ->formatStateUsing(fn (mixed $state): string => $state ? FilamentUi::text('Yes') : FilamentUi::text('No')),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
