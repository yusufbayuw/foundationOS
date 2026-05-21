<?php

namespace Modules\Library\Filament\Resources\Fines\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class FineInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Scope')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('loan.id')
                            ->label(FilamentUi::text('Loan')),
                    ]),

                Section::make('Fine Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('fine_type')
                            ->label(FilamentUi::field('fine_type')),
                        TextEntry::make('amount')
                            ->label(FilamentUi::field('amount'))
                            ->numeric(),
                        TextEntry::make('paid_amount')
                            ->label(FilamentUi::field('paid_amount'))
                            ->numeric(),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                    ]),

                Section::make('Timeline & Notes')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('issued_at')
                            ->label(FilamentUi::field('issued_at'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('paid_at')
                            ->label(FilamentUi::field('paid_at'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Timestamps')
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
