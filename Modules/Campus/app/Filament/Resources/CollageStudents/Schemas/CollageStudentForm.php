<?php

namespace Modules\Campus\Filament\Resources\CollageStudents\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class CollageStudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Academic Registration'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('study_program_id')
                            ->label(FilamentUi::field('study_program_id'))
                            ->relationship('studyProgram', 'name'),
                        Select::make('academic_advisor_id')
                            ->label(FilamentUi::field('academic_advisor_id'))
                            ->relationship('academicAdvisor', 'id'),
                        TextInput::make('student_number')
                            ->label(FilamentUi::field('student_number'))
                            ->required(),
                        TextInput::make('national_student_number')
                            ->label(FilamentUi::field('national_student_number')),
                        TextInput::make('admission_type')
                            ->label(FilamentUi::field('admission_type')),
                    ]),

                Section::make(FilamentUi::text('Personal Information'))
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label(FilamentUi::field('user_id'))
                            ->relationship('user', 'name'),
                        TextInput::make('full_name')
                            ->label(FilamentUi::field('full_name')),
                        TextInput::make('email')
                            ->label(FilamentUi::text('Email address'))
                            ->email(),
                        TextInput::make('phone')
                            ->label(FilamentUi::field('phone'))
                            ->tel(),
                    ]),

                Section::make(FilamentUi::text('Academic Progress'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('entry_year')
                            ->label(FilamentUi::field('entry_year'))
                            ->numeric(),
                        TextInput::make('entry_semester')
                            ->label(FilamentUi::field('entry_semester'))
                            ->numeric(),
                        TextInput::make('current_semester')
                            ->label(FilamentUi::field('current_semester'))
                            ->required()
                            ->numeric()
                            ->default(1),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('active'),
                        DatePicker::make('graduation_date')
                            ->label(FilamentUi::field('graduation_date')),
                    ]),
            ]);
    }
}
