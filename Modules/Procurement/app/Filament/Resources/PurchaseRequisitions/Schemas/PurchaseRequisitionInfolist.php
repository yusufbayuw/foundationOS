<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitions\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class PurchaseRequisitionInfolist
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
                        TextEntry::make('user.name')
                            ->label(FilamentUi::text('User'))
                            ->placeholder('-'),
                        TextEntry::make('requested_by')
                            ->label(FilamentUi::field('requested_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('approved_by')
                            ->label(FilamentUi::field('approved_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('request_number')
                            ->label(FilamentUi::field('request_number')),
                        TextEntry::make('priority')
                            ->label(FilamentUi::field('priority')),
                    ]),

                Section::make(FilamentUi::text('Dates'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('request_date')
                            ->label(FilamentUi::field('request_date'))
                            ->date(),
                        TextEntry::make('required_date')
                            ->label(FilamentUi::field('required_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('approved_at')
                            ->label(FilamentUi::field('approved_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Financial Summary'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('total_items')
                            ->label(FilamentUi::field('total_items'))
                            ->numeric(),
                        TextEntry::make('total_estimated_amount')
                            ->label(FilamentUi::field('total_estimated_amount'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Status & Notes'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        IconEntry::make('ready_for_sourcing')
                            ->label(FilamentUi::text('Ready For Sourcing'))
                            ->boolean(),
                        TextEntry::make('justification')
                            ->label(FilamentUi::field('justification'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('rejection_reason')
                            ->label(FilamentUi::field('rejection_reason'))
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
