<?php

namespace Modules\School\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name'),
                Select::make('academic_year_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('academic_year_id'))
                    ->relationship('academicYear', 'name'),
                Select::make('user_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('user_id'))
                    ->relationship('user', 'name'),
                TextInput::make('nis')
                    ->label(\Modules\Core\Support\FilamentUi::field('nis')),
                TextInput::make('nisn')
                    ->label(\Modules\Core\Support\FilamentUi::field('nisn')),
                DatePicker::make('entry_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_date')),
                TextInput::make('entry_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_type')),
                TextInput::make('previous_school')
                    ->label(\Modules\Core\Support\FilamentUi::field('previous_school')),
                TextInput::make('previous_school_npsn')
                    ->label(\Modules\Core\Support\FilamentUi::field('previous_school_npsn')),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('active'),
                DatePicker::make('graduation_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('graduation_date')),
                TextInput::make('ijazah_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('ijazah_number')),
                TextInput::make('skhun_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('skhun_number')),
                TextInput::make('track')
                    ->label(\Modules\Core\Support\FilamentUi::field('track')),
                Textarea::make('extracurricular_activities')
                    ->label(\Modules\Core\Support\FilamentUi::field('extracurricular_activities'))
                    ->columnSpanFull(),
                Textarea::make('achievements')
                    ->label(\Modules\Core\Support\FilamentUi::field('achievements'))
                    ->columnSpanFull(),
                Textarea::make('health_notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('health_notes'))
                    ->columnSpanFull(),
                Textarea::make('special_needs')
                    ->label(\Modules\Core\Support\FilamentUi::field('special_needs'))
                    ->columnSpanFull(),
                TextInput::make('scholarship_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('scholarship_status')),
                TextInput::make('family_card_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('family_card_number')),
                TextInput::make('father_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('father_name')),
                TextInput::make('father_nik')
                    ->label(\Modules\Core\Support\FilamentUi::field('father_nik')),
                TextInput::make('father_education')
                    ->label(\Modules\Core\Support\FilamentUi::field('father_education')),
                TextInput::make('father_job')
                    ->label(\Modules\Core\Support\FilamentUi::field('father_job')),
                TextInput::make('father_phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('father_phone'))
                    ->tel(),
                TextInput::make('mother_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('mother_name')),
                TextInput::make('mother_nik')
                    ->label(\Modules\Core\Support\FilamentUi::field('mother_nik')),
                TextInput::make('mother_education')
                    ->label(\Modules\Core\Support\FilamentUi::field('mother_education')),
                TextInput::make('mother_job')
                    ->label(\Modules\Core\Support\FilamentUi::field('mother_job')),
                TextInput::make('mother_phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('mother_phone'))
                    ->tel(),
                TextInput::make('guardian_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('guardian_name')),
                TextInput::make('guardian_relation')
                    ->label(\Modules\Core\Support\FilamentUi::field('guardian_relation')),
                TextInput::make('guardian_phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('guardian_phone'))
                    ->tel(),
                Textarea::make('guardian_address')
                    ->label(\Modules\Core\Support\FilamentUi::field('guardian_address'))
                    ->columnSpanFull(),
                TextInput::make('residence_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('residence_type')),
                TextInput::make('transport_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('transport_type')),
                TextInput::make('travel_time_minutes')
                    ->label(\Modules\Core\Support\FilamentUi::field('travel_time_minutes'))
                    ->numeric(),
                TextInput::make('distance_km')
                    ->label(\Modules\Core\Support\FilamentUi::field('distance_km'))
                    ->numeric(),
            ]);
    }
}
