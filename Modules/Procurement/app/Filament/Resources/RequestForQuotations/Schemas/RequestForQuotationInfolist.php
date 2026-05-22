<?php

namespace Modules\Procurement\Filament\Resources\RequestForQuotations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class RequestForQuotationInfolist
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
                        TextEntry::make('purchaseRequisition.id')
                            ->label(FilamentUi::text('Purchase requisition')),
                        TextEntry::make('created_by')
                            ->label(FilamentUi::field('created_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('rfq_number')
                            ->label(FilamentUi::field('rfq_number')),
                        TextEntry::make('rfq_date')
                            ->label(FilamentUi::field('rfq_date'))
                            ->date(),
                        TextEntry::make('closing_date')
                            ->label(FilamentUi::field('closing_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Budget & Status'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('total_estimated_budget')
                            ->label(FilamentUi::field('total_estimated_budget'))
                            ->numeric(),
                        TextEntry::make('currency')
                            ->label(FilamentUi::field('currency')),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('award_criteria')
                            ->label(FilamentUi::field('award_criteria'))
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
