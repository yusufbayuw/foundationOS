<?php

namespace Modules\Enrollment\Filament\Resources\Applicants\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ApplicantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('admission_period_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('admission_period_id'))
                    ->relationship('admissionPeriod', 'name')
                    ->required(),
                TextInput::make('registration_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('registration_number'))
                    ->required(),
                TextInput::make('full_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('full_name'))
                    ->required(),
                TextInput::make('birth_place')
                    ->label(\Modules\Core\Support\FilamentUi::field('birth_place')),
                DatePicker::make('birth_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('birth_date')),
                TextInput::make('gender')
                    ->label(\Modules\Core\Support\FilamentUi::field('gender')),
                TextInput::make('religion')
                    ->label(\Modules\Core\Support\FilamentUi::field('religion')),
                Textarea::make('address')
                    ->label(\Modules\Core\Support\FilamentUi::field('address'))
                    ->columnSpanFull(),
                TextInput::make('phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                    ->tel(),
                TextInput::make('email')
                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                    ->email(),
                TextInput::make('parent_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('parent_name')),
                TextInput::make('parent_phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('parent_phone'))
                    ->tel(),
                TextInput::make('previous_school')
                    ->label(\Modules\Core\Support\FilamentUi::field('previous_school')),
                Textarea::make('previous_school_address')
                    ->label(\Modules\Core\Support\FilamentUi::field('previous_school_address'))
                    ->columnSpanFull(),
                TextInput::make('nisn')
                    ->label(\Modules\Core\Support\FilamentUi::field('nisn')),
                TextInput::make('ijazah_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('ijazah_number')),
                TextInput::make('average_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('average_score'))
                    ->numeric(),
                TextInput::make('achievement_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('achievement_count'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('achievement_details')
                    ->label(\Modules\Core\Support\FilamentUi::field('achievement_details'))
                    ->columnSpanFull(),
                TextInput::make('program_choice_1_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('program_choice_1_id'))
                    ->numeric(),
                TextInput::make('program_choice_2_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('program_choice_2_id'))
                    ->numeric(),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('registered'),
                TextInput::make('test_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('test_score'))
                    ->numeric(),
                TextInput::make('interview_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('interview_score'))
                    ->numeric(),
                TextInput::make('final_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('final_score'))
                    ->numeric(),
                TextInput::make('ranking')
                    ->label(\Modules\Core\Support\FilamentUi::field('ranking'))
                    ->numeric(),
                Toggle::make('is_passed')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_passed')),
                Select::make('accepted_program_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('accepted_program_id'))
                    ->relationship('acceptedProgram', 'name'),
                DatePicker::make('enrollment_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('enrollment_date')),
                TextInput::make('converted_to_student_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('converted_to_student_id'))
                    ->numeric(),
                TextInput::make('photo')
                    ->label(\Modules\Core\Support\FilamentUi::field('photo')),
                Textarea::make('documents')
                    ->label(\Modules\Core\Support\FilamentUi::field('documents'))
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
