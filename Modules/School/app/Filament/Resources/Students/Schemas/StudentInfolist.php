<?php

namespace Modules\School\Filament\Resources\Students\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Organization'))
                            ->placeholder('-'),
                        TextEntry::make('academicYear.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('Academic year'))
                            ->placeholder('-'),
                        TextEntry::make('user.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('User'))
                            ->placeholder('-'),
                        TextEntry::make('nis')
                            ->label(\Modules\Core\Support\FilamentUi::field('nis'))
                            ->placeholder('-'),
                        TextEntry::make('nisn')
                            ->label(\Modules\Core\Support\FilamentUi::field('nisn'))
                            ->placeholder('-'),
                    ]),

                Section::make('Enrollment')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('entry_date')
                            ->label(\Modules\Core\Support\FilamentUi::field('entry_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('entry_type')
                            ->label(\Modules\Core\Support\FilamentUi::field('entry_type'))
                            ->placeholder('-'),
                        TextEntry::make('previous_school')
                            ->label(\Modules\Core\Support\FilamentUi::field('previous_school'))
                            ->placeholder('-'),
                        TextEntry::make('previous_school_npsn')
                            ->label(\Modules\Core\Support\FilamentUi::field('previous_school_npsn'))
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->label(\Modules\Core\Support\FilamentUi::field('status')),
                        TextEntry::make('graduation_date')
                            ->label(\Modules\Core\Support\FilamentUi::field('graduation_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('ijazah_number')
                            ->label(\Modules\Core\Support\FilamentUi::field('ijazah_number'))
                            ->placeholder('-'),
                        TextEntry::make('skhun_number')
                            ->label(\Modules\Core\Support\FilamentUi::field('skhun_number'))
                            ->placeholder('-'),
                        TextEntry::make('track')
                            ->label(\Modules\Core\Support\FilamentUi::field('track'))
                            ->placeholder('-'),
                    ]),

                Section::make('Academic & Health')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('extracurricular_activities')
                            ->label(\Modules\Core\Support\FilamentUi::field('extracurricular_activities'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('achievements')
                            ->label(\Modules\Core\Support\FilamentUi::field('achievements'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('health_notes')
                            ->label(\Modules\Core\Support\FilamentUi::field('health_notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('special_needs')
                            ->label(\Modules\Core\Support\FilamentUi::field('special_needs'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('scholarship_status')
                            ->label(\Modules\Core\Support\FilamentUi::field('scholarship_status'))
                            ->placeholder('-'),
                        TextEntry::make('family_card_number')
                            ->label(\Modules\Core\Support\FilamentUi::field('family_card_number'))
                            ->placeholder('-'),
                    ]),

                Section::make('Father Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('father_name')
                            ->label(\Modules\Core\Support\FilamentUi::field('father_name'))
                            ->placeholder('-'),
                        TextEntry::make('father_nik')
                            ->label(\Modules\Core\Support\FilamentUi::field('father_nik'))
                            ->placeholder('-'),
                        TextEntry::make('father_education')
                            ->label(\Modules\Core\Support\FilamentUi::field('father_education'))
                            ->placeholder('-'),
                        TextEntry::make('father_job')
                            ->label(\Modules\Core\Support\FilamentUi::field('father_job'))
                            ->placeholder('-'),
                        TextEntry::make('father_phone')
                            ->label(\Modules\Core\Support\FilamentUi::field('father_phone'))
                            ->placeholder('-'),
                    ]),

                Section::make('Mother Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('mother_name')
                            ->label(\Modules\Core\Support\FilamentUi::field('mother_name'))
                            ->placeholder('-'),
                        TextEntry::make('mother_nik')
                            ->label(\Modules\Core\Support\FilamentUi::field('mother_nik'))
                            ->placeholder('-'),
                        TextEntry::make('mother_education')
                            ->label(\Modules\Core\Support\FilamentUi::field('mother_education'))
                            ->placeholder('-'),
                        TextEntry::make('mother_job')
                            ->label(\Modules\Core\Support\FilamentUi::field('mother_job'))
                            ->placeholder('-'),
                        TextEntry::make('mother_phone')
                            ->label(\Modules\Core\Support\FilamentUi::field('mother_phone'))
                            ->placeholder('-'),
                    ]),

                Section::make('Guardian Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('guardian_name')
                            ->label(\Modules\Core\Support\FilamentUi::field('guardian_name'))
                            ->placeholder('-'),
                        TextEntry::make('guardian_relation')
                            ->label(\Modules\Core\Support\FilamentUi::field('guardian_relation'))
                            ->placeholder('-'),
                        TextEntry::make('guardian_phone')
                            ->label(\Modules\Core\Support\FilamentUi::field('guardian_phone'))
                            ->placeholder('-'),
                        TextEntry::make('guardian_address')
                            ->label(\Modules\Core\Support\FilamentUi::field('guardian_address'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Residence & Transport')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('residence_type')
                            ->label(\Modules\Core\Support\FilamentUi::field('residence_type'))
                            ->placeholder('-'),
                        TextEntry::make('transport_type')
                            ->label(\Modules\Core\Support\FilamentUi::field('transport_type'))
                            ->placeholder('-'),
                        TextEntry::make('travel_time_minutes')
                            ->label(\Modules\Core\Support\FilamentUi::field('travel_time_minutes'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('distance_km')
                            ->label(\Modules\Core\Support\FilamentUi::field('distance_km'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
