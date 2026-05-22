<?php

namespace Modules\School\Filament\Resources\ClassStudents\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class ClassStudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Enrollment Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('academic_period_id')
                            ->label(FilamentUi::field('academic_period_id'))
                            ->relationship('academicPeriod', 'name'),
                        TextInput::make('class_id')
                            ->label(FilamentUi::field('class_id'))
                            ->required()
                            ->numeric(),
                        Select::make('student_id')
                            ->label(FilamentUi::field('student_id'))
                            ->relationship('student', 'id')
                            ->required(),
                    ]),

                Section::make(FilamentUi::text('Status & Period'))
                    ->columns(2)
                    ->schema([
                        DatePicker::make('entry_date')
                            ->label(FilamentUi::field('entry_date')),
                        DatePicker::make('exit_date')
                            ->label(FilamentUi::field('exit_date')),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status')),
                        TextInput::make('entry_type')
                            ->label(FilamentUi::field('entry_type')),
                        Textarea::make('exit_reason')
                            ->label(FilamentUi::field('exit_reason'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Achievement'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('ranking')
                            ->label(FilamentUi::field('ranking'))
                            ->numeric(),
                        TextInput::make('certificate_number')
                            ->label(FilamentUi::field('certificate_number')),
                    ]),
            ]);
    }
}
