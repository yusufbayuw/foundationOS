<?php

namespace Modules\Campus\Filament\Resources\Lecturers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class LecturerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Academic Affiliation'))
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
                        TextEntry::make('studyProgram.name')
                            ->label(FilamentUi::text('Study program'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Personal Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('full_name')
                            ->label(FilamentUi::field('full_name'))
                            ->placeholder('-'),
                        TextEntry::make('nidn')
                            ->label(FilamentUi::field('nidn'))
                            ->placeholder('-'),
                        TextEntry::make('employee_number')
                            ->label(FilamentUi::field('employee_number'))
                            ->placeholder('-'),
                        TextEntry::make('email')
                            ->label(FilamentUi::text('Email address'))
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->label(FilamentUi::field('phone'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Academic Position'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('academic_title_prefix')
                            ->label(FilamentUi::field('academic_title_prefix'))
                            ->placeholder('-'),
                        TextEntry::make('academic_title_suffix')
                            ->label(FilamentUi::field('academic_title_suffix'))
                            ->placeholder('-'),
                        TextEntry::make('functional_position')
                            ->label(FilamentUi::field('functional_position'))
                            ->placeholder('-'),
                        TextEntry::make('employment_status')
                            ->label(FilamentUi::field('employment_status'))
                            ->placeholder('-'),
                        TextEntry::make('join_date')
                            ->label(FilamentUi::field('join_date'))
                            ->date()
                            ->placeholder('-'),
                        IconEntry::make('is_active')
                            ->boolean(),
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
