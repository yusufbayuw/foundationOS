<?php

namespace Modules\Campus\Filament\Resources\CollageStudents\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CollageStudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Relationships')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Organization'))
                            ->placeholder('-'),
                        TextEntry::make('user.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('User'))
                            ->placeholder('-'),
                        TextEntry::make('studyProgram.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Study program'))
                            ->placeholder('-'),
                        TextEntry::make('academicAdvisor.id')
                            ->label(\Modules\Core\Support\FilamentUi::text('Academic advisor'))
                            ->placeholder('-'),
                    ]),

                Section::make('Student Identity')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('student_number')
                            ->label(\Modules\Core\Support\FilamentUi::field('student_number')),
                        TextEntry::make('national_student_number')
                            ->label(\Modules\Core\Support\FilamentUi::field('national_student_number'))
                            ->placeholder('-'),
                        TextEntry::make('full_name')
                            ->label(\Modules\Core\Support\FilamentUi::field('full_name'))
                            ->placeholder('-'),
                        TextEntry::make('admission_type')
                            ->label(\Modules\Core\Support\FilamentUi::field('admission_type'))
                            ->placeholder('-'),
                    ]),

                Section::make('Enrollment')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('entry_year')
                            ->label(\Modules\Core\Support\FilamentUi::field('entry_year'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('entry_semester')
                            ->label(\Modules\Core\Support\FilamentUi::field('entry_semester'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('current_semester')
                            ->label(\Modules\Core\Support\FilamentUi::field('current_semester'))
                            ->numeric(),
                        TextEntry::make('status')
                            ->label(\Modules\Core\Support\FilamentUi::field('status')),
                        TextEntry::make('graduation_date')
                            ->label(\Modules\Core\Support\FilamentUi::field('graduation_date'))
                            ->date()
                            ->placeholder('-'),
                    ]),

                Section::make('Contact')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('email')
                            ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                            ->placeholder('-'),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
