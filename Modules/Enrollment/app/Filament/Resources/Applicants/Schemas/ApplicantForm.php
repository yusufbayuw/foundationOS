<?php

namespace Modules\Enrollment\Filament\Resources\Applicants\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class ApplicantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Admission Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('admission_period_id')
                            ->label(FilamentUi::field('admission_period_id'))
                            ->relationship('admissionPeriod', 'name')
                            ->required(),
                        TextInput::make('registration_number')
                            ->label(FilamentUi::field('registration_number'))
                            ->required(),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('registered'),
                    ]),

                Section::make(FilamentUi::text('Personal Data'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('full_name')
                            ->label(FilamentUi::field('full_name'))
                            ->required(),
                        TextInput::make('birth_place')
                            ->label(FilamentUi::field('birth_place')),
                        DatePicker::make('birth_date')
                            ->label(FilamentUi::field('birth_date')),
                        TextInput::make('gender')
                            ->label(FilamentUi::field('gender')),
                        TextInput::make('religion')
                            ->label(FilamentUi::field('religion')),
                        TextInput::make('phone')
                            ->label(FilamentUi::field('phone'))
                            ->tel(),
                        TextInput::make('email')
                            ->label(FilamentUi::text('Email address'))
                            ->email(),
                        Textarea::make('address')
                            ->label(FilamentUi::field('address'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Parent & Previous School'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('parent_name')
                            ->label(FilamentUi::field('parent_name')),
                        TextInput::make('parent_phone')
                            ->label(FilamentUi::field('parent_phone'))
                            ->tel(),
                        TextInput::make('previous_school')
                            ->label(FilamentUi::field('previous_school')),
                        TextInput::make('nisn')
                            ->label(FilamentUi::field('nisn')),
                        TextInput::make('ijazah_number')
                            ->label(FilamentUi::field('ijazah_number')),
                        TextInput::make('average_score')
                            ->label(FilamentUi::field('average_score'))
                            ->numeric(),
                        Textarea::make('previous_school_address')
                            ->label(FilamentUi::field('previous_school_address'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Achievements & Program Choices'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('achievement_count')
                            ->label(FilamentUi::field('achievement_count'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('program_choice_1_id')
                            ->label(FilamentUi::field('program_choice_1_id'))
                            ->numeric(),
                        TextInput::make('program_choice_2_id')
                            ->label(FilamentUi::field('program_choice_2_id'))
                            ->numeric(),
                        Textarea::make('achievement_details')
                            ->label(FilamentUi::field('achievement_details'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Selection & Enrollment'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('test_score')
                            ->label(FilamentUi::field('test_score'))
                            ->numeric(),
                        TextInput::make('interview_score')
                            ->label(FilamentUi::field('interview_score'))
                            ->numeric(),
                        TextInput::make('final_score')
                            ->label(FilamentUi::field('final_score'))
                            ->numeric(),
                        TextInput::make('ranking')
                            ->label(FilamentUi::field('ranking'))
                            ->numeric(),
                        Toggle::make('is_passed')
                            ->label(FilamentUi::field('is_passed')),
                        Select::make('accepted_program_id')
                            ->label(FilamentUi::field('accepted_program_id'))
                            ->relationship('acceptedProgram', 'name'),
                        DatePicker::make('enrollment_date')
                            ->label(FilamentUi::field('enrollment_date')),
                        TextInput::make('converted_to_student_id')
                            ->label(FilamentUi::field('converted_to_student_id'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Documents & Notes'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('photo')
                            ->label(FilamentUi::field('photo')),
                        Textarea::make('documents')
                            ->label(FilamentUi::field('documents'))
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
