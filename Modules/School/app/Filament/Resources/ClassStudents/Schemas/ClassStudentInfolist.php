<?php

namespace Modules\School\Filament\Resources\ClassStudents\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ClassStudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Enrollment Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('academicPeriod.name')
                            ->label(FilamentUi::text('Academic period'))
                            ->placeholder('-'),
                        TextEntry::make('class_id')
                            ->label(FilamentUi::field('class_id'))
                            ->numeric(),
                        TextEntry::make('student.id')
                            ->label(FilamentUi::text('Student')),
                    ]),

                Section::make(FilamentUi::text('Status & Period'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('entry_date')
                            ->label(FilamentUi::field('entry_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('exit_date')
                            ->label(FilamentUi::field('exit_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status'))
                            ->placeholder('-'),
                        TextEntry::make('entry_type')
                            ->label(FilamentUi::field('entry_type'))
                            ->placeholder('-'),
                        TextEntry::make('exit_reason')
                            ->label(FilamentUi::field('exit_reason'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Achievement'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('ranking')
                            ->label(FilamentUi::field('ranking'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('certificate_number')
                            ->label(FilamentUi::field('certificate_number'))
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
