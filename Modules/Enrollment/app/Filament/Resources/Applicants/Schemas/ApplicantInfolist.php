<?php

namespace Modules\Enrollment\Filament\Resources\Applicants\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ApplicantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Admission Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('admissionPeriod.name')
                            ->label(FilamentUi::text('Admission period')),
                        TextEntry::make('registration_number')
                            ->label(FilamentUi::field('registration_number')),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                    ]),

                Section::make(FilamentUi::text('Personal Data'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('full_name')
                            ->label(FilamentUi::field('full_name')),
                        TextEntry::make('birth_place')
                            ->label(FilamentUi::field('birth_place'))
                            ->placeholder('-'),
                        TextEntry::make('birth_date')
                            ->label(FilamentUi::field('birth_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('gender')
                            ->label(FilamentUi::field('gender'))
                            ->placeholder('-'),
                        TextEntry::make('religion')
                            ->label(FilamentUi::field('religion'))
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->label(FilamentUi::field('phone'))
                            ->placeholder('-'),
                        TextEntry::make('email')
                            ->label(FilamentUi::text('Email address'))
                            ->placeholder('-'),
                        TextEntry::make('address')
                            ->label(FilamentUi::field('address'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Parent & Previous School'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('parent_name')
                            ->label(FilamentUi::field('parent_name'))
                            ->placeholder('-'),
                        TextEntry::make('parent_phone')
                            ->label(FilamentUi::field('parent_phone'))
                            ->placeholder('-'),
                        TextEntry::make('previous_school')
                            ->label(FilamentUi::field('previous_school'))
                            ->placeholder('-'),
                        TextEntry::make('nisn')
                            ->label(FilamentUi::field('nisn'))
                            ->placeholder('-'),
                        TextEntry::make('ijazah_number')
                            ->label(FilamentUi::field('ijazah_number'))
                            ->placeholder('-'),
                        TextEntry::make('average_score')
                            ->label(FilamentUi::field('average_score'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('previous_school_address')
                            ->label(FilamentUi::field('previous_school_address'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Achievements & Program Choices'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('achievement_count')
                            ->label(FilamentUi::field('achievement_count'))
                            ->numeric(),
                        TextEntry::make('program_choice_1_id')
                            ->label(FilamentUi::field('program_choice_1_id'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('program_choice_2_id')
                            ->label(FilamentUi::field('program_choice_2_id'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('achievement_details')
                            ->label(FilamentUi::field('achievement_details'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Selection & Enrollment'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('test_score')
                            ->label(FilamentUi::field('test_score'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('interview_score')
                            ->label(FilamentUi::field('interview_score'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('final_score')
                            ->label(FilamentUi::field('final_score'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('ranking')
                            ->label(FilamentUi::field('ranking'))
                            ->numeric()
                            ->placeholder('-'),
                        IconEntry::make('is_passed')
                            ->boolean()
                            ->placeholder('-'),
                        TextEntry::make('acceptedProgram.name')
                            ->label(FilamentUi::text('Accepted program'))
                            ->placeholder('-'),
                        TextEntry::make('enrollment_date')
                            ->label(FilamentUi::field('enrollment_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('converted_to_student_id')
                            ->label(FilamentUi::field('converted_to_student_id'))
                            ->numeric()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Documents & Notes'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('photo')
                            ->label(FilamentUi::field('photo'))
                            ->placeholder('-'),
                        TextEntry::make('documents')
                            ->label(FilamentUi::field('documents'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Timestamps'))
                    ->columns(2)
                    ->schema([
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
