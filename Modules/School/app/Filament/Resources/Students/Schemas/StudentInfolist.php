<?php

namespace Modules\School\Filament\Resources\Students\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization'))
                            ->placeholder('-'),
                        TextEntry::make('academicYear.name')
                            ->label(FilamentUi::text('Academic year'))
                            ->placeholder('-'),
                        TextEntry::make('user.name')
                            ->label(FilamentUi::text('User'))
                            ->placeholder('-'),
                        TextEntry::make('nis')
                            ->label(FilamentUi::field('nis'))
                            ->placeholder('-'),
                        TextEntry::make('nisn')
                            ->label(FilamentUi::field('nisn'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Enrollment'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('entry_date')
                            ->label(FilamentUi::field('entry_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('entry_type')
                            ->label(FilamentUi::field('entry_type'))
                            ->placeholder('-'),
                        TextEntry::make('previous_school')
                            ->label(FilamentUi::field('previous_school'))
                            ->placeholder('-'),
                        TextEntry::make('previous_school_npsn')
                            ->label(FilamentUi::field('previous_school_npsn'))
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('graduation_date')
                            ->label(FilamentUi::field('graduation_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('ijazah_number')
                            ->label(FilamentUi::field('ijazah_number'))
                            ->placeholder('-'),
                        TextEntry::make('skhun_number')
                            ->label(FilamentUi::field('skhun_number'))
                            ->placeholder('-'),
                        TextEntry::make('track')
                            ->label(FilamentUi::field('track'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Academic & Health'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('extracurricular_activities')
                            ->label(FilamentUi::field('extracurricular_activities'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('achievements')
                            ->label(FilamentUi::field('achievements'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('health_notes')
                            ->label(FilamentUi::field('health_notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('special_needs')
                            ->label(FilamentUi::field('special_needs'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('scholarship_status')
                            ->label(FilamentUi::field('scholarship_status'))
                            ->placeholder('-'),
                        TextEntry::make('family_card_number')
                            ->label(FilamentUi::field('family_card_number'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Father Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('father_name')
                            ->label(FilamentUi::field('father_name'))
                            ->placeholder('-'),
                        TextEntry::make('father_nik')
                            ->label(FilamentUi::field('father_nik'))
                            ->placeholder('-'),
                        TextEntry::make('father_education')
                            ->label(FilamentUi::field('father_education'))
                            ->placeholder('-'),
                        TextEntry::make('father_job')
                            ->label(FilamentUi::field('father_job'))
                            ->placeholder('-'),
                        TextEntry::make('father_phone')
                            ->label(FilamentUi::field('father_phone'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Mother Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('mother_name')
                            ->label(FilamentUi::field('mother_name'))
                            ->placeholder('-'),
                        TextEntry::make('mother_nik')
                            ->label(FilamentUi::field('mother_nik'))
                            ->placeholder('-'),
                        TextEntry::make('mother_education')
                            ->label(FilamentUi::field('mother_education'))
                            ->placeholder('-'),
                        TextEntry::make('mother_job')
                            ->label(FilamentUi::field('mother_job'))
                            ->placeholder('-'),
                        TextEntry::make('mother_phone')
                            ->label(FilamentUi::field('mother_phone'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Guardian Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('guardian_name')
                            ->label(FilamentUi::field('guardian_name'))
                            ->placeholder('-'),
                        TextEntry::make('guardian_relation')
                            ->label(FilamentUi::field('guardian_relation'))
                            ->placeholder('-'),
                        TextEntry::make('guardian_phone')
                            ->label(FilamentUi::field('guardian_phone'))
                            ->placeholder('-'),
                        TextEntry::make('guardian_address')
                            ->label(FilamentUi::field('guardian_address'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Residence & Transport'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('residence_type')
                            ->label(FilamentUi::field('residence_type'))
                            ->placeholder('-'),
                        TextEntry::make('transport_type')
                            ->label(FilamentUi::field('transport_type'))
                            ->placeholder('-'),
                        TextEntry::make('travel_time_minutes')
                            ->label(FilamentUi::field('travel_time_minutes'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('distance_km')
                            ->label(FilamentUi::field('distance_km'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
