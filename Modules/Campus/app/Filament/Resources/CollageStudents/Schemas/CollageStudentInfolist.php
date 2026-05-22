<?php

namespace Modules\Campus\Filament\Resources\CollageStudents\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class CollageStudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Relationships'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization'))
                            ->placeholder('-'),
                        TextEntry::make('user.name')
                            ->label(FilamentUi::text('User'))
                            ->placeholder('-'),
                        TextEntry::make('studyProgram.name')
                            ->label(FilamentUi::text('Study program'))
                            ->placeholder('-'),
                        TextEntry::make('academicAdvisor.id')
                            ->label(FilamentUi::text('Academic advisor'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Student Identity'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('student_number')
                            ->label(FilamentUi::field('student_number')),
                        TextEntry::make('national_student_number')
                            ->label(FilamentUi::field('national_student_number'))
                            ->placeholder('-'),
                        TextEntry::make('full_name')
                            ->label(FilamentUi::field('full_name'))
                            ->placeholder('-'),
                        TextEntry::make('admission_type')
                            ->label(FilamentUi::field('admission_type'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Enrollment'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('entry_year')
                            ->label(FilamentUi::field('entry_year'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('entry_semester')
                            ->label(FilamentUi::field('entry_semester'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('current_semester')
                            ->label(FilamentUi::field('current_semester'))
                            ->numeric(),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('graduation_date')
                            ->label(FilamentUi::field('graduation_date'))
                            ->date()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Contact'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('email')
                            ->label(FilamentUi::text('Email address'))
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->label(FilamentUi::field('phone'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Timestamps'))
                    ->columns(2)
                    ->schema([
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
