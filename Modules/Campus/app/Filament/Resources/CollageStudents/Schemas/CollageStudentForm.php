<?php

namespace Modules\Campus\Filament\Resources\CollageStudents\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class CollageStudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name'),
                Select::make('user_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('user_id'))
                    ->relationship('user', 'name'),
                Select::make('study_program_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('study_program_id'))
                    ->relationship('studyProgram', 'name'),
                Select::make('academic_advisor_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('academic_advisor_id'))
                    ->relationship('academicAdvisor', 'id'),
                TextInput::make('student_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('student_number'))
                    ->required(),
                TextInput::make('national_student_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('national_student_number')),
                TextInput::make('full_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('full_name')),
                TextInput::make('entry_year')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_year'))
                    ->numeric(),
                TextInput::make('entry_semester')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_semester'))
                    ->numeric(),
                TextInput::make('admission_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('admission_type')),
                TextInput::make('current_semester')
                    ->label(\Modules\Core\Support\FilamentUi::field('current_semester'))
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('active'),
                TextInput::make('email')
                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                    ->email(),
                TextInput::make('phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                    ->tel(),
                DatePicker::make('graduation_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('graduation_date'))
            ]);
    }
}
