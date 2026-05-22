<?php

namespace Modules\Procurement\Filament\Resources\RfqVendors\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class RfqVendorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('References'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('requestForQuotation.id')
                            ->label(FilamentUi::text('Request for quotation')),
                        TextEntry::make('vendor.name')
                            ->label(FilamentUi::text('Vendor')),
                    ]),

                Section::make(FilamentUi::text('Invitation & Response'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('invitation_date')
                            ->label(FilamentUi::field('invitation_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('response_deadline')
                            ->label(FilamentUi::field('response_deadline'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('responded_at')
                            ->label(FilamentUi::field('responded_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('quotation_amount')
                            ->label(FilamentUi::field('quotation_amount'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('quotation_document')
                            ->label(FilamentUi::field('quotation_document'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Evaluation'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('technical_score')
                            ->label(FilamentUi::field('technical_score'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('price_score')
                            ->label(FilamentUi::field('price_score'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('total_score')
                            ->label(FilamentUi::field('total_score'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('ranking')
                            ->label(FilamentUi::field('ranking'))
                            ->numeric()
                            ->placeholder('-'),
                        IconEntry::make('is_shortlisted')
                            ->boolean(),
                        IconEntry::make('is_awarded')
                            ->boolean(),
                        TextEntry::make('award_reason')
                            ->label(FilamentUi::field('award_reason'))
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
