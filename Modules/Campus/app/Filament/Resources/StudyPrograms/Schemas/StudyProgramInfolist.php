<?php

namespace Modules\Campus\Filament\Resources\StudyPrograms\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StudyProgramInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Relationships'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization'))
                            ->placeholder('-'),
                        TextEntry::make('faculty.name')
                            ->label(FilamentUi::text('Faculty'))
                            ->placeholder('-'),
                        TextEntry::make('headOfProgram.id')
                            ->label(FilamentUi::text('Head of program'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Program Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code'))
                            ->placeholder('-'),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('degree_level')
                            ->label(FilamentUi::field('degree_level'))
                            ->placeholder('-'),
                        TextEntry::make('accreditation')
                            ->label(FilamentUi::field('accreditation'))
                            ->placeholder('-'),
                        TextEntry::make('total_credits_required')
                            ->label(FilamentUi::field('total_credits_required'))
                            ->numeric(),
                        IconEntry::make('is_active')
                            ->boolean(),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
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
