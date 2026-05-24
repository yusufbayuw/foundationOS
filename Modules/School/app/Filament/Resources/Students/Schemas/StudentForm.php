<?php

namespace Modules\School\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class StudentForm
{
    private const STATUS_OPTIONS = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'graduated' => 'Graduated',
        'transferred' => 'Transferred',
        'dropped_out' => 'Dropped Out',
    ];

    private const ENTRY_TYPE_OPTIONS = [
        'new_student' => 'New Student',
        'transfer' => 'Transfer',
        'returning' => 'Returning',
        'mutation' => 'Mutation',
        'other' => 'Other',
    ];

    private const TRACK_OPTIONS = [
        'regular' => 'Regular',
        'science' => 'Science',
        'social' => 'Social',
        'language' => 'Language',
        'vocational' => 'Vocational',
        'religious' => 'Religious',
        'inclusive' => 'Inclusive',
        'other' => 'Other',
    ];

    private const EDUCATION_OPTIONS = [
        'no_school' => 'No School',
        'elementary' => 'Elementary',
        'junior_high' => 'Junior High',
        'senior_high' => 'Senior High',
        'diploma' => 'Diploma',
        'bachelor' => 'Bachelor',
        'master' => 'Master',
        'doctorate' => 'Doctorate',
        'other' => 'Other',
    ];

    private const RESIDENCE_TYPE_OPTIONS = [
        'parents' => 'Parents',
        'guardian' => 'Guardian',
        'boarding_house' => 'Boarding House',
        'dormitory' => 'Dormitory',
        'relatives' => 'Relatives',
        'other' => 'Other',
    ];

    private const TRANSPORT_TYPE_OPTIONS = [
        'walking' => 'Walking',
        'bicycle' => 'Bicycle',
        'motorcycle' => 'Motorcycle',
        'car' => 'Car',
        'public_transport' => 'Public Transport',
        'school_bus' => 'School Bus',
        'ride_hailing' => 'Ride Hailing',
        'other' => 'Other',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Information'))
                    ->columns(2)
                    ->schema(static::basicInformationFields()),

                Section::make(FilamentUi::text('Enrollment'))
                    ->columns(2)
                    ->schema(static::enrollmentFields()),

                Section::make(FilamentUi::text('Academic & Health'))
                    ->columns(2)
                    ->schema(static::academicAndHealthFields()),

                Section::make(FilamentUi::text('Father Information'))
                    ->columns(2)
                    ->schema(static::fatherInformationFields()),

                Section::make(FilamentUi::text('Mother Information'))
                    ->columns(2)
                    ->schema(static::motherInformationFields()),

                Section::make(FilamentUi::text('Guardian Information'))
                    ->columns(2)
                    ->schema(static::guardianInformationFields()),

                Section::make(FilamentUi::text('Residence & Transport'))
                    ->columns(2)
                    ->schema(static::residenceAndTransportFields()),
            ]);
    }

    public static function basicInformationFields(): array
    {
        return [
            TenantField::make(),
            TenantField::organizationSelect(),
            Select::make('academic_year_id')
                ->label(FilamentUi::field('academic_year_id'))
                ->relationship('academicYear', 'name'),
            Select::make('user_id')
                ->label(FilamentUi::field('user_id'))
                ->relationship('user', 'name'),
            TextInput::make('nis')
                ->label(FilamentUi::field('nis'))
                ->maxLength(50)
                ->regex('/^[A-Za-z0-9.\/-]+$/'),
            TextInput::make('nisn')
                ->label(FilamentUi::field('nisn'))
                ->maxLength(20)
                ->regex('/^[0-9]+$/'),
        ];
    }

    public static function enrollmentFields(): array
    {
        return [
            DatePicker::make('entry_date')
                ->label(FilamentUi::field('entry_date')),
            Select::make('entry_type')
                ->label(FilamentUi::field('entry_type'))
                ->options(self::ENTRY_TYPE_OPTIONS),
            TextInput::make('previous_school')
                ->label(FilamentUi::field('previous_school'))
                ->maxLength(255),
            TextInput::make('previous_school_npsn')
                ->label(FilamentUi::field('previous_school_npsn'))
                ->maxLength(20)
                ->regex('/^[0-9]+$/'),
            Select::make('status')
                ->label(FilamentUi::field('status'))
                ->options(self::STATUS_OPTIONS)
                ->required()
                ->default('active'),
            DatePicker::make('graduation_date')
                ->label(FilamentUi::field('graduation_date')),
            TextInput::make('ijazah_number')
                ->label(FilamentUi::field('ijazah_number'))
                ->maxLength(100),
            TextInput::make('skhun_number')
                ->label(FilamentUi::field('skhun_number'))
                ->maxLength(100),
            Select::make('track')
                ->label(FilamentUi::field('track'))
                ->options(self::TRACK_OPTIONS),
        ];
    }

    public static function academicAndHealthFields(): array
    {
        return [
            TagsInput::make('extracurricular_activities')
                ->label(FilamentUi::field('extracurricular_activities'))
                ->columnSpanFull(),
            TagsInput::make('achievements')
                ->label(FilamentUi::field('achievements'))
                ->columnSpanFull(),
            TagsInput::make('health_notes')
                ->label(FilamentUi::field('health_notes'))
                ->columnSpanFull(),
            TagsInput::make('special_needs')
                ->label(FilamentUi::field('special_needs'))
                ->columnSpanFull(),
            TextInput::make('scholarship_status')
                ->label(FilamentUi::field('scholarship_status'))
                ->maxLength(255),
            TextInput::make('family_card_number')
                ->label(FilamentUi::field('family_card_number'))
                ->maxLength(50)
                ->regex('/^[0-9]+$/'),
        ];
    }

    public static function fatherInformationFields(): array
    {
        return [
            TextInput::make('father_name')
                ->label(FilamentUi::field('father_name'))
                ->maxLength(255),
            TextInput::make('father_nik')
                ->label(FilamentUi::field('father_nik'))
                ->maxLength(20)
                ->regex('/^[0-9]+$/'),
            Select::make('father_education')
                ->label(FilamentUi::field('father_education'))
                ->options(self::EDUCATION_OPTIONS),
            TextInput::make('father_job')
                ->label(FilamentUi::field('father_job'))
                ->maxLength(255),
            TextInput::make('father_phone')
                ->label(FilamentUi::field('father_phone'))
                ->tel()
                ->maxLength(30)
                ->regex('/^[0-9+\-\s().]+$/'),
        ];
    }

    public static function motherInformationFields(): array
    {
        return [
            TextInput::make('mother_name')
                ->label(FilamentUi::field('mother_name'))
                ->maxLength(255),
            TextInput::make('mother_nik')
                ->label(FilamentUi::field('mother_nik'))
                ->maxLength(20)
                ->regex('/^[0-9]+$/'),
            Select::make('mother_education')
                ->label(FilamentUi::field('mother_education'))
                ->options(self::EDUCATION_OPTIONS),
            TextInput::make('mother_job')
                ->label(FilamentUi::field('mother_job'))
                ->maxLength(255),
            TextInput::make('mother_phone')
                ->label(FilamentUi::field('mother_phone'))
                ->tel()
                ->maxLength(30)
                ->regex('/^[0-9+\-\s().]+$/'),
        ];
    }

    public static function guardianInformationFields(): array
    {
        return [
            TextInput::make('guardian_name')
                ->label(FilamentUi::field('guardian_name'))
                ->maxLength(255),
            TextInput::make('guardian_relation')
                ->label(FilamentUi::field('guardian_relation'))
                ->maxLength(100),
            TextInput::make('guardian_phone')
                ->label(FilamentUi::field('guardian_phone'))
                ->tel()
                ->maxLength(30)
                ->regex('/^[0-9+\-\s().]+$/'),
            Textarea::make('guardian_address')
                ->label(FilamentUi::field('guardian_address'))
                ->maxLength(1000)
                ->columnSpanFull(),
        ];
    }

    public static function residenceAndTransportFields(): array
    {
        return [
            Select::make('residence_type')
                ->label(FilamentUi::field('residence_type'))
                ->options(self::RESIDENCE_TYPE_OPTIONS),
            Select::make('transport_type')
                ->label(FilamentUi::field('transport_type'))
                ->options(self::TRANSPORT_TYPE_OPTIONS),
            TextInput::make('travel_time_minutes')
                ->label(FilamentUi::field('travel_time_minutes'))
                ->integer()
                ->minValue(0)
                ->maxValue(1440),
            TextInput::make('distance_km')
                ->label(FilamentUi::field('distance_km'))
                ->numeric()
                ->minValue(0)
                ->maxValue(999999.99),
        ];
    }
}
