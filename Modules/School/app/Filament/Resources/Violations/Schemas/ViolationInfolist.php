<?php

namespace Modules\School\Filament\Resources\Violations\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ViolationInfolist
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
                        TextEntry::make('student.id')
                            ->label(FilamentUi::text('Student')),
                        TextEntry::make('violationType.name')
                            ->label(FilamentUi::text('Violation type'))
                            ->placeholder('-'),
                        TextEntry::make('date')
                            ->label(FilamentUi::field('date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('severity')
                            ->label(FilamentUi::field('severity'))
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status'))
                            ->placeholder('-'),
                        TextEntry::make('location')
                            ->label(FilamentUi::field('location'))
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Reporting & Handling'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('reported_by')
                            ->label(FilamentUi::field('reported_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('handled_by')
                            ->label(FilamentUi::field('handled_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('witnesses')
                            ->label(FilamentUi::field('witnesses'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Sanctions & Resolution'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('sanctions')
                            ->label(FilamentUi::field('sanctions'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('sanction_duration_days')
                            ->label(FilamentUi::field('sanction_duration_days'))
                            ->numeric()
                            ->placeholder('-'),
                        IconEntry::make('parent_notified')
                            ->boolean(),
                        TextEntry::make('parent_meeting_date')
                            ->label(FilamentUi::field('parent_meeting_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('resolution_notes')
                            ->label(FilamentUi::field('resolution_notes'))
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
