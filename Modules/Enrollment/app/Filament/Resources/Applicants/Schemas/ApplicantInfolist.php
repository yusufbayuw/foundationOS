<?php

namespace Modules\Enrollment\Filament\Resources\Applicants\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ApplicantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('admissionPeriod.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Admission period')),
                TextEntry::make('registration_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('registration_number')),
                TextEntry::make('full_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('full_name')),
                TextEntry::make('birth_place')
                    ->label(\Modules\Core\Support\FilamentUi::field('birth_place'))
                    ->placeholder('-'),
                TextEntry::make('birth_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('birth_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('gender')
                    ->label(\Modules\Core\Support\FilamentUi::field('gender'))
                    ->placeholder('-'),
                TextEntry::make('religion')
                    ->label(\Modules\Core\Support\FilamentUi::field('religion'))
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->label(\Modules\Core\Support\FilamentUi::field('address'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                    ->placeholder('-'),
                TextEntry::make('parent_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('parent_name'))
                    ->placeholder('-'),
                TextEntry::make('parent_phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('parent_phone'))
                    ->placeholder('-'),
                TextEntry::make('previous_school')
                    ->label(\Modules\Core\Support\FilamentUi::field('previous_school'))
                    ->placeholder('-'),
                TextEntry::make('previous_school_address')
                    ->label(\Modules\Core\Support\FilamentUi::field('previous_school_address'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('nisn')
                    ->label(\Modules\Core\Support\FilamentUi::field('nisn'))
                    ->placeholder('-'),
                TextEntry::make('ijazah_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('ijazah_number'))
                    ->placeholder('-'),
                TextEntry::make('average_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('average_score'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('achievement_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('achievement_count'))
                    ->numeric(),
                TextEntry::make('achievement_details')
                    ->label(\Modules\Core\Support\FilamentUi::field('achievement_details'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('program_choice_1_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('program_choice_1_id'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('program_choice_2_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('program_choice_2_id'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('test_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('test_score'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('interview_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('interview_score'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('final_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('final_score'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('ranking')
                    ->label(\Modules\Core\Support\FilamentUi::field('ranking'))
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_passed')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('acceptedProgram.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Accepted program'))
                    ->placeholder('-'),
                TextEntry::make('enrollment_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('enrollment_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('converted_to_student_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('converted_to_student_id'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('photo')
                    ->label(\Modules\Core\Support\FilamentUi::field('photo'))
                    ->placeholder('-'),
                TextEntry::make('documents')
                    ->label(\Modules\Core\Support\FilamentUi::field('documents'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
