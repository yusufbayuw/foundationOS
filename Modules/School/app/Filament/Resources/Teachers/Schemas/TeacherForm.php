<?php

namespace Modules\School\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('user_id')
                            ->label(FilamentUi::field('user_id'))
                            ->relationship('user', 'name'),
                    ]),

                Section::make(FilamentUi::text('Employment Details'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('nip')
                            ->label(FilamentUi::field('nip')),
                        TextInput::make('nuptk')
                            ->label(FilamentUi::field('nuptk')),
                        TextInput::make('nrg')
                            ->label(FilamentUi::field('nrg')),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status')),
                        TextInput::make('employment_status')
                            ->label(FilamentUi::field('employment_status')),
                        DatePicker::make('join_date')
                            ->label(FilamentUi::field('join_date')),
                        DatePicker::make('resignation_date')
                            ->label(FilamentUi::field('resignation_date')),
                    ]),

                Section::make(FilamentUi::text('Certification & Education'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_certified')
                            ->label(FilamentUi::field('is_certified'))
                            ->required(),
                        TextInput::make('certification_year')
                            ->label(FilamentUi::field('certification_year')),
                        TextInput::make('certification_number')
                            ->label(FilamentUi::field('certification_number')),
                        TextInput::make('highest_education')
                            ->label(FilamentUi::field('highest_education')),
                        TextInput::make('major_study')
                            ->label(FilamentUi::field('major_study')),
                        TextInput::make('university')
                            ->label(FilamentUi::field('university')),
                    ]),

                Section::make(FilamentUi::text('Position & Teaching'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('functional_position')
                            ->label(FilamentUi::field('functional_position')),
                        TextInput::make('structural_position')
                            ->label(FilamentUi::field('structural_position')),
                        TextInput::make('teaching_hours_per_week')
                            ->label(FilamentUi::field('teaching_hours_per_week'))
                            ->numeric(),
                        Textarea::make('subject_specializations')
                            ->label(FilamentUi::field('subject_specializations'))
                            ->columnSpanFull(),
                        Textarea::make('class_advisor_history')
                            ->label(FilamentUi::field('class_advisor_history'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Compensation & Benefits'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('base_salary')
                            ->label(FilamentUi::field('base_salary'))
                            ->numeric(),
                        TextInput::make('allowance')
                            ->label(FilamentUi::field('allowance'))
                            ->numeric(),
                        TextInput::make('bpjs_tk_number')
                            ->label(FilamentUi::field('bpjs_tk_number')),
                        TextInput::make('bpjs_kes_number')
                            ->label(FilamentUi::field('bpjs_kes_number')),
                    ]),
            ]);
    }
}
