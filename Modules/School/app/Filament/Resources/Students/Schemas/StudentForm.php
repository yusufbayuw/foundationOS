<?php

namespace Modules\School\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('academic_year_id')
                            ->label(FilamentUi::field('academic_year_id'))
                            ->relationship('academicYear', 'name'),
                        Select::make('user_id')
                            ->label(FilamentUi::field('user_id'))
                            ->relationship('user', 'name'),
                        TextInput::make('nis')
                            ->label(FilamentUi::field('nis')),
                        TextInput::make('nisn')
                            ->label(FilamentUi::field('nisn')),
                    ]),

                Section::make('Enrollment')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('entry_date')
                            ->label(FilamentUi::field('entry_date')),
                        TextInput::make('entry_type')
                            ->label(FilamentUi::field('entry_type')),
                        TextInput::make('previous_school')
                            ->label(FilamentUi::field('previous_school')),
                        TextInput::make('previous_school_npsn')
                            ->label(FilamentUi::field('previous_school_npsn')),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('active'),
                        DatePicker::make('graduation_date')
                            ->label(FilamentUi::field('graduation_date')),
                        TextInput::make('ijazah_number')
                            ->label(FilamentUi::field('ijazah_number')),
                        TextInput::make('skhun_number')
                            ->label(FilamentUi::field('skhun_number')),
                        TextInput::make('track')
                            ->label(FilamentUi::field('track')),
                    ]),

                Section::make('Academic & Health')
                    ->columns(2)
                    ->schema([
                        Textarea::make('extracurricular_activities')
                            ->label(FilamentUi::field('extracurricular_activities'))
                            ->columnSpanFull(),
                        Textarea::make('achievements')
                            ->label(FilamentUi::field('achievements'))
                            ->columnSpanFull(),
                        Textarea::make('health_notes')
                            ->label(FilamentUi::field('health_notes'))
                            ->columnSpanFull(),
                        Textarea::make('special_needs')
                            ->label(FilamentUi::field('special_needs'))
                            ->columnSpanFull(),
                        TextInput::make('scholarship_status')
                            ->label(FilamentUi::field('scholarship_status')),
                        TextInput::make('family_card_number')
                            ->label(FilamentUi::field('family_card_number')),
                    ]),

                Section::make('Father Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('father_name')
                            ->label(FilamentUi::field('father_name')),
                        TextInput::make('father_nik')
                            ->label(FilamentUi::field('father_nik')),
                        TextInput::make('father_education')
                            ->label(FilamentUi::field('father_education')),
                        TextInput::make('father_job')
                            ->label(FilamentUi::field('father_job')),
                        TextInput::make('father_phone')
                            ->label(FilamentUi::field('father_phone'))
                            ->tel(),
                    ]),

                Section::make('Mother Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('mother_name')
                            ->label(FilamentUi::field('mother_name')),
                        TextInput::make('mother_nik')
                            ->label(FilamentUi::field('mother_nik')),
                        TextInput::make('mother_education')
                            ->label(FilamentUi::field('mother_education')),
                        TextInput::make('mother_job')
                            ->label(FilamentUi::field('mother_job')),
                        TextInput::make('mother_phone')
                            ->label(FilamentUi::field('mother_phone'))
                            ->tel(),
                    ]),

                Section::make('Guardian Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('guardian_name')
                            ->label(FilamentUi::field('guardian_name')),
                        TextInput::make('guardian_relation')
                            ->label(FilamentUi::field('guardian_relation')),
                        TextInput::make('guardian_phone')
                            ->label(FilamentUi::field('guardian_phone'))
                            ->tel(),
                        Textarea::make('guardian_address')
                            ->label(FilamentUi::field('guardian_address'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Residence & Transport')
                    ->columns(2)
                    ->schema([
                        TextInput::make('residence_type')
                            ->label(FilamentUi::field('residence_type')),
                        TextInput::make('transport_type')
                            ->label(FilamentUi::field('transport_type')),
                        TextInput::make('travel_time_minutes')
                            ->label(FilamentUi::field('travel_time_minutes'))
                            ->numeric(),
                        TextInput::make('distance_km')
                            ->label(FilamentUi::field('distance_km'))
                            ->numeric(),
                    ]),
            ]);
    }
}
