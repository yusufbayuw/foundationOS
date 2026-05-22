<?php

namespace Modules\Campus\Filament\Resources\Lecturers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class LecturerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Academic Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('study_program_id')
                            ->label(FilamentUi::field('study_program_id'))
                            ->relationship('studyProgram', 'name'),
                        TextInput::make('nidn')
                            ->label(FilamentUi::field('nidn')),
                        TextInput::make('employee_number')
                            ->label(FilamentUi::field('employee_number')),
                    ]),

                Section::make(FilamentUi::text('Personal Information'))
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label(FilamentUi::field('user_id'))
                            ->relationship('user', 'name'),
                        TextInput::make('full_name')
                            ->label(FilamentUi::field('full_name')),
                        TextInput::make('academic_title_prefix')
                            ->label(FilamentUi::field('academic_title_prefix')),
                        TextInput::make('academic_title_suffix')
                            ->label(FilamentUi::field('academic_title_suffix')),
                    ]),

                Section::make(FilamentUi::text('Position & Status'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('functional_position')
                            ->label(FilamentUi::field('functional_position')),
                        TextInput::make('employment_status')
                            ->label(FilamentUi::field('employment_status')),
                        DatePicker::make('join_date')
                            ->label(FilamentUi::field('join_date')),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                    ]),

                Section::make(FilamentUi::text('Contact'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('email')
                            ->label(FilamentUi::text('Email address'))
                            ->email(),
                        TextInput::make('phone')
                            ->label(FilamentUi::field('phone'))
                            ->tel(),
                    ]),
            ]);
    }
}
