<?php

namespace Modules\School\Filament\Resources\Subjects\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class SubjectInfolist
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
                        TextEntry::make('curriculum.name')
                            ->label(FilamentUi::text('Curriculum'))
                            ->placeholder('-'),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('short_name')
                            ->label(FilamentUi::field('short_name'))
                            ->placeholder('-'),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code')),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Academic Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('grade_level')
                            ->label(FilamentUi::field('grade_level'))
                            ->placeholder('-'),
                        TextEntry::make('credits')
                            ->label(FilamentUi::field('credits'))
                            ->numeric()
                            ->placeholder('-'),
                        IconEntry::make('is_mandatory')
                            ->boolean(),
                        TextEntry::make('subject_group')
                            ->label(FilamentUi::field('subject_group'))
                            ->placeholder('-'),
                        IconEntry::make('has_practicum')
                            ->boolean(),
                    ]),

                Section::make(FilamentUi::text('Display & Outcomes'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('color_code')
                            ->label(FilamentUi::field('color_code'))
                            ->placeholder('-'),
                        TextEntry::make('icon')
                            ->label(FilamentUi::field('icon'))
                            ->placeholder('-'),
                        TextEntry::make('learning_outcomes')
                            ->label(FilamentUi::field('learning_outcomes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
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
