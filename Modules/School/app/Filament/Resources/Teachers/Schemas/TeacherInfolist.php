<?php

namespace Modules\School\Filament\Resources\Teachers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class TeacherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization'))
                            ->placeholder('-'),
                        TextEntry::make('user.name')
                            ->label(FilamentUi::text('User'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Employment Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('nip')
                            ->label(FilamentUi::field('nip'))
                            ->placeholder('-'),
                        TextEntry::make('nuptk')
                            ->label(FilamentUi::field('nuptk'))
                            ->placeholder('-'),
                        TextEntry::make('nrg')
                            ->label(FilamentUi::field('nrg'))
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status'))
                            ->placeholder('-'),
                        TextEntry::make('employment_status')
                            ->label(FilamentUi::field('employment_status'))
                            ->placeholder('-'),
                        TextEntry::make('join_date')
                            ->label(FilamentUi::field('join_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('resignation_date')
                            ->label(FilamentUi::field('resignation_date'))
                            ->date()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Certification & Education'))
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_certified')
                            ->boolean(),
                        TextEntry::make('certification_year')
                            ->label(FilamentUi::field('certification_year'))
                            ->placeholder('-'),
                        TextEntry::make('certification_number')
                            ->label(FilamentUi::field('certification_number'))
                            ->placeholder('-'),
                        TextEntry::make('highest_education')
                            ->label(FilamentUi::field('highest_education'))
                            ->placeholder('-'),
                        TextEntry::make('major_study')
                            ->label(FilamentUi::field('major_study'))
                            ->placeholder('-'),
                        TextEntry::make('university')
                            ->label(FilamentUi::field('university'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Position & Teaching'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('functional_position')
                            ->label(FilamentUi::field('functional_position'))
                            ->placeholder('-'),
                        TextEntry::make('structural_position')
                            ->label(FilamentUi::field('structural_position'))
                            ->placeholder('-'),
                        TextEntry::make('teaching_hours_per_week')
                            ->label(FilamentUi::field('teaching_hours_per_week'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('subject_specializations')
                            ->label(FilamentUi::field('subject_specializations'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('class_advisor_history')
                            ->label(FilamentUi::field('class_advisor_history'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Compensation & Benefits'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('base_salary')
                            ->label(FilamentUi::field('base_salary'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('allowance')
                            ->label(FilamentUi::field('allowance'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('bpjs_tk_number')
                            ->label(FilamentUi::field('bpjs_tk_number'))
                            ->placeholder('-'),
                        TextEntry::make('bpjs_kes_number')
                            ->label(FilamentUi::field('bpjs_kes_number'))
                            ->placeholder('-'),
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
