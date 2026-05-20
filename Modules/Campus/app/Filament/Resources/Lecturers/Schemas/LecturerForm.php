<?php

namespace Modules\Campus\Filament\Resources\Lecturers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class LecturerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Academic Information')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('study_program_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('study_program_id'))
                            ->relationship('studyProgram', 'name'),
                        TextInput::make('nidn')
                            ->label(\Modules\Core\Support\FilamentUi::field('nidn')),
                        TextInput::make('employee_number')
                            ->label(\Modules\Core\Support\FilamentUi::field('employee_number')),
                    ]),

                Section::make('Personal Information')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('user_id'))
                            ->relationship('user', 'name'),
                        TextInput::make('full_name')
                            ->label(\Modules\Core\Support\FilamentUi::field('full_name')),
                        TextInput::make('academic_title_prefix')
                            ->label(\Modules\Core\Support\FilamentUi::field('academic_title_prefix')),
                        TextInput::make('academic_title_suffix')
                            ->label(\Modules\Core\Support\FilamentUi::field('academic_title_suffix')),
                    ]),

                Section::make('Position & Status')
                    ->columns(2)
                    ->schema([
                        TextInput::make('functional_position')
                            ->label(\Modules\Core\Support\FilamentUi::field('functional_position')),
                        TextInput::make('employment_status')
                            ->label(\Modules\Core\Support\FilamentUi::field('employment_status')),
                        DatePicker::make('join_date')
                            ->label(\Modules\Core\Support\FilamentUi::field('join_date')),
                        Toggle::make('is_active')
                            ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                            ->required(),
                    ]),

                Section::make('Contact')
                    ->columns(2)
                    ->schema([
                        TextInput::make('email')
                            ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                            ->email(),
                        TextInput::make('phone')
                            ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                            ->tel(),
                    ]),
            ]);
    }
}
